<?php global $wp_query;
$dsmart_thumbnail = get_option('dsmart_thumbnail');
$dsmart_stock = get_option('dsmart_stock');
$current_term = get_queried_object();

$tax_query = array();
$meta_query = array();
if (isset($_GET['price']) && $_GET['price'] != "") {
    $price_input = $_GET['price'];
    $price_arr = explode(';', $price_input);
    $dsmart_currency = get_option('dsmart_currency');
    if ($dsmart_currency == "2") {
        $currency = "€";
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
    } else {
        $price_arr[0] = $price_arr[0] / $currency_rate;
        $price_arr[1] = $price_arr[1] / $currency_rate;
    }
    $meta_query[] = array('key'  => 'price', 'value' => $price_arr, 'compare'   => 'BETWEEN', 'type' => 'NUMERIC');
} else {
    $price_input = "";
}
if (isset($_GET['rating']) && $_GET['rating'] != "") {
    $input_rating = $_GET['rating'];
    $meta_query[] = array(
        'key'  => 'avg_rating',
        'value'     => $input_rating,
        'compare' => '>=',
        'type' => 'NUMERIC'
    );
} else {
    $status = "";
}

if (count($meta_query) > 0) {
    $meta_query['relation'] = 'AND';
} else {
    $meta_query = null;
}
$order = get_option('dsmart_order');
if ($order == "") {
    $order = "DESC";
}
$orderby = get_option('dsmart_orderby');
if ($orderby == "") {
    $orderby = "date";
}
$dsmart_taxonomy_text = get_option('dsmart_taxonomy_text');
$show_notify = get_option('show_notify');
$notify_text = get_option('notify_text');

$button_color = get_option('button_color', '#50aecc');
$sidebar_color = get_option('sidebar_color', '#ff8000');
$price_color = get_option('price_color', '#b28e2d');

$check_time_open1 = true;
$check_time_open2 = true;
$time1 = get_close_time_shop_nodelay();
$time2 = get_close_time_shop2_nodelay();
$now1 = new DateTime(get_current_time());
$now2 = new DateTime(get_current_time2());
if (count($time1) > 0) {
    foreach ($time1 as $value) {
        $begin = new DateTime($value[0]);
        $end = new DateTime($value[1]);
        if($now1 > $begin && $now1 < $end){
            $check_time_open1 = false;
        }
    }
    foreach ($time2 as $value) {
        $begin = new DateTime($value[0]);
        $end = new DateTime($value[1]);
        if ($now2 > $begin && $now2 < $end) {
            $check_time_open2 = false;
        }
    }
}
?>
<style>
body.dark-style .category_single{
    border: solid 1px rgb(255, 82, 82);
    background-color: black;
    color: white !important;
}
.category_single{
    border-bottom: solid 1px #dfdfdf;
    padding: 10px;
    font-weight: 500;
    color: black !important;
    width: 100%;
    height: 100px;
}

.category_single.active{
    background-color: rgb(255, 82, 82) !important;
    color: white !important;
}

body.dark-style .category_single:hover{
    background-color: rgb(255, 82, 82) !important;
    color: white !important;
}
.category_single:hover{
    background-color: <?php echo $button_color ?> !important;
    color:white !important;
}

.menu-categories li.active a,.menu-categories li a:hover{
    background-color: <?php echo $sidebar_color ?> !important;
    color: #fff !important;
}
</style>
<?php
get_header(); 

if (get_option('homepage_popup') === "2"){
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

?>

<?php if (strcmp(get_option('dsmart_close_shop'), "on") == 0) {
    
    $image_id = get_option('ds_popup');
    $output = "";
    if (intval($image_id) > 0) {
        $url = wp_get_attachment_image_src($image_id, 'full', false);
        $output = $url[0];
    } else {
        $output = plugins_url('img/default-closed-popup.jpeg', __FILE__);
    }
?>
    <div id="myModal" class="modal">

        <!-- The Close Button -->
        <span class="close">&times;</span>

        <!-- Modal Content (The Image) -->
        <img style="margin-top: 10%;" class="modal-content" id="img01" src="<?php echo $output; ?>">
        <!-- Modal Caption (Image Text) -->
        <div id="caption"></div>
    </div>

<?php
}
?>


<div class="container">

    <?php if ($show_notify == "on" && $notify_text != "") : ?>
        <div class="hihi">
            <div class="shop-notify">
                <!-- <marquee onmouseover="this.stop();" onmouseout="this.start();"><?php echo $notify_text; ?></marquee> -->
                <div class="marquee-parent">
                    <div class="marquee-child">
                        <?php echo $notify_text; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
    <div class="info-shop" style="text-align: center;margin-top:20px;">
        <?php $image_id = get_option('ds_logo');
                        if ($image_id != null) {
                            $image = wp_get_attachment_image_src($image_id, 'full'); ?>
        <a href="<?php echo get_option('logo_link')?>"><img src="<?php echo $image[0]; ?>" alt="<?php _e("Logo", 'dsmart') ?>" /></a>
        <div class="line-logo" style="border-bottom: 1px dotted;margin-top: 15px;"></div>
                <?php } ?>
        <?php
        $place_id = get_option('place_id_map');
        $theme =get_option('dsmart_theme_style');
        if ($place_id != null && $place_id !== '') {
            
            $api = get_option('dsmart_google_key');
            $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=$place_id&key=$api";
            $content = file_get_contents($url);
            $content = json_decode($content, true);
            $rating = $content['result']['rating'];
            $total = $content['result']['user_ratings_total'];
            $url_map = $content['result']['url'];
            if ($rating != null && $rating !== 0) {
                echo  "<br>";
                ?>
                <?php 
                    if(strcmp($theme,"dark") == 0){
                        ?>
                                <div style="color:white"><?php echo $rating ?>
                        <?php
                    }else{
                        ?>
                                <div><?php echo $rating ?>
                        <?php
                    }
                ?>
                
            <?php
                $round = intval(round($rating));
                for ($i = 0; $i < $round; $i++) {
        ?>

                    <i style="color:#ffe234;margin-left:5px;" class="bi bi-star-fill"></i>
                <?php
                }

                if ($rating > $round) {
                ?>
                    <i style="color:#ffe234" class="bi bi-star-half"></i>
                    <?php
                    $diff = round(5 - $rating - 1);
                    for ($i = 0; $i < $diff; $i++) {
                    ?>
                        <i style="color:#ffe234" class="bi bi-star"></i>
                    <?php
                    }
                } else {
                    $diff = round(5 - $rating);
                    for ($i = 0; $i < $diff; $i++) {
                    ?>
                        <i style="color:#ffe234" class="bi bi-star"></i>
            <?php
                    }
                }
                ?>
                </div>
                    <?php 
                        if(strcmp($theme,"dark") == 0){
                            ?>
                                 <a target="_blank" style="color:white" href="<?php echo $url_map ?>">(<?php echo $total ?> Bewertungen)</a>
                            <?php
                        }else{
                            ?>
                                 <a target="_blank" href="<?php echo $url_map ?>">(<?php echo $total ?> Bewertungen)</a>
                            <?php
                        }
                    ?>
                   
                <?php
            }
        }
        $check1 = check_time_with_time_shop(date("H:i"), null, null, "shipping");
        $check2 = check_time_with_time_shop(date("H:i"), null, null, "direct");
        $dsmart_method_ship = get_option('dsmart_method_ship');
        $dsmart_method_direct = get_option('dsmart_method_direct');
        if (($dsmart_method_direct === "on" && $check2 || $dsmart_method_ship === "on" && $check1)) {
            ?>
            <p style="font-weight: bold;color:green">Jetzt geöffnet!</p>
            <?php 
                if(strcmp($theme,"dark") == 0){
                    ?>
                        <span style="font-weight: bold;color:white">Infos und Aktionen</span>
                    <?php
                }else{
                    ?>
                        <span style="font-weight: bold">Infos und Aktionen</span>
                    <?php
                }
            ?>
            
            <?php
            ?>
                <?php if ($dsmart_method_direct === "on" && $check2) {
                ?>
                    <div class="row" style="justify-content: center;display: flex;">
                        <?php 
                            if(strcmp($theme,"dark") == 0){
                                ?>
                                    <i style="color:white" class="bi bi-truck"></i>
                                    <div style="margin-left: 5px;color:white">Abholung</div>
                                <?php
                            }else{
                                ?>
                                    <i class="bi bi-truck"></i>
                                    <div style="margin-left: 5px;">Abholung</div>
                                <?php
                            }
                        ?>
                    </div>
                <?php

                } ?>
                <?php if ($dsmart_method_ship === "on" && $check1) {

                ?>
                    <div class="row" style="justify-content: center;display: flex;">
                        <?php 
                            if(strcmp($theme,"dark") == 0){
                                ?>
                                    <i style="color:white" class="bi bi-truck"></i>
                                    <div style="margin-left: 5px;color:white">Lieferung</div>
                                <?php
                            }else{
                                ?>
                                    <i class="bi bi-truck"></i>
                                    <div style="margin-left: 5px;">Lieferung</div>
                                <?php
                            }
                        ?>
                    </div>
                <?php
                }
                ?> 
            <?php
        } else {
            ?>
            <?php 
                if(strcmp($theme,"dark") == 0){
                    ?>
                        <p style="font-weight: bold;color:white">Derzeit sind nur Vorbestellungen möglich!</p>
                    <?php
                }else{
                    ?>
                        <p style="font-weight: bold;color:red">Derzeit sind nur Vorbestellungen möglich!</p>
                    <?php
                }
            ?>
            
        <?php
        }
        ?>
        <div class="line-logo" style="border-bottom: 1px dotted;margin-top: 15px;"></div>
    </div>

    <div class="outer_category_box category_popup">
        <div class="current_category_box" style="background: <?php echo $button_color ?> !important;animation: pulse-red 2s infinite;" onclick="open_category_popup()">
            <div class="current_category">Kategorien</div>
            <div class="expand_icon"><i class="bi bi-justify" style="font-size: 25px;color:white"></i></div>
        </div>
        
    </div>

        <div class="category_popuptext" id="category_popuptext">

        <?php $terms = get_terms(array(
            'taxonomy' => 'product-cat',
            'hide_empty' => false,
            'parent' => 0
        ));
        if ($terms) : ?>
                <?php foreach ($terms as $term) {
                    if (check_category_open_or_not($term->term_id)) {
                        if ($current_term->term_id == $term->term_id) {
                            $class = "active";
                        } else {
                            $class = "";
                        } ?>
                        <div onclick="roll_to_cat(<?php echo $term->term_id ?>)" style="display: table !important;" id="menu_side_<?php echo $term->term_id ?>" class="<?php echo $class; ?> category_single"><span style="display: table-cell !important;vertical-align: middle !important;"><?php echo $term->name; ?></span></div>
                <?php }
                } ?>
        <?php endif; ?>
        </div>


    <div class="dsmart-notify"></div>
    
    <div class="listing-inner">

        <div class="menu-menucard">
            <div class="menu-categories-wrapper">
                <div class="section">
                    <?php $image_id = get_option('ds_logo');
                    if ($image_id != null) {
                        $image = wp_get_attachment_image_src($image_id, 'full'); ?>
                        <div class="ds-logo">
                            <a href="<?php echo get_option('logo_link')?>"><img src="<?php echo $image[0]; ?>" alt="<?php _e("Logo", 'dsmart') ?>" /></a>
                            <?php
                            $place_id = get_option('place_id_map');
                            $theme =get_option('dsmart_theme_style');
                            if ($place_id != null && $place_id !== '') {
                                
                                $api = get_option('dsmart_google_key');
                                $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=$place_id&key=$api";
                                $content = file_get_contents($url);
                                $content = json_decode($content, true);
                                $rating = $content['result']['rating']; 
                                $total = $content['result']['user_ratings_total'];
                                $url_map = $content['result']['url'];
                                if ($rating != null && $rating !== 0) {
                                    echo  "<br>";
                                    ?>
                                        <div style="<?php echo strcmp($theme, "dark") == 0 ? "color:white" : ""?>"><?php echo $rating ?>
                                    <?php
                                    $round = intval(round($rating));
                                    for ($i = 0; $i < $round; $i++) {
                            ?>

                                        <i style="color:#ffe234;margin-left:5px;" class="bi bi-star-fill"></i>
                                    <?php
                                    }

                                    if ($rating > $round) {
                                    ?>
                                        <i style="color:#ffe234" class="bi bi-star-half"></i>
                                        <?php
                                        $diff = round(5 - $rating - 1);
                                        for ($i = 0; $i < $diff; $i++) {
                                        ?>
                                            <i style="color:#ffe234" class="bi bi-star"></i>
                                        <?php
                                        }
                                    } else {
                                        $diff = round(5 - $rating);
                                        for ($i = 0; $i < $diff; $i++) {
                                        ?>
                                            <i style="color:#ffe234" class="bi bi-star"></i>
                                <?php
                                        }
                                    }
                                    ?>
                                    </div>
                                    <?php 
                                        if(strcmp($theme,"dark") == 0){
                                            ?>
                                                <a target="_blank" style="color:white" href="<?php echo $url_map ?>">(<?php echo $total ?> Bewertungen)</a>
                                            <?php
                                        }else{
                                            ?>
                                                <a target="_blank" href="<?php echo $url_map ?>">(<?php echo $total ?> Bewertungen)</a>
                                            <?php
                                        }
                                    ?>
                                        
                                    <?php
                                }
                            }
                            $check1 = check_time_with_time_shop(date("H:i"), null, null, "shipping");
                            $check2 = check_time_with_time_shop(date("H:i"), null, null, "direct");
                            $dsmart_method_ship = get_option('dsmart_method_ship');
                            $dsmart_method_direct = get_option('dsmart_method_direct');
                            if (($dsmart_method_direct == "on" && $check2 || $dsmart_method_ship == "on" && $check1)) {
                                ?>
                                <p style="font-weight: bold;color:green">Jetzt geöffnet!</p>
                                <?php 
                                    if(strcmp($theme,"dark") == 0){
                                        ?>
                                            <span style="font-weight: bold;color:white">Infos und Aktionen</span>
                                        <?php
                                    }else{
                                        ?>
                                            <span style="font-weight: bold">Infos und Aktionen</span>
                                        <?php
                                    }
                                ?>
                                
                                <?php
                                $dsmart_method_ship = get_option('dsmart_method_ship');
                                $dsmart_method_direct = get_option('dsmart_method_direct');
                                ?>
                                    <?php if ($dsmart_method_direct === "on" && $check2) {
                                    ?>
                                        <div class="row" style="justify-content: center;display: flex;">
                                            <?php 
                                                if(strcmp($theme,"dark") == 0){
                                                    ?>
                                                        <i style="color:white" class="bi bi-shop"></i>
                                                        <div style="margin-left: 5px;color:white">Abholung</div>
                                                    <?php
                                                }else{
                                                    ?>
                                                        <i class="bi bi-shop"></i>
                                                        <div style="margin-left: 5px">Abholung</div>
                                                    <?php
                                                }
                                            ?>
                                            
                                        </div>
                                    <?php

                                    } ?>
                                    <?php if ($dsmart_method_ship === "on" && $check1) {

                                    ?>
                                        <div class="row" style="justify-content: center;display: flex;">
                                            <?php 
                                                if(strcmp($theme,"dark") == 0){
                                                    ?>
                                                       <i style="color:white" class="bi bi-truck"></i>
                                                        <div style="margin-left: 5px;color:white">Lieferung</div>
                                                    <?php
                                                }else{
                                                    ?>
                                                        <i class="bi bi-shop"></i>
                                                        <div style="margin-left: 5px">Lieferung</div>
                                                    <?php
                                                }
                                            ?>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                <?php
                            } else {
                                ?>
                                 <?php 
                                    if(strcmp($theme,"dark") == 0){
                                        ?>
                                            <p style="font-weight: bold;color:white">Derzeit sind nur Vorbestellungen möglich!</p>
                                        <?php
                                    }else{
                                        ?>
                                            <p style="font-weight: bold;color:red">Derzeit sind nur Vorbestellungen möglich!</p>
                                        <?php
                                    }
                                ?>
                            <?php
                            }
                            ?>
                        <div class="line-logo" style="border-bottom: 1px dotted;margin-top: 15px;"></div>
                        </div>
                    <?php } ?>
                    <div class="menu-categories">
                        <?php $terms = get_terms(array(
                            'taxonomy' => 'product-cat',
                            'hide_empty' => false,
                            'parent' => 0
                        ));
                        if ($terms) : ?>
                            <ul class="menu-category-list">
                                <?php foreach ($terms as $term) {
                                    if (check_category_open_or_not($term->term_id)) {
                                        if ($current_term->term_id == $term->term_id) {
                                            $class = "active";
                                        } else {
                                            $class = "";
                                        } ?>
                                        <li id="big_menu_side_<?php echo $term->term_id ?>" class="big_menu_side <?php echo $class; ?>"><a href="#" onclick="roll_to_cat(<?php echo $term->term_id ?>)" class=" menu-category <?php echo $class; ?>"><?php echo $term->name; ?></a></li>
                                <?php }
                                } ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <?php if ($show_notify == "on" && $notify_text != "") : ?>
                        <div class="shop-notify shop-mobile">
                            <div class="marquee-parent">
                                <div class="marquee-child">
                                    <?php echo $notify_text; ?>
                                </div>
                            </div>
                            <!-- <marquee onmouseover="this.stop();" onmouseout="this.start();"><?php echo $notify_text; ?></marquee> -->
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="menu-meals">
                <?php foreach ($terms as $term) {
                    if (check_category_open_or_not($term->term_id)) {
                        $tax_query = [];
                        $tax_query[] = array(
                            'taxonomy'  => 'product-cat',
                            'field'     => 'term_id',
                            'terms'     => $term->term_id,
                        );
                        // $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                        $wp_query = new WP_Query(array(
                            'post_type' => 'product',
                            'orderby' => $orderby,
                            'order' => $order,
                            'post_status'    => 'publish',
                            'posts_per_page' => -1,
                            'paged' => $paged,
                            'tax_query' => $tax_query,
                            'meta_query' => $meta_query
                        ));
                        if ($wp_query->have_posts()) : $theme =get_option('dsmart_theme_style');?>
                            <div>
                                <p style="color:rgba(255, 255, 255, 0);">-</p>
                            </div>
                            <div id="link_term_<?php echo $term->term_id ?>">
                                <input type="hidden" name="id" value="<?php echo $term->term_id ?>">
                                <center>
                                    <h2 class="change_when_scroll" style="margin-left: 10px;"><?php echo $term->name ?></h2>
                                    <?php 
                                    if(strcmp($theme,"dark") == 0){
                                        ?>
                                            <p style="color:white;font-weight:bold"><?php echo  $term->description?></p>
                                            <br>
                                        <?php
                                    }else{
                                        ?>
                                            <p style="color:black;font-weight:bold"><?php echo  $term->description?></p>
                                            <br>
                                        <?php
                                    }
                                    ?>
                                </center>
                                <?php if ($term->term_image) {
                                    echo '<div id="image_' . $term->term_id . '" class="tax-banner-2">' . wp_get_attachment_image($term->term_image, 'full') . '</div>';
                                } ?>
                            </div>

                            <div class="list-product">
                                <?php while ($wp_query->have_posts()) : the_post();
                                    if (has_post_thumbnail() && $dsmart_thumbnail != "1") :
                                        $url_img =  wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
                                        $class_img = ($url_img == "") ? "no-img" : "";
                                    else :
                                        $url_img = "";
                                        $class_img = "no-img";
                                    endif;
                                    $array_name = array();
                                    $post_terms = wp_get_post_terms(get_the_ID(), 'product-cat');
                                    if ($post_terms) {
                                        foreach ($post_terms as $item) {
                                            $array_name[] = $item->name;
                                        }
                                    }
                                    $ds_status_product = dsmart_field('status');
                                    $sku = dsmart_field('sku');
                                    $pro_status = dsmart_field('status');
                                    $desc = dsmart_field('desc', get_the_ID());
                                    $sidedish_text = dsmart_field('sidedish_text', get_the_ID());
                                    if($sidedish_text == null || $sidedish_text == '')
                                    {
                                        $sidedish_text = "Beilage";
                                    }
                                    $sharp = intval(dsmart_field('sharp', get_the_ID()));
                                    $price = dsmart_field('price');
                                    $vegetarian = dsmart_field('vegetarian');
                                    $type_promotion = dsmart_field('type_promotion');

                                    $meta['quantity'] = dsmart_field('quantity');
                                    $meta['varialbe_price'] = dsmart_field('varialbe_price');

                                    $meta['extra_name'] = dsmart_field('extra_name');
                                    $meta['extra_type'] = dsmart_field('extra_type');
                                    $meta['extra_price'] = dsmart_field('extra_price');

                                    $meta['sidedish_name'] = dsmart_field('sidedish_name');
                                    $meta['sidedish_price'] = dsmart_field('sidedish_price');
                                    if ($meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']))) :
                                        $price = $meta['varialbe_price'][0];
                                    endif;
                                    if ($meta['sidedish_price'] != null && !empty(array_filter($meta['sidedish_price']))) :
                                        $price = floatval($price) + floatval($meta['sidedish_price'][0]);
                                    endif;
                                ?>
                                    <div class="item <?php echo $class_img; ?>">
                                        <?php
                                        $isExtra = $meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']));
                                        $isSidedish = $meta['sidedish_name'] != null && !empty(array_filter($meta['sidedish_name']));
                                        $isVariable = $meta['quantity'] != null &&  !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']));
                                        if ($url_img != "" && $dsmart_thumbnail != "1") : ?><span class="thumb"><img src="<?php echo $url_img; ?>" alt="<?php the_title(); ?>"></span><?php endif; ?>
                                        <div class="desc">
                                            <div class="content-wrap rowct">
                                                <div class="left-item">
                                                    <h3 class="title">
                                                        <?php the_title(); ?>
                                                        <?php if ($desc != '') : ?>
                                                            <sup><?php echo $desc; ?></sup>
                                                        <?php endif; ?>
                                                        <?php if ($sharp != 0) : ?><span class="sharp"><?php
                                                                                                        for ($i = 1; $i <= $sharp; $i++) { ?>
                                                                    <img src="<?php echo BOOKING_ORDER_PATH . 'img/chili.png'; ?>" alt="" />
                                                                <?php } ?></span><?php endif; ?>
                                                        <?php if ($vegetarian == '1') : ?>
                                                            <span class="leaf"><img src="<?php echo BOOKING_ORDER_PATH . 'img/leaf.png'; ?>" alt="" /></span>
                                                        <?php endif; ?>
                                                        <?php if (!(($dsmart_stock == "1" || ($dsmart_stock != "1" && $ds_status_product == "instock")) && $price != "")) {
                                                            echo '<span class="no-item">Ausverkauft</span>';
                                                        } ?>
                                                    </h3>
                                                    <div class="excerpt"><?php echo get_the_excerpt(); ?></div>
                                                </div>
                                                <div class="right-item">
                                                    <?php if (($dsmart_stock == "1" || ($dsmart_stock != "1" && $ds_status_product == "instock")) && $price != "") :
                                                        if ($isExtra || $isVariable || $isSidedish) : 
                                                            if(!$isVariable){ ?>
                                                                <div class="price"><span style="color:<?php echo $price_color ?> !important"><?php echo ds_price_format_text($price); ?></span></div>
                                                            <?php }?>
                                                        <?php else : ?>
                                                            <div class="price"><span style="color:<?php echo $price_color ?> !important"><?php echo ds_price_format_text($price); ?></span></div>
                                                            <button type="button" class="add-to-cart" style="background-color: <?php echo $button_color?> !important;" data-id="<?php the_ID(); ?>">+</button>
                                                        <?php endif; ?>
                                                    <?php else : ?>
                                                        <?php if (($meta['quantity'] != null &&  !empty(array_filter($meta['quantity']))) || ($meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price'])))) : ?>
                                                            <div class="price"><span style="color:<?php echo $price_color ?> !important"><?php echo ds_price_format_text($price); ?></span></div>
                                                        <?php else : ?>
                                                            <div class="price"><span style="color:<?php echo $price_color ?> !important"><?php echo ds_price_format_text($price); ?></span></div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <?php 
                                            

                                            if (($dsmart_stock == "1" || ($dsmart_stock != "1" && $ds_status_product == "instock")) && $price != "" && ($isExtra || $isVariable || $isSidedish)) : ?>
                                                <div class="variable-product">
                                                    <div class="inner">
                                                        <!-- <h4 class="product-title"><?php the_title(); ?></h4> -->
                                                        <div class="choose-variable">
                                                            <form class="Variable-form">
                                                            <input type="hidden" name="origin_price" value="<?php echo dsmart_field('price');?>">
                                                            <?php
                                                                if ($isSidedish) :
                                                                    echo '<div class="sidedish-product">';
                                                                    echo '<h5 class="sidedish-title">'.$sidedish_text.':</h5>';
                                                                    foreach ($meta['sidedish_name'] as $key => $sidedish) {
                                                                        if ($meta['sidedish_name'][$key] != null) :
                                                                ?>      
                                                                            <label class="custom-radio-checkbox rowct">
                                                                                <input type="radio" name="sidedish_product" <?php echo (($key === 0) ? "checked" : "");?> data-id="<?php echo get_the_ID() . '_' . ($key + 1) . '_sidedish'; ?>" data-quantity="1" data-price="<?php echo $meta['sidedish_price'][$key]; ?>">
                                                                                <span class="text"><?php echo $meta['sidedish_name'][$key] . (isset($meta['sidedish_price'][$key]) && $meta['sidedish_price'][$key] !== "" ? " (+".ds_price_format_text($meta['sidedish_price'][$key]).")" : ""); ?></span>
                                                                            </label>
                                                                <?php endif;
                                                                    }
                                                                    echo '<input type="hidden" name="sidedish_info" value=""/>';
                                                                    echo '</div>';
                                                                endif;
                                                                if ($isExtra) :
                                                                    echo '<div class="extra-product">';
                                                                    echo '<h5 class="extra-title">Ihre Extras:</h5>';
                                                                    foreach ($meta['extra_name'] as $key => $extra) {
                                                                        $extra_type = dsmart_field('extra_type' . ($key + 1));
                                                                        if ($meta['extra_name'][$key] != null && $meta['extra_price'][$key] != null) :
                                                                ?>
                                                                            <label class="custom-radio-checkbox rowct">
                                                                                <input class="ccenter" type="checkbox" name="extra_product" data-id="<?php echo get_the_ID() . '_' . ($key + 1) . '_extra'; ?>" data-quantity="<?php echo ($extra_type == 'tick') ? 1 : ''; ?>" data-price="<?php echo $meta['extra_price'][$key]; ?>">
                                                                                <span class="text"><?php echo $meta['extra_name'][$key] . ' (+' . ds_price_format_text($meta['extra_price'][$key]) . ')'; ?></span>
                                                                                <?php if ($extra_type != 'tick') : ?>
                                                                                    <div class="extra-quantity quantity-wrap flex-list">
                                                                                        <button type="button" class="minus"><i class="fa fa-minus" aria-hidden="true"></i></button>
                                                                                        <input type="number" name="quantity" class="form-control input-quantity" value="1" min="1">
                                                                                        <button type="button" class="plus"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                                                                    </div>
                                                                                <?php endif; ?>
                                                                            </label>
                                                                <?php endif;
                                                                    }
                                                                    echo '<input type="hidden" name="extra_info" value="" />';
                                                                    echo '</div>';
                                                                endif; 
                                                                if ($isVariable) : 
                                                                ?>
                                                                <div class="select-wrap">
                                                                    <select class="variable-select nice-select-custom">
                                                                        <?php foreach ($meta['quantity'] as $index => $value) {
                                                                            if ($meta['quantity'][$index] != null && $meta['varialbe_price'][$index] != null) : ?>
                                                                                <option value="<?php echo get_the_ID() . '_' . ($index + 1) . '_variable'; ?>" data-price="<?php echo $meta['varialbe_price'][$index]; ?>" data-id="<?php echo get_the_ID() . '_' . ($index + 1) . '_variable'; ?>">
                                                                                    <?php echo $meta['quantity'][$index] . ': ' . ds_price_format_text($meta['varialbe_price'][$index]); ?>
                                                                                </option>
                                                                        <?php endif;
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                                <?php endif; ?>
                                                                <div class="quantity-submit flex-list">
                                                                    <div class="total-quantity quantity-wrap flex-list">
                                                                        <button type="button" class="minus"><i class="fa fa-minus" aria-hidden="true"></i></button>
                                                                        <input type="number" name="quantity" class="form-control input-quantity" value="1" min="1">
                                                                        <button type="button" class="plus"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                                                    </div>
                                                                    <?php 
                                                                    if ($isVariable){
                                                                        $price2 = $meta['varialbe_price'][0];
                                                                    }
                                                                    else
                                                                    {
                                                                        $price2 = dsmart_field('price');
                                                                    }
                                                                    if ($meta['sidedish_price'] != null && !empty(array_filter($meta['sidedish_price']))) :
                                                                        $price2 = floatval($price2) + floatval($meta['sidedish_price'][0]);
                                                                    endif;
                                                                    ?>
                                                                    <button type="submit" class="add-to-cart" style="background-color: <?php echo $button_color?> !important;"  data-id="<?php the_ID(); ?>"><?php  echo ds_price_format_text($price2) ?></button>
                                                                </div>
                                                                
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endwhile;
                                echo get_the_posts_pagination(array('mid_size' => 2, 'prev_text' => __('Bisherige', "dsmart"), 'next_text' => __('Nächste', "dsmart")));
                                wp_reset_query(); ?>

                            </div>
                        <?php else : ?>

                        <?php endif; ?>
                <?php
                    }
                }
                ?>
                <?php if ($dsmart_taxonomy_text != "") : ?><div class="more-text">
                        <?php echo $dsmart_taxonomy_text; ?>
                    </div><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="book-loading"><img src="<?php echo plugin_dir_url(__FILE__) . 'img/loading.gif'; ?>"></div>
<?php get_footer(); ?>