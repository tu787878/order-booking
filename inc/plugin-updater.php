<?php
/**
 * Plugin Updater - Supports both GitHub releases and custom server updates
 * 
 * Usage:
 * 
 * For GitHub releases:
 * new Plugin_Updater(__FILE__, [
 *     'type' => 'github',
 *     'github_user' => 'yourusername',
 *     'github_repo' => 'your-plugin-repo',
 *     'github_token' => '', // Optional: for private repos
 * ]);
 * 
 * For custom server:
 * new Plugin_Updater(__FILE__, [
 *     'type' => 'custom',
 *     'update_url' => 'https://yourserver.com/updates/plugin-name.json',
 * ]);
 * 
 * For both (fallback support):
 * new Plugin_Updater(__FILE__, [
 *     'type' => 'both',
 *     'github_user' => 'yourusername',
 *     'github_repo' => 'your-plugin-repo',
 *     'update_url' => 'https://yourserver.com/updates/plugin-name.json',
 *     'prefer' => 'github', // or 'custom'
 * ]);
 */

if (!defined('ABSPATH')) { exit; }

class Plugin_Updater {
    
    private $plugin_file;
    private $plugin_slug;
    private $plugin_basename;
    private $config;
    private $version;
    private $cache_key;
    private $cache_allowed = true;
    
    /**
     * Initialize the updater
     * 
     * @param string $plugin_file Full path to main plugin file
     * @param array $config Configuration array
     */
    public function __construct($plugin_file, $config = []) {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = dirname(plugin_basename($plugin_file));
        $this->plugin_basename = plugin_basename($plugin_file);
        $this->config = wp_parse_args($config, [
            'type' => 'github', // 'github', 'custom', or 'both'
            'github_user' => '',
            'github_repo' => '',
            'github_token' => '', // Optional, for private repos
            'update_url' => '', // For custom server
            'prefer' => 'github', // When type is 'both'
            'cache_time' => 12 * HOUR_IN_SECONDS, // 12 hours
        ]);
        
        // Get plugin version from header
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugin_data = get_plugin_data($plugin_file);
        $this->version = $plugin_data['Version'];
        
        $this->cache_key = 'plugin_updater_' . md5($this->plugin_basename);
        
        // Hook into WordPress update system
        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
        add_filter('upgrader_source_selection', [$this, 'fix_source_dir'], 10, 4);
        add_action('upgrader_process_complete', [$this, 'clear_cache'], 10, 2);
    }
    
    /**
     * Check for plugin updates
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $update_data = $this->get_update_data();
        
        if ($update_data && version_compare($this->version, $update_data['version'], '<')) {
            $obj = new stdClass();
            $obj->slug = $this->plugin_slug;
            $obj->plugin = $this->plugin_basename;
            $obj->new_version = $update_data['version'];
            $obj->url = $update_data['homepage'] ?? '';
            $obj->package = $update_data['download_url'];
            $obj->tested = $update_data['tested'] ?? '';
            $obj->requires = $update_data['requires'] ?? '';
            $obj->requires_php = $update_data['requires_php'] ?? '';
            
            $transient->response[$this->plugin_basename] = $obj;
        }
        
        return $transient;
    }
    
    /**
     * Get plugin information for the update screen
     */
    public function plugin_info($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return $result;
        }
        
        if (!isset($args->slug) || $args->slug !== $this->plugin_slug) {
            return $result;
        }
        
        $update_data = $this->get_update_data();
        
        if (!$update_data) {
            return $result;
        }
        
        $obj = new stdClass();
        $obj->name = $update_data['name'] ?? $this->plugin_slug;
        $obj->slug = $this->plugin_slug;
        $obj->version = $update_data['version'];
        $obj->author = $update_data['author'] ?? '';
        $obj->homepage = $update_data['homepage'] ?? '';
        $obj->requires = $update_data['requires'] ?? '';
        $obj->tested = $update_data['tested'] ?? '';
        $obj->requires_php = $update_data['requires_php'] ?? '';
        $obj->download_link = $update_data['download_url'];
        $obj->sections = [
            'description' => $update_data['description'] ?? '',
            'changelog' => $update_data['changelog'] ?? '',
        ];
        $obj->banners = $update_data['banners'] ?? [];
        $obj->last_updated = $update_data['last_updated'] ?? '';
        
        return $obj;
    }
    
    /**
     * Fix the plugin directory name after extraction
     * GitHub releases extract to repo-name-version, but WordPress expects plugin-slug
     */
    public function fix_source_dir($source, $remote_source, $upgrader, $hook_extra = null) {
        global $wp_filesystem;

        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_basename) {
            return $source;
        }

        // Check if source already has the correct name
        if (basename($source) === $this->plugin_slug) {
            return $source;
        }

        $new_source = trailingslashit($remote_source) . $this->plugin_slug;

        // Only move if the new location doesn't already exist
        if ($wp_filesystem->exists($new_source)) {
            $wp_filesystem->delete($new_source, true);
        }

        if ($wp_filesystem->move($source, $new_source)) {
            return $new_source;
        }

        return $source;
    }
    
    /**
     * Clear update cache after upgrade
     */
    public function clear_cache($upgrader, $options) {
        if ($options['type'] === 'plugin' && isset($options['plugins'])) {
            foreach ($options['plugins'] as $plugin) {
                if ($plugin === $this->plugin_basename) {
                    delete_transient($this->cache_key);
                }
            }
        }
    }
    
    /**
     * Get update data from configured source(s)
     */
    private function get_update_data() {
        // Check cache first
        if ($this->cache_allowed) {
            $cached = get_transient($this->cache_key);
            if ($cached !== false) {
                return $cached;
            }
        }
        
        $data = null;
        $type = $this->config['type'];
        
        if ($type === 'github') {
            $data = $this->get_github_data();
        } elseif ($type === 'custom') {
            $data = $this->get_custom_data();
        } elseif ($type === 'both') {
            // Try preferred source first, then fallback
            if ($this->config['prefer'] === 'github') {
                $data = $this->get_github_data();
                if (!$data) {
                    $data = $this->get_custom_data();
                }
            } else {
                $data = $this->get_custom_data();
                if (!$data) {
                    $data = $this->get_github_data();
                }
            }
        }
        
        // Cache the result
        if ($data && $this->cache_allowed) {
            set_transient($this->cache_key, $data, $this->config['cache_time']);
        }
        
        return $data;
    }
    
    /**
     * Get update data from GitHub releases
     */
    private function get_github_data() {
        if (empty($this->config['github_user']) || empty($this->config['github_repo'])) {
            return null;
        }
        
        $url = sprintf(
            'https://api.github.com/repos/%s/%s/releases/latest',
            $this->config['github_user'],
            $this->config['github_repo']
        );
        
        $args = [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
            ],
        ];
        
        // Add authorization header if token provided
        if (!empty($this->config['github_token'])) {
            $args['headers']['Authorization'] = 'token ' . $this->config['github_token'];
        }
        
        $response = wp_remote_get($url, $args);
        
        if (is_wp_error($response)) {
            return null;
        }
        
        $body = wp_remote_retrieve_body($response);
        $release = json_decode($body, true);
        
        if (!$release || !isset($release['tag_name'])) {
            return null;
        }
        
        // Find the zip asset
        $download_url = null;
        if (!empty($release['assets'])) {
            foreach ($release['assets'] as $asset) {
                if (pathinfo($asset['name'], PATHINFO_EXTENSION) === 'zip') {
                    $download_url = $asset['browser_download_url'];
                    break;
                }
            }
        }
        
        // Fallback to zipball if no zip asset found
        if (!$download_url) {
            $download_url = $release['zipball_url'];
        }
        
        // Parse version from tag (remove 'v' prefix if present)
        $version = ltrim($release['tag_name'], 'v');
        
        return [
            'version' => $version,
            'download_url' => $download_url,
            'name' => $release['name'] ?? $this->plugin_slug,
            'homepage' => $release['html_url'] ?? '',
            'description' => wp_strip_all_tags($release['body'] ?? ''),
            'changelog' => $this->parse_github_changelog($release['body'] ?? ''),
            'last_updated' => $release['published_at'] ?? '',
            'author' => $this->config['github_user'],
        ];
    }
    
    /**
     * Get update data from custom server
     * 
     * Expected JSON format:
     * {
     *   "version": "1.0.8",
     *   "download_url": "https://yourserver.com/plugins/plugin-name-1.0.8.zip",
     *   "name": "Plugin Name",
     *   "homepage": "https://yoursite.com",
     *   "requires": "5.0",
     *   "tested": "6.4",
     *   "requires_php": "7.4",
     *   "description": "Plugin description",
     *   "changelog": "<h4>1.0.8</h4><ul><li>Feature added</li></ul>",
     *   "last_updated": "2026-01-29 12:00:00"
     * }
     */
    private function get_custom_data() {
        if (empty($this->config['update_url'])) {
            return null;
        }
        
        $response = wp_remote_get($this->config['update_url'], [
            'timeout' => 10,
        ]);
        
        if (is_wp_error($response)) {
            return null;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (!$data || !isset($data['version']) || !isset($data['download_url'])) {
            return null;
        }
        
        return $data;
    }
    
    /**
     * Parse GitHub markdown changelog to HTML
     */
    private function parse_github_changelog($markdown) {
        if (empty($markdown)) {
            return '';
        }
        
        // Basic markdown to HTML conversion
        $html = $markdown;
        
        // Headers
        $html = preg_replace('/^### (.+)$/m', '<h4>$1</h4>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^# (.+)$/m', '<h2>$1</h2>', $html);
        
        // Lists
        $html = preg_replace('/^\* (.+)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/^- (.+)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>\n?)+/s', '<ul>$0</ul>', $html);
        
        // Bold and italic
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
        
        // Links
        $html = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2">$1</a>', $html);
        
        // Line breaks
        $html = nl2br($html);
        
        return $html;
    }
}
