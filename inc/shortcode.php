<?php
add_shortcode('get_best_seller_product','get_best_seller_product');
function get_best_seller_product($atts, $content = null){
     extract(shortcode_atts(array(
        'number' => 3
    ), $atts));
    global $wp_query;
    if(is_user_logged_in()){
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
    }
    $wp_query  = new WP_Query( array(
        'post_type'      => 'orders',
        'post_status'   => 'publish',
        'posts_per_page' => 20,
        'order'          => 'desc',
        'orderby' => 'date',
        'author' => $user_id
    ));
    ob_start();
    if ($wp_query->have_posts()): ?>
    	<div class="dsmart-notify"></div>
        <div class="dsmart-table table-responsive">
            <table class="table" id="list-order-user">
                <thead>
                    <tr>
                        <th><?php _e('Order','dsmart') ?></th>
                        <th><?php _e('Full name','dsmart') ?></th>
                        <th><?php _e('Phone number','dsmart') ?></th>
                        <th><?php _e('Email address','dsmart') ?></th>
                        <th><?php _e('Total','dsmart') ?></th>
                        <th><?php _e('Status','dsmart') ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($wp_query->have_posts()) : $wp_query->the_post(); 
                        $currency = dsmart_field('currency');
                        $customer_name = dsmart_field('customer_name');
                        $customer_email = dsmart_field('customer_email');
                        $customer_phone = dsmart_field('customer_phone');
                        $total = dsmart_field('total');
                        $status = dsmart_field('status'); ?>
                        <tr data-item="<?php the_ID(); ?>">
                            <td><?php the_title(); ?></td>
                            <td><?php echo $customer_name; ?></td>
                            <td><?php echo $customer_phone; ?></td>
                            <td><?php echo $customer_email; ?></td>
                            <td><?php echo ds_price_format_text_with_symbol($total,$currency); ?></td>
                            <td>
                                <?php if ($status == "processing") { ?>
                                    <span class="processing"><?php _e("Processing",'dsmart') ?></span>
                                <?php }elseif ($status == "cancelled") { ?>
                                    <span class="cancelled"><?php _e("Cancelled",'dsmart') ?></span>
                                <?php }elseif ($status == "completed") { ?>
                                    <span class="completed"><?php _e("Completed",'dsmart') ?></span>
                                <?php } ?>         
                            </td>
                            <td><button type="button" class="dsmart-button print-order"><?php _e("Print order",'dsmart'); ?></button></td>
                        </tr>
                    <?php endwhile;  ?>
                </tbody>
            </table>
        </div>
        <?php echo get_the_posts_pagination( array( 'mid_size' => 2,'prev_text' => __('Trước','dsmart'),'next_text' => __('Sau','dsmart') ));wp_reset_query(); ?>
        <div class="book-loading"><img src="<?php echo plugin_dir_url( __FILE__ ).'img/loading.gif'; ?>"></div>
    <?php
    endif;
    $list_icon = ob_get_contents();
    ob_end_clean();
    return $list_icon;
}
add_shortcode('get_date_time_shop','get_date_time_shop');
function get_date_time_shop($atts, $content = null){
     extract(shortcode_atts(array(
        'number' => 3
    ), $atts));
    ob_start();?>
        <div class="show-hide-time">
            <a href="#" class="show-hide-button"><?php _e("Show/Hide time of shop",'dsmart'); ?></a>
            <div class="content-time">
                <?php $dsmart_custom_date = get_option('dsmart_custom_date');
                //closed time
                // $closed_time = get_option('closed_time');
                $closed_time = get_option('closed_time_2');
                //mon
                $time_open_shop_mo = get_option('time_open_shop_mo');
                $time_close_shop_mo = get_option('time_close_shop_mo');
                //tue
                $time_open_shop_tu = get_option('time_open_shop_tu');
                $time_close_shop_tu = get_option('time_close_shop_tu');
                //wed
                $time_open_shop_we = get_option('time_open_shop_we');
                $time_close_shop_we = get_option('time_close_shop_we');
                //thu
                $time_open_shop_th = get_option('time_open_shop_th');
                $time_close_shop_th = get_option('time_close_shop_th');
                //fri
                $time_open_shop_fr = get_option('time_open_shop_fr');
                $time_close_shop_fr = get_option('time_close_shop_fr');
                //sat
                $time_open_shop_sa = get_option('time_open_shop_sa');
                $time_close_shop_sa = get_option('time_close_shop_sa');
                //sun
                $time_open_shop_su = get_option('time_open_shop_su');
                $time_close_shop_su = get_option('time_close_shop_su'); ?>
                <div class="regular-date">
                    <h3><?php _e("Regular date",'dsmart'); ?></h3>
                    <div class="dsmart-table table-striped">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center"><?php _e("Date",'dsmart') ?></th>
                                    <th class="text-center"><?php _e("Time open",'dsmart') ?></th>
                                    <th class="text-center"><?php _e("Time close",'dsmart') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center"><?php _e("Monday",'dsmart') ?></td>
                                    <td class="text-center"><?php echo $time_open_shop_mo ?></td>
                                    <td class="text-center"><?php echo $time_close_shop_mo ?></td>
                                </tr>
                                <tr>
                                    <td class="text-center"><?php _e("Tuesday",'dsmart') ?></td>
                                    <td class="text-center"><?php echo $time_open_shop_tu ?></td>
                                    <td class="text-center"><?php echo $time_close_shop_tu ?></td>
                                </tr>
                                <tr>
                                    <td class="text-center"><?php _e("Wednesday",'dsmart') ?></td>
                                    <td class="text-center"><?php echo $time_open_shop_we ?></td>
                                    <td class="text-center"><?php echo $time_close_shop_we ?></td>
                                </tr>
                                <tr>
                                    <td class="text-center"><?php _e("Thursday",'dsmart') ?></td>
                                    <td class="text-center"><?php echo $time_open_shop_th ?></td>
                                    <td class="text-center"><?php echo $time_close_shop_th ?></td>
                                </tr>
                                <tr>
                                    <td class="text-center"><?php _e("Friday",'dsmart') ?></td>
                                    <td class="text-center"><?php echo $time_open_shop_fr ?></td>
                                    <td class="text-center"><?php echo $time_close_shop_fr ?></td>
                                </tr>
                                <tr>
                                    <td class="text-center"><?php _e("Saturday",'dsmart') ?></td>
                                    <td class="text-center"><?php echo $time_open_shop_sa ?></td>
                                    <td class="text-center"><?php echo $time_close_shop_sa ?></td>
                                </tr>
                                <tr>
                                    <td class="text-center"><?php _e("Sunday",'dsmart') ?></td>
                                    <td class="text-center"><?php echo $time_open_shop_su ?></td>
                                    <td class="text-center"><?php echo $time_close_shop_su ?></td>
                                </tr>
                            </tbody>    
                        </table>
                    </div>
                </div>
                <?php if($closed_time != ""){ ?>
                    <div class="close-time">
                        <h3><?php _e("Closed time",'dsmart'); ?></h3>
                        <div class="dsmart-table">
                            <table class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php _e("Date",'dsmart') ?></th>
                                        <th class="text-center"><?php _e("Open time",'dsmart') ?></th>
                                        <th class="text-center"><?php _e("Close time",'dsmart') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($closed_time as $item) { ?>
                                        <tr>
                                            <td class="text-center">
                                                <?php if($item['date'] == "mo"){ 
                                                    _e("Monday",'dsmart');
                                                }elseif($item['date'] == "tu"){
                                                    _e("Tuesday",'dsmart');
                                                }elseif($item['date'] == "we"){
                                                    _e("Wednesday",'dsmart');
                                                }elseif($item['date'] == "th"){
                                                    _e("Thursday",'dsmart');
                                                }elseif($item['date'] == "fr"){
                                                    _e("Friday",'dsmart');
                                                }elseif($item['date'] == "sa"){
                                                    _e("Saturday",'dsmart');
                                                }elseif($item['date'] == "su"){
                                                    _e("Sunday",'dsmart');
                                                }?>
                                            </td>
                                            <td class="text-center"><?php echo $item['from'] ?></td>
                                            <td class="text-center"><?php echo $item['to'] ?></td>
                                        </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php }?>
                <?php if($dsmart_custom_date != ""){ ?>
                    <div class="custom-date">
                        <h3><?php _e("Special date",'dsmart'); ?></h3>
                        <div class="dsmart-table">
                            <table class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php _e("Date",'dsmart') ?></th>
                                        <th class="text-center"><?php _e("Open time",'dsmart') ?></th>
                                        <th class="text-center"><?php _e("Close time",'dsmart') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dsmart_custom_date as $item) { ?>
                                        <tr>
                                            <td class="text-center"><?php echo $item['date']; ?></td>
                                            <td class="text-center"><?php echo $item['open'] ?></td>
                                            <td class="text-center"><?php echo $item['close'] ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div> 
                <?php } ?>
            </div>
        </div>
    <?php $list_icon = ob_get_contents();
    ob_end_clean();
    return $list_icon;
}
//add search form
add_shortcode('show_search_form','show_search_form');
function show_search_form($atts, $content = null){
    ob_start();
    $next_link_shortcode = (get_option('next_link_shortcode') != "") ? get_option('next_link_shortcode') : home_url();
    echo '<div class="ds-searchform">
        <div class="ds-wrap">
            <div class="input-field">
                <i class="fa fa-map-marker"></i>
                <input type="text" placeholder="PLZ" class="dsmart-field ds-zipcode"/>
            </div>
            <div class="ds-list-button">
                <a href="javascript:;" class="cs-btn ds-liefern" data-href="'.$next_link_shortcode.'">Liefern</a>
                <span>oder</span>
                <a href="javascript:;" class="cs-btn ds-abholen" data-href="'.$next_link_shortcode.'">Abholen</a>
            </div>
        </div>
    </div>';
    $list_icon = ob_get_contents();
    ob_end_clean();
    return $list_icon;
}