<?php 
if(isset($_POST['submit-setting'])){
	$dsmart_min_order = stripslashes_deep($_POST['dsmart_min_order']);
	$dsmart_distance = stripslashes_deep($_POST['dsmart_distance']);
	$time_open_shop = isset($_POST['time_open_shop']) ? $_POST['time_open_shop'] : '';
	$time_close_shop = isset($_POST['time_close_shop']) ? $_POST['time_close_shop'] : '';
	$dsmart_min_order_free = $_POST['dsmart_min_order_free'];
	$dsmart_shipping_fee = $_POST['dsmart_shipping_fee'];
	$delay_time = $_POST['delay_time'];
	$dsmart_shipping_from = $_POST['dsmart_shipping_from'];
	$dsmart_shipping_to = $_POST['dsmart_shipping_to']; 
	$dsmart_shipping_cs_fee = $_POST['dsmart_shipping_cs_fee'];
	$dsmart_custom_method = isset($_POST['dsmart_custom_method']) ? $_POST['dsmart_custom_method'] : '';
	$dsmart_barzahlung = $_POST['dsmart_custom_method'];
	$dsmart_paypal = isset($_POST['dsmart_paypal']) ? $_POST['dsmart_paypal'] : '';
	$dsmart_klarna = isset($_POST['dsmart_klarna']) ? $_POST['dsmart_klarna'] : '';
	// close shop
	//=======================================================
	if(isset($_POST['close_shop'])):
		$close_shop = $_POST['close_shop'];
	else:
		$close_shop = null;
	endif;	
	$custom_date = $_POST['custom_date'];
	$custom_open_time = $_POST['custom_open_time'];
	$custom_close_time = $_POST['custom_close_time'];

	// float cart
	//=======================================================
	if(isset($_POST['float_cart'])){
		$float_cart = $_POST['float_cart'];
	}else{
		$float_cart = '';
	}	

	$dsmart_tax = $_POST['dsmart_tax'];
	// shipping method
	//=======================================================
	if(isset($_POST['dsmart_method_ship'])){
		$dsmart_method_ship = $_POST['dsmart_method_ship'];
	}else{
		$dsmart_method_ship = '';
	}	
	if(isset($_POST['dsmart_method_direct'])){
		$dsmart_method_direct = $_POST['dsmart_method_direct'];
	}else{
		$dsmart_method_direct = '';
	}	

	//show in list product page
	//==========================================================
	$dsmart_taxonomy_text = $_POST['dsmart_taxonomy_text'];

	//custom customer mail
	//=======================================================
	$dsmart_header_mail_order_cs = $_POST['dsmart_header_mail_order_cs'];
	$dsmart_header_mail_success_cs = $_POST['dsmart_header_mail_success_cs'];
	$dsmart_header_mail_cancel_cs = $_POST['dsmart_header_mail_cancel_cs'];
	//==========================================================
	$dsmart_text_mail_order_cs = $_POST['dsmart_text_mail_order_cs'];
	$dsmart_text_mail_success_cs = $_POST['dsmart_text_mail_success_cs'];
	$dsmart_text_mail_cancel_cs = $_POST['dsmart_text_mail_cancel_cs'];
	//==========================================================
	$dsmart_thankyou_text = stripslashes_deep($_POST['dsmart_thankyou_text']);
	//==========================================================
	$dsmart_term_text = stripslashes_deep($_POST['dsmart_term_text']);
	//==========================================================
	$dsmart_cart_text = stripslashes_deep($_POST['dsmart_cart_text']);

	//promotion discount
	//=======================================================
	$type_promotion = $_POST['type_promotion'];
	$promotion = $_POST['promotion'];

	$type_promotion_2 = $_POST['type_promotion_2'];
	$promotion_2 = $_POST['promotion_2'];
	//discount type
	//=======================================================
	if(isset($_POST['discount_cod'])){
		$discount_cod = $_POST['discount_cod'];
	}else{
		$discount_cod = '';
	}

	if(isset($_POST['discount_shop'])){
		$discount_shop = $_POST['discount_shop'];
	}else{
		$discount_shop = '';
	}

	update_option('float_cart',$float_cart,'yes');
	update_option('dsmart_tax',$dsmart_tax,'yes');
	update_option( 'dsmart_close_shop', $close_shop, 'yes' );
	update_option( 'dsmart_distance', $dsmart_distance, 'yes' );
	update_option( 'time_open_shop', $time_open_shop, 'yes' );
	update_option( 'time_close_shop', $time_close_shop, 'yes' );
	update_option( 'dsmart_min_order', $dsmart_min_order, 'yes' );
	update_option( 'dsmart_min_order_free', $dsmart_min_order_free, 'yes' );
	update_option( 'dsmart_shipping_fee', $dsmart_shipping_fee, 'yes' );
	update_option('dsmart_method_direct',$dsmart_method_direct,'yes');
	update_option('dsmart_method_ship',$dsmart_method_ship,'yes');
	update_option('dsmart_shipping_from',$dsmart_shipping_from,'yes');
	update_option('dsmart_shipping_to',$dsmart_shipping_to,'yes');
	update_option('dsmart_shipping_cs_fee',$dsmart_shipping_cs_fee,'yes');
	update_option('dsmart_custom_method',$dsmart_custom_method,'yes');
	update_option('dsmart_paypal',$dsmart_paypal,'yes');
	update_option('dsmart_klarna',$dsmart_klarna,'yes');

	//show in list product page
	//==========================================================
	update_option('dsmart_taxonomy_text',$dsmart_taxonomy_text,'yes');

	//custom customer mail
	//=======================================================
	update_option('dsmart_header_mail_order_cs',$dsmart_header_mail_order_cs,'yes');
	update_option('dsmart_header_mail_success_cs',$dsmart_header_mail_success_cs,'yes');
	update_option('dsmart_header_mail_cancel_cs',$dsmart_header_mail_cancel_cs,'yes');
	//==========================================================
	update_option('dsmart_text_mail_order_cs',$dsmart_text_mail_order_cs,'yes');
	update_option('dsmart_text_mail_success_cs',$dsmart_text_mail_success_cs,'yes');
	update_option('dsmart_text_mail_cancel_cs',$dsmart_text_mail_cancel_cs,'yes');
	//==========================================================
	update_option('dsmart_thankyou_text',$dsmart_thankyou_text,'yes');
	//==========================================================
	update_option('dsmart_term_text',$dsmart_term_text,'yes');
	//==========================================================
	update_option('dsmart_cart_text',$dsmart_cart_text,'yes');

	//promotion discount
	//=======================================================
	update_option('type_promotion',$type_promotion,'yes');
	update_option('promotion', $promotion, 'yes');

	update_option('type_promotion_2',$type_promotion_2,'yes');
	update_option('promotion_2', $promotion_2, 'yes');

	//mon
	//==========================================================
	$time_open_shop_mo = $_POST['time_open_shop_mo'];
	$time_close_shop_mo = $_POST['time_close_shop_mo'];
	update_option( 'time_open_shop_mo', $time_open_shop_mo, 'yes' );
	update_option( 'time_close_shop_mo', $time_close_shop_mo, 'yes' );

	$time_open_shop_2_mo = $_POST['time_open_shop_2_mo'];
	$time_close_shop_2_mo = $_POST['time_close_shop_2_mo'];
	update_option( 'time_open_shop_2_mo', $time_open_shop_2_mo, 'yes' );
	update_option( 'time_close_shop_2_mo', $time_close_shop_2_mo, 'yes' );

	//tue
	//==========================================================
	$time_open_shop_tu = $_POST['time_open_shop_tu'];
	$time_close_shop_tu = $_POST['time_close_shop_tu'];
	update_option( 'time_open_shop_tu', $time_open_shop_tu, 'yes' );
	update_option( 'time_close_shop_tu', $time_close_shop_tu, 'yes' );

	$time_open_shop_2_tu = $_POST['time_open_shop_2_tu'];
	$time_close_shop_2_tu = $_POST['time_close_shop_2_tu'];
	update_option( 'time_open_shop_2_tu', $time_open_shop_2_tu, 'yes' );
	update_option( 'time_close_shop_2_tu', $time_close_shop_2_tu, 'yes' );

	//wed
	//==========================================================
	$time_open_shop_we = $_POST['time_open_shop_we'];
	$time_close_shop_we = $_POST['time_close_shop_we'];
	update_option( 'time_open_shop_we', $time_open_shop_we, 'yes' );
	update_option( 'time_close_shop_we', $time_close_shop_we, 'yes' );

	$time_open_shop_2_we = $_POST['time_open_shop_2_we'];
	$time_close_shop_2_we = $_POST['time_close_shop_2_we'];
	update_option( 'time_open_shop_2_we', $time_open_shop_2_we, 'yes' );
	update_option( 'time_close_shop_2_we', $time_close_shop_2_we, 'yes' );

	//thu
	//==========================================================
	$time_open_shop_th = $_POST['time_open_shop_th'];
	$time_close_shop_th = $_POST['time_close_shop_th'];
	update_option( 'time_open_shop_th', $time_open_shop_th, 'yes' );
	update_option( 'time_close_shop_th', $time_close_shop_th, 'yes' );

	$time_open_shop_2_th = $_POST['time_open_shop_2_th'];
	$time_close_shop_2_th = $_POST['time_close_shop_2_th'];
	update_option( 'time_open_shop_2_th', $time_open_shop_2_th, 'yes' );
	update_option( 'time_close_shop_2_th', $time_close_shop_2_th, 'yes' );

	//fri
	//==========================================================
	$time_open_shop_fr = $_POST['time_open_shop_fr'];
	$time_close_shop_fr = $_POST['time_close_shop_fr'];
	update_option( 'time_open_shop_fr', $time_open_shop_fr, 'yes' );
	update_option( 'time_close_shop_fr', $time_close_shop_fr, 'yes' );

	$time_open_shop_2_fr = $_POST['time_open_shop_2_fr'];
	$time_close_shop_2_fr = $_POST['time_close_shop_2_fr'];
	update_option( 'time_open_shop_2_fr', $time_open_shop_2_fr, 'yes' );
	update_option( 'time_close_shop_2_fr', $time_close_shop_2_fr, 'yes' );

	//sat
	//==========================================================
	$time_open_shop_sa = $_POST['time_open_shop_sa'];
	$time_close_shop_sa = $_POST['time_close_shop_sa'];
	update_option( 'time_open_shop_sa', $time_open_shop_sa, 'yes' );
	update_option( 'time_close_shop_sa', $time_close_shop_sa, 'yes' );

	$time_open_shop_2_sa = $_POST['time_open_shop_2_sa'];
	$time_close_shop_2_sa = $_POST['time_close_shop_2_sa'];
	update_option( 'time_open_shop_2_sa', $time_open_shop_2_sa, 'yes' );
	update_option( 'time_close_shop_2_sa', $time_close_shop_2_sa, 'yes' );

	//sun
	//==========================================================
	$time_open_shop_su = $_POST['time_open_shop_su'];
	$time_close_shop_su = $_POST['time_close_shop_su'];
	update_option( 'time_open_shop_su', $time_open_shop_su, 'yes' );
	update_option( 'time_close_shop_su', $time_close_shop_su, 'yes' );

	$time_open_shop_2_su = $_POST['time_open_shop_2_su'];
	$time_close_shop_2_su = $_POST['time_close_shop_2_su'];
	update_option( 'time_open_shop_2_su', $time_open_shop_2_su, 'yes' );
	update_option( 'time_close_shop_2_su', $time_close_shop_2_su, 'yes' );

	$custom_close_time = $_POST['custom_close_time'];
	if($custom_date != "" && count($custom_date) > 0){
		$count = 0;
		$array = array();
		foreach ($custom_date as $item) {
			if($item != "" && $custom_open_time[$count] != "" && $custom_close_time[$count] != ""){
				$array[] = array("date"=>$item,"open" => $custom_open_time[$count],"close" => $custom_close_time[$count]);
			}
		$count++;}
		if(count($array) > 0){
			update_option( 'dsmart_custom_date', $array, 'yes' );
		}else{
			update_option( 'dsmart_custom_date', "", 'yes' );
		}
	}else{
		update_option( 'dsmart_custom_date', "", 'yes' );
	}

	//Closed time
	//==========================================================
	$closed_time_date = $_POST['closed_time_date'];
	$closed_time_from = $_POST['closed_time_from'];
	$closed_time_to = $_POST['closed_time_to'];
	if($closed_time_date != "" && count($closed_time_date) > 0){
		$count = 0;
		$array = array();
		foreach ($closed_time_date as $item) {
			if($item != "" && $closed_time_from[$count] != "" && $closed_time_to[$count] != ""){
				$array[] = array("date" => $item,"from" => $closed_time_from[$count],"to" => $closed_time_to[$count]);
			}
		$count++;}
		if(count($array) > 0){
			update_option( 'closed_time', $array, 'yes' );
		}else{
			update_option( 'closed_time', "", 'yes' );
		}
	}else{
		update_option( 'closed_time', "", 'yes' );
	}

	$closed_time_date_2 = $_POST['closed_time_date_2'];
	$closed_time_from_2 = $_POST['closed_time_from_2'];
	$closed_time_to_2 = $_POST['closed_time_to_2'];
	if($closed_time_date_2 != "" && count($closed_time_date_2) > 0){
		$count = 0;
		$array = array();
		foreach ($closed_time_date_2 as $item) {
			if($item != "" && $closed_time_from_2[$count] != "" && $closed_time_to_2[$count] != ""){
				$array[] = array("date" => $item,"from" => $closed_time_from_2[$count],"to" => $closed_time_to_2[$count]);
			}
		$count++;}
		if(count($array) > 0){
			update_option( 'closed_time_2', $array, 'yes' );
		}else{
			update_option( 'closed_time_2', "", 'yes' );
		}
	}else{
		update_option( 'closed_time_2', "", 'yes' );
	}

	//time discount
	//mon
	//=======================================================
	$time_discount_shop_mo = $_POST['time_discount_shop_mo'];
	update_option('time_discount_shop_mo', $time_discount_shop_mo, 'yes');

	$time_discount_shop_2_mo = $_POST['time_discount_shop_2_mo'];
	update_option('time_discount_shop_2_mo', $time_discount_shop_2_mo, 'yes');

	//tue
	//=======================================================
	$time_discount_shop_tu = $_POST['time_discount_shop_tu'];
	update_option('time_discount_shop_tu', $time_discount_shop_tu, 'yes');

	$time_discount_shop_2_tu = $_POST['time_discount_shop_2_tu'];
	update_option('time_discount_shop_2_tu', $time_discount_shop_2_tu, 'yes');

	//wed
	//=======================================================
	$time_discount_shop_we = $_POST['time_discount_shop_we'];
	update_option('time_discount_shop_we', $time_discount_shop_we, 'yes');

	$time_discount_shop_2_we = $_POST['time_discount_shop_2_we'];
	update_option('time_discount_shop_2_we', $time_discount_shop_2_we, 'yes');

	//thu
	//=======================================================
	$time_discount_shop_th = $_POST['time_discount_shop_th'];
	update_option('time_discount_shop_th', $time_discount_shop_th, 'yes');

	$time_discount_shop_2_th = $_POST['time_discount_shop_2_th'];
	update_option('time_discount_shop_2_th', $time_discount_shop_2_th, 'yes');

	//fri
	//=======================================================
	$time_discount_shop_fr = $_POST['time_discount_shop_fr'];
	update_option('time_discount_shop_fr', $time_discount_shop_fr, 'yes');

	$time_discount_shop_2_fr = $_POST['time_discount_shop_2_fr'];
	update_option('time_discount_shop_2_fr', $time_discount_shop_2_fr, 'yes');

	//sat
	//=======================================================
	$time_discount_shop_sa = $_POST['time_discount_shop_sa'];
	update_option('time_discount_shop_sa', $time_discount_shop_sa, 'yes');

	$time_discount_shop_2_sa = $_POST['time_discount_shop_2_sa'];
	update_option('time_discount_shop_2_sa', $time_discount_shop_2_sa, 'yes');

	//sun
	//=======================================================
	$time_discount_shop_su = $_POST['time_discount_shop_su'];
	update_option('time_discount_shop_su', $time_discount_shop_su, 'yes');

	$time_discount_shop_2_su = $_POST['time_discount_shop_2_su'];
	update_option('time_discount_shop_2_su', $time_discount_shop_2_su, 'yes');

	//custom discount time
	//=======================================================
	$custom_discount_date = $_POST['custom_discount_date'];
	$custom_discount_time = $_POST['custom_discount_time'];
	if($custom_discount_date != "" && count($custom_discount_date) > 0){
		$count = 0;
		$array = array();
		foreach ($custom_discount_date as $item) {
			if($item != "" && $custom_discount_time[$count] != ""){
				$array[] = array("date" => $item,"time" => $custom_discount_time[$count]);
			}
		$count++;}
		if(count($array) > 0){
			update_option( 'dsmart_custom_discount_date', $array, 'yes' );
		}else{
			update_option( 'dsmart_custom_discount_date', "", 'yes' );
		}
	}else{
		update_option( 'dsmart_custom_discount_date', "", 'yes' );
	}
	//back link in cart page
	//=========================================================
	$back_link_in_cart = $_POST['back_link_in_cart'];
	update_option('back_link_in_cart', $back_link_in_cart, 'yes');
	update_option('delay_time',$delay_time,'yes');
	$delay_delivery = $_POST['delay_delivery'];
	update_option('delay_delivery',$delay_delivery,'yes');
	$tax_shipping = $_POST['tax_shipping'];
	update_option('tax_shipping',$tax_shipping,'yes');
	//second order number
	//=========================================================
	$show_second_number = $_POST['show_second_number'];
	update_option('show_second_number',$show_second_number,'yes');
}
$dsmart_method_ship = get_option('dsmart_method_ship');
$dsmart_method_direct = get_option('dsmart_method_direct');
$float_cart = get_option('float_cart');
$dsmart_tax = get_option('dsmart_tax');
$close_shop = get_option('dsmart_close_shop');
$time_open_shop = get_option('time_open_shop');
$time_close_shop = get_option('time_close_shop');
$dsmart_distance = get_option('dsmart_distance');
$dsmart_min_order = get_option('dsmart_min_order');
$dsmart_min_order_free = get_option('dsmart_min_order_free');
$dsmart_shipping_fee = get_option('dsmart_shipping_fee');
$dsmart_custom_date = get_option('dsmart_custom_date');

//show in list product page
//==========================================================
$dsmart_taxonomy_text = get_option('dsmart_taxonomy_text');

//custom customer mail
//==========================================================
$dsmart_header_mail_order_cs = get_option('dsmart_header_mail_order_cs');
$dsmart_header_mail_success_cs = get_option('dsmart_header_mail_success_cs');
$dsmart_header_mail_cancel_cs = get_option('dsmart_header_mail_cancel_cs');
//==========================================================
$dsmart_text_mail_order_cs = get_option('dsmart_text_mail_order_cs');
$dsmart_text_mail_success_cs = get_option('dsmart_text_mail_success_cs');
$dsmart_text_mail_cancel_cs = get_option('dsmart_text_mail_cancel_cs');
//==========================================================
$dsmart_thankyou_text = get_option('dsmart_thankyou_text');
//==========================================================
$dsmart_term_text = get_option('dsmart_term_text');
//==========================================================
$dsmart_cart_text = get_option('dsmart_cart_text');

//promotion discount
//=======================================================
$type_promotion = get_option('type_promotion');
$promotion = get_option('promotion');

$type_promotion_2 = get_option('type_promotion_2');
$promotion_2 = get_option('promotion_2');

//closed time
//==========================================================
$closed_time = get_option('closed_time');

//mon
//==========================================================
$time_open_shop_mo = get_option('time_open_shop_mo');
$time_close_shop_mo = get_option('time_close_shop_mo');

$time_open_shop_2_mo = get_option('time_open_shop_2_mo');
$time_close_shop_2_mo = get_option('time_close_shop_2_mo');

//tue
//==========================================================
$time_open_shop_tu = get_option('time_open_shop_tu');
$time_close_shop_tu = get_option('time_close_shop_tu');

$time_open_shop_2_tu = get_option('time_open_shop_2_tu');
$time_close_shop_2_tu = get_option('time_close_shop_2_tu');

//wed
//==========================================================
$time_open_shop_we = get_option('time_open_shop_we');
$time_close_shop_we = get_option('time_close_shop_we');

$time_open_shop_2_we = get_option('time_open_shop_2_we');
$time_close_shop_2_we = get_option('time_close_shop_2_we');

//thu
//==========================================================
$time_open_shop_th = get_option('time_open_shop_th');
$time_close_shop_th = get_option('time_close_shop_th');

$time_open_shop_2_th = get_option('time_open_shop_2_th');
$time_close_shop_2_th = get_option('time_close_shop_2_th');

//fri
//==========================================================
$time_open_shop_fr = get_option('time_open_shop_fr');
$time_close_shop_fr = get_option('time_close_shop_fr');

$time_open_shop_2_fr = get_option('time_open_shop_2_fr');
$time_close_shop_2_fr = get_option('time_close_shop_2_fr');

//sat
//==========================================================
$time_open_shop_sa = get_option('time_open_shop_sa');
$time_close_shop_sa = get_option('time_close_shop_sa');

$time_open_shop_2_sa = get_option('time_open_shop_2_sa');
$time_close_shop_2_sa = get_option('time_close_shop_2_sa');

//sun
//==========================================================
$time_open_shop_su = get_option('time_open_shop_su');
$time_close_shop_su = get_option('time_close_shop_su');

$time_open_shop_2_su = get_option('time_open_shop_2_su');
$time_close_shop_2_su = get_option('time_close_shop_2_su');

//discount type
//=======================================================
$discount_cod 			= get_option('discount_cod');
$discount_shop 			= get_option('discount_shop');

//time discount
//mon
//=======================================================
$time_discount_shop_mo = get_option('time_discount_shop_mo');
$time_discount_shop_2_mo = get_option('time_discount_shop_2_mo');

//tue
//=======================================================
$time_discount_shop_tu = get_option('time_discount_shop_tu');
$time_discount_shop_2_tu = get_option('time_discount_shop_2_tu');

//wed
//=======================================================
$time_discount_shop_we = get_option('time_discount_shop_we');
$time_discount_shop_2_we = get_option('time_discount_shop_2_we');

//thu
//=======================================================
$time_discount_shop_th = get_option('time_discount_shop_th');
$time_discount_shop_2_th = get_option('time_discount_shop_2_th');

//fri
//=======================================================
$time_discount_shop_fr = get_option('time_discount_shop_fr');
$time_discount_shop_2_fr = get_option('time_discount_shop_2_fr');
//sat
//=======================================================
$time_discount_shop_sa = get_option('time_discount_shop_sa');
$time_discount_shop_2_sa = get_option('time_discount_shop_2_sa');

//sun
//=======================================================
$time_discount_shop_su = get_option('time_discount_shop_su');
$time_discount_shop_2_su = get_option('time_discount_shop_2_su');

$dsmart_custom_discount_date = get_option('dsmart_custom_discount_date');

//back link in cart page
//=========================================================
$back_link_in_cart = get_option('back_link_in_cart');

$delay_time = get_option('delay_time');
$dsmart_shipping_from = get_option('dsmart_shipping_from');
$dsmart_shipping_to = get_option('dsmart_shipping_to'); 
$dsmart_shipping_cs_fee = get_option('dsmart_shipping_cs_fee');
$dsmart_custom_method = get_option('dsmart_custom_method');
$dsmart_barzahlung = get_option('dsmart_barzahlung');
$dsmart_paypal = get_option('dsmart_paypal');
$dsmart_klarna = get_option('dsmart_klarna');
$delay_delivery = get_option('delay_delivery');
$tax_shipping = get_option('tax_shipping');
$show_second_number = get_option('show_second_number');
$current_date = date('Ymd');
$total_order_in_date = (get_option('total_order_'.$current_date) != "") ? intval(get_option('total_order_'.$current_date)) : 0; ?>

<h2 class="dsmart-title"><?php _e("Setting"); ?></h2>
<div class="dsmart-setting-page">
	<form action="#" method="POST" novalidate>
		<div class="form-group">
			<label class="checkbox-label"><input type="checkbox" name="close_shop" <?php if($close_shop == "on"){echo 'checked';} ?> value="on"/><?php _e('Close shop?'); ?></label>
		</div>
		<div class="form-group">
			<label class="checkbox-label"><input type="checkbox" name="float_cart" <?php if($float_cart == "on"){echo 'checked';} ?> value="on"/><?php _e('Float cart?'); ?></label>
		</div>
		<div class="form-group">
			<label class="checkbox-label"><?php _e('Tax'); ?></label>
			<select name="dsmart_tax" class="form-control">
				<option value="" <?php if($dsmart_tax == ""){echo 'selected';} ?>><?php _e("No select"); ?></option>
				<option value="7" <?php if($dsmart_tax == "7"){echo 'selected';} ?>>7%</option>
				<option value="19" <?php if($dsmart_tax == "19"){echo 'selected';} ?>>19%</option>
			</select>
		</div>
		<div class="form-group">
			<label><?php _e('Shipping method'); ?></label>
			<label class="checkbox-label"><input type="checkbox" name="dsmart_method_ship" <?php if($dsmart_method_ship == "on"){echo 'checked';} ?> value="on"/><?php _e('Ship COD'); ?></label>
			<label class="checkbox-label"><input type="checkbox" name="dsmart_method_direct" <?php if($dsmart_method_direct == "on"){echo 'checked';} ?> value="on"/><?php _e('Go to shop'); ?></label>
		</div>		
		<div class="form-group">
			<label><?php _e('Payment method'); ?></label>
			<label class="checkbox-label"><input type="checkbox" name="dsmart_paypal" <?php if($dsmart_paypal == "on"){echo 'checked';} ?> value="on"/><?php _e('Paypal'); ?></label>
			<label class="checkbox-label"><input type="checkbox" name="dsmart_klarna" <?php if($dsmart_klarna == "on"){echo 'checked';} ?> value="on"/><?php _e('Klarna'); ?></label>
		</div>		
		<div class="form-group">
			<label><?php _e('Zweite Bestellungsnummer'); ?></label>
			<label class="checkbox-label"><input type="radio" name="show_second_number" <?php if($show_second_number != "1"){echo 'checked';} ?> value="0"/><?php _e('Aus'); ?></label>
			<label class="checkbox-label"><input type="radio" name="show_second_number" <?php if($show_second_number == "1"){echo 'checked';} ?> value="1"/><?php _e('An'); ?></label>
			<p>Derzeitige Angezeigte-Nummer: <?php echo $total_order_in_date; ?></p>
			<button class="dsmart-button reset-order" type="button">Zurücksetzen</button>
		</div>
		<div class="form-group">
			<label><?php _e('Barzahlung text'); ?></label>
			<input type="text" name="dsmart_barzahlung" class="dsmart-field" value="<?php echo $dsmart_barzahlung; ?>">
		</div>
		<div class="form-group custom-time-group">
			<label><?php _e('Custom payment method'); ?></label>
			<table class="table table-responsive table-custom-method">
				<thead>
					<tr>
						<th><?php _e("Method") ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php if($dsmart_custom_method != ""){
						foreach ($dsmart_custom_method as $key => $item) { ?>
							<tr>
								<td><input type="text" name="dsmart_custom_method[]" class="form-control" value="<?php echo $item; ?>" autocomplete="off" required/></td>
								<td><span class="btn btn-danger remove-row3"><i class="fa fa-trash-o" aria-hidden="true"></i></span></td>
							</tr>
						<?php }
					} ?>
				</tbody>
			</table>
			<span class="dsmart-button dsmart-add-new-row3"><?php _e("Add new") ?></span>
		</div>
		<h3 class="title-group"><?php _e("Shop setting"); ?></h3>
		<div class="form-group">
			<label><?php _e('Delay Time for order'); ?></label>
			<input type="number" name="delay_time" class="dsmart-field" value="<?php echo $delay_time; ?>">
		</div>
		<div class="form-group">
			<label><?php _e('Delay Time for delivery'); ?></label>
			<input type="number" name="delay_delivery" class="dsmart-field" value="<?php echo $delay_delivery; ?>">
		</div>		
		<div class="form-group">
			<label><?php _e('Mwst für Lieferung %'); ?></label>
			<input type="number" name="tax_shipping" class="dsmart-field" value="<?php echo $tax_shipping; ?>">
		</div>		
		<div class="form-group choose-time-group">
			<label><?php _e('Öffnungszeiten Lieferung'); ?></label>
			<div class="item-time">
				<span class="date"><?php _e("Montag:"); ?></span>
				<input type="text" name="time_open_shop_mo" class="timepicker-input" value="<?php echo $time_open_shop_mo; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_mo" class="timepicker-input" value="<?php echo $time_close_shop_mo; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
			<div class="item-time">
				<span class="date"><?php _e("Dienstag:"); ?></span>
				<input type="text" name="time_open_shop_tu" class="timepicker-input" value="<?php echo $time_open_shop_tu; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_tu" class="timepicker-input" value="<?php echo $time_close_shop_tu; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
			<div class="item-time">
				<span class="date"><?php _e("Mittwoch:"); ?></span>
				<input type="text" name="time_open_shop_we" class="timepicker-input" value="<?php echo $time_open_shop_we; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_we" class="timepicker-input" value="<?php echo $time_close_shop_we; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
			<div class="item-time">
				<span class="date"><?php _e("Thurday:"); ?></span>
				<input type="text" name="time_open_shop_th" class="timepicker-input" value="<?php echo $time_open_shop_th; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_th" class="timepicker-input" value="<?php echo $time_close_shop_th; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
			<div class="item-time">
				<span class="date"><?php _e("Freitag:"); ?></span>
				<input type="text" name="time_open_shop_fr" class="timepicker-input" value="<?php echo $time_open_shop_fr; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_fr" class="timepicker-input" value="<?php echo $time_close_shop_fr; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
			<div class="item-time">
				<span class="date"><?php _e("Samstag:"); ?></span>
				<input type="text" name="time_open_shop_sa" class="timepicker-input" value="<?php echo $time_open_shop_sa; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_sa" class="timepicker-input" value="<?php echo $time_close_shop_sa; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
			<div class="item-time">
				<span class="date"><?php _e("Sonntag:"); ?></span>
				<input type="text" name="time_open_shop_su" class="timepicker-input" value="<?php echo $time_open_shop_su; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_su" class="timepicker-input" value="<?php echo $time_close_shop_su; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
		</div>
		<div class="form-group choose-time-group">
			<label><?php _e('Öffnungszeiten Abholung'); ?></label>
			<div class="item-time">
				<span class="date"><?php _e("Montag:"); ?></span>
				<input type="text" name="time_open_shop_2_mo" class="timepicker-input" value="<?php echo $time_open_shop_2_mo; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_2_mo" class="timepicker-input" value="<?php echo $time_close_shop_2_mo; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
			<div class="item-time">
				<span class="date"><?php _e("Dienstag:"); ?></span>
				<input type="text" name="time_open_shop_2_tu" class="timepicker-input" value="<?php echo $time_open_shop_2_tu; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_2_tu" class="timepicker-input" value="<?php echo $time_close_shop_2_tu; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
			<div class="item-time">
				<span class="date"><?php _e("Mittwoch:"); ?></span>
				<input type="text" name="time_open_shop_2_we" class="timepicker-input" value="<?php echo $time_open_shop_2_we; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_2_we" class="timepicker-input" value="<?php echo $time_close_shop_2_we; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
			<div class="item-time">
				<span class="date"><?php _e("Thurday:"); ?></span>
				<input type="text" name="time_open_shop_2_th" class="timepicker-input" value="<?php echo $time_open_shop_2_th; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_2_th" class="timepicker-input" value="<?php echo $time_close_shop_2_th; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
			<div class="item-time">
				<span class="date"><?php _e("Freitag:"); ?></span>
				<input type="text" name="time_open_shop_2_fr" class="timepicker-input" value="<?php echo $time_open_shop_2_fr; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_2_fr" class="timepicker-input" value="<?php echo $time_close_shop_2_fr; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
			<div class="item-time">
				<span class="date"><?php _e("Samstag:"); ?></span>
				<input type="text" name="time_open_shop_2_sa" class="timepicker-input" value="<?php echo $time_open_shop_2_sa; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_2_sa" class="timepicker-input" value="<?php echo $time_close_shop_2_sa; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
			<div class="item-time">
				<span class="date"><?php _e("Sonntag:"); ?></span>
				<input type="text" name="time_open_shop_2_su" class="timepicker-input" value="<?php echo $time_open_shop_2_su; ?>" placeholder="<?php _e('Open time');?>"/>
				<span><?php _e('to'); ?></span>
				<input type="text" name="time_close_shop_2_su" class="timepicker-input" value="<?php echo $time_close_shop_2_su; ?>"placeholder="<?php _e('Close time');?>"/>
			</div>
		</div>
		<div class="form-group closed-time-group">
			<label><?php _e('Schliesszeit Lieferung'); ?></label>
			<table class="table table-responsive table-custom-time">
				<tbody>
					<?php if($closed_time != ""){
						foreach ($closed_time as $item) { ?>
							<tr>
								<td>
									<select class="form-control" name="closed_time_date[]" required>
										<option value="mo" <?php if($item['date'] == "mo"){echo 'selected';} ?>><?php _e("Montag") ?></option>
										<option value="tu" <?php if($item['date'] == "tu"){echo 'selected';} ?>><?php _e("Dienstag") ?></option>
										<option value="we" <?php if($item['date'] == "we"){echo 'selected';} ?>><?php _e("Mittwoch") ?></option>
										<option value="th" <?php if($item['date'] == "th"){echo 'selected';} ?>><?php _e("Donnerstag") ?></option>
										<option value="fr" <?php if($item['date'] == "fr"){echo 'selected';} ?>><?php _e("Freitag") ?></option>
										<option value="sa" <?php if($item['date'] == "sa"){echo 'selected';} ?>><?php _e("Samstag") ?></option>
										<option value="su" <?php if($item['date'] == "su"){echo 'selected';} ?>><?php _e("Sonntag"); ?></option>
									</select>
								</td>
								<td><input type="text" name="closed_time_from[]" class="form-control timepicker-input" value="<?php echo $item['from'] ?>" autocomplete="off" required/></td>
								<td><input type="text" name="closed_time_to[]" class="form-control timepicker-input" value="<?php echo $item['to'] ?>" autocomplete="off" required/></td>
								<td><span class="btn btn-danger remove-row"><i class="fa fa-trash-o" aria-hidden="true"></i></span></td>
							</tr>
						<?php }
					} ?>
					<tr class="hidden-field">
						<td>
							<select class="form-control" name="closed_time_date[]">
								<option value="mo"><?php _e("Montag") ?></option>
								<option value="tu"><?php _e("Dienstag") ?></option>
								<option value="we"><?php _e("Mittwoch") ?></option>
								<option value="th"><?php _e("Donnerstag") ?></option>
								<option value="fr"><?php _e("Freitag") ?></option>
								<option value="sa"><?php _e("Samstag") ?></option>
								<option value="su"><?php _e("Sonntag"); ?></option>
							</select>
						</td>
						<td><input type="text" name="closed_time_from[]" autocomplete="off" class="form-control timepicker-input"/></td>
						<td><input type="text" name="closed_time_to[]" autocomplete="off" class="form-control timepicker-input"/></td>
						<td><span class="btn btn-danger remove-row"><i class="fa fa-trash-o" aria-hidden="true"></i></span></td>
					</tr>
				</tbody>
			</table>
			<span class="dsmart-button dsmart-add-new-row-time"><?php _e("Add new") ?></span>
		</div>
		<div class="form-group closed-time-group">
			<label><?php _e('Schliesszeit Lieferung'); ?></label>
			<table class="table table-responsive table-custom-time">
				<tbody>
					<?php if($closed_time_2 != ""){
						foreach ($closed_time_2 as $item) { ?>
							<tr>
								<td>
									<select class="form-control" name="closed_time_date_2[]" required>
										<option value="mo" <?php if($item['date'] == "mo"){echo 'selected';} ?>><?php _e("Montag") ?></option>
										<option value="tu" <?php if($item['date'] == "tu"){echo 'selected';} ?>><?php _e("Dienstag") ?></option>
										<option value="we" <?php if($item['date'] == "we"){echo 'selected';} ?>><?php _e("Mittwoch") ?></option>
										<option value="th" <?php if($item['date'] == "th"){echo 'selected';} ?>><?php _e("Donnerstag") ?></option>
										<option value="fr" <?php if($item['date'] == "fr"){echo 'selected';} ?>><?php _e("Freitag") ?></option>
										<option value="sa" <?php if($item['date'] == "sa"){echo 'selected';} ?>><?php _e("Samstag") ?></option>
										<option value="su" <?php if($item['date'] == "su"){echo 'selected';} ?>><?php _e("Sonntag"); ?></option>
									</select>
								</td>
								<td><input type="text" name="closed_time_from_2[]" class="form-control timepicker-input" value="<?php echo $item['from'] ?>" autocomplete="off" required/></td>
								<td><input type="text" name="closed_time_to_2[]" class="form-control timepicker-input" value="<?php echo $item['to'] ?>" autocomplete="off" required/></td>
								<td><span class="btn btn-danger remove-row"><i class="fa fa-trash-o" aria-hidden="true"></i></span></td>
							</tr>
						<?php }
					} ?>
					<tr class="hidden-field">
						<td>
							<select class="form-control" name="closed_time_date_2[]">
								<option value="mo"><?php _e("Montag") ?></option>
								<option value="tu"><?php _e("Dienstag") ?></option>
								<option value="we"><?php _e("Wednesday") ?></option>
								<option value="th"><?php _e("Donnerstag") ?></option>
								<option value="fr"><?php _e("Freitag") ?></option>
								<option value="sa"><?php _e("Samstag") ?></option>
								<option value="su"><?php _e("Sonntag"); ?></option>
							</select>
						</td>
						<td><input type="text" name="closed_time_from_2[]" autocomplete="off" class="form-control timepicker-input"/></td>
						<td><input type="text" name="closed_time_to_2[]" autocomplete="off" class="form-control timepicker-input"/></td>
						<td><span class="btn btn-danger remove-row"><i class="fa fa-trash-o" aria-hidden="true"></i></span></td>
					</tr>
				</tbody>
			</table>
			<span class="dsmart-button dsmart-add-new-row-time"><?php _e("Add new") ?></span>
		</div>
		<div class="form-group custom-time-group">
			<label><?php _e('Custom date'); ?></label>
			<table class="table table-responsive table-custom-date">
				<thead>
					<tr>
						<th><?php _e("Date") ?></th>
						<th><?php _e("Open time") ?></th>
						<th><?php _e("Close time") ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php if($dsmart_custom_date != ""){
						foreach ($dsmart_custom_date as $item) { ?>
							<tr>
								<td><input type="text" name="custom_date[]" class="form-control dsmart-datepicker" value="<?php echo $item['date']; ?>" autocomplete="off" required/></td>
								<td><input type="text" name="custom_open_time[]" class="form-control timepicker-input" value="<?php echo $item['open'] ?>" autocomplete="off" required/></td>
								<td><input type="text" name="custom_close_time[]" class="form-control timepicker-input" value="<?php echo $item['close'] ?>" autocomplete="off" required/></td>
								<td><span class="btn btn-danger remove-row"><i class="fa fa-trash-o" aria-hidden="true"></i></span></td>
							</tr>
						<?php }
					} ?>
					<tr class="hidden-field">
						<td><input type="text" name="custom_date[]" autocomplete="off" class="form-control dsmart-datepicker"/></td>
						<td><input type="text" name="custom_open_time[]" autocomplete="off" class="form-control timepicker-input"/></td>
						<td><input type="text" name="custom_close_time[]" autocomplete="off" class="form-control timepicker-input"/></td>
						<td><span class="btn btn-danger remove-row"><i class="fa fa-trash-o" aria-hidden="true"></i></span></td>
					</tr>
				</tbody>
			</table>
			<span class="dsmart-button dsmart-add-new-row"><?php _e("Add new") ?></span>
		</div>
		<h3 class="title-group"><?php _e("Shipping setting"); ?></h3>
		<div class="form-group">
			<label><?php _e('Maximum radius(km)'); ?></label>
			<input type="number" name="dsmart_distance" class="dsmart-field" value="<?php echo $dsmart_distance; ?>"/>
		</div>
		<div class="form-group">
			<label><?php _e("Minimum Order to Ship COD") ?></label>
			<input type="number" name="dsmart_min_order" id="dsmart_min_order" class="dsmart-field" placeholder="<?php _e('Minimum Order to Ship COD'); ?>" value="<?php echo $dsmart_min_order; ?>" autocomplete="off">
		</div>
		<div class="form-group">
			<label><?php _e("Minimum order for Freeship") ?></label>
			<input type="number" name="dsmart_min_order_free" id="dsmart_min_order_free" class="dsmart-field" placeholder="<?php _e('Minimum order for Freeship') ?>" value="<?php echo $dsmart_min_order_free; ?>" autocomplete="off">
		</div>
		<div class="form-group">
			<label><?php _e('Default Shipping fee($)'); ?></label>
			<input type="text" name="dsmart_shipping_fee" id="dsmart_shipping_fee" class="dsmart-field" placeholder="<?php _e('Shipping fee'); ?>" value="<?php echo $dsmart_shipping_fee; ?>" autocomplete="off">
		</div>
		<div class="form-group custom-time-group">
			<label><?php _e('Custom Shipping fee($)'); ?></label>
			<table class="table table-responsive table-custom-shipping">
				<thead>
					<tr>
						<th><?php _e("From") ?></th>
						<th><?php _e("To") ?></th>
						<th><?php _e("Price") ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php if($dsmart_shipping_from != ""){
						foreach ($dsmart_shipping_from as $key => $item) { ?>
							<tr>
								<td><input type="text" name="dsmart_shipping_from[]" class="form-control" value="<?php echo $dsmart_shipping_from[$key]; ?>" autocomplete="off" required/></td>
								<td><input type="text" name="dsmart_shipping_to[]" class="form-control" value="<?php echo $dsmart_shipping_to[$key] ?>" autocomplete="off" required/></td>
								<td><input type="text" name="dsmart_shipping_cs_fee[]" class="form-control" value="<?php echo $dsmart_shipping_cs_fee[$key] ?>" autocomplete="off" required/></td>
								<td><span class="btn btn-danger remove-row2"><i class="fa fa-trash-o" aria-hidden="true"></i></span></td>
							</tr>
						<?php }
					} ?>
				</tbody>
			</table>
			<span class="dsmart-button dsmart-add-new-row2"><?php _e("Add new") ?></span>
		</div>
		<h3 class="title-group"><?php _e("Text in frontpage") ?></h3>
		<div class="form-group">
			<label for="dsmart_taxonomy_text"><?php _e('Text') ?></label>
			<textarea name="dsmart_taxonomy_text" id="dsmart_taxonomy_text" class="dsmart-field" placeholder="<?php _e('Text in frontpage') ?>" rows="10"><?php echo $dsmart_taxonomy_text; ?></textarea>
		</div>
		<h3 class="title-group"><?php _e("Text mail to customer") ?></h3>
		<div class="form-group">
			<label><?php _e('Text when customer order') ?></label>
			<input type="text" name="dsmart_header_mail_order_cs" class="dsmart-field" placeholder="<?php _e('Header mail for customer') ?>" value="<?php echo $dsmart_header_mail_order_cs; ?>" autocomplete="off"/>
			<textarea name="dsmart_text_mail_order_cs" rows="5" class="dsmart-field" placeholder="<?php _e('Text mail when customer order') ?>"><?php echo $dsmart_text_mail_order_cs; ?></textarea>
		</div>
		<div class="form-group">
			<label><?php _e('Text when change order to completed') ?></label>
			<input type="text" name="dsmart_header_mail_success_cs" class="dsmart-field" placeholder="<?php _e('Header mail for customer') ?>" value="<?php echo $dsmart_header_mail_success_cs; ?>" autocomplete="off"/>
			<textarea name="dsmart_text_mail_success_cs" rows="5" class="dsmart-field" placeholder="<?php _e('Text when change order to completed') ?>"><?php echo $dsmart_text_mail_success_cs; ?></textarea>
		</div>
		<div class="form-group">
			<label><?php _e('Text when change order to cancelled') ?></label>
			<input type="text" name="dsmart_header_mail_cancel_cs" class="dsmart-field" placeholder="<?php _e('Header mail for customer') ?>" value="<?php echo $dsmart_header_mail_cancel_cs; ?>" autocomplete="off"/>
			<textarea name="dsmart_text_mail_cancel_cs" rows="5" class="dsmart-field" placeholder="<?php _e('Text when change order to cancelled') ?>"><?php echo $dsmart_text_mail_cancel_cs; ?></textarea>
		</div>
		<h3 class="title-group"><?php _e("Thank you"); ?></h3>
		<div class="form-group">
			<label><?php _e('Text thankyou') ?></label>
			<textarea name="dsmart_thankyou_text" rows="5" class="dsmart-field" placeholder="<?php _e('Thankyou Text') ?>"><?php echo $dsmart_thankyou_text; ?></textarea>
		</div>
		<h3 class="title-group"><?php _e("Terms & Conditions text"); ?></h3>
		<div class="form-group">
			<label><?php _e('Terms & Conditions text') ?></label>
			<textarea name="dsmart_term_text" rows="5" class="dsmart-field" placeholder="<?php _e('Terms & Conditions text') ?>"><?php echo $dsmart_term_text; ?></textarea>
		</div>
		<h3 class="title-group"><?php _e('Cart custom text'); ?></h3>
		<div class="form-group">
			<label><?php _e('Cart custom text'); ?></label>
			<textarea name="dsmart_cart_text" rows="5" class="dsmart-field" placeholder="<?php _e('Cart custom text') ?>"><?php echo $dsmart_cart_text; ?></textarea>
		</div>
		<h3 class="title-group"><?php _e('Rabatt (Shipping)'); ?></h3>
		<div class="form-group">
			<div class="radio-wrap">
				<label>						
					<input type="radio" name="type_promotion" value="%" <?php echo  ($type_promotion == '%' ||$type_promotion == '') ? 'checked' : ''; ?> >
					<?php echo __("%","booking-order"); ?>
				</label>
				<label>						
					<input type="radio" name="type_promotion" value="number" <?php echo  ($type_promotion == 'number') ? 'checked' : ''; ?>>
					<?php echo __("Number","booking-order"); ?>
				</label>	
			</div>
			<input type="number" class="dsmart-field" name="promotion" value="<?php echo $promotion; ?>"> 
		</div>
		<h3 class="title-group"><?php _e('Rabatt (Go to shop)'); ?></h3>
		<div class="form-group">
			<div class="radio-wrap">
				<label>						
					<input type="radio" name="type_promotion_2" value="%" <?php echo  ($type_promotion_2 == '%' ||$type_promotion_2 == '') ? 'checked' : ''; ?> >
					<?php echo __("%","booking-order"); ?>
				</label>
				<label>						
					<input type="radio" name="type_promotion_2" value="number" <?php echo  ($type_promotion_2 == 'number') ? 'checked' : ''; ?>>
					<?php echo __("Number","booking-order"); ?>
				</label>	
			</div>
			<input type="number" class="dsmart-field" name="promotion_2" value="<?php echo $promotion_2; ?>"> 
		</div>
		<div class="form-group">
			<h3 class="title-group"><?php echo __("Rabatt type","booking-order"); ?></h3>
			<div class="radio-wrap">
				<label>						
					<input type="checkbox" name="discount_cod" value="on" <?php echo  ($discount_cod == 'on') ? 'checked' : ''; ?> >
					<?php echo __("Ship COD","booking-order"); ?>
				</label>
				<label>						
					<input type="checkbox" name="discount_shop" value="on" <?php echo  ($discount_shop == 'on') ? 'checked' : ''; ?>>
					<?php echo __("Go To Shop","booking-order"); ?>
				</label>	
			</div>
		</div>
		<h3 class="title-group"><?php _e("Lieferung"); ?></h3>
		<div id="discount-group" class="form-group choose-time-group">
			<label><?php _e('Time discount'); ?></label>
			<div id="discount-mon" class="item-time">
				<span class="date"><?php _e("Montag:"); ?></span>
				<input type="text" name="time_discount_shop_mo" class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_mo; ?>" />	
				<button type="button" class="add-time dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>			
			</div>
			<div id="discount-tu" class="item-time">
				<span class="date"><?php _e("Dienstag:"); ?></span>
				<input type="text" name="time_discount_shop_tu" class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_tu; ?>" />
				<button type="button" class="add-time dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>		
			</div>
			<div id="discount-we" class="item-time">
				<span class="date"><?php _e("Mittwoch:"); ?></span>
				<input type="text" name="time_discount_shop_we" class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_we; ?>"/>
				<button type="button" class="add-time dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>		
			</div>
			<div id="discount-th" class="item-time">
				<span class="date"><?php _e("Thurday:"); ?></span>
				<input type="text" name="time_discount_shop_th" class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_th; ?>" />
					<button type="button" class="add-time dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>		
			</div>
			<div id="discount-fr" class="item-time">
				<span class="date"><?php _e("Freitag:"); ?></span>
				<input type="text" name="time_discount_shop_fr" class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_fr; ?>" />
					<button type="button" class="add-time dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>			
			</div>
			<div id="discount-sa" class="item-time">
				<span class="date"><?php _e("Samstag:"); ?></span>
				<input type="text" name="time_discount_shop_sa"class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_sa; ?>" />
					<button type="button" class="add-time dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>			
			</div>
			<div id="discount-su" class="item-time">
				<span class="date"><?php _e("Sonntag:"); ?></span>
				<input type="text" name="time_discount_shop_su" class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_su; ?>" />
				<button type="button" class="add-time dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>		
			</div>
		</div>
		<h3 class="title-group"><?php _e("Öffnungszeiten Abholung"); ?></h3>
		<div id="discount-group-2" class="form-group choose-time-group">
			<label><?php _e('Time discount'); ?></label>
			<div id="discount-mon-2" class="item-time">
				<span class="date"><?php _e("Montag:"); ?></span>
				<input type="text" name="time_discount_shop_2_mo" class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_2_mo; ?>" />	
				<button type="button" class="add-time-2 dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>			
			</div>
			<div id="discount-tu-2" class="item-time">
				<span class="date"><?php _e("Dienstag:"); ?></span>
				<input type="text" name="time_discount_shop_2_tu" class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_2_tu; ?>" />
				<button type="button" class="add-time-2 dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>		
			</div>
			<div id="discount-we-2" class="item-time">
				<span class="date"><?php _e("Mittwoch:"); ?></span>
				<input type="text" name="time_discount_shop_2_we" class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_2_we; ?>"/>
				<button type="button" class="add-time-2 dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>		
			</div>
			<div id="discount-th-2" class="item-time">
				<span class="date"><?php _e("Thurday:"); ?></span>
				<input type="text" name="time_discount_shop_2_th" class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_2_th; ?>" />
					<button type="button" class="add-time-2 dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>		
			</div>
			<div id="discount-fr-2" class="item-time">
				<span class="date"><?php _e("Freitag:"); ?></span>
				<input type="text" name="time_discount_shop_2_fr" class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_2_fr; ?>" />
					<button type="button" class="add-time-2 dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>			
			</div>
			<div id="discount-sa-2" class="item-time">
				<span class="date"><?php _e("Samstag:"); ?></span>
				<input type="text" name="time_discount_shop_2_sa"class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_2_sa; ?>" />
					<button type="button" class="add-time-2 dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>			
			</div>
			<div id="discount-su-2" class="item-time">
				<span class="date"><?php _e("Sonntag:"); ?></span>
				<input type="text" name="time_discount_shop_2_su" class="dsmart-field multi_timepicker" placeholder="<?php _e('Rabatt time') ?>" value="<?php echo $time_discount_shop_2_su; ?>" />
				<button type="button" class="add-time-2 dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button>		
			</div>
		</div>
		<h3 class="title-group"><?php _e("Custom Opening Times"); ?></h3>
		<div id="#custom-discount-group" class="form-group custom-time-group">
			<label><?php _e('Custom times'); ?></label>
			<table class="table table-responsive table-custom-date">
				<thead>
					<tr>
						<th><?php _e("Date") ?></th>
						<th><?php _e("Rabatt Time") ?></th>
						<th><?php _e("Add Time") ?></th>
						<th><?php _e("Action") ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if($dsmart_custom_discount_date != ""){ 
						$index = 1;
						foreach ($dsmart_custom_discount_date as $item) { ?>
							<tr id="custom-time<?php echo $index; ?>">
								<td><input type="text" name="custom_discount_date[]" class="dsmart-field dsmart-datepicker" value="<?php echo $item['date']; ?>" autocomplete="off" required/></td>
								<td><input type="text" name="custom_discount_time[]" class="dsmart-field multi_timepicker" value="<?php echo $item['time'] ?>" autocomplete="off" required/></td>
								<td><button type="button" class="add-time dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button></td>
								<td><span class="btn btn-danger remove-row"><i class="fa fa-trash-o" aria-hidden="true"></i></span></td>
							</tr>
						<?php $index++; } 
					} ?>
					<tr class="hidden-field">
						<td><input type="text" name="custom_discount_date[]" autocomplete="off" class="dsmart-field dsmart-datepicker"/></td>
						<td><input type="text" name="custom_discount_time[]" class="dsmart-field multi_timepicker" value="" autocomplete="off" required/></td>
						<td><button type="button" class="add-time dsmart-button"><?php _e( 'Add rabatt time','dsmart'); ?></button></td>
						<td><span class="btn btn-danger remove-row"><i class="fa fa-trash-o" aria-hidden="true"></i></span></td>
					</tr>
				</tbody>
			</table>
			<span class="dsmart-button dsmart-add-new-row"><?php _e("Add new") ?></span>
		</div>
		<h3 class="title-group"><?php _e('Weiter Einkaufen button link'); ?></h3>
		<div class="form-group">			
			<input type="text" class="dsmart-field" name="back_link_in_cart" value="<?php echo $back_link_in_cart; ?>"> 
		</div>
		<button type="submit" name="submit-setting" class="dsmart-button"><?php _e("Update"); ?></button>
	</form>
	<div class="add-time-form">
	 	<h4 class="text-center"><?php _e( 'Add discount time', 'dsmart'); ?></h4>
	 	<p class="message hidden"></p>
	 	<input type="hidden" name="object" class="object" value="">
	 	<input type="text" name="discount_form_mo" class="timepicker" value="" placeholder="<?php _e( 'Rabatt strart time', 'dsmart'); ?>">
	 	<input type="text" name="discount_to_mo" class="timepicker" value="" placeholder="<?php _e( 'Rabatt end time', 'dsmart'); ?>">
	 	<button type="button" class="dsmart-button button-primary btn-ok"><?php _e( 'Ok', 'dsmart'); ?></button>
	 	<button type="button" class="dsmart-button button-primary btn-cancel"><?php _e( 'Close', 'dsmart'); ?></button>
	</div>
	<div class="add-time-form-2">
	 	<h4 class="text-center"><?php _e( 'Add discount time', 'dsmart'); ?></h4>
	 	<p class="message hidden"></p>
	 	<input type="hidden" name="object" class="object" value="">
	 	<input type="text" name="discount_form_mo" class="timepicker" value="" placeholder="<?php _e( 'Rabatt strart time', 'dsmart'); ?>">
	 	<input type="text" name="discount_to_mo" class="timepicker" value="" placeholder="<?php _e( 'Rabatt end time', 'dsmart'); ?>">
	 	<button type="button" class="dsmart-button button-primary btn-ok"><?php _e( 'Ok', 'dsmart'); ?></button>
	 	<button type="button" class="dsmart-button button-primary btn-cancel"><?php _e( 'Close', 'dsmart'); ?></button>
	</div>
</div>