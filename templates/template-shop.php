<?php if(get_option('redirect_link_shop') != ""){
    wp_redirect(get_option('redirect_link_shop'));
    exit;
}
$post_terms = wp_get_post_terms(12993,'product-cat',array( 'fields' => 'ids' ));

$array = array(456 => '123',789 => '456','test 3' => '456',123 => '456');
$array1 = array(0,0,0,1,2,3,3,3,5,7);
//$inserted = array(4123 => 'kael');
//$res = array_merge(array_slice($array, 0, 1, true),$inserted,array_slice($array, 0, count($array), true)) ;
// var_dump($array1);
global $wp_query;
$dsmart_thumbnail = get_option('dsmart_thumbnail');
$dsmart_stock = get_option('dsmart_stock');
$current_term = get_queried_object();
$tax_query = array();
$meta_query = array();
if(isset($_GET['price']) && $_GET['price'] != ""){
    $price_input = $_GET['price'];
    $price_arr = explode(';',$price_input);
    $dsmart_currency = get_option('dsmart_currency');
    if($dsmart_currency == "2"){
        $currency = "€";
    }else{
        $currency = "$";
    }
    $dsmart_currency_rate = get_option('dsmart_currency_rate');
    if($dsmart_currency_rate != ""){
        $currency_rate = floatval($dsmart_currency_rate);
    }else{
        $currency_rate = 1;
    }
    if($currency == "$"){
        
    }else{
        $price_arr[0] = $price_arr[0]/$currency_rate;
        $price_arr[1] = $price_arr[1]/$currency_rate;
    }
    $meta_query[] = array('key'  => 'price','value' => $price_arr, 'compare'   => 'BETWEEN','type' => 'NUMERIC');

}else{
    $price_input = "";
}
if(isset($_GET['rating']) && $_GET['rating'] != ""){
    $input_rating = $_GET['rating'];
    $meta_query[] = array(
        'key'  => 'avg_rating',
        'value'     => $input_rating,
        'compare' => '>=',
        'type' => 'NUMERIC'
    );
}else{
    $status = "";

}
$all_category_not_open = get_all_category_not_open();
if(count($all_category_not_open) > 0){
    $tax_query[] = array(
        'taxonomy'  => 'product-cat',
        'field'     => 'term_id',
        'terms'     => $all_category_not_open,
        'operator'  => 'NOT IN' 
    );
}
if(count($tax_query)>0){
    $tax_query['relation'] = 'AND';
}else {
    $tax_query = null;
}
if(count($meta_query)>0){
    $meta_query['relation'] = 'AND';
}else {
    $meta_query = null;
}
$order = get_option('dsmart_order');
if($order == ""){
    $order = "DESC";
}
$orderby = get_option('dsmart_orderby');
if($orderby == ""){
    $orderby = "date";
}
$dsmart_taxonomy_text = get_option('dsmart_taxonomy_text');
$show_notify = get_option('show_notify');
$notify_text = get_option('notify_text');
get_header(); ?>
<div class="container shop-content">
    <?php if(has_post_thumbnail()):
        $current_img =  wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
        echo '<div class="tax-banner"><img src="'.$current_img.'" alt=""/></div>';
    endif;?>
    <?php if($show_notify == "on" && $notify_text != ""): ?>
        <div class="shop-notify">
            <div class="marquee-parent">
                <div class="marquee-child">
                    <?php echo $notify_text; ?>
                </div>
            </div>
            <!-- <marquee onmouseover="this.stop();" onmouseout="this.start();"><?php echo $notify_text; ?></marquee> -->
        </div>
    <?php endif; ?>
    <div class="dsmart-notify"></div>
    <div class="listing-inner">
        <div class="menu-menucard">
            <div class="menu-categories-wrapper">
                <div class="section">
                    <?php $image_id = get_option( 'ds_logo' );
                    if($image_id != null && isset(wp_get_attachment_image_src( $image_id , 'full' )[0]) && wp_get_attachment_image_src( $image_id , 'full' )[0] != null){
                        $image = wp_get_attachment_image_src( $image_id , 'full' ); ?>
                        <div class="ds-logo">
                            <img src="<?php echo $image[0]; ?>" alt="<?php _e("Logo",'dsmart') ?>"/>
                        </div>
                    <?php }?>
                    <div class="menu-categories">
                        <?php $terms = get_terms( array(
                            'taxonomy' => 'product-cat',
                            'hide_empty' => false,
                            'parent' => 0
                        ) );
                        if($terms): ?>
                            <ul class="menu-category-list">
                                <?php foreach ($terms as $term) {
                                    if(check_category_open_or_not($term->term_id)){
                                        if($current_term->term_id == $term->term_id){
                                            $class = "active";
                                        }else{
                                            $class = "";
                                        } ?>
                                        <li class="<?php echo $class; ?>"><a href="<?php echo get_term_link($term->term_id); ?>" class="menu-category <?php echo $class; ?>"><?php echo $term->name; ?></a></li>
                                    <?php }
                                } ?>
                            </ul>
                        <?php endif;?>
                    </div>
                    <?php if($show_notify == "on" && $notify_text != ""): ?>
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
                <?php 
                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                $wp_query = new WP_Query(array(
                    'post_type' => 'product',
                    'orderby' => $orderby,
                    'order' => $order, 
                    'post_status'    => 'publish',
                    'posts_per_page' => 10,
                    'paged' => $paged,
                    'tax_query' => $tax_query,
                    'meta_query' => $meta_query           
                ));
                if($wp_query->have_posts()): ?>
                    <div class="list-product">
                        <?php while($wp_query->have_posts()): the_post();
                            if(has_post_thumbnail() && $dsmart_thumbnail != "1"):
                                $url_img =  wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
                                $class_img = ($url_img == "") ? "no-img" : "";
                            else:
                                $url_img = "";
                                $class_img = "no-img";
                            endif;
                             $array_name = array();
                            $post_terms = wp_get_post_terms( get_the_ID(), 'product-cat');
                            if($post_terms){
                                foreach ($post_terms as $item) {
                                    $array_name[] = $item->name;
                                }
                            }
                            $sku = dsmart_field('sku');
                            $ds_status_product = dsmart_field('status');
                            $desc = dsmart_field('desc',get_the_ID());
                            $sharp = intval(dsmart_field('sharp',get_the_ID()));
                            $price = dsmart_field('price');
                            $vegetarian = dsmart_field('vegetarian');
                            $type_promotion = dsmart_field('type_promotion');

                            $meta['quantity'] = dsmart_field('quantity');
                            $meta['varialbe_price'] = dsmart_field('varialbe_price');

                            $meta['extra_name'] = dsmart_field('extra_name');
                            $meta['extra_type'] = dsmart_field('extra_type');
                            $meta['extra_price'] = dsmart_field('extra_price'); 
                            if($meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']))): 
                                $price = $meta['varialbe_price'][0];
                            endif;  ?>
                            <div class="item <?php echo $class_img; ?>">
                                <?php if($url_img != "" && $dsmart_thumbnail != "1"): ?><span class="thumb"><img src="<?php echo $url_img; ?>" alt="<?php the_title(); ?>"></span><?php endif; ?>
                                <div class="desc">
                                    <div class="content-wrap rowct">
                                        <div class="left-item">
                                            <h3 class="title">
                                                <?php the_title(); ?>
                                                <?php if($desc != ''): ?>
                                                    <sup><?php echo $desc; ?></sup>
                                                <?php endif; ?>    
                                                <?php if($sharp != 0): ?><span class="sharp"><?php 
                                                for ($i=1; $i <= $sharp; $i++) { ?>
                                                    <img src="<?php echo BOOKING_ORDER_PATH.'img/chili.png'; ?>" alt=""/>
                                                <?php }?></span><?php endif;?>
                                                <?php if($vegetarian == '1'): ?>
                                                    <span class="leaf"><img src="<?php echo BOOKING_ORDER_PATH.'img/leaf.png'; ?>" alt=""/></span>
                                                <?php endif; ?>    
                                                <?php if(!(($dsmart_stock == "1" || ($dsmart_stock != "1" && $ds_status_product == "instock")) && $price != "")){
                                                    echo '<span class="no-item">Ausverkauft</span>';
                                                } ?>
                                            </h3>
                                            <div class="excerpt"><?php echo get_the_excerpt(); ?></div>
                                        </div>
                                        <div class="right-item">
                                            <?php if(($dsmart_stock == "1" || ($dsmart_stock != "1" && $ds_status_product == "instock")) && $price != ""): 
                                                if(($meta['quantity'] != null &&  !empty(array_filter($meta['quantity']))) || ($meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price'])))): ?>
                                                    <div class="price"><span><?php echo ds_price_format_text($price); ?></span></div>
                                                <?php else: ?>                                                    
                                                    <div class="price"><span><?php echo ds_price_format_text($price); ?></span></div>
                                                    <button type="button" class="add-to-cart" data-id="<?php the_ID(); ?>">+</button>
                                                <?php endif; ?>  
                                            <?php else: ?>
                                                <?php if(($meta['quantity'] != null &&  !empty(array_filter($meta['quantity']))) || ($meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price'])))): ?>
                                                    <div class="price"><span><?php echo ds_price_format_text($price); ?></span></div>
                                                <?php else: ?>                                                    
                                                    <div class="price"><span><?php echo ds_price_format_text($price); ?></span></div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                     <?php if(($dsmart_stock == "1" || ($dsmart_stock != "1" && $ds_status_product == "instock")) && $price != "" && $meta['quantity'] != null &&  !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']))): ?>
                                        <div class="variable-product">
                                            <div class="inner">
                                                <h4 class="product-title"><?php the_title(); ?></h4>
                                                <div class="choose-variable">
                                                   <form class="Variable-form">
                                                         <?php 
                                                         if($meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']))):
                                                            echo '<div class="extra-product">';
                                                            echo '<h5 class="extra-title">Ihre Extras:</h5>';
                                                            foreach ($meta['extra_name'] as $key => $extra) {
                                                                $extra_type = dsmart_field('extra_type'.($key+1)); ?>
                                                                <label class="custom-radio-checkbox rowct">
                                                                    <input type="checkbox" name="extra_product" data-id="<?php echo get_the_ID().'_'.($key+1).'_extra'; ?>" data-quantity="<?php echo ($extra_type == 'tick')? 1 : ''; ?>" data-price="<?php echo $meta['extra_price'][$key]; ?>"> 
                                                                    <span class="text"><?php echo $meta['extra_name'][$key] .' (+'. ds_price_format_text($meta['extra_price'][$key]) .')'; ?></span> 
                                                                    <?php if($extra_type != 'tick'): ?>
                                                                        <div class="extra-quantity quantity-wrap flex-list">
                                                                            <button type="button" class="minus"><i class="fa fa-minus" aria-hidden="true"></i></button>
                                                                            <input type="number" name="quantity" class="form-control input-quantity" value="1" min="1">
                                                                            <button type="button" class="plus"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                                                        </div>
                                                                    <?php endif; ?>    
                                                                </label>
                                                            <?php ; } 
                                                            echo '<input type="hidden" name="extra_info" value="" />';
                                                            echo '</div>';
                                                        endif; ?>
                                                        <div class="select-wrap">
                                                            <select class="variable-select">
                                                               <?php foreach ($meta['quantity'] as $index => $value) { ?>
                                                                    <option value="<?php echo $meta['varialbe_price'][$index]; ?>" data-id="<?php echo get_the_ID().'_'.($index+1).'_variable'; ?>" >
                                                                        <?php echo $meta['quantity'][$index] .': '. ds_price_format_text($meta['varialbe_price'][$index]) ; ?>
                                                                    </option>
                                                               <?php } ?>
                                                            </select>
                                                            <i class="fa fa-angle-down" aria-hidden="true"></i>
                                                        </div>                                                       
                                                        <div class="quantity-submit flex-list">
                                                            <div class="total-quantity quantity-wrap flex-list">
                                                                <button type="button" class="minus"><i class="fa fa-minus" aria-hidden="true"></i></button>
                                                                <input type="number" name="quantity" class="form-control input-quantity" value="1" min="1">
                                                                <button type="button" class="plus"><i class="fa fa-plus" aria-hidden="true"></i></button>                                                            
                                                            </div>
                                                            <button type="submit" class="add-to-cart" data-id="<?php the_ID(); ?>"><?php echo ds_price_format_text($meta['varialbe_price'][0]); ?></button>
                                                        </div>
                                                   </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>    
                                </div>
                            </div>
                        <?php endwhile;
                        echo get_the_posts_pagination( array( 'mid_size' => 2,'prev_text' => __('Bisherige',"dsmart"),'next_text' => __('Nächste',"dsmart") ));
                        wp_reset_query();?>
						<?php if($dsmart_taxonomy_text !=""): ?><div class="more-text">
							<?php echo $dsmart_taxonomy_text; ?>
						</div><?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger"><?php _e("No product.","dsmart") ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>   
</div>
<div class="book-loading"><img src="<?php echo plugin_dir_url( __FILE__ ).'img/loading.gif'; ?>"></div>
<?php get_footer(); ?>