<?php
// Run: php tests/discount-window-test.php
$GLOBALS['options'] = array('promotion' => '15', 'discount_cod' => 'on');
function get_option($key) { return $GLOBALS['options'][$key] ?? false; }
function update_option($key, $value, $autoload = null) { $GLOBALS['options'][$key] = $value; }
function current_user_can($capability) { return true; }
function wp_verify_nonce($nonce, $action) { return $nonce === 'valid' && $action === 'ds_discount_window'; }
function wp_unslash($value) { return $value; }
require __DIR__ . '/../inc/discount-window.php';
function same($expected, $actual, $label) {
    if ($expected !== $actual) throw new RuntimeException($label . ': ' . var_export($actual, true));
}
function discount_at($date) { return ds_discount_option('promotion', strtotime($date)); }
same('15', discount_at('2026-11-18T12:00:00+01:00'), 'No window retains discount');
$GLOBALS['options']['ds_discount_profiles'] = array(array('from' => '2020-01-01', 'to' => '2020-01-02', 'promotion' => '99'));
same('15', discount_at('2026-11-18T12:00:00+01:00'), 'Former profiles ignored');
$GLOBALS['options']['ds_discount_window'] = array('from' => '2026-11-18T12:00', 'to' => '2026-11-19T14:30');
same('', discount_at('2026-11-18T11:59:59+01:00'), 'Before window');
same('15', discount_at('2026-11-18T12:00:00+01:00'), 'Inclusive start');
same('15', discount_at('2026-11-19T14:30:59+01:00'), 'Entire end minute included');
same('', discount_at('2026-11-19T14:31:00+01:00'), 'After end');
same('15', discount_at('2026-11-18T11:00:00Z'), 'Berlin winter time');
same(array(), ds_discount_option('dsmart_custom_discount_date', strtotime('2026-11-20')), 'Inactive special dates return array');
$GLOBALS['options']['ds_discount_window'] = array('from' => '2026-07-01T12:00', 'to' => '2026-07-01T13:00');
same('15', discount_at('2026-07-01T10:00:00Z'), 'Berlin summer time');
same(false, ds_discount_parse_datetime('2026-02-30T12:00'), 'Invalid date rejected');
same(false, ds_discount_parse_datetime('2026-11-18T25:00'), 'Invalid hour rejected');
$_POST = array('ds_discount_window_present' => 1, 'ds_discount_window_nonce' => 'valid', 'ds_discount_from_date' => '2026-12-01', 'ds_discount_from_time' => '09:00', 'ds_discount_to_date' => '2026-12-02', 'ds_discount_to_time' => '18:00');
ds_save_discount_window();
$saved = get_option('ds_discount_window');
same(array('from' => '2026-12-01T09:00', 'to' => '2026-12-02T18:00'), $saved, 'Save range');
$_POST['ds_discount_window_nonce'] = 'invalid'; $_POST['ds_discount_from_date'] = '2027-01-01';
ds_save_discount_window(); same($saved, get_option('ds_discount_window'), 'Invalid nonce preserves range');
$_POST['ds_discount_window_nonce'] = 'valid';
ob_start(); ds_save_discount_window(); ob_end_clean();
same($saved, get_option('ds_discount_window'), 'Reversed range preserves saved range');
$_POST['ds_discount_from_date'] = '2026-12-01'; $_POST['ds_discount_to_time'] = '';
ob_start(); ds_save_discount_window(); ob_end_clean();
same($saved, get_option('ds_discount_window'), 'Partial range rejected');
foreach (array('from_date', 'from_time', 'to_date', 'to_time') as $field) $_POST['ds_discount_' . $field] = '';
ds_save_discount_window();
same('15', discount_at('2030-01-01T00:00:00Z'), 'Clear range restores unrestricted discount');
same('15', get_option('promotion'), 'Existing discount unchanged');
echo "Discount window tests passed\n";
