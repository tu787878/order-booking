<?php
// Run: php tests/discount-profiles-test.php
$GLOBALS['options'] = array('promotion' => '5', 'discount_cod' => 'on');
function get_option($key) { return $GLOBALS['options'][$key] ?? false; }
function update_option($key, $value, $autoload = null) { $GLOBALS['options'][$key] = $value; }
function current_user_can($capability) { return true; }
function wp_verify_nonce($nonce, $action) { return $nonce === 'valid'; }
function wp_unslash($value) { return $value; }
function sanitize_text_field($value) { return strip_tags($value); }
require __DIR__ . '/../inc/discount-profiles.php';
function same($expected, $actual, $label) {
    if ($expected !== $actual) throw new RuntimeException($label . ': ' . var_export($actual, true));
}
function discount_at($date, $key = 'promotion') { return ds_discount_option($key, strtotime($date)); }
same('5', discount_at('2026-11-12T12:00:00+01:00'), 'Default without profiles');
$profile = array('name' => 'Winter', 'from' => '2026-12-01', 'to' => '2026-12-31', 'promotion' => '10', 'discount_cod' => 'on');
$GLOBALS['options']['ds_discount_profiles'] = array($profile);
same('', discount_at('2026-11-30T23:59:59+01:00'), 'Before start suppresses default');
same('10', discount_at('2026-12-01T00:00:00+01:00'), 'Inclusive start');
same('10', discount_at('2026-12-31T23:59:59+01:00'), 'Inclusive last day');
same('', discount_at('2027-01-01T00:00:00+01:00'), 'Expired suppresses default');
same('10', discount_at('2026-11-30T23:00:00Z'), 'Berlin timezone');
same('', discount_at('2026-12-15T12:00:00+01:00', 'discount_shop'), 'Custom never inherits omitted default fields');
$second = $profile; $second['promotion'] = '20';
$GLOBALS['options']['ds_discount_profiles'][] = $second;
same('10', discount_at('2026-12-15T12:00:00+01:00'), 'First overlap wins');
$_POST = array('ds_discount_profiles_present' => 1, 'ds_discount_profiles_nonce' => 'invalid');
ds_save_discount_profiles();
same(2, count(get_option('ds_discount_profiles')), 'Invalid nonce preserves profiles');
$_POST['ds_discount_profiles_nonce'] = 'valid';
$_POST['ds_discount_profiles'] = array(array_merge($profile, array('from' => '2026-02-30')));
ob_start(); ds_save_discount_profiles(); ob_end_clean();
same(2, count(get_option('ds_discount_profiles')), 'Invalid dates preserve profiles');
$_POST['ds_discount_profiles'] = array(array_merge($profile, array('special_dates' => '2026-12-24 | 12:00-14:00')));
ds_save_discount_profiles();
same(array(array('date' => '24-12-2026', 'time' => '12:00-14:00')), get_option('ds_discount_profiles')[0]['dsmart_custom_discount_date'], 'Special dates saved');
unset($_POST['ds_discount_profiles']); ds_save_discount_profiles();
same(array(), get_option('ds_discount_profiles'), 'Delete all profiles');
same('5', discount_at('2026-12-15T12:00:00+01:00'), 'Delete all restores permanent default');
same('5', get_option('promotion'), 'Default remains unchanged');
echo "Discount profile tests passed\n";
