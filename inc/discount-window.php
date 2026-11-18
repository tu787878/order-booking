<?php
/** Optional activation window for the single direct-discount configuration. */
function ds_discount_parse_datetime($value)
{
    if (!is_string($value)) return false;
    $date = DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i', $value, new DateTimeZone('Europe/Berlin'));
    return $date && $date->format('Y-m-d\\TH:i') === $value ? $date : false;
}

function ds_discount_window_active($timestamp = null)
{
    $window = get_option('ds_discount_window');
    if (!is_array($window)) return true;
    $from = $window['from'] ?? '';
    $to = $window['to'] ?? '';
    if ($from === '' && $to === '') return true;
    $start = ds_discount_parse_datetime($from);
    $end = ds_discount_parse_datetime($to);
    if (!$start || !$end || $start > $end) return false;
    $now = $timestamp === null ? time() : (int) $timestamp;
    // Include the entire selected end minute.
    return $now >= $start->getTimestamp() && $now < $end->getTimestamp() + 60;
}

/** Shared by cart, checkout and order creation; former profiles are ignored. */
function ds_discount_option($key, $timestamp = null)
{
    if (!ds_discount_window_active($timestamp)) return $key === 'dsmart_custom_discount_date' ? array() : '';
    return get_option($key);
}

function ds_save_discount_window()
{
    if (!isset($_POST['ds_discount_window_present'], $_POST['ds_discount_window_nonce']) ||
        !current_user_can('manage_options') || !wp_verify_nonce($_POST['ds_discount_window_nonce'], 'ds_discount_window')) return;
    $values = array();
    foreach (array('from', 'to') as $bound) {
        $date = $_POST['ds_discount_' . $bound . '_date'] ?? '';
        $time = $_POST['ds_discount_' . $bound . '_time'] ?? '';
        if (!is_string($date) || !is_string($time)) return;
        $values[$bound] = $date === '' && $time === '' ? '' : wp_unslash($date) . 'T' . wp_unslash($time);
    }
    if ($values['from'] !== '' || $values['to'] !== '') {
        $start = ds_discount_parse_datetime($values['from']);
        $end = ds_discount_parse_datetime($values['to']);
        if (!$start || !$end || $start > $end) {
            echo '<div class="notice notice-error"><p>Rabatt-Zeitraum nicht gespeichert: Bitte Start- und Enddatum mit Uhrzeit vollständig und in der richtigen Reihenfolge angeben.</p></div>';
            return;
        }
    }
    update_option('ds_discount_window', $values, false);
}

function ds_render_discount_window()
{
    $window = get_option('ds_discount_window');
    wp_nonce_field('ds_discount_window', 'ds_discount_window_nonce');
    echo '<input type="hidden" name="ds_discount_window_present" value="1"><div class="form-group ds-discount-window"><h2>Rabatt-Zeitraum (optional)</h2>';
    foreach (array('from' => 'Von', 'to' => 'Bis') as $bound => $label) {
        $parsed = ds_discount_parse_datetime(is_array($window) ? ($window[$bound] ?? '') : '');
        echo '<label>' . esc_html($label) . ' <input type="date" class="widefat" name="ds_discount_' . esc_attr($bound) . '_date" value="' . esc_attr($parsed ? $parsed->format('Y-m-d') : '') . '"> <input type="time" class="widefat" name="ds_discount_' . esc_attr($bound) . '_time" value="' . esc_attr($parsed ? $parsed->format('H:i') : '') . '"></label>';
    }
    echo '</div>';
    ?>
    <script>
    (function () {
        var group = document.querySelector('.ds-discount-window');
        var fields = Array.from(group.querySelectorAll('input'));
        function validate() {
            var filled = fields.some(function (input) { return input.value !== ''; });
            fields.forEach(function (input) { input.required = filled; input.setCustomValidity(''); });
            var fromDate = group.querySelector('[name="ds_discount_from_date"]');
            var fromTime = group.querySelector('[name="ds_discount_from_time"]');
            var toDate = group.querySelector('[name="ds_discount_to_date"]');
            var toTime = group.querySelector('[name="ds_discount_to_time"]');
            if (fields.every(function (input) { return input.value !== ''; }) && fromDate.value + 'T' + fromTime.value > toDate.value + 'T' + toTime.value) {
                toDate.setCustomValidity('Das Ende muss am oder nach dem Start liegen.');
            }
        }
        group.addEventListener('input', validate);
        group.addEventListener('change', validate);
        validate();
    })();
    </script>
    <?php
}
