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
function slot_blocked($ranges, $clock) {
    foreach ($ranges as $range) if ($clock >= $range[0] && $clock < $range[1]) return true;
    return false;
}
$now = (new DateTimeImmutable('2026-11-02 09:00', new DateTimeZone('Europe/Berlin')))->getTimestamp();
$opening = array(array('00:00', '12:00'), array('20:00', '24:00'));
$ranges = ds_preparation_closed_ranges($opening, 'delay_time', '03-11-2026', $now);
expect_same(true, slot_blocked($ranges, '12:00'), 'Preparation blocks opening');
expect_same(true, slot_blocked($ranges, '12:19'), 'Preparation blocks until twenty minutes');
expect_same(false, slot_blocked($ranges, '12:20'), 'First custom slot exactly 12:20');
expect_same(false, slot_blocked($ranges, '13:59'), 'Lunch availability');
expect_same(true, slot_blocked($ranges, '14:44'), 'Default preparation outside window');
expect_same(false, slot_blocked($ranges, '14:45'), 'Default interval first slot');
expect_same(true, slot_blocked($ranges, '18:34'), 'Evening custom preparation');
expect_same(false, slot_blocked($ranges, '18:35'), 'Evening first slot');
$pickup = ds_preparation_closed_ranges($opening, 'delay_delivery', '03-11-2026', $now);
expect_same(true, slot_blocked($pickup, '12:29'), 'Independent pickup default');
expect_same(false, slot_blocked($pickup, '12:30'), 'Pickup first slot');
$later = (new DateTimeImmutable('2026-11-03 12:10', new DateTimeZone('Europe/Berlin')))->getTimestamp();
$today = ds_preparation_closed_ranges($opening, 'delay_time', '03-11-2026', $later);
expect_same(true, slot_blocked($today, '12:29'), 'Today respects remaining preparation');
expect_same(false, slot_blocked($today, '12:30'), 'Today first available slot');
$breaks = ds_preparation_closed_ranges(array_merge($opening, array(array('15:00', '16:00'))), 'delay_time', '03-11-2026', $now);
expect_same(true, slot_blocked($breaks, '16:44'), 'Preparation after break');
expect_same(false, slot_blocked($breaks, '16:45'), 'First slot after break');
expect_same(array(), ds_sanitize_interval_windows(array(array('from'=>'12:00', 'to'=>'14:00', 'minutes'=>0))), 'Intervals must be positive');
$_POST = array('ds_preparation_nonce'=>'valid', 'dsmart_delivery_time_step_windows_present'=>1, 'dsmart_delivery_time_step_windows'=>$windows);
ds_save_preparation_windows();
expect_same(20, get_option('dsmart_delivery_time_step_windows')[0]['minutes'], 'Save interval windows');
$_POST = array('ds_preparation_nonce'=>'valid');
ds_save_preparation_windows();
expect_same(20, get_option('dsmart_delivery_time_step_windows')[0]['minutes'], 'Legacy settings request preserves intervals');
$_POST = array('ds_preparation_nonce'=>'valid', 'dsmart_delivery_time_step_windows_present'=>1);
ds_save_preparation_windows();
expect_same(array(), get_option('dsmart_delivery_time_step_windows'), 'Remove all interval windows');
echo "Preparation-time regression checks passed.\n";
