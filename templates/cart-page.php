<?php 
$coupon_notify = "";
if(isset($_COOKIE['coupon_notify'])){
	$coupon_notify = $_COOKIE['coupon_notify'];
	setcookie('coupon_notify', null, time()-2592000, '/', NULL, 0);
}
if(isset($_COOKIE['cart']) && $_COOKIE['cart'] != ""){
	$cart = unserialize(base64_decode($_COOKIE['cart']));
}else{
	$cart = array();
}
$cart = make_cart_items_available($cart);

$tax 					= get_option('dsmart_tax');
$total_after_tax 		= 100 - intval($tax);
$coupon_price 			= 0;
$total_all 				= 0;
$total_all_before 		= 0;
$total_all_use_coupon = 0;
$total_all_before_use_coupon = 0;
$taxes                  = 0;
$check 					= false;
$dsmart_method_ship 	= get_option('dsmart_method_ship');
$dsmart_method_direct 	= get_option('dsmart_method_direct');
$dsmart_cart_text 		= get_option('dsmart_cart_text');	
$dsmart_cart_color 		= get_option('dsmart_cart_color');	
$dsmart_cart_background = get_option('dsmart_cart_background');	

$type_promotion 		= get_option('type_promotion');	
$promotion 				= get_option('promotion');	
$type_promotion_2 		= get_option('type_promotion_2');	
$promotion_2 			= get_option('promotion_2');	
$has_discount 			= is_discount_time();
$back_link_in_cart 		= get_option('back_link_in_cart');
$tax_shipping 		    = get_option('tax_shipping');
$check_zipcode 		    = (get_option('zipcode_status') == "on") ? true : false;
$profile_zipcode = "";
if(is_user_logged_in()):
	$current_user = wp_get_current_user();
	$address = (is_array(get_user_meta($current_user->ID,'address',true))) ? get_user_meta($current_user->ID,'address',true) : array();
	$full_address = "";
    /*if($address['customer_etage'] != ""){
        $full_address = $address['customer_etage'].", ";
    }*/
    $profile_zipcode = $address['customer_zipcode'];
    if($check_zipcode == false): 
    	$full_address .= $address['customer_street'].', '.$address['customer_zipcode'].' '.$address['customer_city'];
    else:
		$full_address .= $address['customer_street'].', '.$address['customer_zipcode'].' '.$address['customer_city'];
    endif;
else:
	$address = array();
endif;

$button_color = get_option('button_color', '#50aecc');
$price_color = get_option('price_color', '#b28e2d');
get_header(); ?>
<div class="cart-page container">
	<?php if(count($cart) > 0): ?>
		<?php if($coupon_notify != ""){show_coupon_notify($coupon_notify);} ?>
		<div class="dsmart-notify"></div>
		<div class="dsmart-table">
			<table class="table" id="dsmart-cart-table">
				<thead>
					<tr>
						<th></th>
						<th><?php _e("Produkt",'dsmart') ?></th>
						<th><?php _e("Preis",'dsmart') ?></th>
						<th><?php _e("Anzahl",'dsmart') ?></th>
						<th><?php _e("Zwischensumme",'dsmart') ?></th>
					</tr>
				</thead>	
				<tbody>
					<?php  foreach ($cart as $key_item => $value_item) {
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

						$taxes_price = 0;
						$status_item 	= dsmart_field('status',$product_id);
						$vat_item 		= dsmart_field('vat',$product_id);
						$taxes_item 	= dsmart_field('taxes',$product_id);
						
						if($taxes_item != ""){
							$taxes_price 	= $price - round($price/(1+$taxes_item/100),2);
							$taxes = $taxes + $taxes_price;
						}elseif($vat_item != ""){
							$taxes_price 	= $price - round($price/(1+$vat_item/100),2);
							$taxes 	= $taxes + $taxes_price;
						} ?>
				 		<tr data-id="<?php echo $key_item; ?>">
				 			<?php if(has_post_thumbnail($product_id)):
							    $current_img =  wp_get_attachment_url(get_post_thumbnail_id($product_id));
							else:
								$current_img =  "";
							endif; ?>
							<td class="dsmart-action">
								<span class="dsmart-remove-item">x</span>
							</td>
							<td class="dsmart-item" data-title="<?php _e("Produkt",'dsmart'); ?>">
								<?php if($current_img != ""): ?><span class="thumb"><img src="<?php echo $current_img; ?>" width="120" alt="<?php echo get_the_title($product_id); ?>"/></span><?php endif; ?>
								<span class="dsmart-remove-item dsmart-remove-item-mobile">x</span>
								<h4 class="dsmart-title" style="vertical-align:middle !important">
									<span><?php echo get_the_title($product_id); ?></span>
									<?php if(check_product_can_use_coupon_or_not($product_id) == false): ?>
										<em><?php _e("Kein Rabatt möglich!") ?></em>
									<?php endif;?>
								</h4>
								<?php
								
								$isExtra = has_extra($value_item);
								$isVariable = has_variable($value_item);
								$isSidedish = has_sidedish($value_item);

								if($isExtra || $isVariable || $isSidedish):?>
									<div class="variable-product cart-variable">
										<?php 
											display_sidedish($isSidedish, $product_id, $sidedish_info); 
											display_variable($isVariable, $variable_id, $product_id);
											display_extra($isExtra, $product_id, $extra_info); 
										?>	
									</div>
								<?php endif; ?>	
							</td>
							<td class="price" data-title="<?php _e("Preis",'dsmart'); ?>">
								<?php if($status_item == "instock" && $price_item != ""){ ?>
									<div style="color:<?php echo $price_color ?> !important" class="price"><?php echo ds_price_format_text($price_item); ?></div>
								<?php }else{ ?>
									--
								<?php }?>
							</td>
							<td class="quantity" data-title="<?php _e("Anzahl",'dsmart'); ?>">
								<div class="quantity-wrap flex-list">
									<button type="button" class="minus"><i class="fa fa-minus" aria-hidden="true"></i></button>
									<input type="number" name="quantity" class="form-control input-quantity" value="<?php echo $quantity === "0" ? 1 : $quantity; ?>" min="1"/>
									<button type="button" class="plus"><i class="fa fa-plus" aria-hidden="true"></i></button>
								</div>
							</td>
							<td class="dsmart-subprice" data-title="<?php _e("Zwischensumme",'dsmart'); ?>">
								<?php if($status_item == "instock" && $price_item != ""){
									$total_all = $total_all_before = $total_all + $price;
									if(check_product_can_use_coupon_or_not($product_id) == true){
										$total_all_use_coupon = $total_all_before_use_coupon = $total_all_use_coupon + $price;
									} ?>
									<div style="color:<?php echo $price_color ?> !important" class="price"><?php echo ds_price_format_text_no_convert($price); ?></div>
								<?php }else{
									$check = true;
									unset($cart[$key_item]); ?>
									<p class="error"><?php _e("Produkt ist ausverkauft.",'dsmart'); ?></p>
								<?php }?>
							</td>
				 		</tr>
					<?php 						
					}
					if($check == true){
						setcookie('cart', base64_encode(serialize($cart)), time()+2592000, '/', NULL, 0);
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="6">
							<div class="dsmart-coupon">
								<form action="#" method="POST">
									<input type="text" name="coupon_code" class="coupon-code" id="coupon_code" placeholder="<?php _e("Rabattcode",'dsmart'); ?>" required>
									<button style="background-color: <?php echo $button_color?> !important;" type="submit" class="button dsmart-button" name="dsmart_apply_coupon" value="<?php _e("Übernehmen",'dsmart') ?>"><?php _e("Übernehmen",'dsmart') ?></button>
								</form>
							</div>
							<div class="update-cart"><button style="background-color: <?php echo $button_color?>" type="button" name="update_cart" class="dsmart-button"><?php _e('Warenkorb Aktualisieren','dsmart'); ?></button></div>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php
		 if($dsmart_cart_text != ''): ?>
			<p class="dsmart-cart-note" style="color: <?php echo $dsmart_cart_color; ?>;background-color: <?php echo $dsmart_cart_background; ?>;"><?php echo $dsmart_cart_text; ?></p>
		<?php endif; ?>		
		<div class="dsmart-cart-total">
			<div class="shipping_caculate">
				<?php if($dsmart_method_ship == "on" || $dsmart_method_direct == "on"): ?>
					<h2><?php _e("Versandart",'dsmart') ?></h2>
					<div class="select-wrap">
						<select name="choose-shipping" class="form-control dsmart-field nice-select-custom">
							<option value=""><?php _e("Versandmethode auswählen",'dsmart'); ?></option>
							<?php if($dsmart_method_ship == "on"): ?><option value="shipping"><?php _e("Lieferung",'dsmart'); ?></option><?php endif;?>
							<?php if($dsmart_method_direct == "on"): ?><option value="direct"><?php _e("im Laden Abholen",'dsmart'); ?></option><?php endif;?>
						</select>
					</div>
					<?php if($dsmart_method_ship == "on"): ?>
						<div class="select-wrap get-current-location"/>
							<div class="select-wrap get-delivery-date"/>
								<input type="text" name="delivery_date" class="dsmart-field ds-datepicker" placeholder="<?php _e("Wählen sie ein Datum",'dsmart') ?>" readonly/>
							</div>
							<div class="select-wrap get-delivery-time"/>
								<input type="text" name="delivery_time" class="dsmart-field timepicker2" placeholder="<?php _e("Zeit wählen",'dsmart') ?>"/>
							</div>
							<?php if($check_zipcode == true): ?>
								<input type="text" class="form-control dsmart-field" id="zipcode-location" name="zipcode-location" value="<?php echo (isset($_COOKIE['filled_zipcode'])) ? $_COOKIE['filled_zipcode'] : $profile_zipcode; ?>" placeholder="<?php _e("Zipcode",'dsmart') ?>">
							<?php else: ?>
								<input type="text" class="form-control dsmart-field" id="user-location" name="user-location" placeholder="<?php _e("Adresse eingeben",'dsmart') ?>" value="<?php echo isset($full_address) ? $full_address : ""; ?>">
								<button type="button" class="dsmart-button" name="current-location" id="current-location" style="display: none;"><?php _e("Momentane Adresse",'dsmart') ?></button>
								<input type="hidden" name="latitude"/>
								<input type="hidden" name="longitude">
								<div class="list-suggestion-address"></div>
							<?php endif; ?>
						</div>
						<?php if($check_zipcode == false): ?>
							<script type="text/javascript">
								function initMap() {
									var startLocation = document.getElementById('user-location');
									new google.maps.places.Autocomplete(startLocation);
									var geocoder = new google.maps.Geocoder();
									autocomplete = new google.maps.places.Autocomplete(jQuery("#user-location").get(0));
									if(jQuery("#user-location").val() != ""){
										var address = document.getElementById( 'user-location' ).value;
										geocoder.geocode( { 'address' : address }, function( results, status ) {
									        if( status == google.maps.GeocoderStatus.OK ) {
									            jQuery("input[name=latitude]").val(results[0].geometry.location.lat());
												jQuery("input[name=longitude]").val(results[0].geometry.location.lng());
									        } else {
									            alert( 'Geocode was not successful for the following reason: ' + status );
									        }
									    } );
									}
									google.maps.event.addListener(autocomplete, 'place_changed', function() {
										var address = jQuery("#user-location").val();
										geocoder.geocode( { 'address': address}, function(results, status) {
											if (status == google.maps.GeocoderStatus.OK) {
											    var latitude = results[0].geometry.location.lat();
												var longitude = results[0].geometry.location.lng();
												jQuery("input[name=latitude]").val(latitude);
												jQuery("input[name=longitude]").val(longitude);
												let shipping_method = jQuery(".dsmart-cart-total select[name=choose-shipping]").val();
										        let user_location = jQuery(".dsmart-cart-total input[name=user-location]").val();
										        let delivery_time = jQuery("input[name=delivery_time]").val();
										        if(user_location != "" && latitude != "" && longitude != ""){
										            jQuery(".dsmart-notify").hide();
										            jQuery.ajax({
										                type: 'POST',
										                dataType: 'json',       
										                url: bookingVars.ajaxurl,
										                data:{
										                    'action':'get_shipping_fee',
										                    'shipping_method' : shipping_method,
										                    'user_location' : user_location,
										                    'latitude' : latitude,
										                    'longitude' : longitude,
										                    'delivery_time' : delivery_time
										                },
										                beforeSend:function(){
										                    jQuery(".book-loading").show();
										                },
										                success:function(data){
										                    jQuery(".book-loading").hide();
										                    if(data.check == false){
										                        jQuery(".dsmart-notify").removeClass("dsmart-danger").removeClass("dsmart-success");
										                        jQuery(".dsmart-notify").show();
										                        jQuery(".dsmart-notify").addClass("dsmart-danger");
										                        jQuery(".dsmart-notify").text(data.message);
										                        var time = bookingVars.popup_time;
																jQuery(".popup-alert").remove();
																$("body").append('<div class="popup-alert"><div class="popup-alert-wrap">'+data.message+'</div></div>');
														        jQuery(".popup-alert").fadeIn();
																setTimeout(function(){
																	jQuery(".popup-alert").fadeOut();
																},time);
										                    }else{
										                        jQuery(".shipping-text").text(data.shipping);
										                        /*$(".tax-text7").text(data.vat7);
										                        $(".tax-text19").text(data.vat19);*/
										                        jQuery(".tax-taxes").text(data.taxes);
										                        jQuery(".total-text").text(data.total);
										                        jQuery(".cart-coupon").remove();
										                        jQuery.each(data.coupon, function (key, val) {
										                            var $coupon = '<tr class="cart-coupon" data-type="'+val.type+'" data-coupon="'+key+'"><td><span class="remove-coupon">x</span>Rabattcode ('+key+')</td><td><span class="coupon-text">'+val.price+'</span></td></tr>';
										                            jQuery(".order-total").before($coupon);
										                        });                                 
										                        jQuery(".cart-discount .percent").text(' ('+data.reduce_percent+')');
										                        jQuery(".cart-discount .number").text('- '+data.reduce);
										                        if(data.has_reduce === true){    
										                            jQuery(".cart-discount").removeClass("hidden");
										                        }else{
										                            jQuery(".cart-discount").addClass("hidden");
										                        }
										                    }
										                }
										            });
										        }else if(user_location != "" && latitude == "" && longitude == ""){
										            jQuery(".dsmart-notify").removeClass("dsmart-danger").removeClass("dsmart-success");
										            jQuery(".dsmart-notify").show();
										            jQuery(".dsmart-notify").addClass("dsmart-danger");
										            jQuery(".dsmart-notify").text("Die Adresse existiert nicht in Google Maps");
										        }else{
										            jQuery(".dsmart-notify").removeClass("dsmart-danger").removeClass("dsmart-success");
										            jQuery(".dsmart-notify").show();
										            jQuery(".dsmart-notify").addClass("dsmart-danger");
										            jQuery(".dsmart-notify").text("Die Adresse existiert nicht in Google Maps");
										        }
										    } 
										});
									});
								}
							</script>
							<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_google_map_key(); ?>&libraries=places&callback=initMap" async defer></script>
						<?php endif; ?>
				    <?php endif;?>
				    <?php if($dsmart_method_direct == "on"): ?>
						<div class="select-wrap get-current-time"/>
							<input type="text" name="shipping_time" class="dsmart-field timepicker" placeholder="<?php _e("Zeit wählen",'dsmart') ?>"/>
						</div>
					<?php endif; ?>
				<?php else: ?>
					<div class="alert alert-danger"><?php _e('Derzeit gibt es keine Zahlungsmethoden.','dsmart') ?></div>
				<?php endif;?>
			</div>
			<div class="cart_totals">
				<h2><?php _e("Gesamtsumme",'dsmart') ?></h2>
				<table cellspacing="0" class="dsmart-table">
					<tbody>
						<tr class="cart-shipping">
							<td><?php _e("Lieferungskosten",'dsmart') ?></td>
							<td><span class="shipping-text"><?php echo ds_price_format_text_no_convert(0); ?></span></td>
						</tr>
						<?php
						$reduce = '';
						if($promotion != null || $promotion_2 != null):  ?>
							<tr class="cart-discount hidden">
								<td><?php _e("Rabatt",'dsmart'); ?> <span class="percent"></span></td>
								<td class="number"></td>
							</tr>
						<?php endif;	
						 if(isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != ""):
						 	$coupon = explode(',',$_COOKIE['coupon']);
							foreach ($coupon as $key => $item) {
								$coupon_shipping = dsmart_field('coupon_shipping');
								if(check_coupon_available($item) == 1){
									$coupon_price = get_price_of_coupon($item,$total_all_use_coupon);
									$total_all = $total_all - $coupon_price;
									$total_all_use_coupon = $total_all_use_coupon - $coupon_price; ?>
									<tr class="cart-coupon" data-type="<?php echo $coupon_shipping; ?>" data-coupon="<?php echo $item; ?>">
										<td><span class="remove-coupon">x</span><?php _e("Rabattcode",'dsmart') ?> (<?php echo $item; ?>)</td>
										<td><span class="coupon-text">- <?php echo ds_price_format_text_no_convert($coupon_price); ?></span></td>
									</tr>
								<?php }
							}?>
						<?php endif; 
						$total_all += $taxes;
						$total_all = ($total_all < 0) ? 0 : $total_all; ?>
						<tr class="order-total">
							<td><?php _e("Gesamtsumme",'dsmart') ?></td>
							<td>
								<div class="price">
									<span style="color:<?php echo $price_color ?> !important" class="total-text"><?php echo ds_price_format_text_no_convert($total_all); ?></span>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="vat-text">Alle Preise inklusive USt</p>
				<div class="proceed-to-checkout">
					<?php if($back_link_in_cart != ''): ?>
						<a style="background-color: <?php echo $button_color?> !important;" href="<?php echo $back_link_in_cart; ?>" class="dsmart-button"><?php _e("Weiter Einkaufen",'dsmart'); ?></a>
					<?php endif; ?>	
					<button style="background-color: <?php echo $button_color?> !important;" type="button" name="checkout-now" class="dsmart-button"><?php _e("Zur Kasse",'dsmart'); ?></button>
				</div>		
			</div>
		</div>
		<div class="book-loading"><img src="<?php echo plugin_dir_url( __FILE__ ).'img/loading.gif'; ?>"></div>
	<?php else: ?>
		<div class="alert alert-danger"><?php _e("Einkaufswagen ist leer.") ?></div>
		<?php if($back_link_in_cart != ''): ?>
			<a style="background-color: <?php echo $button_color?> !important;margin: 0 auto;display: block;text-align: center;width: 100px;" href="<?php echo $back_link_in_cart; ?>" class="dsmart-button"><?php _e("Zurück",'dsmart'); ?></a>
		<?php endif; ?>	
	<?php endif;?>
</div>
<?php get_footer(); ?>