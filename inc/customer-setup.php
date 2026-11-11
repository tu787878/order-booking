<?php
/**
 * Customer-facing update access setup.
 *
 * The token is stored per WordPress installation and deliberately never
 * included in a release ZIP.
 */

if (!defined('ABSPATH')) {
    exit;
}

function dsmart_update_access_token_option() {
    return 'dsmart_order_booking_update_access_token';
}

function dsmart_get_update_access_token() {
    return (string) get_option(dsmart_update_access_token_option(), '');
}

add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=product',
        'Update-Zugang',
        'Update-Zugang',
        'manage_options',
        'dsmart-update-access',
        'dsmart_render_update_access_page'
    );
});

function dsmart_render_update_access_page() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to access this page.'));
    }

    $notice = '';
    if (isset($_POST['dsmart_update_access_action'])) {
        check_admin_referer('dsmart_update_access');

        if ($_POST['dsmart_update_access_action'] === 'save') {
            $token = isset($_POST['dsmart_update_access_token'])
                ? sanitize_text_field(wp_unslash($_POST['dsmart_update_access_token']))
                : '';

            if ($token !== '') {
                update_option(dsmart_update_access_token_option(), $token, false);
                $notice = __('Update-Zugang gespeichert.', 'dsmart');
            } else {
                $notice = __('Bitte einen Zugangstoken eingeben oder den gespeicherten Token entfernen.', 'dsmart');
            }
        }

        if ($_POST['dsmart_update_access_action'] === 'remove') {
            delete_option(dsmart_update_access_token_option());
            $notice = __('Gespeicherter Update-Zugang entfernt.', 'dsmart');
        }
    }

    $has_token = dsmart_get_update_access_token() !== '';
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Update-Zugang', 'dsmart'); ?></h1>
        <?php if ($notice !== '') : ?>
            <div class="notice notice-success is-dismissible"><p><?php echo esc_html($notice); ?></p></div>
        <?php endif; ?>
        <p><?php esc_html_e('Ein Techniker kann den Zugang beim Einrichten speichern. Alternativ kann der Kunde seinen eigenen Zugang hier eintragen.', 'dsmart'); ?></p>
        <p><?php esc_html_e('Der Token wird nur in dieser WordPress-Installation gespeichert und ist nicht Teil der Plugin-ZIP-Datei.', 'dsmart'); ?></p>
        <p><strong><?php echo $has_token ? esc_html__('Status: Token gespeichert.', 'dsmart') : esc_html__('Status: Kein Token gespeichert.', 'dsmart'); ?></strong></p>
        <form method="post">
            <?php wp_nonce_field('dsmart_update_access'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="dsmart_update_access_token"><?php esc_html_e('Update-Zugangstoken', 'dsmart'); ?></label></th>
                    <td>
                        <input name="dsmart_update_access_token" id="dsmart_update_access_token" type="password" class="regular-text" autocomplete="new-password" />
                        <p class="description"><?php esc_html_e('Der gespeicherte Wert wird aus Sicherheitsgründen nicht angezeigt. Einen neuen Wert eintragen, um ihn zu ersetzen.', 'dsmart'); ?></p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" name="dsmart_update_access_action" value="save" class="button button-primary"><?php esc_html_e('Token speichern', 'dsmart'); ?></button>
                <?php if ($has_token) : ?>
                    <button type="submit" name="dsmart_update_access_action" value="remove" class="button button-secondary" onclick="return confirm('<?php echo esc_js(__('Gespeicherten Token wirklich entfernen?', 'dsmart')); ?>');"><?php esc_html_e('Token entfernen', 'dsmart'); ?></button>
                <?php endif; ?>
            </p>
        </form>
    </div>
    <?php
}
