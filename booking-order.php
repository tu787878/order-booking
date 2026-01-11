<?php

/**
 * Plugin Name: TCG Restaurant Shop
 * Description: Restaurant Shop for delivery and take away
 * Version: 1.6.22
 * License: GPLv2 or later
 */
define('BOOKING_ORDER_PATH', plugin_dir_url(__FILE__));
define('BOOKING_ORDER_PATH2', plugin_dir_path(__FILE__));
date_default_timezone_set('Europe/Berlin');

// Restrict search to admins only
add_action('template_redirect', function() {
    $is_search_request = is_search() || 
                        (isset($_GET['s']) && !empty($_GET['s']));
    
    if ($is_search_request && !current_user_can('manage_options')) {
        wp_redirect(home_url());
        exit;
    }
}, 1);

// Hide search form from non-admins
add_filter('get_search_form', function($form) {
    return current_user_can('manage_options') ? $form : '';
});

// Remove search widget for non-admins
add_action('widgets_init', function() {
    if (!current_user_can('manage_options')) {
        unregister_widget('WP_Widget_Search');
    }
});

function book_slice_orderby( $query ) {
  if( !is_admin() )
       return;
  if($query->query['suppress_filters']) {
    return;
  }
  $query->set( 'orderby', 'date' );
  $query->set( 'order', 'desc' );
}

add_action( 'pre_get_posts', 'book_slice_orderby' );

function getVersion(){
    $plugin_data = get_plugin_data( __FILE__ );
    return $plugin_data['Version'];
}

function custom_footer_code() {
    if ( (is_home() || is_front_page()) && get_option('homepage_popup') === "1"){
        $image_id = get_option('ds_popup_homepage');
        $output = "";
        if (intval($image_id) > 0) {
            $url = wp_get_attachment_image_src($image_id, 'full', false);
            $output = $url[0];
        } else {
            $output = plugins_url('img/default-closed-popup.jpeg', __FILE__);
        }
    ?>
        <div id="myModal2" class="modal">

        <!-- The Close Button -->
        <span class="close">&times;</span>

        <!-- Modal Content (The Image) -->
        <img style="margin-top: 10%;" class="modal-content" id="img01" src="<?php echo $output; ?>">
        <!-- Modal Caption (Image Text) -->
        <div id="caption"></div>
        </div>
    <?php
    }
}
add_action( 'wp_footer', 'custom_footer_code' );

function book_posts_stickiness( $column, $post_id ) {
    if ($column == 'kunden'){
       $kunden = get_post_meta( $post_id, 'customer_name1', true );
       $kunden2 = get_post_meta( $post_id, 'customer_name2', true );
		
		echo $kunden2 . " " . $kunden;
    }
	 if ($column == 'bestellnummer'){
       $bestellnummer = get_option('show_second_number');
		if(strcmp($bestellnummer, "1") == 0) {
		 echo get_post_meta( $post_id, 'second_order_number', true );
		}else{
			echo get_the_title($post_id);
		}
    }
	 if ($column == 'email'){
		 echo get_post_meta( $post_id, 'customer_email', true );
    }
	 if ($column == 'telefon'){
		 echo get_post_meta( $post_id, 'customer_phone', true );
    }
	if ($column == 'total'){
		 echo "€".get_post_meta( $post_id, 'total', true );
    }
	   
}
add_action( 'manage_orders_posts_custom_column' , 'book_posts_stickiness', 10, 2 );

add_filter( 'manage_orders_posts_columns', 'orders_filter_posts_columns' );
function orders_filter_posts_columns( $columns) {
	 $columns = array(
      'cb' => $columns['cb'],
      'kunden' => __( 'Kundenname' ),
      'bestellnummer' => __( 'Bestellnummer' ),
      'email' => __( 'Email' ),
      'telefon' => __( 'Telefon' ),
      'total' => __( 'Gesamtsumme' ),
      'date' => __( 'Datum' ),
    );

  return $columns;
}


// add custom pool
function wporg_register_taxonomy_course()
{
    $labels = array(
        'name'              => _x('Pool Druckverteilung', 'taxonomy general name'),
        'singular_name'     => _x('Pool Druckverteilung', 'taxonomy singular name'),
        'search_items'      => __('Search Pool Druckverteilung'),
        'all_items'         => __('All Pool Druckverteilungen'),
        'parent_item'       => __('Parent Pool Druckverteilung'),
        'parent_item_colon' => __('Parent Pool Druckverteilung:'),
        'edit_item'         => __('Edit Pool Druckverteilung'),
        'update_item'       => __('Update Pool Druckverteilung'),
        'add_new_item'      => __('Add New Pool Druckverteilung'),
        'new_item_name'     => __('New Pool Druckverteilung Name'),
        'menu_name'         => __('Pool Druckverteilung'),
    );

    $capabilities = [
        'manage_terms' => 'manage_pools',
        'edit_terms' => 'edit_pools',
        'delete_terms' => 'delete_pools',
        'assign_terms' => 'assign_pools'
    ];

    $args   = array(
        'hierarchical'      => true, // make it hierarchical (like categories)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'pool'],
        'default_term'      =>  array('name' => 'Allgemein Pool Druckverteilung', 'slug' => 'all_pool', 'description' => ''),
        "capabilities"          => $capabilities, 
    );
    if (get_option('enable_pool') == 1)
        register_taxonomy('pool', ['product', 'product-cat'], $args);
}
add_action('init', 'wporg_register_taxonomy_course');

//add css and js to admin page
function book_admin_script()
{
    wp_enqueue_style('jquery.timepicker.min.css', BOOKING_ORDER_PATH . 'css/jquery.timepicker.min.css');
    wp_enqueue_style('jquery-ui.min.css', BOOKING_ORDER_PATH . 'css/jquery-ui.min.css');
    wp_enqueue_style('book-admin.css', BOOKING_ORDER_PATH . 'css/book-admin.css', array(), rand());
    wp_enqueue_style('coloris.css', BOOKING_ORDER_PATH . 'css/coloris.css', array(), rand());
    wp_enqueue_script('bootstrap.min.js', BOOKING_ORDER_PATH . 'js/bootstrap.min.js', array('jquery'), '3.8', true);
    wp_enqueue_script('jquery.timepicker.min.js', BOOKING_ORDER_PATH . 'js/jquery.timepicker.min.js', array('jquery'), '3.8', true);
    wp_enqueue_script('jquery-ui.min.js', BOOKING_ORDER_PATH . 'js/jquery-ui.min.js', array('jquery'), '3.8', true);
    wp_enqueue_script('book-admin.js', BOOKING_ORDER_PATH . 'js/book-admin.js', array('jquery'), rand(), true);
    wp_enqueue_script('coloris_js', BOOKING_ORDER_PATH . 'js/coloris.js', array('jquery'), rand(), true);
    wp_localize_script(
        'book-admin.js',
        'bookingVars',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'time_discount_error1' => __("please fill start time and end time", "dsmart"),
            'time_discount_error2' => __("please fill start time and end time different", "dsmart"),
            'time_discount_error3' => __("please fill start time smaller then end time", "dsmart"),
        )
    );
}
add_action('admin_head', 'book_admin_script');

add_action('admin_enqueue_scripts', 'load_wp_media_files');
function load_wp_media_files($page)
{
    // change to the $page where you want to enqueue the script
    if (isset($_GET['page']) && $_GET['page'] == "general-booking-setting") {
        // Enqueue WordPress media scripts
        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
        // Enqueue custom script that will interact with wp.media
        wp_enqueue_script('gallery_script', plugins_url('/js/galleryscript.js', __FILE__), array('jquery'), rand());
    } elseif ($page == "profile.php" || $page == "user-edit.php") {
        wp_enqueue_media();
    }
}

//add css and js to front page

function book_frontend_scripts()
{
    wp_enqueue_style('styles.css', BOOKING_ORDER_PATH . 'styles.css');
    wp_enqueue_style('ion.rangeSlider', BOOKING_ORDER_PATH . 'css/ion.rangeSlider.css');
    wp_enqueue_style('jquery.timepicker.min.css', BOOKING_ORDER_PATH . 'css/jquery.timepicker.min.css');
    wp_enqueue_style('ion.rangeSlider.skinFlat.css', BOOKING_ORDER_PATH . 'css/ion.rangeSlider.skinFlat.css');
    wp_enqueue_style('fontawesome-stars.css', BOOKING_ORDER_PATH . 'css/fontawesome-stars.css');
    wp_enqueue_style('slick.css', BOOKING_ORDER_PATH . 'css/slick.css');
    wp_enqueue_style('jquery-ui.min.css', BOOKING_ORDER_PATH . 'css/jquery-ui.min.css');
    wp_enqueue_style('slick-theme.css', BOOKING_ORDER_PATH . 'css/slick-theme.css');
    wp_enqueue_style('book-carousel.css', BOOKING_ORDER_PATH . 'css/owl.carousel.min.css');
    wp_enqueue_style('book-frontend-2.css', BOOKING_ORDER_PATH . 'css/book-frontend-2.css');
    wp_enqueue_style('book-frontend.css', BOOKING_ORDER_PATH . 'css/book-frontend.css', array(), rand());
    wp_enqueue_style('book-responsive.css', BOOKING_ORDER_PATH . 'css/book-responsive.css', array(), rand());
    wp_enqueue_style('nice-select-css.css', BOOKING_ORDER_PATH . 'css/nice-select.css', array(), rand());
    wp_enqueue_style('bootstrap_icon_css', '//cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css');

    
    //wp_enqueue_script( 'jquery_min_js', BOOKING_ORDER_PATH.'js/jquery.min.js',array('jquery'), '3.8', true );
    wp_enqueue_script('bootstrap.min.js', BOOKING_ORDER_PATH . 'js/bootstrap.min.js', array('jquery'), '3.8', true);
    wp_enqueue_script('jquery.timepicker.min.js', BOOKING_ORDER_PATH . 'js/jquery.timepicker.min.js', array('jquery'), '3.8', true);
    wp_enqueue_script('ion.rangeSlider.min.js', BOOKING_ORDER_PATH . 'js/ion.rangeSlider.min.js', array('jquery'), '3.8', true);
    wp_enqueue_script('jquery-ui.min.js', BOOKING_ORDER_PATH . 'js/jquery-ui.min.js', array('jquery'), '3.8', true);
    wp_enqueue_script('jquery.barrating.min.js', BOOKING_ORDER_PATH . 'js/jquery.barrating.min.js', array('jquery'), '3.8', true);
    wp_enqueue_script('nice-select-js.js', BOOKING_ORDER_PATH . 'js/jquery.nice-select.js', array('jquery'), '3.8', true);
    wp_enqueue_script('slick_js', BOOKING_ORDER_PATH . 'js/slick.js', array('jquery'), '3.8', true);
    wp_enqueue_script('carousel_js', BOOKING_ORDER_PATH . 'js/owl.carousel.min.js', array('jquery'), '3.8', true);
    wp_enqueue_script('print.min.js', BOOKING_ORDER_PATH . 'js/print.min.js', array('jquery'), '3.8', true);
    wp_enqueue_script('main_js', BOOKING_ORDER_PATH . 'js/book-frontend.js', array('jquery'), rand(), true);
    if (is_tax('product-cat')) {
        $cat = get_queried_object();
        wp_localize_script(
            'main_js',
            'bookingVars',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'currency' => ds_price_symbol(),
                'min_price' => ds_get_min_price($cat->taxonomy, $cat->term_id),
                'max_price' => ds_get_max_price($cat->taxonomy, $cat->term_id),
                'currency_rate' => get_option('dsmart_currency_rate'),
                'popup_time' => (get_option('time_to_show_alert') != "") ? get_option('time_to_show_alert') : 3000,
            )
        );
    } else {
        wp_localize_script(
            'main_js',
            'bookingVars',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'min_price' => ds_get_min_price(),
                'max_price' => ds_get_max_price(),
                'close_time2' => get_close_time_shop(),
                'close_time' => get_close_time_shop2(),
                'close_all' => get_close_time_shop_all_week(),
                'delivery_step' => get_option('dsmart_delivery_time_step', 15),
                'takeaway_step' => get_option('dsmart_takeaway_time_step', 15),
                'currency' => ds_price_symbol(),
                'currency_rate' => get_option('dsmart_currency_rate'),
                'popup_time' => (get_option('time_to_show_alert') != "") ? get_option('time_to_show_alert') : 3000,
                'buynow' => get_option('dsmart_buynow'),
            )
        );
    }
}
add_action('wp_enqueue_scripts', 'book_frontend_scripts');

//include file
require plugin_dir_path(__FILE__) . '/inc/create-posttype.php';
require plugin_dir_path(__FILE__) . '/inc/create-taxonomy.php';
require plugin_dir_path(__FILE__) . '/inc/metabox.php';
require plugin_dir_path(__FILE__) . '/inc/term_metabox.php';
require plugin_dir_path(__FILE__) . '/inc/shortcode.php';
require plugin_dir_path(__FILE__) . '/templates/add-to-cart.php';
require plugin_dir_path(__FILE__) . '/inc/admin-menu.php';
require plugin_dir_path(__FILE__) . '/inc/create-table.php';
require plugin_dir_path(__FILE__) . '/inc/ajax-functions.php';
require plugin_dir_path(__FILE__) . '/inc/coupon-functions.php';
require plugin_dir_path(__FILE__) . '/inc/comments-function.php';
require plugin_dir_path(__FILE__) . '/inc/taxonomy-term-image.php';
require plugin_dir_path(__FILE__) . '/inc/user_metabox.php';
require plugin_dir_path(__FILE__) . '/vendor/autoload.php';
require plugin_dir_path(__FILE__) . '/inc/cart_handle.php';

//include API Mobile
require plugin_dir_path(__FILE__) . '/inc/mobile-api.php';


require_once BOOKING_ORDER_PATH2 . 'inc/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

//filter wp mail html
if (!function_exists('dsmart_set_html_mail_content_type')) {
    add_filter('wp_mail_content_type', 'dsmart_set_html_mail_content_type');
    function dsmart_set_html_mail_content_type($content_type)
    {
        return 'text/html';
    }
}

// Function to change sender name
if (!function_exists('ds_sender_name')) {
    function ds_sender_name($original_email_from)
    {
        $ds_mail_name = get_option('ds_mail_name');
        if ($ds_mail_name != "") {
            $original_email_from = $ds_mail_name;
        }
        return $original_email_from;
    }
}
add_filter('wp_mail_from_name', 'ds_sender_name');
if (!function_exists('ds_sender_email')) {
    function ds_sender_email($original_email_address)
    {
        $ds_sender_email = get_option('ds_sender_email');
        if ($ds_sender_email != "") {
            $original_email_address = $ds_sender_email;
        }
        return $original_email_address;
    }
}

// Hooking up our functions to WordPress filters 
add_filter('wp_mail_from', 'ds_sender_email');

//add template taxonomy
add_filter('template_include', 'taxonomy_template');
function taxonomy_template($template)
{
    if (is_tax('product-cat')) {
        $template = dirname(__FILE__) . '/templates/taxonomy-product-cat.php';
    } elseif (is_post_type_archive('orders')) {
        // Set this to the template file inside your plugin folder
        $template = WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)) . '/templates/archive-orders.php';
    }
    return $template;
}
//remove screen reader text
function ds_sanitize_pagination($content)
{
    // Remove h2 tag
    $content = preg_replace('#<h2.*?>(.*?)<\/h2>#si', '', $content);
    return $content;
}

add_action('navigation_markup_template', 'ds_sanitize_pagination');

//status product
function ds_status_product($product_status)
{ ?>
    <option value="instock" <?php if ($product_status == "instock") {
                                echo 'selected';
                            } ?>><?php _e("auf Lager", "dsmart"); ?></option>
    <option value="outstock" <?php if ($product_status == "outstock") {
                                    echo 'selected';
                                } ?>><?php _e("Nicht auf Lager", "dsmart"); ?></option>
<?php }
//status product
function ds_vat_product($vat)
{ ?>
    <option value="" <?php if ($vat == "") {
                            echo 'selected';
                        } ?>><?php _e("Nichst ausgewählt", "dsmart"); ?></option>
    <option value="7" <?php if ($vat == "7") {
                            echo 'selected';
                        } ?>><?php _e("7%", "dsmart"); ?></option>
    <option value="19" <?php if ($vat == "19") {
                            echo 'selected';
                        } ?>><?php _e("19%", "dsmart"); ?></option>
    <?php }

//get field
function dsmart_field($field, $id = null, $type = 'post')
{
    if ($id == null) {
        if ($type == 'post') :
            $id = get_the_ID();
        else :
            $id = get_queried_object_id();
        endif;
    }
    if ($type == 'post') :
        return get_post_meta($id, $field, true);
    else :
        return get_term_meta($id, $field, true);
    endif;
}
//symbol price
if (!function_exists('ds_price_symbol')) {
    function ds_price_symbol($dsmart_currency = null)
    {
        if ($dsmart_currency == null) {
            $dsmart_currency = get_option('dsmart_currency');
        }
        if ($dsmart_currency == "2") {
            $currency = "€";
        } elseif ($dsmart_currency == "3") {
            $currency = "CHF";
        } else {
            $currency = "$";
        }
        return $currency;
    }
}
function ds_cs_price_symbol($dsmart_currency = null)
{
    if ($dsmart_currency == null) {
        $dsmart_currency = get_option('dsmart_currency');
    }
    if ($dsmart_currency == "2") {
        $currency = "EURO";
    } elseif ($dsmart_currency == "3") {
        $currency = "CHF";
    } else {
        $currency = "USD";
    }
    return $currency;
}
function ds_cs_price_symbol2($dsmart_currency = null)
{
    if ($dsmart_currency == null) {
        $dsmart_currency = get_option('dsmart_currency');
    }
    if ($dsmart_currency == "2") {
        $currency = "EUR";
    } elseif ($dsmart_currency == "3") {
        $currency = "CHF";
    } else {
        $currency = "USD";
    }
    return $currency;
}
function ds_convert_price($price)
{
    $dsmart_currency = get_option('dsmart_currency');
    if ($dsmart_currency == "2") {
        $currency = "€";
    } elseif ($dsmart_currency == "3") {
        $currency = "CHF";
    } else {
        $currency = "$";
    }
    $dsmart_currency_rate = get_option('dsmart_currency_rate');
    if ($dsmart_currency_rate != "") {
        $currency_rate = floatval($dsmart_currency_rate);
    } else {
        $currency_rate = 1;
    }
    if ($currency == "$") {
        return floatval(number_format($price, 2));
    } else {
        return floatval(number_format(floatval($price) * $currency_rate, 2));
    }
}
function ds_convert_currency_price($currency, $price)
{
    $dsmart_currency_rate = get_option('dsmart_currency_rate');
    if ($dsmart_currency_rate != "") {
        $currency_rate = floatval($dsmart_currency_rate);
    } else {
        $currency_rate = 1;
    }
    if ($currency == "$") {
        return floatval(number_format($price, 2));
    } else {
        return floatval(number_format(floatval($price) * $currency_rate, 2));
    }
}
//price text
function ds_price_format_text($price)
{
    $price = ds_convert_price($price);
    $price = number_format($price, 2, '.', ' ') . ' ' . ds_price_symbol();
    return $price;
}
function ds_price_format_text_with_symbol($price, $symbol = null)
{
    $price =  number_format($price, 2, '.', ' ') . ' ' .  ds_price_symbol($symbol);
    return $price;
}
function cs_ds_price_format_text_with_symbol($price, $symbol = null)
{
    $price = ds_cs_price_symbol($symbol) . ' ' . number_format($price, 2, '.', ' ');
    return $price;
}
function ds_price_format_text_no_convert($price)
{
    $price = floatval($price);
    $price = ds_price_symbol() . ' ' . number_format($price, 2, '.', ' ');
    return $price;
}
//caculate price
function ds_caculate_item_price($id, $quantity = 1)
{
    $price = ds_convert_price(dsmart_field('price', $id));
    if(intval($quantity) > 0){
        $total = $price * intval($quantity);
    }else{
        $total = $price;
    }
    
    return $total;
}

//get min price and max price
function ds_get_min_price($product_cat = null, $cat_id = null)
{
    global $wp_query;
    $price = 0;
    if ($product_cat != null && $cat_id != null) {
        $wp_query = new WP_Query(array(
            'post_type' => 'product',
            'meta_key' => 'price',
            'orderby'   => 'meta_value_num',
            'order' => 'ASC',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'tax_query' => array(
                array(
                    'taxonomy' => $product_cat,
                    'field'    => 'term_id',
                    'terms'    => $cat_id
                )
            )
        ));
    } else {
        $wp_query = new WP_Query(array(
            'post_type' => 'product',
            'meta_key' => 'price',
            'orderby'   => 'meta_value_num',
            'order' => 'ASC',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
        ));
    }
    if ($wp_query->have_posts()) :
        while ($wp_query->have_posts()) : the_post();
            $price = ds_convert_price(dsmart_field('price'));
        endwhile;
        wp_reset_query();
    endif;
    return intval($price);
}
function ds_get_max_price($product_cat = null, $cat_id = null)
{
    global $wp_query;
    $price = 0;
    if ($product_cat != null && $cat_id != null) {
        $wp_query = new WP_Query(array(
            'post_type' => 'product',
            'meta_key' => 'price',
            'orderby'   => 'meta_value_num',
            'order' => 'DESC',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'tax_query' => array(
                array(
                    'taxonomy' => $product_cat,
                    'field'    => 'term_id',
                    'terms'    => $cat_id
                )
            )
        ));
    } else {
        $wp_query = new WP_Query(array(
            'post_type' => 'product',
            'meta_key' => 'price',
            'orderby'   => 'meta_value_num',
            'order' => 'DESC',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
        ));
    }
    if ($wp_query->have_posts()) :
        while ($wp_query->have_posts()) : the_post();
            $price = ds_convert_price(dsmart_field('price'));
        endwhile;
        wp_reset_query();
    endif;
    return ceil($price);
}

//add custom template
function ds_post_page_template($post_templates, $wp_theme, $post, $post_type)
{
    $post_templates['templates/cart-page.php'] = __('Warenkorb');
    return $post_templates;
}

add_filter('theme_page_templates', 'ds_post_page_template', 10, 4);

//create checkout page
function ds_install_plugin_page()
{
    global $wpdb;
    $cart_page_template = 'templates/cart-page.php';
    $checkout_page_template = 'templates/checkout-page.php';
    $shop_page_template = 'templates/shop-page.php';
    $thankyou_page_template = 'templates/template-thankyou.php';
    $download_template = 'templates/template-download.php';
    $register_template = 'templates/template-register.php';
    $login_template = 'templates/template-login.php';
    $lostpass_template = 'templates/template-lostpass.php';
    $profile_template = 'templates/template-profile.php';
    $pages = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $cart_page_template
    ));
    $new_page = array(
        'post_type' => 'page',
        'post_title' => "Warenkorb",
        'post_content' => "",
        'post_status' => 'publish',
        'post_author' => 1,
    );
    if (!isset($pages[0]->ID)) {
        $new_page_id = wp_insert_post($new_page);
        if (!empty($cart_page_template)) {
            update_post_meta($new_page_id, '_wp_page_template', $cart_page_template);
        }
    }
    $pages1 = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $checkout_page_template
    ));
    $new_page1 = array(
        'post_type' => 'page',
        'post_title' => "Zur Kasse",
        'post_content' => "",
        'post_status' => 'publish',
        'post_author' => 1,
    );
    if (!isset($pages1[0]->ID)) {
        $new_page_id1 = wp_insert_post($new_page1);
        if (!empty($new_page_id1)) {
            update_post_meta($new_page_id1, '_wp_page_template', $checkout_page_template);
        }
    }
    $pages2 = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $shop_page_template
    ));
    $new_page2 = array(
        'post_type' => 'page',
        'post_title' => "Shop Manager",
        'post_content' => "",
        'post_status' => 'publish',
        'post_author' => 1,
    );
    if (!isset($pages2[0]->ID)) {
        $new_page_id2 = wp_insert_post($new_page2);
        if (!empty($new_page_id2)) {
            update_post_meta($new_page_id2, '_wp_page_template', $shop_page_template);
        }
    }
    $pages3 = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $thankyou_page_template
    ));
    $new_page3 = array(
        'post_type' => 'page',
        'post_title' => "Vielen Dank",
        'post_content' => "",
        'post_status' => 'publish',
        'post_author' => 1,
    );
    if (!isset($pages3[0]->ID)) {
        $new_page_id3 = wp_insert_post($new_page3);
        if (!empty($new_page_id3)) {
            update_post_meta($new_page_id3, '_wp_page_template', $thankyou_page_template);
        }
    }
    $pages4 = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $download_template
    ));
    $new_page4 = array(
        'post_type' => 'page',
        'post_title' => "Herunterladen",
        'post_content' => "",
        'post_status' => 'publish',
        'post_author' => 1,
    );
    if (!isset($pages4[0]->ID)) {
        $new_page_id4 = wp_insert_post($new_page4);
        if (!empty($new_page_id4)) {
            update_post_meta($new_page_id4, '_wp_page_template', $download_template);
        }
    }

    $register_page = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $register_template
    ));
    $new_register_page = array(
        'post_type' => 'page',
        'post_title' => "Register",
        'post_content' => "",
        'post_status' => 'publish',
        'post_author' => 1,
    );
    if (!isset($register_page[0]->ID)) {
        $register_id = wp_insert_post($new_register_page);
        if (!empty($register_id)) {
            update_post_meta($register_id, '_wp_page_template', $register_template);
        }
    }
    $login_page = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $login_template
    ));
    $new_login_page = array(
        'post_type' => 'page',
        'post_title' => "Login",
        'post_content' => "",
        'post_status' => 'publish',
        'post_author' => 1,
    );
    if (!isset($login_page[0]->ID)) {
        $login_id = wp_insert_post($new_login_page);
        if (!empty($login_id)) {
            update_post_meta($login_id, '_wp_page_template', $login_template);
        }
    }
    $lostpass_page = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $lostpass_template
    ));
    $new_lostpass_page = array(
        'post_type' => 'page',
        'post_title' => "Lost password",
        'post_content' => "",
        'post_status' => 'publish',
        'post_author' => 1,
    );
    if (!isset($lostpass_page[0]->ID)) {
        $lostpass_id = wp_insert_post($new_lostpass_page);
        if (!empty($lostpass_id)) {
            update_post_meta($lostpass_id, '_wp_page_template', $lostpass_template);
        }
    }
    $profile_page = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $profile_template
    ));
    $new_profile_page = array(
        'post_type' => 'page',
        'post_title' => "Profile",
        'post_content' => "",
        'post_status' => 'publish',
        'post_author' => 1,
    );
    if (!isset($profile_page[0]->ID)) {
        $profile_id = wp_insert_post($new_profile_page);
        if (!empty($profile_id)) {
            update_post_meta($profile_id, '_wp_page_template', $profile_template);
        }
    }

    $tablename = $wpdb->prefix . "shop_address";
    $check = $wpdb->get_var("SELECT COUNT(*) FROM {$tablename}");
    if ($check == 0) {
        $wpdb->insert(
            $tablename,
            array(
                'shop_name'     => 'Demo',
                'shop_address'    => "23 Nguyễn Huệ, Vĩnh Ninh, Thành phố Huế, Thua Thien Hue, Vietnam",
                'latitude' => "16.4574048",
                'longitude'   => "107.5846745",
                'email'      => get_option("admin_email")
            )
        );
    }
}
register_activation_hook(__FILE__, 'ds_install_plugin_page');
//add to template
function ds_add_template_to_select($post_templates, $wp_theme, $post, $post_type)
{

    // Add custom template named template-custom.php to select dropdown 
    $cart_page_template = 'templates/cart-page.php';
    $checkout_page_template = 'templates/checkout-page.php';
    $shop_page_template = 'templates/shop-page.php';
    $product_page_template = 'templates/template-shop.php';
    $thankyou_page_template = 'templates/template-thankyou.php';
    $download_template = 'templates/template-download.php';
    $order_page_template = 'templates/archive-orders.php';
    $register_template = 'templates/template-register.php';
    $login_template = 'templates/template-login.php';
    $lostpass_template = 'templates/template-lostpass.php';
    $profile_template = 'templates/template-profile.php';

    $post_templates[$cart_page_template] = __('Warenkorb');
    $post_templates[$checkout_page_template] = __('Zur Kasse');
    $post_templates[$shop_page_template] = __('Shop-Seite');
    $post_templates[$product_page_template] = __('Produkt auflisten');
    $post_templates[$thankyou_page_template] = __('Vielen Dank');
    $post_templates[$download_template] = __('Herunterladen');
    $post_templates[$order_page_template] = __('Bestellseite');
    $post_templates[$register_template] = __('Register');
    $post_templates[$login_template] = __('Login');
    $post_templates[$lostpass_template] = __('Lost password');
    $post_templates[$profile_template] = __('Profile');

    return $post_templates;
}

add_filter('theme_page_templates', 'ds_add_template_to_select', 10, 4);
//get page id by template name
if (!function_exists('get_page_id_by_template')) {
    function get_page_id_by_template($template_name)
    {
        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => $template_name
        ));
        if (isset($pages[0]->ID))
            return $pages[0]->ID;
        else
            return false;
    }
}

//load template
function ds_book_load_template_page($template)
{
    if (get_page_template_slug() === 'templates/cart-page.php') {
        if ($theme_file = locate_template(array('templates/cart-page.php'))) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path(__FILE__) . 'templates/cart-page.php';
        }
    } elseif (get_page_template_slug() === 'templates/checkout-page.php') {
        if ($theme_file = locate_template(array('templates/checkout-page.php'))) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path(__FILE__) . 'templates/checkout-page.php';
        }
    } elseif (get_page_template_slug() === 'templates/shop-page.php') {
        if ($theme_file = locate_template(array('templates/shop-page.php'))) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path(__FILE__) . 'templates/shop-page.php';
        }
    } elseif (get_page_template_slug() === 'templates/template-shop.php') {
        if ($theme_file = locate_template(array('templates/template-shop.php'))) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path(__FILE__) . 'templates/template-shop.php';
        }
    } elseif (get_page_template_slug() === 'templates/template-thankyou.php') {
        if ($theme_file = locate_template(array('templates/template-thankyou.php'))) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path(__FILE__) . 'templates/template-thankyou.php';
        }
    } elseif (get_page_template_slug() === 'templates/template-download.php') {
        if ($theme_file = locate_template(array('templates/template-download.php'))) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path(__FILE__) . 'templates/template-download.php';
        }
    } elseif (get_page_template_slug() === 'templates/template-register.php') {
        if ($theme_file = locate_template(array('templates/template-register.php'))) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path(__FILE__) . 'templates/template-register.php';
        }
    } elseif (get_page_template_slug() === 'templates/template-login.php') {
        if ($theme_file = locate_template(array('templates/template-login.php'))) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path(__FILE__) . 'templates/template-login.php';
        }
    } elseif (get_page_template_slug() === 'templates/template-lostpass.php') {
        if ($theme_file = locate_template(array('templates/template-lostpass.php'))) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path(__FILE__) . 'templates/template-lostpass.php';
        }
    } elseif (get_page_template_slug() === 'templates/template-profile.php') {
        if ($theme_file = locate_template(array('templates/template-profile.php'))) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path(__FILE__) . 'templates/template-profile.php';
        }
    }
    if ($template == '') {
        throw new \Exception('No template found');
    }
    return $template;
}
add_filter('template_include', 'ds_book_load_template_page');

//merge query string
if (!function_exists('ds_merge_querystring')) {
    function ds_merge_querystring($url = null, $query = null, $recursive = false)
    {
        if ($url == null)
            return false;
        if ($query == null)
            return $url;
        $url_components = parse_url($url);
        if (empty($url_components['query']))
            return $url . '?' . ltrim($query, '?');
        parse_str($url_components['query'], $original_query_string);
        parse_str(parse_url($query, PHP_URL_QUERY), $merged_query_string);
        if ($recursive == true)
            $merged_result = array_merge_recursive($original_query_string, $merged_query_string);
        else
            $merged_result = array_merge($original_query_string, $merged_query_string);
        return str_replace($url_components['query'], http_build_query($merged_result), $url);
    }
}

//get list shop from database
function get_list_address_shop()
{
    global $wpdb;
    $tablename = $wpdb->prefix . "shop_address";
    $data = $wpdb->get_results("SELECT * FROM {$tablename}");
    return $data;
}
function get_shop_id()
{
    global $wpdb;
    $tablename = $wpdb->prefix . "shop_address";
    $data = $wpdb->get_row("SELECT ID FROM {$tablename}");
    return $data->ID;
}

//check shop exist or not
function check_shop_id($shop_id)
{
    global $wpdb;
    $tablename = $wpdb->prefix . "shop_address";
    $data = $wpdb->get_row("SELECT * FROM {$tablename} WHERE id = " . $shop_id);
    if ($data) {
        return true;
    } else {
        return false;
    }
}

//get shop by id
function get_shop_data_by_id($shop_id)
{
    global $wpdb;
    $tablename = $wpdb->prefix . "shop_address";
    $data = $wpdb->get_row("SELECT * FROM {$tablename} WHERE id = " . $shop_id);
    return $data;
}

//get google map key
function get_google_map_key()
{
    $dsmart_google_key = get_option('dsmart_google_key');
    return $dsmart_google_key;
}
//get cart total item
function ds_get_cart_total_item($cart = null)
{
    if ($cart == null) {
        if (isset($_COOKIE['cart']) && $_COOKIE['cart'] != "") {
            $cart = unserialize(base64_decode($_COOKIE['cart']));
        } else {
            $cart = array();
        }
    }
    $total_all = 0;
    if ($cart) {
        foreach ($cart as $key_item => $value_item) {
            $product_id = intval($value_item['product_id']);
            $quantity         = $value_item['quantity'];
            $meta['quantity'] = dsmart_field('quantity', $product_id);
            $meta['varialbe_price'] = dsmart_field('varialbe_price', $product_id);

            $meta['extra_name'] = dsmart_field('extra_name', $product_id);
            $meta['extra_price'] = dsmart_field('extra_price', $product_id);
            $meta['sidedish_name'] = dsmart_field('sidedish_name', $product_id);
            $meta['sidedish_price'] = dsmart_field('sidedish_price', $product_id);
            $extra_price = 0;
            if (isset($value_item['extra_info']) && $value_item['extra_info'] != null && $meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']))) :
                $extra_info = json_decode(stripslashes($value_item['extra_info']));
                foreach ($extra_info as $extra_key => $extra_value) {
                    $extra_id = intval(explode('_', $extra_value->extra_id)[1]) - 1;
                    $extra_quantity = $extra_value->extra_quantity;
                    $temp_price = $meta['extra_price'][$extra_id];
                    $temp_price = floatval($temp_price) * intval($extra_quantity);
                    $extra_price = $extra_price + $temp_price;
                }
            else :
                $extra_info = [];
                $extra_price = 0;
            endif;

            $sidedish_price = 0;
            if (isset($value_item['sidedish_info']) && $value_item['sidedish_info'] != null && $meta['sidedish_name'] != null && !empty(array_filter($meta['sidedish_name']))) :
                $sidedish_info = json_decode(stripslashes($value_item['sidedish_info']));
                foreach ($sidedish_info as $sidedish_key => $sidedish_value) {
                    $sidedish_id = intval(explode('_', $sidedish_value->sidedish_id)[1]) - 1;
                    $sidedish_price = isset($meta['sidedish_price'][$sidedish_id]) && $meta['sidedish_price'][$sidedish_id] != "" ? floatval($meta['sidedish_price'][$sidedish_id]) : 0;
                }
            else :
                $sidedish_info = [];
                $sidedish_price = 0;
            endif;

            if (isset($value_item['variable_id']) && $meta['quantity'] != null &&  !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']))) :
                $variable_id = intval(explode('_', $value_item['variable_id'])[1]) - 1;
                $price_item =  floatval($meta['varialbe_price'][$variable_id]) + $extra_price + $sidedish_price;
                $price         = ds_convert_price($price_item) * intval($quantity);
            else :
                $variable_id = '';
                $price_item = dsmart_field('price', $product_id);
                $price         = ds_caculate_item_price($product_id, $quantity) + ($extra_price + $sidedish_price) * intval($quantity);
            endif;

            $status_item = dsmart_field('status', $product_id);
            $quantity = $value_item['quantity'];
            if ($status_item == "instock" && $price_item != "") {
                $total_all = $total_all + floatval($price);
            }
        }
    }
    return $total_all;
}
//get cart total item can use coupon
function ds_get_cart_total_item_use_coupon($cart = null)
{
    if ($cart == null) {
        if (isset($_COOKIE['cart']) && $_COOKIE['cart'] != "") {
            $cart = unserialize(base64_decode($_COOKIE['cart']));
        } else {
            $cart = array();
        }
    }
    $total_all = 0;
    if ($cart) {
        foreach ($cart as $key_item => $value_item) {
            $product_id = intval($value_item['product_id']);
            if (check_product_can_use_coupon_or_not($product_id) == true) {
                $quantity       = $value_item['quantity'];
                $meta['quantity'] = dsmart_field('quantity', $product_id);
                $meta['varialbe_price'] = dsmart_field('varialbe_price', $product_id);

                $meta['extra_name'] = dsmart_field('extra_name', $product_id);
                $meta['extra_price'] = dsmart_field('extra_price', $product_id);
                $extra_price = 0;
                if (isset($value_item['extra_info']) && $value_item['extra_info'] != null && $meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']))) :
                    $extra_info = json_decode(stripslashes($value_item['extra_info']));
                    foreach ($extra_info as $extra_key => $extra_value) {
                        $extra_id = intval(explode('_', $extra_value->extra_id)[1]) - 1;
                        $extra_quantity = $extra_value->extra_quantity;
                        $temp_price = $meta['extra_price'][$extra_id];
                        $temp_price = floatval($temp_price) * intval($extra_quantity);
                        $extra_price = $extra_price + $temp_price;
                    }
                else :
                    $extra_info = [];
                    $extra_price = 0;
                endif;

                if (isset($value_item['variable_id']) && $meta['quantity'] != null &&  !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']))) :
                    $variable_id = intval(explode('_', $value_item['variable_id'])[1]) - 1;
                    $price_item =  floatval($meta['varialbe_price'][$variable_id]) + $extra_price;
                    $price      = ds_convert_price($price_item) * intval($quantity);
                else :
                    $variable_id = '';
                    $price_item = dsmart_field('price', $product_id);
                    $price      = ds_caculate_item_price($product_id, $quantity) + $extra_price * intval($quantity);;
                endif;

                $status_item = dsmart_field('status', $product_id);
                $quantity = $value_item['quantity'];
                if ($status_item == "instock" && $price_item != "") {
                    $total_all = $total_all + floatval($price);
                }
            }
        }
    }
    return $total_all;
}
//check product can use coupon or not
function check_product_can_use_coupon_or_not($product_id)
{
    $can_not_use_coupon = dsmart_field('can_not_use_coupon', $product_id);
    if ($can_not_use_coupon == "1") return false;
    $terms = wp_get_post_terms($product_id, 'product-cat');
    if ($terms) {
        foreach ($terms as $term) {
            $can_not_use_coupon = get_term_meta($term->term_id, 'can_not_use_coupon', true);
            if ($can_not_use_coupon == "1") {
                return false;
            }
        }
    }
    return true;
}
//get vat total item
function ds_get_vat_total_item($cart = null)
{
    if ($cart == null) {
        if (isset($_COOKIE['cart']) && $_COOKIE['cart'] != "") {
            $cart = unserialize(base64_decode($_COOKIE['cart']));
        } else {
            $cart = array();
        }
    }
    $vat7 = 0;
    $vat19 = 0;
    $taxes = 0;
    if ($cart) {
        foreach ($cart as $key_item => $value_item) {
            $product_id = intval($value_item['product_id']);
            $quantity   = $value_item['quantity'];

            $meta['quantity'] = dsmart_field('quantity', $product_id);
            $meta['varialbe_price'] = dsmart_field('varialbe_price', $product_id);

            $meta['extra_name'] = dsmart_field('extra_name', $product_id);
            $meta['extra_price'] = dsmart_field('extra_price', $product_id);
            $extra_price = 0;
            if (isset($value_item['extra_info']) && $value_item['extra_info'] != null && $meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']))) :
                $extra_info = json_decode(stripslashes($value_item['extra_info']));
                foreach ($extra_info as $extra_key => $extra_value) {
                    $extra_id = intval(explode('_', $extra_value->extra_id)[1]) - 1;
                    $extra_quantity = $extra_value->extra_quantity;
                    $temp_price = $meta['extra_price'][$extra_id];
                    $temp_price = floatval($temp_price) * intval($extra_quantity);
                    $extra_price = $extra_price + $temp_price;
                }
            else :
                $extra_info = [];
                $extra_price = 0;
            endif;

            if (isset($value_item['variable_id']) && $meta['quantity'] != null &&  !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']))) :
                $variable_id = intval(explode('_', $value_item['variable_id'])[1]) - 1;
                $price_item =  floatval($meta['varialbe_price'][$variable_id]) + $extra_price;
                $price      = $price_item * intval($quantity);
            else :
                $variable_id = '';
                $price_item = dsmart_field('price', $product_id);
                $price      = ds_caculate_item_price($product_id, $quantity) + $extra_price * intval($quantity);;
            endif;
            $status_item = dsmart_field('status', $value_item['product_id']);
            $quantity = $value_item['quantity'];
            if ($status_item == "instock" && $price_item != "") {
                $vat_item       = dsmart_field('vat', $product_id);
                $taxes_item     = dsmart_field('taxes', $product_id);
                if ($vat_item != "") {
                    $vat_price = ($price - round($price / (1 + $vat_item / 100), 2));
                    if ($vat_item == "7") {
                        $vat7 = $vat7 + $vat_price;
                    } else {
                        $vat19 = $vat19 + $vat_price;
                    }
                }
                if ($taxes_item != "") {
                    $tax_price = $price - round($price / (1 + $taxes_item / 100), 2);
                    $taxes = $taxes + $tax_price;
                }
            }
        }
    }
    $vat = array(
        'vat7' => $vat7,
        'vat19' => $vat19,
        'taxes' => $taxes,
    );
    return $vat;
}
//total quantity
function ds_get_cart_total_quantity($cart = null)
{
    if ($cart == null) {
        if (isset($_COOKIE['cart']) && $_COOKIE['cart'] != "") {
            $cart = unserialize(base64_decode($_COOKIE['cart']));
        } else {
            $cart = array();
        }
    }
    $total_all = 0;
    if ($cart) {
        foreach ($cart as $key_item => $value_item) {
            $product_id = intval($value_item['product_id']);
            $quantity   = $value_item['quantity'];

            $meta['quantity'] = dsmart_field('quantity', $product_id);
            $meta['varialbe_price'] = dsmart_field('varialbe_price', $product_id);

            $meta['extra_name'] = dsmart_field('extra_name', $product_id);
            $meta['extra_price'] = dsmart_field('extra_price', $product_id);
            $extra_price = 0;
            if (isset($value_item['extra_info']) && $value_item['extra_info'] != null && $meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']))) :
                $extra_info = json_decode(stripslashes($value_item['extra_info']));
                foreach ($extra_info as $extra_key => $extra_value) {
                    $extra_id = intval(explode('_', $extra_value->extra_id)[1]) - 1;
                    $extra_quantity = $extra_value->extra_quantity;
                    $temp_price = $meta['extra_price'][$extra_id];
                    $temp_price = floatval($temp_price) * intval($extra_quantity);
                    $extra_price = $extra_price + $temp_price;
                }
            else :
                $extra_info = [];
                $extra_price = 0;
            endif;

            if (isset($value_item['variable_id']) && $meta['quantity'] != null &&  !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']))) :
                $variable_id = intval(explode('_', $value_item['variable_id'])[1]) - 1;
                $price_item =  floatval($meta['varialbe_price'][$variable_id]) + $extra_price;
            else :
                $variable_id = '';
                $price_item = floatval(dsmart_field('price', $product_id))  + $extra_price * intval($quantity);;
            endif;
            $status_item = dsmart_field('status', $value_item['product_id']);
            if ($status_item == "instock" && $price_item != "") {
                $total_all = $total_all + intval($quantity);
            }
        }
    }
    return $total_all < 1 ? 1 : $total_all;
}

//convert from m to km
function ds_convert_m_to_km($value)
{
    $value = floatval($value) / 1000;
    return $value;
}

//convert from km to m
function ds_convert_km_to_m($value)
{
    $value = floatval($value) * 1000;
    return $value;
}

//get distance from customer to shop
function get_distance_from_customer_to_shop($shop_lat, $shop_long, $customer_lat, $customer_long)
{
    $distance = GetDrivingDistance($shop_lat, $customer_lat, $shop_long, $customer_long);
    return floatval($distance['meter']);
}
function GetDrivingDistance($lat1, $lat2, $long1, $long2)
{
    $apiKey = get_option('dsmart_google_key');
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&key=" . $apiKey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $response_a = json_decode($response, true);
    $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    $meter = $response_a['rows'][0]['elements'][0]['distance']['value'];
    $time = $response_a['rows'][0]['elements'][0]['duration']['text'];

    return array('distance' => $dist, 'meter' => $meter, 'time' => $time);
}
//format number
function dsmart_format_number($number)
{
    $number = floatval($number);
    $number = number_format($number, 0, '.', ' ');
    return $number;
}

//function get all option user
function dsmart_option_user($book_user)
{
    $users = get_users(array('fields' => array('ID')));
    foreach ($users as $user_id) {
        if ($book_user != "") {
            if (in_array($user_id->ID, $book_user)) {
                $select = "selected";
            } else {
                $select = "";
            }
        }
        $userdata = get_userdata($user_id->ID);
        echo '<option value="' . $user_id->ID . '" ' . $select . '>' . $userdata->user_login . '( ID : ' . $user_id->ID . ' )' . '</option>';
    }
}

//check shipping info available
function check_shipping_available($shipping_info)
{
    date_default_timezone_set('Europe/Berlin');
    $check = false;
    $shop_id = $shipping_info['shop'];
    $shipping_method = $shipping_info['shipping_method'];
    $check_zipcode = (get_option('zipcode_status') == "on") ? true : false;
    if ($shipping_method == "shipping") {
        $user_location = $shipping_info['location'];
        $user_latitude = $shipping_info['latitude'];
        $user_longitude = $shipping_info['longitude'];
        $delivery_time = $shipping_info['delivery_time'];
        $delivery_date = $shipping_info['delivery_date'];
        $delivery_zipcode = $shipping_info['zipcode'];
    }
    $close_shop = get_option('dsmart_close_shop');
    $dsmart_distance = get_option('dsmart_distance');
    $dsmart_min_order = ds_convert_price(get_option('dsmart_min_order'));
    $dsmart_min_order_free = get_option('dsmart_min_order_free') != "" ? ds_convert_price(get_option('dsmart_min_order_free')) : "";
    $dsmart_shipping_fee = "";
    $dsmart_shipping_from   = get_option('dsmart_shipping_from');
    $dsmart_shipping_to     = get_option('dsmart_shipping_to');
    $dsmart_shipping_cs_fee = get_option('dsmart_shipping_cs_fee');
    //$dsmart_shipping_cs_fee = get_option('dsmart_min_cs_fee');
    $dsmart_min_cs_fee = get_option('dsmart_min_cs_fee');
    $shipping_fee = 0;
    $total_cart = ds_get_cart_total_item();
    $data = array();
    /*if(isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "" && check_coupon_available($_COOKIE['coupon']) == 1){
        $coupon_price = get_price_of_coupon($_COOKIE['coupon']);
    }else{
        $coupon_price = 0;
    }*/
    if ($shop_id == "" || ($shop_id != "" && check_shop_id($shop_id) == false) || $close_shop == "on") {
        $data['check'] = false;
    } else {
        $shop = get_shop_data_by_id($shop_id);
        if ($close_shop == "on") {
            $data['check'] = false;
        } elseif ($shipping_method == "shipping") {
            if ($check_zipcode == true && $shipping_info['zipcode'] != "") {
                $zipcode_get = get_data_zipcode($shipping_info['zipcode']);
                if ($zipcode_get == false) {
                    $data['check'] = false;
                } else {
                    $zipcode = $zipcode_get['zipcode'];
                    $minium_order = intval($zipcode_get['minium_order']);
                    $zipcode_price = floatval($zipcode_get['price']);
                    if ($minium_order > $total_cart) {
                        $data['check'] = false;
                    } else {
                        $data['check'] = true;
                        $data['price'] = $zipcode_price;
                    }
                }
            } else {
                if (isset($delivery_time) && $delivery_time != "") {
                    $check = check_time_with_time_shop($delivery_time, null, $delivery_date, $shipping_method);
                } else {
                    $check = check_time_with_time_shop(date("H:i"), null, $delivery_date, $shipping_method);
                }
                if ($check == true) {
                    $distance = get_distance_from_customer_to_shop($shop->latitude, $shop->longitude, $user_latitude, $user_longitude);
                    $min = null;
                    if (count($dsmart_shipping_from) > 0) {
                        foreach ($dsmart_shipping_from as $key => $value) {
                            if ($distance >= intval($dsmart_shipping_from[$key])*1000 && $distance <= intval($dsmart_shipping_to[$key])*1000) {
                                $dsmart_shipping_fee = ds_convert_price($dsmart_shipping_cs_fee[$key]);
                                if ($dsmart_min_cs_fee[$key] != "") {
                                    $min = $dsmart_min_cs_fee[$key];
                                }
                            }
                        }
                    }
                    if ($dsmart_distance != "" && $distance > floatval($dsmart_distance)*1000) {
                        $data['check'] = false;
                    } elseif ($min !== null && $total_cart < $min) {
                        $data['check'] = false;
                    } else {
                        if (($dsmart_min_order_free != "" && $total_cart > $dsmart_min_order_free)) {
                            $shipping = 0;
                        } else {
                            if ($dsmart_shipping_fee == "") {
                                $shipping = 0;
                            } else {
                                $shipping = $dsmart_shipping_fee;
                            }
                        }
                        $data['check'] = true;
                        $data['price'] = $shipping;
                    }
                } else {
                    $data['check'] = false;
                }
            }
        } else {
            $check = check_time_with_time_shop($shipping_info['time'], date("H:i"), null, $shipping_method);
            $shipping = 0;
            $data['check'] = $check;
            $data['price'] = $shipping;
        }
    }
    return $data;
}

function check_time_with_time_shop($time, $current_time = null, $current_date = null, $type = "shipping")
{
    date_default_timezone_set('Europe/Berlin');
    if ($time !== "So schnell wie möglich") {
        $time_data = date('H:i', strtotime($time));
        $time = new DateTime($time);
    } else {
        $zxc_time   = date('H:i');
        $time = new DateTime($zxc_time);
    }
    $close_shop = get_option('dsmart_close_shop');
    $current_date_text = custom_date();
    if ($current_date == null) {
        $current_date = date('d-m-Y');
        $current_date_text = custom_date();
    } else {
        $current_date_text = substr(strtolower(date('D', strtotime($current_date))), 0, 2);
    }
    if ($type == "shipping") {
        $time_open_shop = get_option('time_open_shop_' . $current_date_text);
        $time_close_shop = get_option('time_close_shop_' . $current_date_text);
        // $closed_time = get_option('closed_time');
        $closed_time = get_option('closed_time_2');
    } else {
        $time_open_shop = get_option('time_open_shop_2_' . $current_date_text);
        $time_close_shop = get_option('time_close_shop_2_' . $current_date_text);
        // $closed_time = get_option('closed_time_2');
        $closed_time = get_option('closed_time');
    }
    $dsmart_custom_date = get_option('dsmart_custom_date');
    $time_shop_array = array();
    if ($dsmart_custom_date != "" && count($dsmart_custom_date) > 0) {
        // Sort by 'open' time to ensure chronological gap calculation
        usort($dsmart_custom_date, function($a, $b) {
            return strcmp($a['open'], $b['open']);
        });
        foreach ($dsmart_custom_date as $item) {
            if ($current_date == $item['date']) {
                $time_shop_array[] = array("open" => $item['open'], "close" => $item['close']);
            }
        }
    }
    $closed_time_array = array();
    if ($closed_time != "" && count($closed_time) > 0) {
        foreach ($closed_time as $item) {
            if ($item['date'] == $current_date_text) {
                $closed_time_array[] = array("from" => $item['from'], "to" => $item['to']);
            }
        }
    }
    if ($close_shop == "on") {
        return false;
    } elseif ($current_time != null && date('H:i', strtotime($current_time)) > $time_data) {
        return false;
    } else {
        if (count($time_shop_array) > 0) {
            $check_data = true;
            foreach ($time_shop_array as $item_data) {
                $time_open_shop = new DateTime($item_data['open']);
                $time_close_shop = new DateTime($item_data['close']);
                if ($time <= $time_close_shop && $time >= $time_open_shop) {
                    $check_data = true;
                    break;
                } else {
                    $check_data = false;
                }
            }
            return $check_data;
        } elseif (count($closed_time_array) > 0) {
            $check_data = true;
            foreach ($closed_time_array as $item_data) {
                $closed_from = date('H:i', strtotime($item_data['from']));
                $closed_to = date('H:i', strtotime($item_data['to']));
                if ($time_data >= $closed_from && $time_data <= $closed_to) {
                    $check_data = true;
                    break;
                } else {
                    $check_data = false;
                }
            }
            if ($check_data == true) {
                return false;
            } else {
                if ($time_open_shop == "" && $time_close_shop == "") {
                    return true;
                } elseif ($time_open_shop == "" && $time_close_shop != "") {
                    $time_close_shop = new DateTime($time_close_shop);
                    if ($time <= $time_close_shop) {
                        return true;
                    } else {
                        return false;
                    }
                } elseif ($time_open_shop != "" && $time_close_shop == "") {
                    $time_open_shop = new DateTime($time_open_shop);
                    if ($time >= $time_open_shop) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    $time_open_shop = new DateTime($time_open_shop);
                    $time_close_shop = new DateTime($time_close_shop);
                    if ($time <= $time_close_shop && $time >= $time_open_shop) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        } else {
            if ($time_open_shop == "" && $time_close_shop == "") {
                return true;
            } elseif ($time_open_shop == "" && $time_close_shop != "") {
                $time_close_shop = new DateTime($time_close_shop);
                if ($time <= $time_close_shop) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($time_open_shop != "" && $time_close_shop == "") {
                $time_open_shop = new DateTime($time_open_shop);
                if ($time >= $time_open_shop) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $time_open_shop = new DateTime($time_open_shop);
                $time_close_shop = new DateTime($time_close_shop);
                if ($time <= $time_close_shop && $time >= $time_open_shop) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
}
//random coupon code
function ds_random_order_code()
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $prefix = 'CP';
    $date = date('dmy');
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass) . $date;
}
function generateRandomString($length = 16)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function dsmart_getDistance($addressFrom, $addressTo)
{
    $apiKey = get_option('dsmart_google_key');
    $distance = file_get_contents('https://maps.googleapis.com/maps/api/directions/json?origin=' . urlencode($addressFrom) . '&destination=' . urlencode($addressTo) . '&key=' . $apiKey);
    $distance = json_decode($distance);
    return $distance;
    if ($distance != null && $distance->error_message == null) :
        return $distance;
    else :
        return null;
    endif;
}

//send mail after order
function send_mail_after_order($order_id)
{
    $download_id = get_page_id_by_template('templates/template-download.php');
    $admin_email = get_option('admin_email');
    $dsmart_text_mail_order = get_option('dsmart_text_mail_order');
    $dsmart_text_mail_order_cs = get_option('dsmart_text_mail_order_cs');
    $shop_id = dsmart_field('shop_id', $order_id);
    $shop_data = get_shop_data_by_id($shop_id);
    $shop_email = $shop_data->email;
    $currency = dsmart_field('currency', $order_id);
    $customer_name1 = dsmart_field('customer_name1', $order_id);
    $customer_name2 = dsmart_field('customer_name2', $order_id);
    $customer_email = dsmart_field('customer_email', $order_id);
    $customer_etage = dsmart_field('customer_etage', $order_id);
    $customer_zipcode = dsmart_field('customer_zipcode', $order_id);
    $customer_phone = dsmart_field('customer_phone', $order_id);
    $more_additional = dsmart_field('more_additional', $order_id);
    $tax = dsmart_field('tax', $order_id);
    $items = dsmart_field('item', $order_id);
    $subtotal = dsmart_field('subtotal', $order_id);
    $coupon = dsmart_field('coupon', $order_id);
    $coupon_price = dsmart_field('coupon_price', $order_id);
    $shipping_method = dsmart_field('shipping_method', $order_id);
    $user_location = dsmart_field('user_location', $order_id);
    $user_latitude = dsmart_field('user_latitude', $order_id);
    $user_longitude = dsmart_field('user_longitude', $order_id);
    $user_time = dsmart_field('user_time', $order_id);
    $user_date = dsmart_field('user_date', $order_id);
    $user_delivery_time = dsmart_field('user_delivery_time', $order_id);
    $user_delivery_date = dsmart_field('user_delivery_date', $order_id);
    $shipping_fee = dsmart_field('shipping_fee', $order_id);
    $total = dsmart_field('total', $order_id);
    //$taxes = dsmart_field('taxes',$order_id);
    $method = dsmart_field('method', $order_id);
    $reduce = dsmart_field('reduce', $order_id);
    $second_order_number = dsmart_field('second_order_number', $order_id);
    $reduce_percent = dsmart_field('reduce_percent', $order_id);
    $bab = dsmart_field('bab', $order_id);
    $ar = dsmart_field('ar', $order_id);
    if ($bab == "1") {
        $bab_text = "Ja";
    } else {
        $bab_text = "Nein";
    }
    if (is_array($ar) && isset($ar['ar']) && $ar['ar'] == 1) {
        $ar_text = "Ja";
        $r_prefix_int = $ar['r_prefix'];
        switch ($r_prefix_int) {
            case '1':
                $r_prefix = "Frau";
                break;
            case '0':
                $r_prefix = "Herr";
                break;
            default:
                $r_prefix = "Herr";
                break;
        }
        $r_first_name = $ar['r_first_name'];
        $r_last_name = $ar['r_last_name'];
        $r_company = $ar['r_company'];
        $r_zipcode = $ar['r_zipcode'];
        $r_city = $ar['r_city'];
        $r_street = $ar['r_street'];
    } else {
        $ar_text = "Nein";
    }
    $address = $shop_data->shop_address;
    $dsmart_barzahlung  = (get_option('dsmart_barzahlung') != "") ? get_option('dsmart_barzahlung') : 'Barzahlung';
    $method = dsmart_field('method', $order_id);
    $get_distance = get_option('get_distance');
    $html_file = "";
    $data_item = "";
    if ($get_distance == "on") :
        $distance_data = GetDrivingDistance($shop_data->latitude, $user_latitude, $shop_data->longitude, $user_longitude);
        $distance = $distance_data['distance'];
    else :
        $distance = null;
    endif;
    switch ($method) {
        case 'paypal':
            $method_text = "Paypal";
            break;
        case 'klarna':
            $method_text = "Klarna";
            break;
        case 'cash':
            $method_text = $dsmart_barzahlung;
            break;
        default:
            $method_text = $method;
            break;
    }

    $dsmart_header_mail_order = get_option('dsmart_header_mail_order');
    $dsmart_header_mail_order_cs = get_option('dsmart_header_mail_order_cs');

    if ($dsmart_header_mail_order == "") {
        $title = "Order " . get_the_title($order_id);
    } else {
        $title = $dsmart_header_mail_order;
    }
    if ($dsmart_header_mail_order_cs == "") {
        $title1 = "You have new order " . get_the_title($order_id);
    } else {
        $title1 = $dsmart_header_mail_order_cs . " " . get_the_title($order_id);
    }
    $show_second_number = get_option('show_second_number');
    $order_date = get_the_date('d-m-Y', $order_id);
    $height = 400;
    $body_header = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
    <html lang='en-US' xmlns='http://www.w3.org/1999/xhtml' xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml'
    xmlns:o='urn:schemas-microsoft-com:office:office'>
        <head>
           <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
           <link rel='profile' href='http://gmpg.org/xfn/11'>
           <link rel='pingback' href='http://api.dsmart.vn/xmlrpc.php'>
           <style>
                table{
                    font-size: 14pt;
                    line-height: 20pt;
                }
                .cart-variable{
                    padding: 5px;
                    margin-top: 5px;
                    background-color: #f8f5f2;
                }
                .cart-variable h5 {
                    font-size: 18pt;
                    line-height: 24pt;
                    margin: 0;
                }
                .cart-variable p, .cart-variable ul {
                    font-size: 18pt;
                    line-height: 24pt;
                    margin: 0;
                }
                .cart-variable ul {
                    list-style: none;
                    padding-left: 0;
                    margin-bottom: 0;
                }
                .table-title,  .dsmart-title{
                    font-size: 14pt;
                    line-height: 20pt;
                }
                .dsmart-title{
                    margin: 0;
                    font-size: 18pt;
                    line-height: 24pt;
                }
           </style>
       </head>
       <body>";
    $body = "";
    $body_shop = "";
    $body_cs = "";
    $body_shop .= '<p style="color:#333333;font-family:arial; font-size:14pt; line-height: 20pt; margin: 0;">' . $dsmart_text_mail_order . '</p>';
    $body_cs .= '<p style="color:#333333;font-family:arial; font-size:14pt; line-height: 20pt; margin: 0;">' . $dsmart_text_mail_order_cs . '</p>';
    $body .= '<h3 class="table-title" style="font-size:14pt; line-height: 20pt; margin: 10px 0;">Kundeninformationenn</h3>';
    $body .= '<div style="display:table;font-size:14pt; line-height: 20pt;padding-left:8px;padding-bottom:2px">';
    $body .= '<table style="border-spacing:2px;text-indent:0;border-collapse:collapse;width:1000px">';
    $body .= '<tbody>';
    if ($show_second_number == "1") {
        $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;width:150px;"><b>Bestellnummer</b></td>';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px" colspan="3">' . $second_order_number . '</td>';
        $body .= '</tr>';
    }
    $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;width:150px;"><b>Nachname</b></td>';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px" colspan="3">' . $customer_name1 . '</td>';
    $body .= '</tr>';
    $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;width:150px;"><b>Vorname</b></td>';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px" colspan="3">' . $customer_name2 . '</td>';
    $body .= '</tr>';
    $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Email Adresse</b></td>';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px">' . $customer_email . '</td>';
    $body .= '</tr>';
    $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Telefonnummer</b></td>';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px">' . $customer_phone . '</td>';
    $body .= '</tr>';
    $master_time = '';
    if ($shipping_method == "shipping") {
        $master_time = $user_delivery_time;
        $height += 100;
        $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Adresse</b></td>';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px">' . $user_location . '</td>';
        $body .= '</tr>';
        $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Bestelldatum</b></td>';
        if ($order_date == $user_delivery_date) {
            $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;font-size:18px;font-weight:bold;color:green;">' . $user_delivery_date . '</td>';
        } else {
            $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;font-size:18px;font-weight:bold;color:black;">ACHTUNG++' . $user_delivery_date . '++ACHTUNG</td>';
        }
        $body .= '</tr>';
        $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Lieferzeit</b></td>';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px">' . $user_delivery_time . '</td>';
        $body .= '</tr>';
    } else {
        $master_time = $user_time;
        $height += 40;
        $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Liefer- / Abholzeit</b></td>';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px">' . $user_time . '</td>';
        $body .= '</tr>';
    }
    $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Postleitzahl</b></td>';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px">' . $customer_zipcode . '</td>';
    $body .= '</tr>';
    $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Etage</b></td>';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px">' . $customer_etage . '</td>';
    $body .= '</tr>';

    $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Bestell Notiz</b></td>';
    $body .= '<td style="font-weight:bold;border:1px solid #aaaaaa;padding:8px 15px">' . $more_additional . '</td>';
    $body .= '</tr>';
    $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Bezahlmethode</b></td>';
    $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px">' . $method_text . '</td>';
    $body .= '</tr>';
    if ($shipping_method == "shipping") {
        $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Versandart</b></td>';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px; font-size: 22px; line-height: 28pt;"><b>Lieferung</b></td>';
        $body .= '</tr>';
    } else {
        $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Versandart</b></td>';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px; font-size: 22pt; line-height: 28pt;"><b>im Laden Abholen</b></td>';
        $body .= '</tr>';
    }
    if ($shipping_method == "shipping" && $distance != null) {
        $height += 30;
        $body .= '<tr style="display:table-row;vertical-align:inherit; font-size:14pt; line-height: 20pt;">';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Entfernung</b></td>';
        $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px">' . $distance . '</td>';
        $body .= '</tr>';
    }
    // else{
    //     $body .= '<tr style="display:table-row;vertical-align:inherit">';
    //     $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px"><b>Entfernung</b></td>';
    //     $body .= '<td style="border:1px solid #aaaaaa;padding:8px 15px">0km</td>';
    //     $body .= '</tr>';
    // }
    $body .= '</tbody>';
    $body .= '</table>';
    $body .= '</div>';

    $body_shop2 .= '<div style="display:table;font-size:14pt; line-height: 20pt; padding-left:8px;padding-bottom:2px">';
    $body_shop2 .= '<table style="border-spacing:2px;text-indent:0;border-collapse:collapse;width:1000px">';
    $body_shop2 .= '<tbody>';
    $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Bewirtungsbeleg als Bon</b></td>';
    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;">' . $bab_text . '</td>';
    $body_shop2 .= '</tr>';
    $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Alternative Rechnungsadresse</b></td>';
    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;">' . $ar_text . '</td>';
    $body_shop2 .= '</tr>';
    if ($ar['ar'] == 1) {
        $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Anrede</b></td>';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;">' . $r_prefix . '</td>';
        $body_shop2 .= '</tr>';
        $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Vorname</b></td>';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;">' . $r_first_name . '</td>';
        $body_shop2 .= '</tr>';
        $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Nachname</b></td>';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;">' . $r_last_name . '</td>';
        $body_shop2 .= '</tr>';

        $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Firma</b></td>';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;">' . $r_company . '</td>';
        $body_shop2 .= '</tr>';
        $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>PLZ</b></td>';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;">' . $r_zipcode . '</td>';
        $body_shop2 .= '</tr>';
        $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Stadt</b></td>';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;">' . $r_city . '</td>';
        $body_shop2 .= '</tr>';
        $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Straße</b></td>';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;">' . $r_street . '</td>';
        $body_shop2 .= '</tr>';
    }
    $body_shop2 .= '</tbody>';
    $body_shop2 .= '</table>';
    $body_shop2 .= '</div>';

    $body_shop2 .= '<h3 class="table-title" style="font-size:14pt; line-height: 20pt; margin: 10px 0;">Bestellinformationen</h3>';
    $body_shop2 .= '<div style="display:table;font-size:14pt; line-height: 20pt; padding-left:8px;padding-bottom:2px">';
    $body_shop2 .= '<table style="border-spacing:2px;text-indent:0;border-collapse:collapse;width:1000px">';
    $body_shop2 .= '<thead>';
    $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
    $body_shop2 .= '<th style="border:1px solid #aaaaaa;padding:8px 15px;width:250px;">Produkt</th>';
    $body_shop2 .= '<th style="border:1px solid #aaaaaa;padding:8px 15px">Zwischensumme</th>';
    $body_shop2 .= '</tr>';
    $body_shop2 .= '</thead>';
    $body_shop2 .= '<tbody>';
    if ($items != "") {
        $sub_total = 0;
        foreach ($items as $key => $item) {
            if (isset($item['product_id'])) :
                $product_id = intval($item['product_id']);
            else :
                $product_id = $key;
            endif;
            //$sub_total = $sub_total + $item['price'];
            $meta['quantity'] = dsmart_field('quantity', $product_id);
            $meta['varialbe_price'] = dsmart_field('varialbe_price', $product_id);
            $meta['sidedish_name'] = dsmart_field('sidedish_name', $product_id);
            $meta['sidedish_price'] = dsmart_field('sidedish_price', $product_id);
            $meta['extra_name'] = dsmart_field('extra_name', $product_id);
            $meta['extra_price'] = dsmart_field('extra_price', $product_id);
            $sidedish_text = dsmart_field('sidedish_text', $product_id);
            if($sidedish_text == null || $sidedish_text == '')
            {
                $sidedish_text = "Beilage";
            }
            
            $data_item .= '<div class="section" style="margin-bottom: 10px;padding-bottom: 10px;border-bottom: 1px dashed #000;">';
            
            $isExtra = isset($item['extra_info']) && $item['extra_info'] != null && $meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']));
            $isSidedish = isset($item['sidedish_info']) && $item['sidedish_info'] != null && $meta['sidedish_name'] != null && !empty(array_filter($meta['sidedish_name']));
            $isVariable = isset($item['variable_id']) && $meta['quantity'] != null && !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']));
            
            if($isVariable)
            {
                $data_item .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>' . $item['quantity'] . 'x ' . $item['title'] . '</b></p>';
            }
            else
            {
                $data_item .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>' . $item['quantity'] . 'x ' . $item['title'] . '</b> ' . ds_price_format_text_with_symbol($item['price'], $currency) . '</p>';
            }
            
            if ($isExtra || $isVariable || $isSidedish) :
                $variable_id = intval(explode('_', $item['variable_id'])[1]) - 1;
                $variable_text = '<div class="variable-product cart-variable" style="padding: 5px; margin-top: 5px; background-color: #f8f5f2;">';
                if ($isSidedish) :
                    $height += 60;
                    $data_item .= '<h3 class="table-title" style="font-size:14pt; line-height: 20pt; margin: 10px 0;text-align: center;text-transform: capitalize;">'.$sidedish_text .'</h3>';
                    $sidedish_info = json_decode(stripslashes($item['sidedish_info']));
                    $sidedish_text = '<div class="item">';
                    $sidedish_text .= '<h3 class="table-title" style="font-size:14pt; line-height: 20pt; margin: 10px 0;text-align: center;text-transform: capitalize;">'.$sidedish_text .'</h3>';
                    $sidedish_text .= '<ul style="font-size: 18pt; line-height: 24pt; list-style: none; padding-left: 0; margin: 0;">';
                    foreach ($sidedish_info as $sidedish_key => $sidedish_value) {
                        $sidedish_id = intval(explode('_', $sidedish_value->sidedish_id)[1]) - 1;
                        $height += 30;
                        $data_item .= '<p style="line-height: 1.3;margin: 0;text-align: center;">' . $meta['sidedish_name'][$sidedish_id] . (isset($meta['sidedish_price'][$sidedish_id]) && $meta['sidedish_price'][$sidedish_id] !== "" ? " (+".ds_price_format_text($meta['sidedish_price'][$sidedish_id]).")" : "") . '</p>';
                        $sidedish_text .= '<li style="margin: 0;">' . $meta['sidedish_name'][$sidedish_id] . (isset($meta['sidedish_price'][$sidedish_id]) && $meta['sidedish_price'][$sidedish_id] !== "" ? " (+".ds_price_format_text($meta['sidedish_price'][$sidedish_id]).")" : "") . '</li>';
                    }
                    $sidedish_text .= '</ul>';
                else :
                    $height += 40;
                    $sidedish_text = '';
                endif;
                if($isVariable){
                    $variable_text .=   '<div class="item" style="margin-bottom: 10px; font-size: 18pt; line-height: 24pt;">';
                    $variable_text .=       '<h5 style="font-size: 18pt; line-height: 24pt; margin: 0;">' . __('AUSGEWÄHLTE PRODUKT', 'dsmart') . '</h5>';
                    $variable_text .=       '<p style="margin: 0;">' . $meta['quantity'][$variable_id] . ': ' . ds_price_format_text($meta['varialbe_price'][$variable_id]) . '</p>';
                    $variable_text .=   '</div>';
                    $data_item .= '<p style="line-height: 1.3;margin: 0;text-align: center;">' . $meta['quantity'][$variable_id] . ': ' . ds_price_format_text($meta['varialbe_price'][$variable_id]) . '</p>';
                }
                if ($isExtra) :
                    $height += 60;
                    $data_item .= '<h3 class="table-title" style="font-size:14pt; line-height: 20pt; margin: 10px 0;text-align: center;text-transform: capitalize;">Extra</h3>';
                    $extra_info = json_decode(stripslashes($item['extra_info']));
                    $extra_text = '<div class="item">';
                    $extra_text .= '<h3 class="table-title" style="font-size:14pt; line-height: 20pt; margin: 10px 0;text-align: center;text-transform: capitalize;">Extra</h3>';
                    $extra_text .= '<ul style="font-size: 18pt; line-height: 24pt; list-style: none; padding-left: 0; margin: 0;">';
                    foreach ($extra_info as $extra_key => $extra_value) {
                        $extra_id = intval(explode('_', $extra_value->extra_id)[1]) - 1;
                        $extra_quantity = $extra_value->extra_quantity;
                        $height += 30;
                        $data_item .= '<p style="line-height: 1.3;margin: 0;text-align: center;">' . $meta['extra_name'][$extra_id] . ':' . ds_price_format_text($meta['extra_price'][$extra_id]) . ' x ' . $extra_quantity . '</p>';
                        $extra_text .= '<li style="margin: 0;">' . $meta['extra_name'][$extra_id] . '(+' . ds_price_format_text($meta['extra_price'][$extra_id]) . ') x ' . $extra_quantity . '</li>';
                    }
                    $extra_text .= '</ul>';
                else :
                    $height += 40;
                    $extra_text = '';
                endif;
                $variable_text = $sidedish_text  . ' ' . $variable_text . ' ' . $extra_text;
            else :
                $height += 45;
                $variable_text = '';
            endif;
            $data_item .= '</div>';
            if (intval($item['quantity']) > 0) {
                $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
                $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px">';
                $body_shop2 .= '<h4 class="dsmart-title" style="font-size: 18pt; line-height: 24pt; margin: 0;"><span>' . $item['quantity'] . ' x</span> ' . $item['title'] . '</h4>';
                if ($variable_text != '') :
                    $body_shop2 .= $variable_text;
                endif;
                $body_shop2 .= '</td>';
                $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right; ; font-size:14pt; line-height: 20pt;">';
                $body_shop2 .= '<b>' . ds_price_format_text_with_symbol($item['price'], $currency) . '</b>';
                $body_shop2 .= '</td>';
                $body_shop2 .= '</tr>';
            }
        }
    }
    $body_shop2 .= '</tbody>';
    $body_shop2 .= '<tfoot>';
    $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Lieferungskosten</b></td>';
    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;">' . ds_price_format_text_with_symbol($shipping_fee, $currency) . '</td>';
    $body_shop2 .= '</tr>';
    /*$body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
                    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Mehrwertsteuer</b></td>';
                    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;">'.ds_price_format_text_with_symbol($taxes,$currency).'</td>';
                $body_shop2 .= '</tr>';*/
    $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Zwischensumme</b></td>';
    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;">' . ds_price_format_text_with_symbol($subtotal, $currency) . '</td>';
    $body_shop2 .= '</tr>';
    if ($reduce != "") {
        $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Rabatt (-' . $reduce_percent . ')</b></td>';
        $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height:20pt;font-weight:bold;"> - ' . ((strpos($reduce, ds_price_symbol($currency)) !== false) ? $reduce : ds_price_format_text_with_symbol($reduce, $currency)) . '</td>';
        $body_shop2 .= '</tr>';
    }
    if ($coupon != "") {
        foreach ($coupon as $keycoupon => $valuecoupon) {
            $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
            $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Rabattcode (' . $keycoupon . ')</b></td>';
            $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;font-size:14pt; line-height: 20pt;font-weight:bold;"> - ' . ds_price_format_text_with_symbol($valuecoupon, $currency) . '</td>';
            $body_shop2 .= '</tr>';
        }
    }
    $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Gesamtsumme</b></td>';
    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right;color:#f30;font-size:14pt; line-height: 20pt; font-weight:bold;">' . ds_price_format_text_with_symbol($total, $currency) . '</td>';
    $body_shop2 .= '</tr>';
    $body_shop2 .= '<tr style="display:table-row;vertical-align:inherit">';
    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;"><b>Bezahlverfahren</b></td>';
    $body_shop2 .= '<td style="border:1px solid #aaaaaa;padding:8px 15px;text-align:right; font-size: 22pt; line-height: 28pt;font-weight:bold; ">' . $method_text . '</td>';
    $body_shop2 .= '</tr>';
    $body_shop2 .= '</tfoot>';
    $body_shop2 .= '</table>';
    $body_shop2 .= '</div>';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $html_file = '<!DOCTYPE html> <html style="margin: 0;padding: 0;"> <head> <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> </head> <body style="padding: 0px 15px 0px 15px;"> <div style="margin-bottom: 10px;padding-bottom: 10px;border-bottom: 1px dashed #000;"> <h3 style="text-align: center;margin-top: 0;margin-bottom: 10px;">Kundeninformationen</h3>';
    if ($show_second_number == "1") {
        $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>Bestellnummer: </b>' . $second_order_number . '</p>';
    }
    $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>Name: </b>' . $customer_name1 . '</p> <p style="line-height: 1.3;margin: 0;"><b>Vorname: </b>' . $customer_name2 . '</p> <p style="line-height: 1.3;margin: 0;"><b>Email: </b>' . $customer_email . '</p> <p style="line-height: 1.3;margin: 0;"><b>Etage: </b>' . $customer_etage . '</p> <p style="line-height: 1.3;margin: 0;"><b>Postleitzahl: </b>' . $customer_zipcode . '</p> <p style="line-height: 1.3;margin: 0;"><b>Telefonnummer: </b>' . $customer_phone . '</p>';
    $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>Bewirtungsbeleg als Bon: </b>' . $bab_text . '</p>';
    $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>Alternative Rechnungsadresse: </b>' . $ar_text . '</p>';
    if ($ar['ar'] == 1) {
        $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>Anrede: </b>' . $r_prefix . '</p>';
        $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>Vorname: </b>' . $r_first_name . '</p>';
        $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>Nachname: </b>' . $r_last_name . '</p>';
        $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>Firma: </b>' . $r_company . '</p>';
        $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>PLZ: </b>' . $r_zipcode . '</p>';
        $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>Stadt: </b>' . $r_city . '</p>';
        $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>Straße: </b>' . $r_street . '</p>';
    }
    $html_file .= '</div>';
    $html_file .= '<div style="margin-bottom: 10px;padding-bottom: 10px;border-bottom: 1px dashed #000;">';
    $html_file .= '<h3 style="text-align: center;margin-top: 0;margin-bottom: 10px;">Versandart:</h3>';
    if ($shipping_method == "shipping") {
        $html_file .= '<h2 style="text-align: center;margin-top: 0;margin-bottom: 10px;">Lieferung</h2>';
        if ($distance != null) {
            $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>Entfernung:</b> ' . $distance . '</p>';
        }
    } else {
        $html_file .= '<h2 style="text-align: center;margin-top: 0;margin-bottom: 10px;">im Laden Abholen</h2>';
    }
    $html_file .= '</div>';
    $html_file .= '<div style="margin-bottom: 10px;padding-bottom: 10px;border-bottom: 1px dashed #000;">';
    $order_time_info = order_time_info($shipping_method, $user_location, $order_date, $user_delivery_date, $user_delivery_time, $user_date, $user_time);
    $html_file .= $order_time_info;
    if ($more_additional != "")
        $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>Bestellnotiz:</b></p> <p style="line-height: 1.3;margin: 0;text-align: center;"><b> ' . $more_additional . '</b></p>';
    $html_file .= '</div>';

    $html_file .= '<div style="margin-bottom: 10px;padding-bottom: 10px;border-bottom: 1px dashed #000;">';
    $html .= '</div>';

    $html_file .= '<div style="margin-bottom: 0;padding-bottom: 0;border-bottom: none;">';
    $html_file .= '<h3 style="text-align: center;margin-top: 0;margin-bottom: 10px;">Bestellinformationen</h3>';
    $html_file .= '</div>';
    $html_file .= $data_item;
    $html_file .= '<div style="margin-bottom: 10px;padding-bottom: 0px;">';
    $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;">Lieferungskosten: ';
    $html_file .= ($shipping_fee != '') ? ds_price_format_text_with_symbol($shipping_fee, $currency) : '';
    $html_file .= '</p>';
    //$html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;">Mehrwertsteuer: '.ds_price_format_text_with_symbol($taxes,$currency).'</p>';
    $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;">Zwischensumme: ' . ds_price_format_text_with_symbol($subtotal) . '</p>';
    if ($coupon != "") :
        foreach ($coupon as $couponkey => $couponvalue) {
            $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;">Rabattcode(' . $couponkey . '): - ' . ds_price_format_text_with_symbol($couponvalue, $currency) . '</p>';
        }
    endif;
    if ($reduce != "") {
        $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;">Rabatt (-' . $reduce_percent . '): - ' . ((strpos($reduce, ds_price_symbol($currency)) !== false) ? $reduce : ds_price_format_text_with_symbol($reduce, $currency)) . '</p>';
    }
    $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;">Gesamtsumme: <b>';
    $html_file .= (strpos($total, ds_price_symbol($currency)) !== false) ? $total : ds_price_format_text_with_symbol($total, $currency);
    $html_file .= '</b></p>';
    $html_file .= '<h2 style="line-height: 1.3;margin: 0;text-align: center;">' . ucfirst($method_text) . '</h2>';
    $html_file .= '</div>';
    $html_file .= '</body> </html>';
    $random_val = "order" . $order_id;
    //create example
    $height_add = 400;
    $dompdf = new DOMPDF();
    $dompdf->set_paper(array(0, 0, 200, $height_add));
    $dompdf->load_html($html_file);
    $dompdf->render();
    $page_count = $dompdf->get_canvas()->get_page_number();
    unset($dompdf);
    while ($page_count > 1) {
        $height_add += 20;
        $dompdf = new DOMPDF();
        $dompdf->set_paper(array(0, 0, 200, $height_add));
        $dompdf->load_html($html_file);
        $dompdf->render();
        $page_count = $dompdf->get_canvas()->get_page_number();
        unset($dompdf);
    }
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html_file);
    //$dompdf->setPaper('A4', 'portrait');
    $customPaper = array(0, 0, 200, $height_add);
    $dompdf->setPaper($customPaper);
    $dompdf->render();
    file_put_contents(BOOKING_ORDER_PATH2 . 'inc/pdfdata/' . $random_val . '.pdf', $dompdf->output());
    $attachments = array(BOOKING_ORDER_PATH2 . 'inc/pdfdata/' . $random_val . '.pdf');
    $shop_html .= '<p><a href="' . get_permalink($download_id) . '?id=' . base64_encode($random_val) . '" style="font-size:18pt; line-height: 24pt;padding:10px 30px;background-color:#f30;color:#fff;text-decoration:none;">BON DRUCKEN</a></p>';
    $shop_html .= '<p style="margin-top: 30px;"><a href="' . get_site_url() .'/wp-json/ordertcg/v1/mail/cancel/order' . '?token=' . get_option("access_token_mobile") . '&orderId='. $order_id . '" style="font-size:18pt; line-height: 24pt;padding:10px 30px;background-color:#f30;color:#fff;text-decoration:none;">STONIEREN</a></p>';
    $body2 .= '</body>
    </html>';


    if (get_option('enable_pool') == '1') {
        $order_time_info2 = order_time_info2($shipping_method, $user_location, $order_date, $user_delivery_date, $user_delivery_time, $user_date, $user_time);
        $pools = [];
		$debug = '';
		$debug2 = '';
        $z = [];
        if ($items != "") {
            $sub_total = 0;
            foreach ($items as $key => $item) {
                if (isset($item['product_id'])) :
                    $product_id = intval($item['product_id']);
                else :
                    $product_id = $key;
                endif;
                //$sub_total = $sub_total + $item['price'];
                $post_tags = get_the_terms( $product_id, 'pool' );
                $tmp_pools = wp_list_pluck( $post_tags, 'name' )[0];
                
                if(strcmp($tmp_pools, '') == 0){
                    $cat = get_the_terms( $product_id, 'product-cat' );
                    $cat_id = wp_list_pluck( $cat, 'term_id' )[0];
                    $pool_cat_id = get_term_meta( $cat_id, 'pool', true );
                    $pool_cat = get_term_by('term_id', intval($pool_cat_id), 'pool');
                    $tmp_pools = $pool_cat->name;
                    $z[] = $pool_cat->name . 'ok';
                }else{
                    $z[] = 'false';
                }
                $pools[] = $tmp_pools;
				//$debug = wp_list_pluck( $post_tags, 'name' )[0];
            }
        }
		
		//$debug2 = $pools[0];
        $pools = array_unique($pools);
		$debug = implode("|",$z);
        $total_pool = count($pools);
        $i = 1;
        foreach($pools as $pool){
        //.for ($i = 0; $i < $total_pool; $i++) {
            //$pool = $pools[$i];
            $data_pool = '';
           foreach ($items as $key => $item) {
                if (isset($item['product_id'])) :
                    $product_id = intval($item['product_id']);
                else :
                    $product_id = $key;
                endif;
                //$sub_total = $sub_total + $item['price'];
                $post_tags = get_the_terms( $product_id, 'pool' );
                $test = wp_list_pluck( $post_tags, 'name' )[0];
                
                if(strcmp($test, '') == 0){
                    $cat = get_the_terms( $product_id, 'product-cat' );
                    $cat_id = wp_list_pluck( $cat, 'term_id' )[0];
                    $pool_cat_id = get_term_meta( $cat_id, 'pool', true );
                    $pool_cat = get_term_by('term_id', intval($pool_cat_id), 'pool');
                    $test = $pool_cat->name;
                }
                if (strcmp($test, $pool) == 0) {
                    $meta['quantity'] = dsmart_field('quantity', $product_id);
                    $meta['varialbe_price'] = dsmart_field('varialbe_price', $product_id);
                    $meta['sidedish_name'] = dsmart_field('sidedish_name', $product_id);
                    $meta['sidedish_price'] = dsmart_field('sidedish_price', $product_id);
                    $meta['extra_name'] = dsmart_field('extra_name', $product_id);
                    $meta['extra_price'] = dsmart_field('extra_price', $product_id);
                    $data_pool .= '<div class="section" style="margin-bottom: 10px;padding-bottom: 10px;border-bottom: 1px dashed #000;">';
                    $data_pool .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>' . $item['quantity'] . 'x ' . $item['title'] . '</b> - ' . ds_price_format_text_with_symbol($item['price'], $currency) . '</p>';
                    $isSidedish = isset($item['sidedish_info']) && $item['sidedish_info'] != null && $meta['sidedish_name'] != null && !empty(array_filter($meta['sidedish_name']));
                    $isExtra = isset($item['extra_info']) && $item['extra_info'] != null && $meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']));
                    $isVariable = isset($item['variable_id']) && $meta['quantity'] != null && !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']));
                    if ($isExtra || $isVariable || $isSidedish) :
                        $variable_id = intval(explode('_', $item['variable_id'])[1]) - 1;
                        $variable_text = '<div class="variable-product cart-variable" style="padding: 5px; margin-top: 5px; background-color: #f8f5f2;">';
                        if($isVariable){
                            $variable_text .=   '<div class="item" style="margin-bottom: 10px; font-size: 18pt; line-height: 24pt;">';
                            $variable_text .=       '<h5 style="font-size: 18pt; line-height: 24pt; margin: 0;">' . __('AUSGEWÄHLTE PRODUKT', 'dsmart') . '</h5>';
                            $variable_text .=       '<p style="margin: 0;">' . $meta['quantity'][$variable_id] . ': ' . ds_price_format_text($meta['varialbe_price'][$variable_id]) . '</p>';
                            $variable_text .=   '</div>';
                            $data_pool .= '<p style="line-height: 1.3;margin: 0;text-align: center;">' . $meta['quantity'][$variable_id] . ': ' . ds_price_format_text($meta['varialbe_price'][$variable_id]) . '</p>';
                        }
                        if ($isExtra) :
                            $height += 60;
                            $data_pool .= '<h3 class="table-title" style="font-size:14pt; line-height: 20pt; margin: 10px 0;text-align: center;text-transform: capitalize;">Extra</h3>';
                            $extra_info = json_decode(stripslashes($item['extra_info']));
                            $extra_text = '<div class="item">';
                            $extra_text .= '<h3 class="table-title" style="font-size:14pt; line-height: 20pt; margin: 10px 0;text-align: center;text-transform: capitalize;">Extra</h3>';
                            $extra_text .= '<ul style="font-size: 18pt; line-height: 24pt; list-style: none; padding-left: 0; margin: 0;">';
                            foreach ($extra_info as $extra_key => $extra_value) {
                                $extra_id = intval(explode('_', $extra_value->extra_id)[1]) - 1;
                                $extra_quantity = $extra_value->extra_quantity;
                                $height += 30;
                                $data_pool .= '<p style="line-height: 1.3;margin: 0;text-align: center;">' . $meta['extra_name'][$extra_id] . ':' . ds_price_format_text($meta['extra_price'][$extra_id]) . ' x ' . $extra_quantity . '</p>';
                                $extra_text .= '<li style="margin: 0;">' . $meta['extra_name'][$extra_id] . '(+' . ds_price_format_text($meta['extra_price'][$extra_id]) . ') x ' . $extra_quantity . '</li>';
                            }
                            $extra_text .= '</ul>';
                        else :
                            $height += 40;
                            $extra_text = '';
                        endif;
                        if ($isSidedish) :
                            $height += 60;
                            $data_pool .= '<h3 class="table-title" style="font-size:14pt; line-height: 20pt; margin: 10px 0;text-align: center;text-transform: capitalize;">'.$sidedish_text .'</h3>';
                            $sidedish_info = json_decode(stripslashes($item['sidedish_info']));
                            $sidedish_text = '<div class="item">';
                            $sidedish_text .= '<h3 class="table-title" style="font-size:14pt; line-height: 20pt; margin: 10px 0;text-align: center;text-transform: capitalize;">'.$sidedish_text .'</h3>';
                            $sidedish_text .= '<ul style="font-size: 18pt; line-height: 24pt; list-style: none; padding-left: 0; margin: 0;">';
                            foreach ($sidedish_info as $sidedish_key => $sidedish_value) {
                                $sidedish_id = intval(explode('_', $sidedish_value->sidedish_id)[1]) - 1;
                                $extra_quantity = $extra_value->extra_quantity;
                                $height += 30;
                                $data_pool .= '<p style="line-height: 1.3;margin: 0;text-align: center;">' . $meta['sidedish_name'][$sidedish_id] . (isset($meta['sidedish_price'][$sidedish_id]) && $meta['sidedish_price'][$sidedish_id] !== "" ? " (+".ds_price_format_text($meta['sidedish_price'][$sidedish_id]).")" : "") . '</p>';
                                $sidedish_text .= '<li style="margin: 0;">' . $meta['sidedish_name'][$sidedish_id] . (isset($meta['sidedish_price'][$sidedish_id]) && $meta['sidedish_price'][$sidedish_id] !== "" ? " (+".ds_price_format_text($meta['sidedish_price'][$sidedish_id]).")" : "") . '</li>';
                            }
                            $sidedish_text .= '</ul>';
                        else :
                            $height += 40;
                            $sidedish_text = '';
                        endif;
                        $variable_text = $sidedish_text . ' ' . $variable_text . ' ' . $extra_text;
                    else :
                        $height += 45;
                        $variable_text = '';
                    endif;
                    $data_pool .= '</div>';
                }
            }
            $attachments[] = create_pool($pool, $random_val, $i++, $total_pool, $show_second_number, $second_order_number, $customer_name1, $customer_name2, $data_pool, $order_time_info2);
        }
    }



    wp_mail($shop_email, $title  . ' ' . $customer_name2 . ' ' . $customer_name1 . ' | ' . $master_time . ' | ' . $customer_phone, $body_header . $body_shop . $body . $body_shop2 . $body2 . $shop_html, $headers, $attachments);
	 //wp_mail('bestellung@kolin-shop.de', $title1.$total_pool, $body_header . $body_shop . $body . $body_shop2 . $body2 . $shop_html, $headers, $attachments);
    //wp_mail('nothingforchange1995@gmail.com',$title1,$body_header.$body_shop.$body.$body_shop2.$body2.$shop_html,$headers,$attachments);
    wp_mail($customer_email, $title1, $body_header . $body_cs . $body . $body2, $headers);
    
    // clean pdf files
    $dir = plugin_dir_path( __FILE__ ) . "/inc/pdfdata/*";
    $files = glob($dir); // get all file names
    foreach($files as $file){ // iterate files
        if(is_file($file)) {
            unlink($file); // delete file
        }
    }
}

function order_time_info($shipping_method, $user_location, $order_date, $user_delivery_date, $user_delivery_time, $user_date, $user_time){
    if ($shipping_method == "shipping") {
        $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>Lieferanschrift:</b></p><p style="line-height: 1.3;margin: 0;text-align: center;">' . $user_location . '</p>';
        if ($order_date == $user_delivery_date) {
            $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>Tag:</b></p><p style="line-height: 1.3;margin: 0;text-align: center;">' . $user_delivery_date . '</p>';
        } else {
            $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>Tag: <span style="color:#000;"></b></p><p style="line-height: 1.3;margin: 0;text-align: center;">ACHTUNG++' . $user_delivery_date . '++ACHTUNG</p>';
        }
        $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>Liefer- / Abholzeit:</b></p><p style="line-height: 1.3;margin: 0;text-align: center;font-size: 30px;">' . $user_delivery_time . '</p>';
    } else {
        $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>Tag:</b></p><p style="line-height: 1.3;margin: 0;text-align: center;">' . $user_date . '</p>';
        $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>Liefer- / Abholzeit: </b></p><p style="line-height: 1.3;margin: 0;text-align: center;font-size: 30px;">' . $user_time . '</p>';
    }
    return $html_file;
}

function order_time_info2($shipping_method, $user_location, $order_date, $user_delivery_date, $user_delivery_time, $user_date, $user_time){
    if ($shipping_method == "shipping") {
        $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>Liefer- / Abholzeit:</b></p><p style="line-height: 1.3;margin: 0;text-align: center;font-size: 30px;">' . $user_delivery_time . '</p>';
    } else {
        $html_file .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>Liefer- / Abholzeit: </b></p><p style="line-height: 1.3;margin: 0;text-align: center;font-size: 30px;">' . $user_time . '</p>';
    }
    return $html_file;
}

function create_pool($pool, $order_id, $index, $total, $show_second_number, $second_order_number, $customer_name1, $customer_name2, $data_pool, $order_time_info)
{
    $html_file = '<!DOCTYPE html> <html style="margin: 0;padding: 0;"> <head> <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> </head> <body style="padding: 0px 15px 0px 15px;"> <div style="margin-bottom: 10px;padding-bottom: 10px;border-bottom: 1px dashed #000;"> <h3 style="text-align: center;margin-top: 0;margin-bottom: 10px;">'. $pool .' ('. $index .'/'. $total .')</h3>';
    if ($show_second_number == "1") {
        $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>Bestellnummer: </b>' . $second_order_number . '</p>';
    }
    $html_file .= '<p style="line-height: 1.3;margin: 0;"><b>Name: </b>' . $customer_name1 . '</p> <p style="line-height: 1.3;margin: 0;"><b>Vorname: </b>' . $customer_name2 . '</p></div>';
    $html_file .= $order_time_info;
    $html_file .= '<div style="margin-bottom: 0;margin-top: 10px;padding-bottom: 0;border-bottom: none;">';
    $html_file .= '<h3 style="text-align: center;margin-top: 0;margin-bottom: 10px;">Bestellinformationen</h3>';
    $html_file .= '</div>';
    $html_file .= $data_pool;
    $html_file .= '</body> </html>';


    //create pdf file
    $height_add = 250;
    $dompdf = new DOMPDF();
    $dompdf->set_paper(array(0, 0, 200, $height_add));
    $dompdf->load_html($html_file);
    $dompdf->render();
    $page_count = $dompdf->get_canvas()->get_page_number();
    unset($dompdf);
    while ($page_count > 1) {
        $height_add += 20;
        $dompdf = new DOMPDF();
        $dompdf->set_paper(array(0, 0, 200, $height_add));
        $dompdf->load_html($html_file);
        $dompdf->render();
        $page_count = $dompdf->get_canvas()->get_page_number();
        unset($dompdf);
    }
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html_file);
    //$dompdf->setPaper('A4', 'portrait');
    $customPaper = array(0, 0, 200, $height_add);
    $dompdf->setPaper($customPaper);
    $dompdf->render();
    file_put_contents(BOOKING_ORDER_PATH2 . 'inc/pdfdata/' . $pool . '_' . $order_id . '.pdf', $dompdf->output());
    return BOOKING_ORDER_PATH2 . 'inc/pdfdata/' . $pool . '_' . $order_id . '.pdf';
}
//add new role
function ds_add_roles_on_plugin_activation()
{
    global $wp_roles;
    if (class_exists('WP_Roles')) {
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
    }

    if (is_object($wp_roles)) {
        add_role('shop', __('Shop Besitzer'), array(
            'read'                         => true,
            'edit_posts'                 => true,
            'delete_posts'                 => true,
            'read_product'                => true,
            'edit_product'                => true,
            'delete_product'            => true,
            'edit_products'                => true,
            'edit_others_products'        => true,
            'publish_products'            => true,
            'delete_products'            => true,
            'delete_published_products'    => true,
            'delete_others_products'    => true,
            'edit_published_products'    => true,
            'manage_product-cats'        => true,
            'edit_product-cats'            => true,
            'delete_product-cats'        => true,
            'assign_product-cats'        => true,
            'read_coupon'              => true,
            'edit_coupon'              => true,
            'delete_coupon'            => true,
            'edit_coupons'             => true,
            'edit_others_coupons'      => true,
            'publish_coupons'          => true,
            'delete_coupons'           => true,
            'delete_published_coupons' => true,
            'delete_others_coupons'    => true,
            'edit_published_coupons'   => true,
            'manage_pools'   => true,
            'edit_pools'   => true,
            'delete_pools'   => true,
            'assign_pools'   => true,
            'upload_files' => true,
            'manage_support-functions' => true
        ));
    }
}
register_activation_hook(__FILE__, 'ds_add_roles_on_plugin_activation');
/*add_action('admin_init', 'rpt_add_role_caps',999);*/
register_activation_hook(__FILE__, 'rpt_add_role_caps');
function rpt_add_role_caps()
{
    $role = get_role('administrator');
    $role->add_cap('read_product');
    $role->add_cap('edit_product');
    $role->add_cap('edit_products');
    $role->add_cap('delete_product');
    $role->add_cap('edit_others_products');
    $role->add_cap('publish_products');
    $role->add_cap('delete_products');
    $role->add_cap('delete_published_products');
    $role->add_cap('delete_others_products');
    $role->add_cap('edit_published_products');
    $role->add_cap('manage_product-cats');
    $role->add_cap('edit_product-cats');
    $role->add_cap('delete_product-cats');
    $role->add_cap('assign_product-cats');
    $role->add_cap('manage_pools');
    $role->add_cap('edit_pools');
    $role->add_cap('delete_pools');
    $role->add_cap('assign_pools');
    $role->add_cap('manage_support-functions');

    $role = get_role('shop');
    $role->add_cap('read_product');
    $role->add_cap('edit_product');
    $role->add_cap('edit_products');
    $role->add_cap('delete_product');
    $role->add_cap('edit_others_products');
    $role->add_cap('publish_products');
    $role->add_cap('delete_products');
    $role->add_cap('delete_published_products');
    $role->add_cap('delete_others_products');
    $role->add_cap('edit_published_products');
    $role->add_cap('manage_product-cats');
    $role->add_cap('edit_product-cats');
    $role->add_cap('delete_product-cats');
    $role->add_cap('assign_product-cats');
    $role->add_cap('manage_pools');
    $role->add_cap('edit_pools');
    $role->add_cap('delete_pools');
    $role->add_cap('assign_pools');
    $role->add_cap('upload_files');
    $role->add_cap('manage_support-functions');
}
//register template part function
function dsmart_locate_template($template_name, $template_path = '', $default_path = '')
{
    if (!$template_path) :
        $template_path = 'order-booking/';
    endif;
    if (!$default_path) :
        $default_path = plugin_dir_path(__FILE__) . 'templates-part/';
    endif;
    $template = locate_template(array(
        $template_path . $template_name,
        $template_name
    ));
    if (!$template) :
        $template = $default_path . $template_name;
    endif;
    return apply_filters('dsmart_locate_template', $template, $template_name, $template_path, $default_path);
}
function dsmart_get_template($template_name, $args = array(), $tempate_path = '', $default_path = '')
{
    if (is_array($args) && isset($args)) :
        extract($args);
    endif;
    $template_file = dsmart_locate_template($template_name, $tempate_path, $default_path);
    if (!file_exists($template_file)) :
        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.0.0');
        return;
    endif;
    include $template_file;
}
function wp_send_mail_order($order_id, $from_status = null, $to_status = null)
{
    $shop_id = dsmart_field('shop_id', $order_id);
    $shop_data = get_shop_data_by_id($shop_id);
    $shop_email = $shop_data->email;
    $dsmart_header_mail_success = get_option('dsmart_header_mail_success');
    $dsmart_header_mail_cancel = get_option('dsmart_header_mail_cancel');
    $dsmart_header_mail_success_cs = get_option('dsmart_header_mail_success_cs');
    $dsmart_header_mail_cancel_cs = get_option('dsmart_header_mail_cancel_cs');

    $dsmart_text_mail_success = get_option('dsmart_text_mail_success');
    $dsmart_text_mail_cancel = get_option('dsmart_text_mail_cancel');
    $dsmart_text_mail_success_cs = get_option('dsmart_text_mail_success_cs');
    $dsmart_text_mail_cancel_cs = get_option('dsmart_text_mail_cancel_cs');
    if ($to_status == "cancelled") {
        if ($dsmart_header_mail_cancel == "") {
            $title = "Order " . get_the_title($order_id) . " canceled";
        } else {
            $title = $dsmart_header_mail_cancel . ' ' . get_the_title($order_id);
        }
        if ($dsmart_header_mail_cancel_cs == "") {
            $title1 = "Order " . get_the_title($order_id) . " canceled";
        } else {
            $title1 = $dsmart_header_mail_cancel_cs . ' ' . get_the_title($order_id);
        }
    } elseif ($to_status == "completed") {
        if ($dsmart_header_mail_success == "") {
            $title = "Order " . get_the_title($order_id) . " completed";
        } else {
            $title = $dsmart_header_mail_success . ' ' . get_the_title($order_id);
        }
        if ($dsmart_header_mail_success_cs == "") {
            $title1 = "Order " . get_the_title($order_id) . " completed";
        } else {
            $title1 = $dsmart_header_mail_success_cs . ' ' . get_the_title($order_id);
        }
    }
    $body = "";
    if ($from_status == "" || $from_status == "processing") {
        $from_text = "Wird bearbeitet";
    } elseif ($from_status == "completed") {
        $from_text = "Abgeschlossen";
    }
    if ($to_status == "" || $to_status == "completed") {
        $to_text = "Abgeschlossen";
    } elseif ($to_status == "processing") {
        $to_text = "Wird bearbeitet";
    } elseif ($to_status == "cancelled") {
        $to_text = "Abgesagt";
    }
    /*$body .= 'Order '.get_the_title($order_id).' status change from "'.$from_text.'" to "'.$to_text.'"';*/

    


    if ($to_status == "completed") {
        if ($dsmart_text_mail_success != "") {
            $body .= $dsmart_text_mail_success;
        } else {
            $body .= 'Bestellnummer ' . get_the_title($order_id) . ' Statusänderung von "' . $from_text . '" nach "' . $to_text . '"';
        }
        if ($dsmart_text_mail_success_cs != "") {
            $body1 .= $dsmart_text_mail_success_cs;
        } else {
            $body1 .= 'Bestellnummer ' . get_the_title($order_id) . ' Statusänderung von "' . $from_text . '" nach "' . $to_text . '"';
        }
    } else {
        if ($dsmart_text_mail_cancel != "") {
            $body .= $dsmart_text_mail_cancel;
        } else {
            $body .= 'Bestellnummer ' . get_the_title($order_id) . ' Statusänderung von "' . $from_text . '" nach "' . $to_text . '"';
        }
        if ($dsmart_text_mail_cancel_cs != "") {
            $body1 .= $dsmart_text_mail_cancel_cs;
        } else {
            $body1 .= 'Bestellnummer ' . get_the_title($order_id) . ' Statusänderung von "' . $from_text . '" nach "' . $to_text . '"';
        }
    }

    
    $customer_name1 = dsmart_field('customer_name1', $order_id);
    $customer_name2 = dsmart_field('customer_name2', $order_id);
    $customer_phone = dsmart_field('customer_phone', $order_id);
    $admin_email = get_option('admin_email');
    $customer_email = dsmart_field('customer_email', $order_id);

    $body .= ". Kundenname: " . $customer_name1 . " " . $customer_name2 . ". Telefon: " . $customer_phone . ". Email: " . $customer_email . ".";
    

    wp_mail($shop_email, $title, $body);
    wp_mail($customer_email, $title1, $body1);
}
if (!function_exists('wpse_11826_search_by_title')) {
    function wpse_11826_search_by_title($search, $wp_query)
    {
        if (!empty($search) && !empty($wp_query->query_vars['search_terms'])) {
            global $wpdb;

            $q = $wp_query->query_vars;
            $n = !empty($q['exact']) ? '' : '%';

            $search = array();

            foreach ((array) $q['search_terms'] as $term)
                $search[] = $wpdb->prepare("$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like($term) . $n);

            if (!is_user_logged_in())
                $search[] = "$wpdb->posts.post_password = ''";

            $search = ' AND ' . implode(' AND ', $search);
        }

        return $search;
    }
}
add_filter('posts_search', 'wpse_11826_search_by_title', 10, 2);

//add float cart to footer
function dsmart_float_cart()
{
    date_default_timezone_set('Europe/Berlin');
    $float_cart = get_option('float_cart');
    if (isset($_COOKIE['cart']) && $_COOKIE['cart'] != "") {
        $cart = unserialize(base64_decode($_COOKIE['cart']));
    } else {
        $cart = array();
    }
    $close_shop_text = get_option('close_shop_text');
    $check1 = check_time_with_time_shop(date("H:i"), null, null, "shipping");
    $check2 = check_time_with_time_shop(date("H:i"), null, null, "direct");
    $dsmart_method_ship = get_option('dsmart_method_ship');
    $dsmart_method_direct = get_option('dsmart_method_direct');
    if ($close_shop_text != "") {
        $check_time_open = true;
        $time1 = get_close_time_shop_nodelay();
        $time2 = get_close_time_shop2_nodelay();
        $now1 = new DateTime(get_current_time());
        $now2 = new DateTime(get_current_time2());
        if (count($time1) > 0) {
            /*foreach ($time1 as $value) {
	    		$begin = new DateTime($value[0]);
				$end = new DateTime($value[1]);
				if($now1 > $begin && $now1 < $end){
					$check_time_open = false;
				}
	    	}*/
            foreach ($time2 as $value) {
                $begin = new DateTime($value[0]);
                $end = new DateTime($value[1]);
                if ($now2 > $begin && $now2 < $end) {
                    $check_time_open = false;
                }
            }
        }

        $closing = !(($check1 && $dsmart_method_ship) || ($check2 && $dsmart_method_direct));

        if ($closing && is_page_template('templates/taxonomy-product-cat.php')) { ?>
            <div class="dsmart-show-notify">
                <div class="dsmart-show-notify-wrap">
                    <?php echo $close_shop_text; ?>
                </div>
            </div>
        <?php }
    }
    if ($float_cart == "on" && !is_page_template('templates/cart-page.php') && !is_page_template('templates/checkout-page.php') && !is_page_template('templates/shop-page.php') && !is_singular('orders')) {
        $cart_id = get_page_id_by_template('templates/cart-page.php');
        $total_quantity = ds_get_cart_total_quantity();
        $total_price = ds_get_cart_total_item(); 
        $button_color = get_option('button_color', '#50aecc');?>
        <div class="dsmart-float-cart<?php if (count($cart) > 0) {
                                            echo ' active';
                                        } ?>">
            <div class="cart-info">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                <span><?php echo $total_quantity; ?></span>
            </div>
            <div class="total-price"><?php echo ds_price_format_text_no_convert($total_price); ?></div>
            <a href="<?php echo get_permalink($cart_id); ?>" style="background-color: <?php echo $button_color?> !important;" class="dsmart-button"><?php _e("Warenkorb", 'dsmart'); ?></a>
        </div>
<?php }
}
add_action('wp_footer', 'dsmart_float_cart');

function custom_date($date = null)
{
    date_default_timezone_set('Europe/Berlin');
    if ($date == null) {
        $date = date('w');
    }
    if ($date == 0) {
        return 'su';
    } elseif ($date == 1) {
        return "mo";
    } elseif ($date == 2) {
        return "tu";
    } elseif ($date == 3) {
        return "we";
    } elseif ($date == 4) {
        return "th";
    } elseif ($date == 5) {
        return "fr";
    } elseif ($date == 6) {
        return "sa";
    }
}

function get_current_time()
{
    date_default_timezone_set('Europe/Berlin');
    $delay_time = get_option('delay_time');
    /*if($delay_time != "" && $delay_time != 0){
        $current_time = date('H:i',strtotime("+".$delay_time." minutes"));
    }else{*/
    $current_time = date('H:i');
    /*}*/
    return $current_time;
}
function get_current_time2()
{
    date_default_timezone_set('Europe/Berlin');
    $delay_time = get_option('delay_delivery');
    $current_time = date('H:i');
    return $current_time;
}

function get_close_time_shop_nodelay()
{
    date_default_timezone_set('Europe/Berlin');
    $delay_time = get_option('delay_time');
    $current_time = date('H:i');
    $time_data = date('H:i', strtotime($current_time));
    $time = new DateTime($current_time);
    $close_shop = get_option('dsmart_close_shop');
    $current_date_text = custom_date();
    $current_date = date('d-m-Y');
    $time_open_shop = get_option('time_open_shop_' . $current_date_text);
    $time_close_shop = get_option('time_close_shop_' . $current_date_text);
    $dsmart_custom_date = get_option('dsmart_custom_date');
    // $closed_time = get_option('closed_time_2');
    $closed_time = get_option('closed_time');
    $time_shop_array = array();
    $custom_date_data = "00:00";
    $custom_date = false;
    $check = false;
    if ($dsmart_custom_date != "" && count($dsmart_custom_date) > 0) {
        // Sort by 'open' time to ensure chronological gap calculation
        usort($dsmart_custom_date, function($a, $b) {
            return strcmp($a['open'], $b['open']);
        });
        foreach ($dsmart_custom_date as $item) {
            if ($current_date == $item['date']) {
                $time_shop_array[] = array($custom_date_data, $item['open']);
                $custom_date_data = $item['close'];
                $check = true;
            }
        }
        if ($check == true) {
            $time_shop_array[] = array($item['close'], "23:59");
        }
    }
    $closed_time_array = array();
    if ($closed_time != "" && count($closed_time) > 0) {
        foreach ($closed_time as $item) {
            if ($item['date'] == $current_date_text) {
                $closed_time_array[] = array($item['from'], date("H:i", strtotime('+1 minutes', strtotime($item['to']))));
            }
        }
    }
    if (count($time_shop_array) > 0) {
        $time_shop_array[] = array('00:00', $current_time);
        return $time_shop_array;
    } elseif (count($closed_time_array) > 0) {
        if ($time_open_shop == "" && $time_close_shop == "") {
        } elseif ($time_open_shop == "" && $time_close_shop != "") {
        } elseif ($time_open_shop != "" && $time_close_shop == "") {
            $closed_time_array[] = array("00:00", date("H:i", strtotime('+1 minutes', strtotime($time_open_shop))));
        } else {
            $closed_time_array[] = array($time_close_shop, "23:59");
            $closed_time_array[] = array("00:00", date("H:i", strtotime('+1 minutes', strtotime($time_open_shop))));
        }
        $closed_time_array[] = array('00:00', $current_time);
        return $closed_time_array;
    } else {
        $array_text = array();
        $array_text[] = array('00:00', $current_time);
        if ($time_open_shop == "" && $time_close_shop == "") {
        } elseif ($time_open_shop == "" && $time_close_shop != "") {
            $array_text[] = array($time_close_shop, "23:59");
        } elseif ($time_open_shop != "" && $time_close_shop == "") {
            $array_text[] = array("00:00", date("H:i", strtotime('+1 minutes', strtotime($time_open_shop))));
        } else {
            $array_text[] = array("00:00", $time_open_shop);
            $array_text[] = array($time_close_shop, "23:59");
        }
        return $array_text;
    }
}
function get_close_time_shop2_nodelay()
{
    date_default_timezone_set('Europe/Berlin');
    $delay_time = get_option('delay_delivery');
    $current_time = date('H:i');
    $time_data = date('H:i', strtotime($current_time));
    $time = new DateTime($current_time);
    $close_shop = get_option('dsmart_close_shop');
    $current_date_text = custom_date();
    $current_date = date('d-m-Y');
    $time_open_shop = get_option('time_open_shop_2_' . $current_date_text);
    $time_close_shop = get_option('time_close_shop_2_' . $current_date_text);
    $dsmart_custom_date = get_option('dsmart_custom_date');
    // $closed_time = get_option('closed_time');
    $closed_time = get_option('closed_time_2');
    $time_shop_array = array();
    $custom_date_data = "00:00";
    $custom_date = false;
    $check = false;
    if ($dsmart_custom_date != "" && count($dsmart_custom_date) > 0) {
        // Sort by 'open' time to ensure chronological gap calculation
        usort($dsmart_custom_date, function($a, $b) {
            return strcmp($a['open'], $b['open']);
        });
        foreach ($dsmart_custom_date as $item) {
            if ($current_date == $item['date']) {
                $time_shop_array[] = array($custom_date_data, $item['open']);
                $custom_date_data = $item['close'];
                $check = true;
            }
        }
        if ($check == true) {
            $time_shop_array[] = array($item['close'], "23:59");
        }
    }
    $closed_time_array = array();
    if ($closed_time != "" && count($closed_time) > 0) {
        foreach ($closed_time as $item) {
            if ($item['date'] == $current_date_text) {
                $closed_time_array[] = array($item['from'], date("H:i", strtotime('+1 minutes', strtotime($item['to']))));
            }
        }
    }
    if (count($time_shop_array) > 0) {
        $time_shop_array[] = array('00:00', $current_time);
        return $time_shop_array;
    } elseif (count($closed_time_array) > 0) {
        if ($time_open_shop == "" && $time_close_shop == "") {
        } elseif ($time_open_shop == "" && $time_close_shop != "") {
        } elseif ($time_open_shop != "" && $time_close_shop == "") {
            $closed_time_array[] = array("00:00", date("H:i", strtotime('+1 minutes', strtotime($time_open_shop))));
        } else {
            $closed_time_array[] = array($time_close_shop, "23:59");
            $closed_time_array[] = array("00:00", date("H:i", strtotime('+1 minutes', strtotime($time_open_shop))));
        }
        $closed_time_array[] = array('00:00', $current_time);
        return $closed_time_array;
    } else {
        $array_text = array();
        $array_text[] = array('00:00', $current_time);
        if ($time_open_shop == "" && $time_close_shop == "") {
        } elseif ($time_open_shop == "" && $time_close_shop != "") {
            $array_text[] = array($time_close_shop, "23:59");
        } elseif ($time_open_shop != "" && $time_close_shop == "") {
            $array_text[] = array("00:00", date("H:i", strtotime('+1 minutes', strtotime($time_open_shop))));
        } else {
            $array_text[] = array("00:00", $time_open_shop);
            $array_text[] = array($time_close_shop, "23:59");
        }
        return $array_text;
    }
}

function get_close_time_category($t_id, $specific_date = null)
{
    $tax_enable = get_term_meta( $t_id, 'tax_enable', true );
    if($tax_enable === "0") return array("00:00", "23:59");

    date_default_timezone_set('Europe/Berlin');
    if($specific_date == null)
    {
        $current_date = date('d-m-Y');
    }
    else
    {
        $current_date = $specific_date;
    }
    
    $tax_custom_date = get_term_meta( $t_id, 'tax_new_custom_date', true );
    if($tax_custom_date == "" || !$tax_custom_date)
    {
		$tax_custom_date = [];
	}
    $time_category_custom_array = array();
    $isCustom = false;

    if ($tax_custom_date != "" && count($tax_custom_date) > 0) 
    {
        // Convert current_date to Y-m-d format for proper comparison
        $current_date_ymd = date("Y-m-d", strtotime($current_date));
        
        foreach ($tax_custom_date as $item) 
        {
            $start_date = date("Y-m-d", strtotime($item["start_date"]));
            $end_date = date("Y-m-d", strtotime($item["end_date"]));
            
            $correct_single = $item["date_type"] === "single" && $start_date === $current_date_ymd;
            $correct_mutiple = $item["date_type"] !== "single" && $current_date_ymd >= $start_date && $current_date_ymd <= $end_date;
            if($correct_single || $correct_mutiple)
            {   
                $isCustom = true; 
                if($item["status"] === "close")
                {
                    if($item["time_type"] === "time_to_time")
                    {
                        $time_category_custom_array[] = array($item["start_time"], $item["end_time"]);
                    }
                    else
                    {
                        $time_category_custom_array[] = array("00:00", "23:59");
                    }
                }
                else
                {
                    if($item["time_type"] === "time_to_time")
                    {
                        $time_category_custom_array[] = array("00:00", $item["start_time"]);
                        $time_category_custom_array[] = array($item["end_time"], "23:59");
                    }
                    else
                    {
                        $time_category_custom_array[] = [];
                    }
                }
            }
        } 
    }

    if(!$isCustom)
    {
        $time_category_array = [];
        if($specific_date != null)
        {
            $week_day = custom_date(date(format: 'w', timestamp: strtotime($specific_date)));
        }
        else
        {
            $week_day = custom_date();
        }
        $tax_time = get_term_meta($t_id, 'tax_time', true);
        if ($tax_time != "" && count($tax_time) > 0) 
        {
            foreach ($tax_time as $value) 
            {
                $time_date = $value['date'];
                $time_open = $value['open'];
                $time_close = $value['close'];
                if ($time_date == $week_day)
                {
                    if($time_open != "")
                        $time_category_array[] = array("00:00", $time_open);
                    if($time_close != "")
                        $time_category_array[] = array($time_close, "23:59");
                }
            }
        }
        return $time_category_array;
    }
    else 
    {
        return $time_category_custom_array;
    }
}

function get_close_time_shop()
{
    date_default_timezone_set('Europe/Berlin');
    $delay_time = get_option('delay_time');
    if ($delay_time != "" && $delay_time != 0) {
        $current_time = date('H:i', strtotime("+" . $delay_time . " minutes"));
    } else {
        $current_time = date('H:i');
    }
    $time_data = date('H:i', strtotime($current_time));
    $time = new DateTime($current_time);
    $close_shop = get_option('dsmart_close_shop');
    $current_date_text = custom_date();
    $current_date = date('d-m-Y');
    $time_open_shop = get_option('time_open_shop_' . $current_date_text);
    $time_close_shop = get_option('time_close_shop_' . $current_date_text);
    $dsmart_custom_date = get_option('dsmart_custom_date');
    $closed_time = get_option('closed_time_2');
    // $closed_time = get_option('closed_time');
    $time_shop_array = array();
    $custom_date_data = "00:00";
    $custom_date = false;
    $check = false;
    if ($dsmart_custom_date != "" && count($dsmart_custom_date) > 0) {
        // Sort by 'open' time to ensure chronological gap calculation
        usort($dsmart_custom_date, function($a, $b) {
            return strcmp($a['open'], $b['open']);
        });
        foreach ($dsmart_custom_date as $item) {
            if ($current_date == $item['date']) {
                $time_shop_array[] = array($custom_date_data, $item['open']);
                $custom_date_data = $item['close'];
                $check = true;
            }
        }
        if ($check == true) {
            $time_shop_array[] = array($item['close'], "23:59");
        }
    }
    $closed_time_array = array();
    if ($closed_time != "" && count($closed_time) > 0) {
        foreach ($closed_time as $item) {
            if ($item['date'] == $current_date_text) {
                $closed_time_array[] = array($item['from'], date("H:i", strtotime('+1 minutes', strtotime($item['to']))));
            }
        }
    }

    $final_array = [];
    if (count($time_shop_array) > 0) {
        $time_shop_array[] = array('00:00', $current_time);
        $final_array = $time_shop_array;
    } elseif (count($closed_time_array) > 0) {
        if ($time_open_shop == "" && $time_close_shop == "") {
        } elseif ($time_open_shop == "" && $time_close_shop != "") {
        } elseif ($time_open_shop != "" && $time_close_shop == "") {
            $closed_time_array[] = array("00:00", date("H:i", strtotime('+1 minutes', strtotime($time_open_shop))));
        } else {
            $closed_time_array[] = array($time_close_shop, "23:59");
            $closed_time_array[] = array("00:00", date("H:i", strtotime('+1 minutes', strtotime($time_open_shop))));
        }
        $closed_time_array[] = array('00:00', $current_time);
        $final_array = $closed_time_array;
    } else {
        $array_text = array();
        $array_text[] = array('00:00', $current_time);
        if ($time_open_shop == "" && $time_close_shop == "") {
        } elseif ($time_open_shop == "" && $time_close_shop != "") {
            $array_text[] = array($time_close_shop, "23:59");
        } elseif ($time_open_shop != "" && $time_close_shop == "") {
            $array_text[] = array("00:00", date("H:i", strtotime('+1 minutes', strtotime($time_open_shop))));
        } else {
            $array_text[] = array("00:00", $time_open_shop);
            $array_text[] = array($time_close_shop, "23:59");
        }
        $final_array = $array_text;
    }

    return merge_close_time_with_category_time($final_array);
}
function get_close_time_shop2()
{
    date_default_timezone_set('Europe/Berlin');
    $delay_time = get_option('delay_delivery');
    if ($delay_time != "" && $delay_time != 0) {
        $current_time = date('H:i', strtotime("+" . $delay_time . " minutes"));
    } else {
        $current_time = date('H:i');
    }
    $time_data = date('H:i', strtotime($current_time));
    $time = new DateTime($current_time);
    $close_shop = get_option('dsmart_close_shop');
    $current_date_text = custom_date();
    $current_date = date('d-m-Y');
    $time_open_shop = get_option('time_open_shop_2_' . $current_date_text);
    $time_close_shop = get_option('time_close_shop_2_' . $current_date_text);
    $dsmart_custom_date = get_option('dsmart_custom_date');
    $closed_time = get_option('closed_time');
    // $closed_time = get_option('closed_time_2');
    $time_shop_array = array();
    $custom_date_data = "00:00";
    $custom_date = false;
    $check = false;
    if ($dsmart_custom_date != "" && count($dsmart_custom_date) > 0) {
        // Sort by 'open' time to ensure chronological gap calculation
        usort($dsmart_custom_date, function($a, $b) {
            return strcmp($a['open'], $b['open']);
        });
        foreach ($dsmart_custom_date as $item) {
            if ($current_date == $item['date']) {
                $time_shop_array[] = array($custom_date_data, $item['open']);
                $custom_date_data = $item['close'];
                $check = true;
            }
        }
        if ($check == true) {
            $time_shop_array[] = array($item['close'], "23:59");
        }
    }
    $closed_time_array = array();
    if ($closed_time != "" && count($closed_time) > 0) {
        foreach ($closed_time as $item) {
            if ($item['date'] == $current_date_text) {
                $closed_time_array[] = array($item['from'], date("H:i", strtotime('+1 minutes', strtotime($item['to']))));
            }
        }
    }
    $final_array = [];
    if (count($time_shop_array) > 0) {
        $time_shop_array[] = array('00:00', $current_time);
        $final_array = $time_shop_array;
    } elseif (count($closed_time_array) > 0) {
        if ($time_open_shop == "" && $time_close_shop == "") {
        } elseif ($time_open_shop == "" && $time_close_shop != "") {
        } elseif ($time_open_shop != "" && $time_close_shop == "") {
            $closed_time_array[] = array("00:00", date("H:i", strtotime('+1 minutes', strtotime($time_open_shop))));
        } else {
            $closed_time_array[] = array($time_close_shop, "23:59");
            $closed_time_array[] = array("00:00", date("H:i", strtotime('+1 minutes', strtotime($time_open_shop))));
        }
        $closed_time_array[] = array('00:00', $current_time);
        $final_array = $closed_time_array;
    } else {
        $array_text = array();
        $array_text[] = array('00:00', $current_time);
        if ($time_open_shop == "" && $time_close_shop == "") {
        } elseif ($time_open_shop == "" && $time_close_shop != "") {
            $array_text[] = array($time_close_shop, "23:59");
        } elseif ($time_open_shop != "" && $time_close_shop == "") {
            $array_text[] = array("00:00", date("H:i", strtotime('+1 minutes', strtotime($time_open_shop))));
        } else {
            $array_text[] = array("00:00", $time_open_shop);
            $array_text[] = array($time_close_shop, "23:59");
        }
        $final_array = $array_text;
    }
    return merge_close_time_with_category_time($final_array);
}

function merge_close_time_with_category_time($final_array, $date=null)
{
    // check categories times
    if (isset($_COOKIE['cart']) && $_COOKIE['cart'] != "") {
        $cart = unserialize(base64_decode($_COOKIE['cart']));
    } else {
        $cart = array();
    }
    $cart = make_cart_items_available($cart);

    if ($cart) {
        foreach ($cart as $key_item => $value_item) {
            $product_id = intval($value_item['product_id']);
            $terms = get_the_terms( $product_id, 'product-cat' );
            foreach ($terms as $term) {
                $product_cat_id = $term->term_id;
                $category_times = get_close_time_category($product_cat_id, $date);
                foreach ($category_times as $category_time){
                    $final_array[] = $category_time;
                }
            }
        }
    }
    return $final_array;
}
function get_close_time_shop_all_week()
{
    date_default_timezone_set('Europe/Berlin');
    $close_shop = get_option('dsmart_close_shop');
    $dsmart_custom_date = get_option('dsmart_custom_date');
    // $closed_time = get_option('closed_time');
    $closed_time = get_option('closed_time_2');
    $delay_time = get_option('delay_delivery');
    $date_from = Date('d-m-Y', strtotime(date('Y-m-d')));
    $date_to = Date('d-m-Y', strtotime("+7 day", strtotime(date('Y-m-d'))));
    $period = new DatePeriod(
        new DateTime($date_from),
        new DateInterval('P1D'),
        new DateTime($date_to)
    );
    $data_array = array();
    $current_time = date('H:i', strtotime("+" . $delay_time . " minutes", strtotime(date('Y-m-d 00:00:00'))));
    foreach ($period as $key => $value) {
        $current_date = $value->format('d-m-Y');
        $current_date_text = substr(strtolower($value->format('D')), 0, 2);
        $time_now = ($key == 0) ? date('H:i', strtotime("+" . $delay_time . " minutes")) : date('H:i', strtotime("+" . $delay_time . " minutes", strtotime(date('Y-m-d 00:00:00'))));
        if ($delay_time != "" && $delay_time != 0) {
            $current_time = date('H:i', strtotime("+" . $delay_time . " minutes"));
        } else {
            $current_time = date('H:i');
        }
        $time = new DateTime($current_time);
        $time_open_shop = get_option('time_open_shop_' . $current_date_text);
        $time_close_shop = get_option('time_close_shop_' . $current_date_text);
        $time_shop_array = array();
        $custom_date_data = "00:00";
        $custom_date = false;
        $check = false;
        if ($dsmart_custom_date != "" && count($dsmart_custom_date) > 0) {
            // Sort by 'open' time to ensure chronological gap calculation
            usort($dsmart_custom_date, function($a, $b) {
                return strcmp($a['open'], $b['open']);
            });
            foreach ($dsmart_custom_date as $item) {
                if ($current_date == $item['date']) {
                    $time_shop_array[] = array($custom_date_data, $item['open']);
                    $custom_date_data = $item['close'];
                    $check = true;
                }
            }
            if ($check == true) {
                $time_shop_array[] = array($item['close'], "23:59");
            }
        }
        $closed_time_array = array();
        if ($closed_time != "" && count($closed_time) > 0) {
            foreach ($closed_time as $item) {
                if ($item['date'] == $current_date_text) {
                    $closed_time_array[] = array($item['from'], $item['to']);
                }
            }
        }
        if (count($time_shop_array) > 0) {
            //$time_shop_array[] = array('00:00',$current_time);
            $data_array[$current_date] = $time_shop_array;
        } elseif (count($closed_time_array) > 0) {
            if ($time_open_shop == "" && $time_close_shop == "") {
            } elseif ($time_open_shop == "" && $time_close_shop != "") {
            } elseif ($time_open_shop != "" && $time_close_shop == "") {
                $closed_time_array[] = array("00:00", $time_open_shop);
            } else {
                $closed_time_array[] = array($time_close_shop, "23:59");
                $closed_time_array[] = array("00:00", $time_open_shop);
            }
            //$closed_time_array[] = array('00:00',$current_time);
            $data_array[$current_date] = $closed_time_array;
        } else {
            $array_text = array();
            //$array_text[] = array('00:00',$current_time);
            if ($time_open_shop == "" && $time_close_shop == "") {
            } elseif ($time_open_shop == "" && $time_close_shop != "") {
                $array_text[] = array($time_close_shop, "23:59");
            } elseif ($time_open_shop != "" && $time_close_shop == "") {
                $array_text[] = array("00:00", $time_open_shop);
            } else {
                $array_text[] = array("00:00", $time_open_shop);
                $array_text[] = array($time_close_shop, "23:59");
            }
            $data_array[$current_date] = $array_text;
        }

        $data_array[$current_date] = merge_close_time_with_category_time($data_array[$current_date], $current_date);
    }
    return $data_array;
}
function create_new_order($data, $status = 'processing', $transaction_id = null)
{
    date_default_timezone_set('Europe/Berlin');
    $customer_name1 = $data['customer_name1'];
    $customer_name2 = $data['customer_name2'];
    $customer_phone = $data['customer_phone'];
    $customer_email = $data['customer_email'];
    $more_additional = $data['more_additional'];
    $customer_etage = $data['customer_etage'];
    $customer_zipcode = $data['customer_zipcode'];
    $customer_address = $data['customer_address'];
    $method = $data['method'];
    $ar = $data['ar'];
    $bab = $data['bab'];
    $tax = get_option('dsmart_tax');
    $total_all = 0;
    $total_all_use_coupon = 0;
    $total_cart = 0;
    $tax_price = 0;
    $vat7 = 0;
    $vat19 = 0;
    $taxes = 0;
    $check_zipcode = (get_option('zipcode_status') == "on") ? true : false;
    $checkout_page = get_page_id_by_template('templates/checkout-page.php');
    $thankyou_page = get_page_id_by_template('templates/template-thankyou.php');
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
    } else {
        $user_id = 0;
    }
    $cart = $data['cart'];
    if (isset($_COOKIE['shipping_info']) && $_COOKIE['shipping_info'] != "") :
        $shipping_info = unserialize(base64_decode($_COOKIE['shipping_info']));
        $shop_id = $shipping_info['shop'];
        $shop_data = get_shop_data_by_id($shop_id);
        $shop_name = $shop_data->shop_name;
    else :
        $shop_name = '';
    endif;
    $order_code = $shop_name . " " . $data['order_code'];
    $order_id = wp_insert_post(array(
        'post_type' => 'orders',
        'post_title' => $order_code,
        'post_content' => "",
        'post_status' => 'publish',
        'post_author' => $user_id,
    ));
    $show_second_number = get_option('show_second_number');
    if ($show_second_number == "1") {
        $current_date = date('Ymd');
        $total_order_in_date = (get_option('total_order_' . $current_date) != "") ? intval(get_option('total_order_' . $current_date)) : 0;
        $total_order_in_date = $total_order_in_date + 1;
        update_option('total_order_' . $current_date, $total_order_in_date);
        add_post_meta($order_id, 'second_order_number', $total_order_in_date);
    }
    $item = array();
    $dsmart_currency = get_option('dsmart_currency');
    add_post_meta($order_id, 'currency', $dsmart_currency);
    add_post_meta($order_id, 'customer_name1', $customer_name1);
    add_post_meta($order_id, 'customer_name2', $customer_name2);
    add_post_meta($order_id, 'customer_phone', $customer_phone);
    add_post_meta($order_id, 'customer_email', $customer_email);
    add_post_meta($order_id, 'customer_etage', $customer_etage);
    add_post_meta($order_id, 'customer_zipcode', $customer_zipcode);
    add_post_meta($order_id, 'customer_zipcode', $customer_zipcode);
    add_post_meta($order_id, 'more_additional', $more_additional);
    add_post_meta($order_id, 'method', $method);
    add_post_meta($order_id, 'transition_id', $transaction_id);
    add_post_meta($order_id, 'bab', $bab);
    add_post_meta($order_id, 'ar', $ar);
    foreach ($cart as $key_item => $value_item) {

        $ret = get_extra_variable_info($value_item);
        $sidedish_info = $ret["sidedish_info"];
        $extra_info = $ret["extra_info"];
        $extra_price = $ret["extra_price"];
        $variable_id = $ret["variable_id"];
        $price_item = $ret["price_item"];
        $price = $ret["price"];
        
        $product_id = intval($value_item['product_id']);
        $quantity = $value_item['quantity'];

        $meta['quantity'] = dsmart_field('quantity', $product_id);
        $meta['varialbe_price'] = dsmart_field('varialbe_price', $product_id);

        $meta['extra_name'] = dsmart_field('extra_name', $product_id);
        $meta['extra_price'] = dsmart_field('extra_price', $product_id);
        $extra_price = 0;
        

        $status_item = dsmart_field('status', $product_id);
        //$vat_item = dsmart_field('vat',$product_id);
        //$taxes_item = dsmart_field('taxes',$product_id);

        if ($status_item == "instock" && $price_item != "") {
            $total_all = $total_all + $price;
            if (check_product_can_use_coupon_or_not($product_id) == true) {
                $total_all_use_coupon = $total_all_use_coupon + $price;
            }
            if ($vat_item != "") {
                $vat_price = $price - round($price / (1 + $vat_item / 100), 2);
                if ($vat_item == "7") {
                    $vat7 = $vat7 + $vat_price;
                } else {
                    $vat19 = $vat19 + $vat_price;
                }
            }
            /*if($taxes_item != ""){
				$taxes_price 	= $price - round($price/(1+$taxes_item/100),2);
				$taxes = $taxes + $taxes_price;
			}*/
            $item[$key_item] = array(
                'product_id'    => $product_id,
                "title"         => get_the_title($product_id),
                "std_price"     => $price_item,
                "quantity"         => $quantity,
                "price"         => $price,
                'variable_id'     => $value_item['variable_id'],
                "extra_info"     => $value_item['extra_info'],
                "sidedish_info"     => $value_item['sidedish_info'],
            );
        }
    }
    add_post_meta($order_id, 'item', $item);
    if (isset($_COOKIE['shipping_info']) && $_COOKIE['shipping_info'] != "") {
        $shipping_info = unserialize(base64_decode($_COOKIE['shipping_info']));
        $shipping_method = $shipping_info['shipping_method'];
        $user_location = "";
        $user_latitude = "";
        $user_longitude = "";
        $user_time = "";
        $user_date = "";
        $shop_id = $shipping_info['shop'];
        add_post_meta($order_id, 'shop_id', $shop_id);
        if ($shipping_method == "shipping") {
            $user_location = $shipping_info['location'];
            $user_latitude = $shipping_info['latitude'];
            $user_longitude = $shipping_info['longitude'];
            $user_delivery_time = $shipping_info['delivery_time'];
            $user_delivery_date = $shipping_info['delivery_date'];
            $has_discount = is_discount_time($user_delivery_time, $user_delivery_date, $shipping_method);
        } else {
            $user_time = $shipping_info['time'];
            $user_date = date("d.m.Y");
            $has_discount = is_discount_time($user_time, null, $shipping_method);
        }
        if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
            $coupon = explode(',', $_COOKIE['coupon']);
            foreach ($coupon as $key => $item) {
                $coupon_id = get_coupon_id_from_code($item);
                $coupon_mutiple = dsmart_field('coupon_mutiple', $coupon_id);
                if ($coupon_mutiple != "yes") {
                    $check_promotion = false;
                    $has_discount = false;
                }
            }
        }
        $shipping_data = check_shipping_available($shipping_info);
        $shipping_fee = $shipping_data['price'];
        $shipping_check = $shipping_data['check'];
        if ($shipping_check == true) {
            add_post_meta($order_id, 'shipping_method', $shipping_method);
            if ($check_zipcode == true) {
                add_post_meta($order_id, 'user_location', $customer_address);
            } else {
                add_post_meta($order_id, 'user_location', $user_location);
                add_post_meta($order_id, 'user_latitude', $user_latitude);
                add_post_meta($order_id, 'user_longitude', $user_longitude);
            }
            add_post_meta($order_id, 'user_time', $user_time);
            add_post_meta($order_id, 'user_date', $user_date);
            add_post_meta($order_id, 'shipping_fee', $shipping_fee);
            add_post_meta($order_id, 'user_delivery_time', $user_delivery_time);
            add_post_meta($order_id, 'user_delivery_date', $user_delivery_date);
        }
        setcookie('shipping_info', null, time() - 2592000, '/', NULL, 0);
    } else {
        $shipping_fee = 0;
    }

    /*$shipping_vat = $shipping_fee - $shipping_fee/(1+19/100);*/
    /*add_post_meta($order_id,'vat7',$vat7);
    add_post_meta($order_id,'vat19',$vat19 + $shipping_vat);*/
    //add_post_meta($order_id,'taxes',$taxes + $vat7 + $vat19 + $shipping_vat);
    $total_all = $total_all;
    $total_all_use_coupon = $total_all_use_coupon;
    add_post_meta($order_id, 'subtotal', $total_all);
    if ($shipping_method == "shipping") {
        $type_promotion = get_option('type_promotion');
        $promotion      = get_option('promotion');
    } else {
        $type_promotion = get_option('type_promotion_2');
        $promotion      = get_option('promotion_2');
    }
    $reduce = '';
    $reduce_percent = '';
    $discount_min = get_option('discount_min');
    if ($discount_min != '' && $discount_min != 0 && $discount_min != '0' && floatval($discount_min) > $total_all) {
    }else{
        if ($promotion != null && $has_discount) :
            if ($type_promotion == '%') :
                $reduce_percent = $promotion;
                $temp_total = $total_all_use_coupon * floatval($reduce_percent) / 100;
                $reduce = $temp_total;
                $total_all = $total_all - $temp_total;
                $total_all_use_coupon = $total_all_use_coupon - $temp_total;
            else :
                $reduce_percent = round($promotion / floatval($total_all_use_coupon) * 100);
                $reduce = $promotion;
                $total_all = $total_all - floatval($promotion);
                $total_all_use_coupon = $total_all_use_coupon - floatval($promotion);

            endif;
        endif;
        $reduce_percent = ($reduce_percent > 100) ? 100 : $reduce_percent;
        // add_post_meta($order_id,'has_discount',$has_discount);
        add_post_meta($order_id, 'reduce', $reduce);
        add_post_meta($order_id, 'reduce_percent', $reduce_percent . '%');
    }
        
    if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
        $coupon_array = array();
        $coupon = explode(',', $_COOKIE['coupon']);
        foreach ($coupon as $key_coupon => $item_coupon) {
            $coupon_id = get_coupon_id_from_code($item_coupon);
            $coupon_mutiple = dsmart_field('coupon_mutiple', $coupon_id);
            $coupon_shipping = dsmart_field('coupon_shipping', $coupon_id);
            if (check_coupon_available($item_coupon) == 1 && ($shipping_method == "" || $coupon_shipping == "" || ($shipping_method != "" && $coupon_shipping != "" && $shipping_method == $coupon_shipping))) {
                $coupon_price = get_price_of_coupon($item_coupon, $total_all_use_coupon);
                $coupon_array[$item_coupon] = $coupon_price;
                $total_all = $total_all - $coupon_price;
                $total_all_use_coupon = $total_all_use_coupon - $temp_total;
                global $wpdb;
                $postid = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '" . $item_coupon . "'");
                $coupon_number_use = dsmart_field('coupon_number_use', $postid);
                if ($coupon_number_use != "") {
                    $coupon_number_use = intval($coupon_number_use) - 1;
                    if ($coupon_number_use < 0) {
                        $coupon_number_use = 0;
                    }
                    update_post_meta($postid, 'coupon_number_use', $coupon_number_use);
                }
            }
        }
        add_post_meta($order_id, 'coupon', $coupon_array);
        setcookie('coupon', null, time() - 2592000, '/', NULL, 0);
    } else {
        add_post_meta($order_id, 'coupon', '');
        add_post_meta($order_id, 'coupon_price', '');
        $coupon_price = 0;
    }
    $total_cart = $total_all + $shipping_fee;
    $total_cart = ($total_cart < 0) ? 0 : $total_cart;
    add_post_meta($order_id, 'total', $total_cart);
    add_post_meta($order_id, 'status', $status);
    setcookie('cart', null, time() - 2592000, '/', NULL, 0);
    setcookie('data', null, time() - 2592000, '/', NULL, 0);
    setcookie('checkout_data', null, time() - 2592000, '/', NULL, 0);
    send_mail_after_order($order_id);
    return $order_id;
}
function orderby_post_title_int($orderby)
{
    $order = get_option('dsmart_order');
    if ($order == "") {
        $order = "DESC";
    }
    return '(wp_posts.post_title+0) ' . $order;
}
add_filter('pre_get_posts', function ($query) {
    if ($query->get('orderby') == 'title_number') add_filter('posts_orderby', 'orderby_post_title_int');
}, 10, 2);

// get_discount time shop
function is_discount_time($shoptime = null, $date = null, $method = "shipping")
{
    date_default_timezone_set('Europe/Berlin');
    $has_discount = false;
    $current_time   = date('H:i');
    $discount_cod   = get_option('discount_cod');
    $discount_shop  = get_option('discount_shop');
    // $time = strtotime($time);
    if ($shoptime != null && $shoptime !== "So schnell wie möglich") :
        $time_data  = strtotime($shoptime);
    else :
        $time_data  = strtotime($current_time);
    endif;
    $close_shop                     = get_option('dsmart_close_shop');
    if ($date == null) {
        $current_date_text              = custom_date();
        $current_date                   = date('d-m-Y');
    } else {
        $current_date_text              = custom_date(date('w', strtotime($date)));
        $current_date                   = $date;
    }
    if ($method == "shipping") {
        $time_discount_shop             = get_option('time_discount_shop_' . $current_date_text);
    } else {
        $time_discount_shop             = get_option('time_discount_shop_2_' . $current_date_text);
    }
    $dsmart_custom_discount_date    = get_option('dsmart_custom_discount_date');
    if ($dsmart_custom_discount_date != "" && count($dsmart_custom_discount_date) > 0) {
        foreach ($dsmart_custom_discount_date as $item) {
            if ($current_date == $item['date']) {
                $time_arr = explode(',', $item['time']);
                foreach ($time_arr as  $val) {
                    $val_Arr = explode('-', $val);
                    if ($time_data >= strtotime($val_Arr[0]) && $time_data <= strtotime($val_Arr[1])) {
                        $has_discount = true;
                        break;
                    }
                }
                break;
            }
        }
    }
    if ($has_discount == false) {
        if ($time_discount_shop != "") {
            $time_discount_shop = explode(',', $time_discount_shop);
            foreach ($time_discount_shop as $time) {
                $time_Arr = explode('-', $time);

                if ($time_data >= strtotime($time_Arr[0]) && $time_data <= strtotime($time_Arr[1])) {
                    $has_discount = true;
                    break;
                }
            }
        }
    }
    if ($close_shop == "on") {
        $has_discount = false;
    } elseif ($method != "shipping") {
        if ($discount_shop != 'on') {
            $has_discount = false;
        }
    } else {
        if ($discount_cod != 'on') {
            $has_discount = false;
        }
    }
    return $has_discount;
}
//get total cart
function get_total_cart($shipping_method, $shipping_data, $coupon_value = null, $check_is = false)
{
    if ($coupon_value === null && $check_is == false && isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
        $coupon_value = $_COOKIE['coupon'];
    }
    date_default_timezone_set('Europe/Berlin');
    $total_cart = ds_get_cart_total_item();
    $total_cart_use_coupon  = ds_get_cart_total_item_use_coupon();
    $vat = ds_get_vat_total_item();
    $total_all = 0;
    if ($shipping_method == "shipping") {
        $promotion = get_option('promotion');
        $type_promotion  = get_option('type_promotion');
        $has_discount = is_discount_time($shipping_data['delivery_time'], $shipping_data['delivery_date'], $shipping_method);
    } else {
        $promotion = get_option('promotion_2');
        $type_promotion  = get_option('type_promotion_2');
        $has_discount = is_discount_time($shipping_data['shipping_time'], null, $shipping_method);
    }
    $discount_cod = get_option('discount_cod');
    $discount_shop = get_option('discount_shop');
    $tax_shipping = get_option('tax_shipping');
    $dsmart_distance        = get_option('dsmart_distance');
    $close_shop             = get_option('dsmart_close_shop');
    $dsmart_min_order       = ds_convert_price(get_option('dsmart_min_order'));
    $dsmart_min_order_free = get_option('dsmart_min_order_free') != "" ? ds_convert_price(get_option('dsmart_min_order_free')) : "";
    $dsmart_shipping_fee    = ds_convert_price(get_option('dsmart_shipping_fee'));
    $dsmart_shipping_from   = get_option('dsmart_shipping_from');
    $dsmart_shipping_to     = get_option('dsmart_shipping_to');
    $dsmart_shipping_cs_fee = get_option('dsmart_shipping_cs_fee');
    $tax = get_option('dsmart_tax');
    $shop_id = get_shop_id();
    $shop = get_shop_data_by_id($shop_id);
    $shipping = 0;
    $coupon_price_all = 0;
    $coupon_array = array();
    $check_promotion = true;
    if (isset($coupon_value) && $coupon_value != "") {
        $coupon = explode(',', $coupon_value);
        foreach ($coupon as $key => $item) {
            $coupon_id = get_coupon_id_from_code($item);
            $coupon_mutiple = dsmart_field('coupon_mutiple', $coupon_id);
            if ($coupon_mutiple != "yes") {
                $check_promotion = false;
                $has_discount = false;
            }
        }
    }
    //$taxes = $vat['vat7'] + $vat['vat19'] + $vat['taxes'];
    if ($shipping_method == "shipping") {
        $user_location  = $_REQUEST['user_location'];
        $user_latitude  = $_REQUEST['latitude'];
        $user_longitude = $_REQUEST['longitude'];
        if ($user_location != "" && $user_latitude != "" && $user_longitude != "") {
            $distance = get_distance_from_customer_to_shop($shop->latitude, $shop->longitude, $user_latitude, $user_longitude);
            if (count($dsmart_shipping_from) > 0) {
                foreach ($dsmart_shipping_from as $key => $value) {
                    if ($distance >= intval($dsmart_shipping_from[$key])*1000 && $distance <= intval($dsmart_shipping_to[$key])*1000) {
                        $dsmart_shipping_fee    = ds_convert_price($dsmart_shipping_cs_fee[$key]);
                    }
                }
            }
            if ($dsmart_distance != "" && $distance > $dsmart_distance*1000) {
                $shipping = 0;
                $total_all = $total_cart;
                $total_all_use_coupon = $shipping + $total_cart_use_coupon;
                if ($coupon_value != null) {
                    $coupon = explode(',', $coupon_value);
                    foreach ($coupon as $key => $item) {
                        $coupon_id = get_coupon_id_from_code($item);
                        $coupon_mutiple = dsmart_field('coupon_mutiple', $coupon_id);
                        $coupon_shipping = dsmart_field('coupon_shipping', $coupon_id);
                        if (count($coupon) > 1 && $coupon_mutiple != "yes") {
                            $check_coupon = false;
                        }
                        if ($coupon_mutiple != "yes") {
                            $check_promotion = false;
                            $has_discount = false;
                        }
                        if (check_coupon_available($item) == 1 && ($shipping_method == "" || $coupon_shipping == "" || ($shipping_method != "" && $coupon_shipping != "" && $shipping_method == $coupon_shipping))) {
                            $coupon_price_all += $coupon_price = get_price_of_coupon($item, $total_all_use_coupon);
                            $coupon_array[$item]['price'] = '- ' . ds_price_format_text_no_convert($coupon_price);
                            $coupon_array[$item]['type'] = dsmart_field('coupon_shipping', $coupon_id);
                            $total_all = $total_all - $coupon_price;
                            $total_all_use_coupon = $total_all_use_coupon - $coupon_price;
                        }
                    }
                } else {
                    $coupon_price = 0;
                    $coupon_price_all = 0;
                }
                $total_all = $total_all - $coupon_price_all;
                $total_all = $shipping + $total_all;
                if ($total_all < 0) {
                    $total_all = 0;
                }
            } else {
                if (($dsmart_min_order_free != "" && $total_cart > $dsmart_min_order_free)) {
                    $shipping = 0;
                } else {
                    if ($dsmart_shipping_fee == "") {
                        $shipping = 0;
                    } else {
                        $shipping = $dsmart_shipping_fee;
                    }
                }
                /*if($tax_shipping != ""){
                    $shipping_vat = $shipping - round($shipping/(1+$tax_shipping/100),2);
                    //$taxes = $taxes + $shipping_vat;
                }else{
                    $shipping_vat = 0;
                }*/
                $total_all = $total_cart;
                $total_all_use_coupon = $total_cart_use_coupon;
                $reduce = '';
                $reduce_percent = '';
                if ($check_promotion == true && $promotion != null && $has_discount && $discount_cod == 'on') :
                    $has_reduce             = true;
                    if ($type_promotion == '%') :
                        $reduce_percent = $promotion;
                        $temp_total     = $total_all_use_coupon * floatval($promotion) / 100;
                        $total_all      = $total_all - $temp_total;
                        $total_all_use_coupon = $total_all_use_coupon - $temp_total;
                        $reduce         = ds_price_format_text_no_convert($temp_total);
                    else :
                        $reduce_percent = round($promotion / floatval($total_all) * 100);
                        $total_all      = $total_all - floatval($promotion);
                        $total_all_use_coupon = $total_all_use_coupon - floatval($promotion);
                        $reduce         = ds_price_format_text_no_convert($promotion);
                    endif;
                endif;
                if ($coupon_value != null) {
                    $coupon = explode(',', $coupon_value);
                    foreach ($coupon as $key => $item) {
                        $coupon_id = get_coupon_id_from_code($item);
                        $coupon_mutiple = dsmart_field('coupon_mutiple', $coupon_id);
                        $coupon_shipping = dsmart_field('coupon_shipping', $coupon_id);
                        if (count($coupon) > 1 && $coupon_mutiple != "yes") {
                            $check_coupon = false;
                        }
                        if ($coupon_mutiple != "yes") {
                            $check_promotion = false;
                            $has_discount = false;
                        }
                        if (check_coupon_available($item) == 1 && ($shipping_method == "" || $coupon_shipping == "" || ($shipping_method != "" && $coupon_shipping != "" && $shipping_method == $coupon_shipping))) {
                            $coupon_price_all += $coupon_price = get_price_of_coupon($item, $total_all_use_coupon);
                            $coupon_array[$item]['price'] = '- ' . ds_price_format_text_no_convert($coupon_price);
                            $coupon_array[$item]['type'] = dsmart_field('coupon_shipping', $coupon_id);
                            $total_all = $total_all - $coupon_price;
                            $total_all_use_coupon = $total_all_use_coupon - $coupon_price;
                        }
                    }
                } else {
                    $coupon_price = 0;
                    $coupon_price_all = 0;
                }
                $total_all = $shipping + $total_all;
                if ($total_all < 0) {
                    $total_all = 0;
                }
            }
        } else {
            $total_all = $total_cart;
            $total_all_use_coupon = $total_cart_use_coupon;
            if ($coupon_value != null) {
                $coupon = explode(',', $coupon_value);
                foreach ($coupon as $key => $item) {
                    $coupon_id = get_coupon_id_from_code($item);
                    $coupon_mutiple = dsmart_field('coupon_mutiple', $coupon_id);
                    $coupon_shipping = dsmart_field('coupon_shipping', $coupon_id);
                    if (count($coupon) > 1 && $coupon_mutiple != "yes") {
                        $check_coupon = false;
                    }
                    if ($coupon_mutiple != "yes") {
                        $check_promotion = false;
                        $has_discount = false;
                    }
                    if (check_coupon_available($item) == 1 && ($shipping_method == "" || $coupon_shipping == "" || ($shipping_method != "" && $coupon_shipping != "" && $shipping_method == $coupon_shipping))) {
                        $coupon_price_all += $coupon_price = get_price_of_coupon($item, $total_all_use_coupon);
                        $coupon_array[$item]['price'] = '- ' . ds_price_format_text_no_convert($coupon_price);
                        $coupon_array[$item]['type'] = dsmart_field('coupon_shipping', $coupon_id);
                        $total_all = $total_all - $coupon_price;
                        $total_all_use_coupon = $total_all_use_coupon - $coupon_price;
                    }
                }
            } else {
                $coupon_price = 0;
                $coupon_price_all = 0;
            }
            $reduce = '';
            $reduce_percent = '';
            if ($check_promotion == true && $promotion != null && $has_discount && $discount_cod == 'on') :
                $has_reduce             = true;
                if ($type_promotion == '%') :
                    $reduce_percent = $promotion;
                    $temp_total     = $total_all_use_coupon * floatval($promotion) / 100;
                    $total_all      = $total_all - $temp_total;
                    $total_all_use_coupon = $total_all_use_coupon - $temp_total;
                    $reduce         = ds_price_format_text_no_convert($temp_total);
                else :
                    $reduce_percent = round($promotion / floatval($total_all) * 100);
                    $total_all      = $total_all - floatval($promotion);
                    $total_all_use_coupon = $total_all_use_coupon - floatval($promotion);
                    $reduce         = ds_price_format_text_no_convert($promotion);
                endif;
            endif;
            if ($total_all < 0) {
                $total_all = 0;
            }
            $shipping = 0;
        }
    } elseif ($shipping_method == "direct") {
        $shipping_time = $_REQUEST['shipping_time'];
        $total_all = $total_cart;
        $total_all_use_coupon = $total_cart_use_coupon;
        $has_discount = is_discount_time($shipping_time, null, $shipping_method);
        if ($check_promotion == true && $promotion != null && $has_discount) :
            $has_reduce         = true;
            if ($type_promotion == '%') :
                $reduce_percent = $promotion;
                $temp_total     = $total_all_use_coupon * floatval($promotion) / 100;
                $total_all      = $total_all - $temp_total;
                $total_all_use_coupon = $total_all_use_coupon - $temp_total;
                $reduce         = ds_price_format_text_no_convert($temp_total);
            else :
                $reduce_percent = round($promotion / floatval($total_all) * 100);
                $total_all      = $total_all_use_coupon - floatval($promotion);
                $total_all_use_coupon = $total_all_use_coupon - floatval($promotion);
                $reduce         = ds_price_format_text_no_convert($promotion);
            endif;
        endif;
        if ($coupon_value != null) {
            $coupon = explode(',', $coupon_value);
            foreach ($coupon as $key => $item) {
                $coupon_id = get_coupon_id_from_code($item);
                $coupon_mutiple = dsmart_field('coupon_mutiple', $coupon_id);
                $coupon_shipping = dsmart_field('coupon_shipping', $coupon_id);
                if (count($coupon) > 1 && $coupon_mutiple != "yes") {
                    $check_coupon = false;
                }
                if ($coupon_mutiple != "yes") {
                    $check_promotion = false;
                    $has_discount = false;
                }
                if (check_coupon_available($item) == 1 && ($shipping_method == "" || $coupon_shipping == "" || ($shipping_method != "" && $coupon_shipping != "" && $shipping_method == $coupon_shipping))) {
                    $coupon_price_all += $coupon_price = get_price_of_coupon($item, $total_all_use_coupon);
                    $coupon_array[$item]['price'] = '- ' . ds_price_format_text_no_convert($coupon_price);
                    $coupon_array[$item]['type'] = dsmart_field('coupon_shipping', $coupon_id);
                    $total_all = $total_all - $coupon_price;
                    $total_all_use_coupon = $total_all_use_coupon - $coupon_price;
                }
            }
        } else {
            $coupon_price = 0;
            $coupon_price_all = 0;
        }
        if ($total_all < 0) {
            $total_all = 0;
        }
        $shipping = 0;
    } else {
        $shipping_time = $_REQUEST['shipping_time'];
        $total_all = $total_cart;
        $total_all_use_coupon = $total_cart_use_coupon;
        if ($coupon_value != null) {
            $coupon = explode(',', $coupon_value);
            foreach ($coupon as $key => $item) {
                $coupon_id = get_coupon_id_from_code($item);
                $coupon_mutiple = dsmart_field('coupon_mutiple', $coupon_id);
                $coupon_shipping = dsmart_field('coupon_shipping', $coupon_id);
                if (count($coupon) > 1 && $coupon_mutiple != "yes") {
                    $check_coupon = false;
                }
                if ($coupon_mutiple != "yes") {
                    $check_promotion = false;
                    $has_discount = false;
                }
                if (check_coupon_available($item) == 1 && ($shipping_method == "" || $coupon_shipping == "" || ($shipping_method != "" && $coupon_shipping != "" && $shipping_method == $coupon_shipping))) {
                    $coupon_price_all += $coupon_price = get_price_of_coupon($item, $total_all_use_coupon);
                    $coupon_array[$item]['price'] = '- ' . ds_price_format_text_no_convert($coupon_price);
                    $coupon_array[$item]['type'] = dsmart_field('coupon_shipping', $coupon_id);
                    $total_all = $total_all - $coupon_price;
                    $total_all_use_coupon = $total_all_use_coupon - $coupon_price;
                }
            }
        } else {
            $coupon_price = 0;
            $coupon_price_all = 0;
        }
        if ($total_all < 0) {
            $total_all = 0;
        }
        $shipping = 0;
    }
    $html .= '';
    $html .= '<tbody>';
    $html .= '<tr class="cart-shipping">';
    $html .= '<td>' . __("Lieferungskosten", 'dsmart') . '</td>';
    $html .= '<td><span class="shipping-text">' . ds_price_format_text_no_convert($shipping) . '</span></td>';
    $html .= '</tr>';
    /*$html .= '<tr class="cart-subtotal">';
            $html .= '<td>'.__("MwSt",'dsmart').'</td>';
            $html .= '<td><span class="tax-taxes">'.ds_price_format_text_no_convert($taxes).'</span></td>';
        $html .= '</tr>';*/
    if ($promotion != null) :
        if (isset($reduce) && $reduce != "") {
            $html .= '<tr class="cart-discount">';
            $html .= '<td>' . __("Rabatt", 'dsmart') . ' <span class="percent">(' . $reduce_percent . '%)</span></td>';
            $html .= '<td class="number">-' . $reduce . '</td>';
            $html .= '</tr>';
        } else {
            $html .= 'dasdasdasd';
            $html .= '<tr class="cart-discount hidden">';
            $html .= '<td>' . __("Rabatt", 'dsmart') . ' <span class="percent"></span></td>';
            $html .= '<td class="number"></td>';
            $html .= '</tr>';
        }
    endif;
    if (isset($coupon_array) && count($coupon_array) > 0) :
        foreach ($coupon_array as $key => $item) {
            $html .= '<tr class="cart-coupon" data-type="' . $item['type'] . '" data-coupon="' . $key . '">';
            $html .= '<td><span class="remove-coupon">x</span>' . __("Rabattcode", 'dsmart') . ' (' . $key . ')</td>';
            $html .= '<td><span class="coupon-text">' . $item['price'] . '</span></td>';
            $html .= '</tr>';
        }
    endif;
    $html .= '<tr class="order-total">';
    $html .= '<td>' . __("Gesamtsumme", 'dsmart') . '</td>';
    $html .= '<td>';
    $html .= '<div class="price">';
    $html .= '<span class="total-text">' . ds_price_format_text_no_convert($total_all) . '</span>';
    $html .= '</div>';
    $html .= '</td>';
    $html .= '</tr>';
    $html .= '</tbody>';
    $html .= $total_all;
    return $html;
}
//redirect when not found
function kill_404_redirect_wpse_92103()
{
    if (is_404()) {
        add_action('redirect_canonical', '__return_false');
    }
}
add_action('template_redirect', 'kill_404_redirect_wpse_92103', 1);

//change theme dark/light
function dsmart_add_class_to_body($classes)
{
    $dsmart_theme_style = get_option('dsmart_theme_style');
    if ($dsmart_theme_style == "dark") {
        $classes[] = 'dark-style';
    }
    return $classes;
}

add_filter('body_class', 'dsmart_add_class_to_body');
//check category time open or not
function check_category_open_or_not($t_id)
{
    $tax_enable = get_term_meta( $t_id, 'tax_enable', true );

    if($tax_enable === "0") return false;

    // new custome date time
    $dsmart_new_custom_date = get_term_meta( $t_id, 'tax_new_custom_date', true );
	if($dsmart_new_custom_date == "" || !$dsmart_new_custom_date){
		$dsmart_new_custom_date = [];
	}
    if ($dsmart_new_custom_date != "" && count($dsmart_new_custom_date) > 0) {
        $current_date = date("Y-m-d");
        $current_time = date("H:i");
        foreach ($dsmart_new_custom_date as $item) {
            $start_date = date("Y-m-d", strtotime($item["start_date"]));
            $end_date = date("Y-m-d", strtotime($item["end_date"]));
            
            $correct_single = $item["date_type"] === "single" && $start_date === $current_date;
            $correct_mutiple = $item["date_type"] !== "single" && $current_date >= $start_date && $current_date <= $end_date;
            if($correct_single || $correct_mutiple){    
                if($item["time_type"] === "time_to_time"){
                    if($current_time >= $item["start_time"] && $current_time <= $item["end_time"]){
                        if ($item["status"] === "close") {
                            return false;
                        }
                    }
                }
                else{
                    if ($item["status"] === "close") {
                        return false;
                    }
                }
            }
        } 
    }


    $current_time = date("H:i");
    $custom_date = custom_date();
    $now = new DateTime($current_time);
    $tax_time = get_term_meta($t_id, 'tax_time', true);
    $check = true;
    if ($tax_time != "" && count($tax_time) > 0) {
        foreach ($tax_time as $key => $value) {
            $time_date = $value['date'];
            $time_open = $value['open'];
            $time_close = $value['close'];
            if ($time_date == $custom_date && $time_open != "" && new DateTime($time_open) > $now) {
                return false;
            } elseif ($time_date == $custom_date && $time_close != "" && new DateTime($time_close) < $now) {
                return false;
            }
        }
    }
    return true;
}
//get all category not open
//get data zipcode
function get_data_zipcode($zipcode)
{
    $zipcode_data = get_option('zipcode_data');
    if (is_array($zipcode_data) && count($zipcode_data) > 0) {
        foreach ($zipcode_data as $key => $value) {
            if ($value['zipcode'] == $zipcode) {
                return $value;
            }
        }
    }
    return false;
}
//add item to array
function add_item_to_array($inserted, $array, $position)
{
    return array_slice($array, 0, $position, true) + $inserted + array_slice($array, $position, count($array) - 1, true);
}
//custom avatar
add_filter('get_avatar', 'ds_get_avatar', 10, 5);
function ds_get_avatar($avatar, $id_or_email, $size, $default, $alt)
{
    if (is_numeric($id_or_email)) {
        $image = get_user_meta($id_or_email, 'wp_user_avatar', true);
        if ($image)
            return wp_get_attachment_image($image, $size);
    } else {
        $user = get_user_by('email', $id_or_email);
        $image = get_user_meta($user->ID, 'wp_user_avatar', true);
        if ($image)
            return wp_get_attachment_image($image, $size);
    }
    return $avatar;
}
//get address by id
function ds_get_address($user_id)
{
    $list_addresses = get_user_meta($user_id, 'address', true);
    return $list_addresses;
}
function ds_auto_delete_orders()
{
    global $wpdb;
    $ds_auto_delete_order = get_option('ds_auto_delete_order');
    $order_date = intval(get_option('order_date'));
    if ($ds_auto_delete_order == "on" && $order_date > 0) {
        $sql = "DELETE FROM `wp_posts` WHERE `post_type` = 'orders' AND DATEDIFF(NOW(), `post_date`) > " . $order_date;
        $results = $wpdb->get_results($sql);
    }
}
add_action('init', 'ds_auto_delete_orders');

