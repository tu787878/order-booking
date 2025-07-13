<?php

function display_variable($isVariable, $variable_id, $product_id){
    $meta['quantity'] = dsmart_field('quantity',$product_id);
    $meta['varialbe_price'] = dsmart_field('varialbe_price',$product_id);
    if($isVariable): ?>
        <div class="item">
            <h5><?php _e( 'AUSGEWÄHLTE PRODUKT', 'dsmart'); ?></h5>
            <p><?php echo $meta['quantity'][$variable_id] .': '. ds_price_format_text($meta['varialbe_price'][$variable_id]) ; ?></p>
        </div>
        <?php endif;
}

function display_extra($isExtra, $product_id, $extra_info){
    $meta['extra_name'] = dsmart_field('extra_name',$product_id);
    $meta['extra_price'] = dsmart_field('extra_price',$product_id);
    if($isExtra): ?>
        <div class="item">
            <h5><?php _e( 'EXTRA', 'dsmart'); ?></h5>											
            <ul>
                <?php foreach ($extra_info as $extra_key => $extra_value) { 
                    $extra_id = intval(explode('_', $extra_value->extra_id)[1])-1;
                    $extra_quantity = $extra_value->extra_quantity; ?>
                    <li><?php echo $meta['extra_name'][$extra_id] .'(+'. ds_price_format_text($meta['extra_price'][$extra_id]) .') x ' . $extra_quantity; ?></li>
                <?php } ?>														
            </ul>											
        </div>
    <?php endif; 
}

function display_sidedish($isSidedish, $product_id, $sidedish_info){
    // var_dump($sidedish_info);
    $meta['sidedish_name'] = dsmart_field('sidedish_name',$product_id);
    $meta['sidedish_price'] = dsmart_field('sidedish_price',$product_id);
    $sidedish_text = dsmart_field('sidedish_text', $product_id);
    if($sidedish_text == null || $sidedish_text == '')
    {
        $sidedish_text = "Beilage";
    }
    if($isSidedish): ?>
        <div class="item">
            <h5><?php _e( $sidedish_text, 'dsmart'); ?></h5>											
            <ul>
                <?php foreach ($sidedish_info as $extra_key => $sidedish_value) { 
                    $sidedish_id = intval(explode('_', $sidedish_value->sidedish_id)[1])-1;
                    ?>
                    <li><?php echo $meta['sidedish_name'][$sidedish_id] . (isset($meta['sidedish_price'][$sidedish_id]) && $meta['sidedish_price'][$sidedish_id] !== "" ? " (+".ds_price_format_text($meta['sidedish_price'][$sidedish_id]).")" : "");?></li>
                <?php } ?>														
            </ul>											
        </div>
    <?php endif; 
}

function has_sidedish($value_item){
    $product_id = intval($value_item['product_id']);

    $meta['sidedish_name'] = dsmart_field('sidedish_name',$product_id);

    return isset($value_item['sidedish_info']) && $value_item['sidedish_info'] != null && $meta['sidedish_name'] != null && !empty(array_filter($meta['sidedish_name']));
}

function has_extra($value_item){
    $product_id = intval($value_item['product_id']);

    $meta['extra_name'] = dsmart_field('extra_name',$product_id);
    $meta['extra_price'] = dsmart_field('extra_price',$product_id);

    return isset($value_item['extra_info']) && $value_item['extra_info'] != null && $meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']));
}

function has_variable($value_item){
    $product_id = intval($value_item['product_id']);
    $meta['quantity'] = dsmart_field('quantity',$product_id);
    $meta['varialbe_price'] = dsmart_field('varialbe_price',$product_id);

    return isset($value_item['variable_id']) && $meta['quantity'] != null &&  !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']));
}
function get_extra_variable_info($value_item){
    $product_id = intval($value_item['product_id']);
    $quantity = $value_item['quantity'];

    $meta['quantity'] = dsmart_field('quantity',$product_id);
    $meta['varialbe_price'] = dsmart_field('varialbe_price',$product_id);

    $meta['sidedish_name'] = dsmart_field('sidedish_name',$product_id);
    $meta['sidedish_price'] = dsmart_field('sidedish_price',$product_id);

    $meta['extra_name'] = dsmart_field('extra_name',$product_id);
    $meta['extra_price'] = dsmart_field('extra_price',$product_id);
    $extra_price = 0;
    if(isset($value_item['extra_info']) && $value_item['extra_info'] != null && $meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']))):
        $extra_info = json_decode(stripslashes($value_item['extra_info'])); 
        foreach ($extra_info as $extra_key => $extra_value) { 
            $extra_id = intval(explode('_', $extra_value->extra_id)[1])-1;
            $extra_quantity = $extra_value->extra_quantity;
            $temp_price = $meta['extra_price'][$extra_id];
            $temp_price = floatval($temp_price)*intval($extra_quantity);
            $extra_price = $extra_price + $temp_price;
        }
    else:
        $extra_info = [];
        $extra_price = 0;
    endif;				
    
    $sidedish_price = 0;
    if(isset($value_item['sidedish_info']) && $value_item['sidedish_info'] != null && $meta['sidedish_name'] != null && !empty(array_filter($meta['sidedish_name']))):
        $sidedish_info = json_decode(stripslashes($value_item['sidedish_info']));
        foreach ($sidedish_info as $sidedish_key => $sidedish_value) { 
            $sidedish_id = intval(explode('_', $sidedish_value->sidedish_id)[1])-1;
            $temp_price = $meta['sidedish_price'][$sidedish_id];
            if(isset($temp_price) && $temp_price != null && $temp_price != ""){
                $sidedish_price = floatval($temp_price);
            }
            else{
                $sidedish_price = 0;
            }
        }
    else:
        $sidedish_info = [];
        $sidedish_price = 0;
    endif;	

    if(isset($value_item['variable_id']) && $meta['quantity'] != null &&  !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']))):
        $variable_id = intval(explode('_', $value_item['variable_id'])[1])-1;
        $price_item =  floatval($meta['varialbe_price'][$variable_id])+$extra_price+$sidedish_price;
        $price 		= ds_convert_price($price_item)*intval($quantity);
    else:
        $variable_id = '';
        $price_item = ds_convert_price(dsmart_field('price', $product_id))+$extra_price+$sidedish_price;
        $price 		= ds_caculate_item_price($product_id,$quantity) + ($extra_price+$sidedish_price)*intval($quantity);
    endif;	

    $ret = [];
    $ret["sidedish_info"] = $sidedish_info;
    $ret["extra_info"] = $extra_info;
    $ret["extra_price"] = $extra_price;
    $ret["variable_id"] = $variable_id;
    $ret["price_item"] = $price_item;
    $ret["price"] = $price;

    return $ret;
}

function make_cart_items_available($cart){
    $all_category_not_open = get_all_category_not_open();
    if(count($all_category_not_open) > 0 && count($cart) > 0){
        foreach ($cart as $key_item => $value_item) {
             $cats = wp_get_post_terms($value_item['product_id'],'product-cat', array( 'fields' => 'ids' ));
             if(count($cats) > 0){
                 foreach ($cats as $cat) {
                     if(in_array($cat, $all_category_not_open)){
                         unset($cart[$key_item]);
                     }
                 }
             }
        }
        setcookie('cart', base64_encode(serialize($cart)), time()+2592000, '/', NULL, 0);
    }
    return $cart;
}

function is_cart_items_available($cart){
    $all_category_not_open = get_all_category_not_open();
    if(count($all_category_not_open) > 0 && count($cart) > 0){
        foreach ($cart as $key_item => $value_item) {
             $cats = wp_get_post_terms($value_item['product_id'],'product-cat', array( 'fields' => 'ids' ));
             if(count($cats) > 0){
                 foreach ($cats as $cat) {
                     if(in_array($cat, $all_category_not_open)){
                         return false;
                     }
                 }
             }
        }
    }
    return true;
}

function get_all_category_not_open()
{
    $terms = get_terms(array(
        'taxonomy' => 'product-cat',
        'hide_empty' => false,
        'parent' => 0
    ));
    $excluded = array();
    foreach ($terms as $term) {
        if (check_category_open_or_not($term->term_id) == false) {
            $excluded[] = $term->term_id;
        }
    }
    return $excluded;
}

function check_shipping_time_new(){
    $zxc_time   = date('H:i');
    $time = new DateTime($zxc_time);
    $close_shop = get_option('dsmart_close_shop');
}

function check_direct_time_new(){

}

?>