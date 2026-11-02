<?php
// Run with: php tests/preparation-time-test.php
$GLOBALS['options'] = array('delay_time' => '45', 'delay_delivery' => '30');
function get_option($key) { return $GLOBALS['options'][$key] ?? false; }
function update_option($key, $value, $autoload = null) { $GLOBALS['options'][$key] = $value; }
function wp_verify_nonce($nonce, $action) { return $nonce === 'valid' && $action === 'ds_preparation_windows'; }
function wp_unslash($value) { return $value; }
require __DIR__ . '/../inc/preparation-time.php';
function expect_same($expected, $actual, $label) {
    if ($expected !== $actual) {
        throw new RuntimeException($label . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}
function at_clock($clock, $option = 'delay_time', $date = '2026-10-29') {
    $time = new DateTimeImmutable($date . ' ' . $clock, new DateTimeZone('Europe/Berlin'));
    return ds_get_preparation_time($option, $time->getTimestamp());
}
expect_same('45', at_clock('12:00'), 'No windows retains fixed default');
$windows = array(
    array('from' => '12:00', 'to' => '14:00', 'minutes' => '20'),
    array('from' => '18:00', 'to' => '19:00', 'minutes' => '35'),
);
$_POST = array('ds_preparation_nonce' => 'invalid', 'delay_time_windows' => $windows);
ds_save_preparation_windows();
expect_same(false, get_option('delay_time_windows'), 'Invalid nonce does not save');
$_POST['ds_preparation_nonce'] = 'valid';
ds_save_preparation_windows();
expect_same(20, at_clock('12:00'), 'Inclusive start');
expect_same(20, at_clock('13:59'), 'Lunch window');
expect_same('45', at_clock('14:00'), 'Exclusive end');
expect_same('45', at_clock('17:59'), 'Gap uses default');
expect_same(35, at_clock('18:00'), 'Second window');
expect_same('45', at_clock('19:00'), 'Second end');
expect_same('30', at_clock('12:30', 'delay_delivery'), 'Pickup remains independent');
expect_same(20, at_clock('12:30', 'delay_time', '2026-07-01'), 'Summer timezone');
$GLOBALS['options']['delay_time_windows'][] = array('from' => '13:00', 'to' => '15:00', 'minutes' => 60);
expect_same(20, at_clock('13:30'), 'First overlapping window wins');
$GLOBALS['options']['delay_delivery_windows'] = array(array('from' => '22:00', 'to' => '02:00', 'minutes' => 0));
expect_same(0, at_clock('22:00', 'delay_delivery'), 'Overnight start and zero minutes');
expect_same(0, at_clock('01:59', 'delay_delivery'), 'Overnight after midnight');
expect_same('30', at_clock('02:00', 'delay_delivery'), 'Overnight exclusive end');
expect_same(array(), ds_sanitize_preparation_windows(array(
    array('from' => '25:00', 'to' => '14:00', 'minutes' => 20),
    array('from' => '12:00', 'to' => '12:00', 'minutes' => 20),
    array('from' => '12:00', 'to' => '14:00', 'minutes' => -1),
    array('from' => '12:00', 'to' => '14:00', 'minutes' => '2.5'),
    array('from' => '12:00', 'to' => '14:00', 'minutes' => 1441),
    array('from' => array(), 'to' => '14:00', 'minutes' => 20),
    'invalid',
)), 'Malformed rows are ignored');
$_POST = array('ds_preparation_nonce' => 'valid');
ds_save_preparation_windows();
expect_same(array(), get_option('delay_time_windows'), 'Remove all delivery windows');
expect_same(array(), get_option('delay_delivery_windows'), 'Remove all pickup windows');
expect_same('45', at_clock('12:30'), 'Removing windows restores default');
$GLOBALS['options']['delay_time_windows'] = $windows;
expect_same('45', ds_get_preparation_time('delay_time'), 'No implicit current-clock window lookup');
expect_same(array('time' => '12:20', 'date' => '02-11-2026', 'minutes' => 20), ds_prepare_selected_time('delay_time', '12:00', '02-11-2026'), 'Selected noon plus custom preparation');
expect_same('12:50', ds_prepare_selected_time('delay_time', '12:30', '02-11-2026')['time'], 'Selected time within lunch window');
expect_same('14:45', ds_prepare_selected_time('delay_time', '14:00', '02-11-2026')['time'], 'Default at exclusive end');
expect_same('18:35', ds_prepare_selected_time('delay_time', '18:00', '02-11-2026')['time'], 'Evening window');
expect_same('19:45', ds_prepare_selected_time('delay_time', '19:00', '02-11-2026')['time'], 'Default outside evening window');
expect_same('12:30', ds_prepare_selected_time('delay_delivery', '12:00', '02-11-2026')['time'], 'Pickup uses own fixed default');
expect_same(array('time' => '00:15', 'date' => '03-11-2026', 'minutes' => 45), ds_prepare_selected_time('delay_time', '23:30', '02-11-2026'), 'Delivery date rollover');
expect_same(false, ds_prepare_selected_time('delay_time', '25:00', '02-11-2026'), 'Invalid selected clock');
expect_same(false, ds_prepare_selected_time('delay_time', '12:00', '31-02-2026'), 'Invalid selected date');
expect_same('So schnell wie möglich', ds_prepare_selected_time('delay_time', 'So schnell wie möglich')['time'], 'ASAP is preserved');
echo "Preparation-time regression checks passed.\n";
