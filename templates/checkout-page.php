<?php 
if(isset($_COOKIE['cart']) && $_COOKIE['cart'] != ""){
	$cart = unserialize(base64_decode($_COOKIE['cart']));
}else{
	$cart = array();
}
$cart = make_cart_items_available($cart);
if(isset($_COOKIE['shipping_info']) && $_COOKIE['shipping_info'] != ""){
	$shipping_info 	= unserialize(base64_decode($_COOKIE['shipping_info']));
	// var_dump($shipping_info);
	$shipping_data 	= check_shipping_available($shipping_info);
	$shipping_fee 	= $shipping_data['price'];
	$shipping_check = $shipping_data['check'];
}else{
	$shipping_info 	= array();
	$shipping_fee 	= 0;
}
$check_zipcode = (get_option('zipcode_status') == "on") ? true : false;
$close_shop 		= get_option('dsmart_close_shop');
$total_all 			= 0;
$total_all_use_coupon = 0;
$taxes 				= 0;
$current_date_text 	= custom_date();
$dsmart_term_text 	= get_option('dsmart_term_text');
$dsmart_paypal 		= get_option('dsmart_paypal');
$dsmart_klarna 		= get_option('dsmart_klarna');
$dsmart_barzahlung	= (get_option('dsmart_barzahlung') != "") ? get_option('dsmart_barzahlung') : 'Barzahlung';
if($shipping_info['shipping_method'] == "shipping"){
	$type_promotion 	= get_option('type_promotion');	
	$promotion 			= get_option('promotion');	
	$has_discount = is_discount_time($shipping_info['delivery_time'],$shipping_info['delivery_date'],$shipping_info['shipping_method']);
}else{
	$type_promotion = get_option('type_promotion_2');	
	$promotion = get_option('promotion_2');	
	$has_discount = is_discount_time($shipping_info['time'],null,$shipping_info['shipping_method']);
}
$dsmart_custom_method = get_option('dsmart_custom_method');
$tax_shipping = get_option('tax_shipping');
$check_promotion = true;
if(isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != ""){
	$coupon = explode(',',$_COOKIE['coupon']);
	foreach ($coupon as $key => $item) {
		$coupon_id = get_coupon_id_from_code($item);
		$coupon_mutiple = dsmart_field('coupon_mutiple',$coupon_id);
		if($coupon_mutiple != "yes"){
			$check_promotion = false;
			$has_discount = false;
		}
	}
}else{
	$coupon_price = 0;
	$coupon_price_all = 0;
}
$show_profile = get_option('show_profile');
$profile_zipcode = "";
if(is_user_logged_in()):
	$current_user = wp_get_current_user();
	$address = (is_array(get_user_meta($current_user->ID,'address',true))) ? get_user_meta($current_user->ID,'address',true) : array();
	$full_address = "";
	$profile_zipcode = $address['customer_zipcode'];
    /*if($address['customer_etage'] != ""){
        $full_address = $address['customer_etage'].", ";
    }*/
    $full_address .= $address['customer_street'].', '.$address['customer_zipcode'].' '.$address['customer_city'];
else:
	$address = array();
endif;

$button_color = get_option('button_color', '#50aecc');
$price_color = get_option('price_color', '#b28e2d');
get_header(); ?>
<div class="checkout-page container">
	<?php if(count($cart) > 0 && count($shipping_info) > 0 && $shipping_check != false){ ?>
		<div class="dsmart-notify"></div>
		<div class="dsmart-section user-info">
			<h3 class="dsmart-title"><?php _e("Kundeninformation",'dsmart') ?></h3>
			<div class="dsmart-row">
				<div class="dsmart-col-6">
					<div class="form-group">
						<label><?php _e("Nachname",'dsmart') ?><span class="required-symbol">*</span></label>
						<input type="text" name="customer_name1" class="dsmart-field" placeholder="<?php _e("Nachname *",'dsmart') ?>" value="<?php if(isset($address['customer_name1'])) echo $address['customer_name1']; ?>"/>
					</div>
					<div class="form-group">
						<label><?php _e("Vorname",'dsmart') ?><span class="required-symbol">*</span></label>
						<input type="text" name="customer_name2" class="dsmart-field" placeholder="<?php _e("Vorname *",'dsmart') ?>" value="<?php if(isset($address['customer_name2'])) echo $address['customer_name2']; ?>"/>
					</div>
					<div class="form-group">
						<label><?php _e("Telefonnummer",'dsmart') ?><span class="required-symbol">*</span></label>
						<input type="text" name="customer_phone" class="dsmart-field" placeholder="<?php _e("Telefonnummer *",'dsmart') ?>" value="<?php if(isset($address['customer_phone'])) echo $address['customer_phone']; ?>"/>
					</div>
					<div class="form-group">
						<label><?php _e("Email Adresse",'dsmart') ?><span class="required-symbol">*</span></label>
						<input type="text" name="customer_email" class="dsmart-field" placeholder="<?php _e("Email Adresse *",'dsmart') ?>" value="<?php if(isset($address['customer_email'])) echo $address['customer_email']; ?>"/>
					</div>
					<?php if($shipping_info['shipping_method'] == "shipping"): ?>
						<?php if($check_zipcode == true): ?>
							<div class="form-group">
								<label><?php _e("Etage",'dsmart') ?><span class="required-symbol">*</span></label>
								<input type="text" name="customer_etage" class="dsmart-field" placeholder="<?php _e("Etage *",'dsmart') ?>" value="<?php echo $address['customer_etage']; ?>"/>
							</div>
							<div class="form-group">
								<label><?php _e("Postleitzahl",'dsmart') ?><span class="required-symbol">*</span></label>
								<input type="text" name="customer_zipcode" class="dsmart-field" placeholder="<?php _e("Postleitzahl *",'dsmart') ?>" value="<?php echo $shipping_info['zipcode']; ?>" readonly/>
							</div>
							<div class="form-group">
								<label><?php _e("Lieferung",'dsmart') ?><span class="required-symbol">*</span></label>
								<input type="text" name="customer_address" class="dsmart-field" placeholder="<?php _e("Lieferanschrift *",'dsmart') ?>" value="<?php if(isset($full_address)) echo $full_address; ?>"/>
							</div>
							<div class="form-group">
								<label><?php _e("Bestelldatum",'dsmart') ?><span class="required-symbol">*</span></label>
								<input type="text" name="customer_date" class="dsmart-field" placeholder="<?php _e("Bestelldatum *",'dsmart') ?>" value="<?php echo $shipping_info['delivery_date']; ?>" readonly/>
							</div>
							<div class="form-group">
								<label><?php _e("Liefer- / Abholzeit",'dsmart') ?><span class="required-symbol">*</span></label>
								<input type="text" name="customer_time" class="dsmart-field" placeholder="<?php _e("Liefer- / Abholzeit *",'dsmart') ?>" value="<?php echo $shipping_info['delivery_time']; ?>" readonly/>
							</div>
						<?php else: ?>
							<div class="form-group">
								<label><?php _e("Etage",'dsmart') ?><span class="required-symbol">*</span></label>
								<input type="text" name="customer_etage" class="dsmart-field" placeholder="<?php _e("Etage *",'dsmart') ?>" value="<?php echo $address['customer_etage']; ?>"/>
							</div>
							<div class="form-group">
								<label><?php _e("Postleitzahl",'dsmart') ?><span class="required-symbol">*</span></label>
								<input type="text" name="customer_zipcode" class="dsmart-field" placeholder="<?php _e("Postleitzahl *",'dsmart') ?>" value="<?php echo $profile_zipcode; ?>"/>
							</div>
							<div class="form-group">
								<label><?php _e("Lieferung",'dsmart') ?><span class="required-symbol">*</span></label>
								<input type="text" name="customer_address" class="dsmart-field" placeholder="<?php _e("Lieferanschrift *",'dsmart') ?>" value="<?php echo $shipping_info['location'] ?>" readonly/>
							</div>
							<div class="form-group">
								<label><?php _e("Bestelldatum",'dsmart') ?><span class="required-symbol">*</span></label>
								<input type="text" name="customer_date" class="dsmart-field" placeholder="<?php _e("Bestelldatum *",'dsmart') ?>" value="<?php echo $shipping_info['delivery_date']; ?>" readonly/>
							</div>
							<div class="form-group">
								<label><?php _e("Liefer- / Abholzeit",'dsmart') ?><span class="required-symbol">*</span></label>
								<input type="text" name="customer_time" class="dsmart-field" placeholder="<?php _e("Liefer- / Abholzeit *",'dsmart') ?>" value="<?php echo $shipping_info['delivery_time']; ?>" readonly/>
							</div>
						<?php endif;?>
					<?php else: ?>
						<div class="form-group">
							<label><?php _e("Liefer- / Abholzeit",'dsmart') ?><span class="required-symbol">*</span></label>
							<input type="text" name="customer_time" class="dsmart-field" placeholder="<?php _e("Liefer- / Abholzeit *",'dsmart') ?>" value="<?php echo $shipping_info['time']; ?>" readonly/>
						</div>
					<?php endif;?>
				</div>
				<div class="dsmart-col-6">
					<label><?php _e("Bestell Notiz",'dsmart') ?></label>
					<textarea class="dsmart-field" name="more_additional" rows="5" placeholder="<?php _e("Bestell Notiz",'dsmart'); ?>"></textarea>
				</div>
			</div>
		</div>
		<div class="dsmart-section cart-info">
			<h3 class="dsmart-title"><?php _e("Deine Bestellung",'dsmart'); ?></h3>
			<div class="dsmart-table">
				<table class="table">
					<thead>
						<tr>
							<th class="product-name"><?php _e("Produkt",'dsmart') ?></th>
							<th class="product-total"><?php _e("Summe",'dsmart') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($cart as $key_item => $value_item) {
							$ret = get_extra_variable_info($value_item);
							$sidedish_info = $ret["sidedish_info"];
							$extra_info = $ret["extra_info"];
							$extra_price = $ret["extra_price"];
							$variable_id = $ret["variable_id"];
							$price_item = $ret["price_item"];
							$price = $ret["price"];
							
							$product_id = intval($value_item['product_id']);
							$quantity = $value_item['quantity'];

							$meta['quantity'] = dsmart_field('quantity',$product_id);
	                        $meta['varialbe_price'] = dsmart_field('varialbe_price',$product_id);

	                        $meta['extra_name'] = dsmart_field('extra_name',$product_id);
	                        $meta['extra_price'] = dsmart_field('extra_price',$product_id);
	                        
							$status_item = dsmart_field('status',$product_id);
							
							if($status_item == "instock" && $price_item != ""){
								// $price = ds_caculate_item_price($key_item,$quantity);
								$vat_item = dsmart_field('vat',$product_id);
								$taxes_item 	= dsmart_field('taxes',$product_id);
								if($taxes_item != ""){
									$taxes_price 	= $price - round($price/(1+$taxes_item/100),2);
									$taxes = $taxes + $taxes_price;
								}elseif($vat_item != ""){
									$taxes_price 	= $price - round($price/(1+$vat_item/100),2);
									$taxes 	= $taxes + $taxes_price;
								}
								$total_all = $total_all + $price;
								if(check_product_can_use_coupon_or_not($product_id) == true){
									$total_all_use_coupon = $total_all_use_coupon + $price;
								} ?>
								<tr>
									<td class="dsmart-title"><?php echo get_the_title($product_id) ?> <strong>x <?php echo $quantity; ?></strong>
										<?php if(check_product_can_use_coupon_or_not($product_id) == false): ?>
											<em><?php _e("Kein Rabatt möglich!") ?></em>
										<?php endif;?>
										<?php 
										$isExtra = has_extra($value_item);
										$isVariable = has_variable($value_item);
										$isSidedish = has_sidedish($value_item);
										if($isExtra || $isVariable || $isSidedish):
										 ?>
										<div class="variable-product cart-variable">
										<?php 
											display_sidedish($isSidedish, $product_id, $sidedish_info); 
											display_variable($isVariable, $variable_id, $product_id);
											display_extra($isExtra, $product_id, $extra_info); 
										?>	
											</div>
										</div>
									<?php endif; ?>	
									</td>
									<td><?php echo ds_price_format_text_no_convert($price); ?></td>
								</tr>
							<?php }
						} ?>
					</tbody>
					<?php $taxes = (float) $taxes + (float)$shipping_fee - (float) $shipping_fee/(1+(float) $tax_shipping/100); 
					?>
					<tfoot>
						<tr>
							<td><?php _e("Lieferungskosten",'dsmart') ?></td>
							<td><?php echo ds_price_format_text_no_convert($shipping_fee); ?></td>
						</tr>
						<tr>
							<td><?php _e("Zwischensumme",'dsmart') ?></td>
							<td><span class="subtotal"><?php echo ds_price_format_text_no_convert($total_all); ?></span></td>
						</tr>
						<?php 
						$reduce = 0;
						$discount_min = get_option('discount_min');
						if ($discount_min != '' && $discount_min != 0 && $discount_min != '0' && floatval($discount_min) > $total_all) {
							$has_discount =false;
							
						}else{
							if($check_promotion == true && $promotion != null && $has_discount){
								if($type_promotion == '%'){
									$reduce_percent = $promotion;
									$temp_total = $total_all_use_coupon*floatval($reduce_percent)/100;	
									$reduce = ds_price_format_text_no_convert($temp_total);
									$total_all = $total_all - $temp_total; 		
									$total_all_use_coupon = $total_all_use_coupon - $temp_total;					
								}else{
									$reduce_percent = round($promotion/floatval($total_all_use_coupon)*100);
									$reduce = ds_price_format_text_no_convert($promotion);								
									$total_all = $total_all - floatval($promotion);
									$total_all_use_coupon = $total_all_use_coupon - floatval($promotion);	
								}
								$reduce_percent = ($reduce_percent > 100) ? 100 : $reduce_percent; 
							?>
							<tr class="cart-discount">
								<td><?php _e("Rabatt",'dsmart'); ?> (-<?php echo $reduce_percent; ?>%)</td>
								<td>- <?php echo $reduce; ?></td>
							</tr>
						<?php }	
						}?>
						<?php if(isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != ""):
							$coupon = explode(',',$_COOKIE['coupon']);
							foreach ($coupon as $key => $item) {
								$coupon_id = get_coupon_id_from_code($item);
								$coupon_shipping = dsmart_field('coupon_shipping',$coupon_id);
								if(check_coupon_available($item) == 1 && ($shipping_info['shipping_method'] == "" || $coupon_shipping == "" || ($shipping_info['shipping_method'] != "" && $coupon_shipping != "" && $shipping_info['shipping_method'] == $coupon_shipping))){
									$coupon_price = get_price_of_coupon($item,$total_all_use_coupon);
									$total_all = $total_all - $coupon_price;
									$total_all_use_coupon = $total_all_use_coupon - $coupon_price; ?>
									<tr class="cart-coupon">
										<td><?php _e("Rabattcode",'dsmart') ?> (<?php echo $item; ?>)</td>
										<td><span class="coupon-text">- <?php echo ds_price_format_text_no_convert($coupon_price); ?></span></td>
									</tr>
								<?php }
							}
						endif; 
						$total_all = $total_all + $shipping_fee;
						$total_all = ($total_all < 0) ? 0 : $total_all; ?>
						<tr class="order-total">
							<td><?php _e("Gesamtsumme",'dsmart') ?></td>
							<td>
								<div class="price">
									<span style="color:<?php echo $price_color ?> !important" class="total-text"><?php echo ds_price_format_text_no_convert($total_all); ?></span>
								</div>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<p class="vat-text">Alle Preise inklusive USt</p>
		</div>
		<div class="dsmart-section dsmart-method">
			<h3 class="dsmart-title"><?php _e("Kundeninformation",'dsmart') ?></h3>
			<?php if($dsmart_paypal == "on"): ?><label><input type="radio" name="dsmart-method" value="paypal"><img src="<?php echo plugin_dir_url( __FILE__ ).'img/paypal.jpg'; ?>"/></label><?php endif; ?>
			<?php if($dsmart_klarna == "on"): ?><label><input type="radio" name="dsmart-method" value="klarna"><img src="<?php echo plugin_dir_url( __FILE__ ).'img/klarna.jpg'; ?>"/></label><?php endif; ?>
			<label><input type="radio" name="dsmart-method" value="cash"><strong><?php echo $dsmart_barzahlung; ?></strong></label>
			<?php if(is_array($dsmart_custom_method) && count($dsmart_custom_method) > 0):
				foreach ($dsmart_custom_method as $value) { ?>
					<label><input type="radio" name="dsmart-method" value="<?php echo $value; ?>"><strong><?php echo $value; ?></strong></label>
				<?php }
			endif; ?>
		</div>
		<div class="dsmart-section dsmart-checkbox">
			<label><input type="checkbox" name="dsmart-ar" class="dsmart-ar"><?php _e("Alternative Rechnungsadresse"); ?></label>
			<div class="show-data">
				<h3 class="dsmart-title"><?php _e("Rechnungsadresse"); ?></h3>
				<div class="form-group">
					<label><?php _e("Anrede"); ?><span class="required-symbol">*</span></label>
					<select name="r_prefix" class="dsmart-field">
						<option value=""><?php _e("Bitte wählen"); ?></option>
						<option value="0"><?php _e("Herr"); ?></option>
						<option value="1"><?php _e("Frau"); ?></option>
					</select>
				</div>
				<div class="form-group">
					<label><?php _e("Vorname"); ?><span class="required-symbol">*</span></label>
					<input type="text" name="r_first_name" class="dsmart-field"/>
				</div>
				<div class="form-group">
					<label><?php _e("Nachname"); ?><span class="required-symbol">*</span></label>
					<input type="text" name="r_last_name" class="dsmart-field"/>
				</div>
				<div class="form-group">
					<label><?php _e("Firma"); ?><span class="required-symbol">*</span></label>
					<input type="text" name="r_company" class="dsmart-field"/>
				</div>
				<div class="form-group">
					<label><?php _e("PLZ"); ?><span class="required-symbol">*</span></label>
					<input type="text" name="r_zipcode" class="dsmart-field"/>
				</div>
				<div class="form-group">
					<label><?php _e("Stadt"); ?><span class="required-symbol">*</span></label>
					<input type="text" name="r_city" class="dsmart-field"/>
				</div>
				<div class="form-group">
					<label><?php _e("Straße"); ?><span class="required-symbol">*</span></label>
					<input type="text" name="r_street" class="dsmart-field"/>
				</div>
			</div>
		</div>
		<div class="dsmart-section dsmart-checkbox">
			<label><input type="checkbox" name="dsmart-bab"><?php _e("Bewirtungsbeleg als Bon"); ?></label>
		</div>
		<div class="dsmart-section dsmart-terms">

			<label><input type="checkbox" name="dsmart-term-condition" checked><?php 
			if($dsmart_term_text == ""){
				_e('Ich habe die <a href="'.home_url('/allgemeinen-geschaftsbedingungen/').'">allgemeinen Geschäftsbedingungen</a> gelesen und akzeptiert.','dsmart');
			}else{
				echo $dsmart_term_text;
			} ?></label>
			
		</div>
		<div class="dsmart-section dsmart-place-order">
			<?php if(get_page_id_by_template('templates/cart-page.php') != false): ?>
				<a style="background-color: <?php echo $button_color?> !important;" href="<?php echo get_page_link(get_page_id_by_template('templates/cart-page.php')); ?>" class="dsmart-button"><?php _e("Zurück",'dsmart'); ?></a>
			<?php endif; ?>	
			<button style="background-color: <?php echo $button_color?> !important;" type="button" name="place-order" class="dsmart-button"><?php _e("Bestellung abschicken",'dsmart'); ?></button>
		</div>
		<div class="book-loading"><img src="<?php echo plugin_dir_url( __FILE__ ).'img/loading.gif'; ?>"></div>
	<?php } ?>
</div>
<?php get_footer(); ?>