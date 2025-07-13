<?php 
//check coupon available
function check_coupon_available($coupon){
	global $wpdb;
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $coupon . "'" );
	if($postid != null){
		$current_user = wp_get_current_user();
		$current_date = date('d-m-Y');
		$coupon_option = dsmart_field('coupon_option',$postid);
		$coupon_value = dsmart_field('coupon_value',$postid);
		$max_coupon_value = dsmart_field('max_coupon_value',$postid);
		$min_order_apply = dsmart_field('min_order_apply',$postid);
		$coupon_number_use = dsmart_field('coupon_number_use',$postid);
		$coupon_date_begin = dsmart_field('coupon_date_begin',$postid);
		$coupon_date_end = dsmart_field('coupon_date_end',$postid);
		$coupon_for = dsmart_field('coupon_for',$postid);
		if($coupon_option == "" || $coupon_value == ""){
			return 2;
		}elseif($coupon_number_use == "0"){
			return 2;
		}elseif($coupon_date_begin != "" && strtotime($coupon_date_begin) >= strtotime($current_date)){
			return 3;
		}elseif($coupon_date_end != "" && strtotime($coupon_date_end) <= strtotime($current_date)){
			return 4;
		// }elseif($coupon_for != "" && !in_array($current_user->ID,$coupon_for)){
		// 	return 5;
		}else{
			return 1;
		}
	}else{
		return 2;
	}
}
function get_coupon_id_from_code($coupon){
	global $wpdb;
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $coupon . "'" );
	return $postid;
}
function show_coupon_notify($notify = null){
	if($notify == "1"){ ?>
		<div class="alert alert-success"><?php _e('Rabattcode hinzugefügt.','dsmart'); ?></div>
	<?php }elseif($notify == "2"){ ?>
		<div class="alert alert-danger"><?php _e('Gutschein existiert nicht.','dsmart'); ?></div>
	<?php }elseif($notify == "3"){ ?>
		<div class="alert alert-danger"><?php _e('Gutschein kann nicht verwendet werden.','dsmart'); ?></div>
	<?php }elseif($notify == "4"){ ?>
		<div class="alert alert-danger"><?php _e('Gutschein ist abgelaufen.','dsmart'); ?></div>
	<?php }elseif($notify == "5"){ ?>
		<div class="alert alert-danger"><?php _e('Gutschein ist nicht für Sie.','dsmart'); ?></div>
	<?php }elseif($notify == "6"){ ?>
		<div class="alert alert-success"><?php _e('Gutschein wurde gelöscht.','dsmart'); ?></div>
	<?php }elseif($notify == "7"){ ?>
		<div class="alert alert-danger"><?php _e('Gutschein kann nicht mit anderen Gutscheinen verwendet werden.','dsmart'); ?></div>
	<?php }elseif($notify == "8"){ ?>
		<div class="alert alert-danger"><?php _e('Sie haben diesen Gutschein bereits in den Warenkorb gelegt.','dsmart'); ?></div>
	<?php }
}
function get_price_of_coupon($coupon,$total = null){
	global $wpdb;
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $coupon . "'" );
	$coupon_option = dsmart_field('coupon_option',$postid);
	$coupon_value = dsmart_field('coupon_value',$postid);
	$max_coupon_value = dsmart_field('max_coupon_value',$postid);
	$min_order_apply = dsmart_field('min_order_apply',$postid);
	if(isset($_COOKIE['cart']) && $_COOKIE['cart'] != ""){
		$cart = unserialize(base64_decode($_COOKIE['cart']));
	}else{
		$cart = array();
	}
	$tax = get_option('dsmart_tax');
	$coupon_price = 0;
	if($cart){
		if($total == null){
			$total_all = 0;
			foreach ($cart as $key_item => $value_item) {
				$price_item = dsmart_field('price',$key_item);
				$status_item = dsmart_field('status',$key_item);
				$quantity = $value_item['quantity'];
				if($status_item == "instock" && $price_item != ""){
					$price = ds_caculate_item_price($key_item,$quantity);
					$total_all = $total_all + $price;
				}
			}
			if($tax != ""){
				$total_all = $total_all + intval($tax)*$total_all/100;
			}
		}else{
			$total_all = $total;
		}
		if($min_order_apply == "" || ($min_order_apply != "" && ds_convert_price($min_order_apply) <= $total_all)){
			if($coupon_value != "" && $coupon_option != ""){
				if($coupon_option == "percent"){
					$coupon_price = $total_all*floatval($coupon_value)/100;
				}else{
					$coupon_price = ds_convert_price($coupon_value);
				}
				if($max_coupon_value != "" && ds_convert_price($max_coupon_value) < $coupon_price){
					$coupon_price = ds_convert_price($max_coupon_value);
				}
			}
		}
	}
	return $coupon_price;
}
?>