<?php
// Standalone regression checks: php tests/shop-analytics-test.php [export]
define('ABSPATH', __DIR__);
function add_action() {}
function current_user_can($capability) { return in_array($capability, $GLOBALS['test_caps'] ?? array(), true); }
function get_option($key) { return '2'; }
function __($s) { return $s; }
function esc_html__($s) { return $s; }
function sanitize_text_field($s) { return trim(strip_tags($s)); }
function wp_unslash($s) { return $s; }
function wp_strip_all_tags($s) { return strip_tags($s); }
function wp_timezone() { return new DateTimeZone('Europe/Berlin'); }
function wp_timezone_string() { return 'Europe/Berlin'; }
function ds_convert_currency_price($currency, $value) { return $currency === '2' ? $value * 2 : $value; }
function ds_price_format_text_with_symbol($v, $c) { return '€' . number_format($v, 2); }
function wp_get_current_user() { return (object) array('roles' => $GLOBALS['test_roles'] ?? array('administrator')); }
function is_user_logged_in() { return $GLOBALS['test_logged_in'] ?? true; }
function wp_die($message, $title = '', $args = array()) { throw new RuntimeException($message); }
function check_admin_referer($s) {}
function nocache_headers() {}
function is_wp_error($v) { return $v instanceof WP_Error; }
class WP_Error { public function __construct($a, $b) {} }
function get_post_meta($id, $key, $single) { return $GLOBALS['fixtures'][$id][$key] ?? ''; }
class WP_Query {
    public $posts;
    public function __construct($args) {
        $GLOBALS['query_args'] = $args;
        $this->posts = $args['paged'] === 1 ? array((object) array('ID' => 1, 'post_date' => '2026-08-31 23:59:59'), (object) array('ID' => 2, 'post_date' => '2026-09-01 00:00:00')) : array();
    }
}
require __DIR__ . '/../inc/shop-analytics.php';
$GLOBALS['fixtures'] = array(
    1 => array('currency' => '2', 'total' => 30, 'shipping_method' => 'shipping', 'item' => array(array('product_id' => 7, 'title' => '=Pizza', 'quantity' => 2, 'price' => 20), array('product_id' => 7, 'title' => '=Pizza', 'quantity' => 1, 'price' => 8))),
    2 => array('currency' => '2', 'total' => 10, 'shipping_method' => 'direct', 'item' => array(7 => array('title' => '=Pizza', 'quantity' => 1, 'price' => 5), 9 => array('title' => 'Tea', 'quantity' => 2, 'price' => 3), 10 => null))
);
function verify($ok, $message) { if (!$ok) { throw new RuntimeException($message); } }
$GLOBALS['test_caps'] = array('edit_products', 'manage_options');
$GLOBALS['test_roles'] = array('shop');
verify(!dsmart_analytics_allowed(), 'Client shop role denied even with extra capabilities');
try { dsmart_analytics_export(); throw new LogicException('Client export unexpectedly allowed'); } catch (RuntimeException $error) { verify(strpos($error->getMessage(), 'Keine Berechtigung') !== false, 'Export denied before querying'); }
try { dsmart_analytics_admin_page(); throw new LogicException('Client page unexpectedly allowed'); } catch (RuntimeException $error) { verify(strpos($error->getMessage(), 'Keine Berechtigung') !== false, 'Admin report denied'); }
$GLOBALS['test_roles'] = array('administrator');
verify(dsmart_analytics_allowed(), 'Administrator retains access');
$GLOBALS['test_logged_in'] = false;
verify(!dsmart_analytics_allowed(), 'Anonymous access denied');
$GLOBALS['test_logged_in'] = true;
verify(dsmart_analytics_money('2', 1234.56) === 1234.56, 'Large amounts retain thousands');
$f = dsmart_analytics_filters(array('period' => 'custom', 'from' => '2026-08-31', 'to' => '2026-09-01'));
$r = dsmart_analytics_report($f);
verify($r['orders'] === 2 && $r['quantity'] == 6 && $r['revenue'] == 40, 'EUR snapshots are not converted again');
$p = $r['products'][0];
verify($p['orders'] === 2 && $p['quantity'] == 4 && $p['revenue'] == 33, 'Variants count once per order; line prices are not multiplied again');
verify($p['shipping'] == 3 && $p['direct'] == 1, 'Fulfilment quantities');
verify($r['heatmap'][0][23] === 1 && $r['heatmap'][1][0] === 1, 'Local weekday and midnight boundaries');
verify($GLOBALS['query_args']['date_query'][0]['before'] === '2026-09-01 23:59:59', 'Inclusive final day');
verify(is_wp_error(dsmart_analytics_filters(array('period' => 'custom', 'from' => '2026-02-30', 'to' => '2026-03-10'))), 'Invalid calendar date rejected');
verify(is_wp_error(dsmart_analytics_filters(array('period' => 'custom', 'from' => '2026-09-02', 'to' => '2026-09-01'))), 'Reversed range rejected');
$f['search'] = 'Tea'; $searched = dsmart_analytics_report($f);
verify(count($searched['products']) === 1 && $searched['orders'] === 2, 'Search only filters products');
$f['search'] = ''; $f['sort'] = 'name'; $f['direction'] = 'asc';
verify(dsmart_analytics_report($f)['products'][0]['name'] === '=Pizza', 'Name sorting');
verify(count(dsmart_analytics_charts($r)) === 3, 'Three shared chart definitions');
verify(strpos(dsmart_analytics_svg(array('type' => 'pie', 'title' => 'Empty', 'labels' => array('None'), 'values' => array(0))), 'Keine Bestellungen') !== false, 'Empty chart state');
if (isset($argv[1]) && $argv[1] === 'export') { $_GET = $f; dsmart_analytics_export(); }
echo "Analytics regression checks passed.\n";
