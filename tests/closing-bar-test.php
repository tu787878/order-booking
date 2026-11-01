<?php
// Run with: php tests/closing-bar-test.php
// Load only the real scheduling/footer functions, without bootstrapping WordPress.
$source = file_get_contents(__DIR__ . '/../booking-order.php');
foreach (array(
    array('check_time_with_time_shop', '//random coupon code'),
    array('dsmart_float_cart', "add_action('wp_footer', 'dsmart_float_cart');"),
) as $section) {
    $start = strpos($source, 'function ' . $section[0] . '(');
    $end = strpos($source, $section[1], $start);
    eval(substr($source, $start, $end - $start));
}
function get_option($key, $default = false) { return $GLOBALS['options'][$key] ?? $default; }
function custom_date() { return 'mo'; }
function is_page_template($template) { return ($GLOBALS['route'] ?? 'page') === 'page' && $template === 'templates/taxonomy-product-cat.php'; }
function is_tax($taxonomy) { return ($GLOBALS['route'] ?? 'page') === 'category' && $taxonomy === 'product-cat'; }
function expect_same($expected, $actual, $label) {
    if ($expected !== $actual) { throw new RuntimeException($label); }
}
$GLOBALS['options'] = array(
    'close_shop_text' => 'Restaurant closed',
    'dsmart_method_ship' => '',
    'dsmart_method_direct' => 'on',
    'time_open_shop_2_mo' => '11:30',
    'time_close_shop_2_mo' => '22:00',
    'closed_time' => array(array('date' => 'mo', 'from' => '14:30', 'to' => '17:00')),
);
foreach (array(false, true) as $custom) {
    $GLOBALS['options']['dsmart_custom_date'] = $custom
        ? array(array('date' => '26-10-2026', 'open' => '11:30', 'close' => '22:00')) : array();
    foreach (array('11:29' => false, '11:30' => true, '14:29' => true, '14:30' => false,
        '15:00' => false, '17:00' => false, '17:01' => true, '22:01' => false) as $clock => $open) {
        expect_same($open, check_time_with_time_shop($clock, null, '26-10-2026', 'direct'),
            "Pickup at $clock, custom schedule: " . (int) $custom);
    }
}
// Check the reported Sunday break against an explicit date.
$GLOBALS['options']['dsmart_custom_date'] = array();
$GLOBALS['options']['time_open_shop_2_su'] = '10:00';
$GLOBALS['options']['time_close_shop_2_su'] = '22:00';
$GLOBALS['options']['closed_time'][] = array('date' => 'su', 'from' => '11:00', 'to' => '15:00');
foreach (array('09:59' => false, '10:59' => true, '11:00' => false, '12:00' => false, '15:00' => false, '15:01' => true, '22:01' => false) as $clock => $open) {
    expect_same($open, check_time_with_time_shop($clock, null, '01-11-2026', 'direct'), "Sunday pickup at $clock");
}
// Exercise actual footer rendering with breaks covering the current clock.
$GLOBALS['options']['dsmart_custom_date'] = array();
$GLOBALS['options']['closed_time'][0]['from'] = '00:00';
$GLOBALS['options']['closed_time'][0]['to'] = '23:59';
function bar_visible() {
    ob_start();
    dsmart_float_cart();
    return strpos(ob_get_clean(), 'dsmart-show-notify') !== false;
}
expect_same(true, bar_visible(), 'Pickup break displays closing bar');
$GLOBALS['options']['dsmart_method_ship'] = 'off';
expect_same(true, bar_visible(), 'Disabled delivery cannot suppress pickup closing bar');
$GLOBALS['options']['dsmart_method_ship'] = 'on';
expect_same(false, bar_visible(), 'Open delivery hides bar during pickup break');
$GLOBALS['options']['closed_time_2'] = $GLOBALS['options']['closed_time'];
expect_same(true, bar_visible(), 'Both methods on break display closing bar');
$GLOBALS['options']['closed_time'] = array();
$GLOBALS['options']['time_open_shop_2_mo'] = '';
$GLOBALS['options']['time_close_shop_2_mo'] = '';
expect_same(false, bar_visible(), 'Open pickup hides bar during delivery break');
$GLOBALS['options']['closed_time_2'] = array();
expect_same(false, bar_visible(), 'Both services open hides closing bar');
$GLOBALS['options']['closed_time_2'] = array(array('date' => 'mo', 'from' => '00:00', 'to' => '23:59'));
$GLOBALS['options']['dsmart_method_ship'] = '';
expect_same(false, bar_visible(), 'Disabled delivery break cannot close open pickup');
$GLOBALS['options']['dsmart_method_direct'] = '';
expect_same(true, bar_visible(), 'No enabled services displays closing bar');
$GLOBALS['options']['dsmart_method_direct'] = 'on';
$GLOBALS['options']['dsmart_close_shop'] = 'on';
expect_same(true, bar_visible(), 'Manual closure displays closing bar');
$GLOBALS['route'] = 'category';
expect_same(true, bar_visible(), 'Closed restaurant displays bar on product-category route');
$GLOBALS['options']['dsmart_close_shop'] = '';
expect_same(false, bar_visible(), 'Open pickup hides bar on product-category route');
$GLOBALS['options']['dsmart_close_shop'] = 'on';
$GLOBALS['options']['close_shop_text'] = '';
expect_same(false, bar_visible(), 'Empty configured message hides bar');
$GLOBALS['options']['close_shop_text'] = 'Preorders available later';
ob_start();
dsmart_float_cart();
expect_same(true, strpos(ob_get_clean(), 'Preorders available later') !== false, 'Configured message is rendered');
$GLOBALS['route'] = 'other';
expect_same(false, bar_visible(), 'Unrelated pages do not display menu closing bar');
echo "Closing-bar regression checks passed.\n";
