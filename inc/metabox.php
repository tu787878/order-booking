<?php 
add_action('admin_init', 'book_add_meta_boxes', 1);
function book_add_meta_boxes() {
	add_meta_box( 'book-info-tour', 'Produkt Informationen', 'product_info_display', 'product', 'normal', 'default');
	add_meta_box('gallery-metabox','Gallery','gallery_meta_callback','product','normal','high');
	add_meta_box('coupon-metabox','Bestellungen','coupon_info_display','coupon','normal','high');
	add_meta_box('orders-metabox','Bestellungen','orders_info_display','orders','normal','high');
}
// product
function product_info_display() {
	global $post;
	$dsmart_stock = get_option('dsmart_stock');
	$sku = dsmart_field('sku');
	$price = dsmart_field('price');
	$status = dsmart_field('status');
	$desc = dsmart_field('desc');
	$sidedish_label = dsmart_field('sidedish_text');
	if($sidedish_label == null || $sidedish_label == '')
    {
        $sidedish_label = "Beilage";
    }
	$sharp = dsmart_field('sharp');
	/*$vat = dsmart_field('vat');
	$taxes = dsmart_field('taxes');*/
	$can_not_use_coupon = dsmart_field('can_not_use_coupon');
	
	$vegetarian = dsmart_field('vegetarian');
	if(!$status){
		$status = "";
	}
	wp_nonce_field( 'info_product_nonce', 'info_product_nonce' ); 
	$current_term = wp_get_post_terms(intval($post->ID), 'product-cat');
	$flag = false;
	$meta['quantity'] = dsmart_field('quantity');
	$meta['varialbe_price'] = dsmart_field('varialbe_price');
	$meta['extra_name'] = dsmart_field('extra_name');
	$meta['extra_price'] = dsmart_field('extra_price');
	$meta['sidedish_name'] = dsmart_field('sidedish_name');
	$meta['sidedish_price'] = dsmart_field('sidedish_price');
	?>
	<table id="product-info" width="100%">
		<tbody>
			<tr>
				<td><?php echo __("Rabatt / Coupon nicht einsetzbar!","booking-order"); ?></td>
				<td class="radio-wrap">
					<label>						
						<input type="checkbox" name="can_not_use_coupon" value="1" <?php echo  ($can_not_use_coupon == '1') ? 'checked' : ''; ?>>
					</label>
				</td>
			</tr>
			<tr>
				<td><?php echo __("SKU","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="sku" value="<?php echo $sku; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Preis","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="price" value="<?php echo $price; ?>" required/>
				</td>
			</tr>
			<?php if($dsmart_stock != "1"){ ?>
				<tr>
					<td><?php echo __("Status","booking-order"); ?></td>
					<td>
						<select name="status" class="widefat">
							<?php ds_status_product($status);?>
						</select>
					</td>
				</tr>
			<?php }else{
				if($status == ""){
					$status = "instock";
				} ?>
				<input type="hidden" name="status" value="<?php echo $status; ?>"/>
			<?php }?>
			<!-- <tr>
				<td><?php echo __("MWST","booking-order");?></td>
				<td>
					<select name="vat" class="widefat">
						<?php ds_vat_product($vat);?>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Taxes(%)","booking-order");?></td>
				<td>
					<input type="number" class="widefat" step="0.1" name="taxes" value="<?php echo $taxes; ?>"/>
				</td>
			</tr> -->
			<tr>
				<td><?php echo __("Zusatzstoffe","booking-order"); ?></td>
				<td>
					<textarea name="desc" class="widefat"><?php echo $desc; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Schärfe","booking-order"); ?></td>
				<td>
					<select name="sharp" class="widefat">
						<option value=""><?php _e("Nein") ?></option>
						<option value="1" <?php if($sharp == "1"){echo 'selected';} ?>><?php _e("leicht scharf (1)") ?></option>
						<option value="2" <?php if($sharp == "2"){echo 'selected';} ?>><?php _e("mittelscharf (2)") ?></option>
						<option value="3" <?php if($sharp == "3"){echo 'selected';} ?>><?php _e("sehr scharf (3)") ?></option>
					</select>
				</td>
			</tr>
			<tr class="vegetarian">
				<td><?php echo __("Vegetarier","booking-order"); ?></td>
				<td class="radio-wrap">
					<label>						
						<input type="checkbox" name="vegetarian" value="<?php echo  ($vegetarian == '1') ? '1' : '0'; ?>" <?php echo  ($vegetarian == '1') ? 'checked' : ''; ?>>
						<?php echo __("Vegetarisch (grüner Blatt)","booking-order"); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Ausgewählte Product") ?></td>
				<td>
					<table class="variable-table acf-table">
						<thead>
							<tr>
								<th><?php echo __("Name","dsmart"); ?></th>
								<th><?php echo __("Preis", "dsmart"); ?></th>
								<th class="remove-td"></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="2">
									<input type="button" value="<?php _e('Variable hinzufügen', 'noo'); ?>" class="button button-default dsmart-clone-fields" data-template="<tr class='fields-group'><td><input type='text' name='quantity[]' class='widefat' /></td><td><input type='text' name='varialbe_price[]' class='widefat' /></td><td class='remove-td'><a href='javascript:void()' class='dsmart-remove-fields'><?php _e('x', 'noo'); ?></a></td></tr>"/>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<?php
							if($meta['quantity'] != null &&  !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']))):
								foreach( $meta['quantity'] as $index => $variable_val ) : 
									if($meta['quantity'][$index] != null && $meta['varialbe_price'][$index] != null):	?>
										<tr class="fields-group">
											<td><input type="text" name="quantity[]" class="widefat" value="<?php echo esc_attr($meta['quantity'][$index]); ?>" /></td>
											<td><input type="text" name="varialbe_price[]" class="widefat" value="<?php echo esc_attr($meta['varialbe_price'][$index]); ?>" /></td>
											<td class="remove-td"><a href="javascript:void()" class="dsmart-remove-fields"><?php _e('x', 'noo'); ?></a></td>
										</tr>
								<?php endif; endforeach; 
							endif;	 ?>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td style="width: 150px;"><?php echo __("Extra Produkt") ?></td>
				<td>
					<table class="variable-table acf-table">
						<thead>
							<tr>
								<th><?php echo __("Name","dsmart"); ?></th>
								<th><?php echo __("Extra Methoden","dsmart"); ?></th>
								<th><?php echo __("Price", "dsmart"); ?></th>
								<th class="remove-td"></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="2">
									<input type="button" value="<?php _e('Extra Produkt hinzufügen', 'noo'); ?>" class="button button-default dsmart-clone-fields" data-template="
									<tr class='fields-group'>
										<td><input type='text' name='extra_name[]' class='widefat' /></td>
										<td> 
											<label><input type='radio' name='extra_type' data-name='extra_type' value='tick' checked> <?php echo __("nur Auswahl","dsmart"); ?></label>
											<label><input type='radio' name='extra_type' data-name='extra_type' value='tick_quantity'> <?php echo __("Auswahl und Menge","dsmart"); ?></label>
										</td>
										<td><input type='text' name='extra_price[]' class='widefat' /></td>
										<td class='remove-td'><a href='javascript:void()' class='dsmart-remove-fields'><?php _e('x'); ?></a></td>
									</tr>"/>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<?php $a=1;
							if($meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']))):
								foreach($meta['extra_name'] as $index => $extra_val ) : 
									$extra_type = dsmart_field('extra_type'.$a);
									if($meta['extra_name'][$index] != null && $meta['extra_price'][$index] != null):	?>
										<tr class="fields-group">
											<td><input type='text' name='extra_name[]' class='widefat' value="<?php echo esc_attr($meta['extra_name'][$index]); ?>" /></td>
											<td>
												<label>
													<input type='radio' name='extra_type<?php echo $a; ?>' value='tick' <?php checked( 'tick', $extra_type) ?>> 
													 <?php echo __("only tick (default quantity=1)","dsmart"); ?>
												</label>
												<label>
													<input type='radio' name='extra_type<?php echo $a; ?>' value='tick_quantity' <?php checked( 'tick_quantity', $extra_type) ?>> 
													<?php echo __("Tick and choose quantity","dsmart"); ?>
												</label>
											</td>
											<td><input type="text" name="extra_price[]" class="widefat" value="<?php echo esc_attr($meta['extra_price'][$index]); ?>" /></td>
											<td class="remove-td"><a href="javascript:void()" class="dsmart-remove-fields"><?php _e('x'); ?></a></td>
										</tr>
								<?php endif; $a++; endforeach;
							endif; ?>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Beilage-Text","booking-order"); ?></td>
				<td>
					<textarea name="sidedish_text" class="widefat"><?php echo $sidedish_label; ?></textarea>
				</td>
			</tr>
			<tr>
				<td style="width: 150px;"><?php echo __("Beilage") ?></td>
				<td>
					<table class="variable-table acf-table">
						<thead>
							<tr>
								<th><?php echo __("Name","dsmart"); ?></th>
								<th><?php echo __("Price", "dsmart"); ?></th>
								<th class="remove-td"></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="2">
									<input type="button" value="<?php _e('Beilage hinzufügen', 'noo'); ?>" class="button button-default dsmart-clone-fields" data-template="
									<tr class='fields-group'>
										<td><input type='text' name='sidedish_name[]' class='widefat' /></td>
										<td><input type='text' name='sidedish_price[]' class='widefat' /></td>
										<td class='remove-td'><a href='javascript:void()' class='dsmart-remove-fields'><?php _e('x'); ?></a></td>
									</tr>"/>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<?php $a=1;
							if($meta['sidedish_name'] != null && !empty(array_filter($meta['sidedish_name']))):
								foreach($meta['sidedish_name'] as $index => $extra_val ) : 
									if($meta['sidedish_name'][$index] != null):	?>
										<tr class="fields-group">
											<td><input type='text' name='sidedish_name[]' class='widefat' value="<?php echo esc_attr($meta['sidedish_name'][$index]); ?>" /></td>
											<td><input type="text" name="sidedish_price[]" class="widefat" value="<?php echo esc_attr($meta['sidedish_price'][$index]); ?>" /></td>
											<td class="remove-td"><a href="javascript:void()" class="dsmart-remove-fields"><?php _e('x'); ?></a></td>
										</tr>
								<?php endif; $a++; endforeach;
							endif; ?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<?php
}
add_action('save_post', 'info_product_save');
function info_product_save($post_id) {
	if ( ! isset( $_POST['info_product_nonce'] ) ||
	! wp_verify_nonce( $_POST['info_product_nonce'], 'info_product_nonce' ) ){
		return;
	}
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;
	
	if (!current_user_can('edit_post', $post_id))
		return;
	$sku = $_POST['sku'];
	update_post_meta( $post_id, 'sku', $sku );
	$price = $_POST['price'];
	update_post_meta( $post_id, 'price', $price );
	/*$vat = $_POST['vat'];
	update_post_meta($post_id,'vat',$vat);
	$taxes = $_POST['taxes'];
	update_post_meta($post_id,'taxes',$taxes);*/
	$status = $_POST['status'];
	update_post_meta( $post_id, 'status', $status );
	$desc = $_POST['desc'];
	update_post_meta( $post_id, 'desc', $desc );
	$sidedish_label = $_POST['sidedish_text'];
	update_post_meta( $post_id, 'sidedish_text', $sidedish_label );
	$sharp = $_POST['sharp'];
	update_post_meta( $post_id, 'sharp', $sharp );
	$vegetarian = $_POST['vegetarian'];
	update_post_meta( $post_id, 'vegetarian', $vegetarian );
	$type_promotion = $_POST['type_promotion'];
	update_post_meta( $post_id, 'type_promotion', $type_promotion );
	$promotion = $_POST['promotion'];
	update_post_meta( $post_id, 'promotion', $promotion );
	$quantity = $_POST['quantity'];
	update_post_meta( $post_id, 'quantity', $quantity );
	$varialbe_price = $_POST['varialbe_price'];
	update_post_meta( $post_id, 'varialbe_price', $varialbe_price );
	$extra_name = $_POST['extra_name'];
	update_post_meta( $post_id, 'extra_name', $extra_name );
	$can_not_use_coupon = $_POST['can_not_use_coupon'];
	update_post_meta($post_id,'can_not_use_coupon',$can_not_use_coupon);
	
	$extra_price = $_POST['extra_price'];
	update_post_meta( $post_id, 'extra_price', $extra_price );
	$meta['extra_name'] = dsmart_field('extra_name');
	$a=1;
	if($meta['extra_name'] != null && !empty(array_filter($meta['extra_name']))):
		foreach($meta['extra_name'] as $index => $extra_val ) : 
			update_post_meta( $post_id, 'extra_type'.$a, $_POST['extra_type'.$a]);
		$a++; endforeach;	
	endif;	

	$sidedish_name = $_POST['sidedish_name'];
	update_post_meta( $post_id, 'sidedish_name', $sidedish_name );
	$sidedish_price = $_POST['sidedish_price'];
	update_post_meta( $post_id, 'sidedish_price', $sidedish_price );

}
//Product gallery
function gallery_meta_callback($post) {
   wp_nonce_field( basename(__FILE__), 'gallery_meta_nonce' );
   $ids = get_post_meta($post->ID, 'book_gallery_id', true);
?>
 <table class="form-table">
   <tr><td>
      <a class="gallery-add button" href="#" data-uploader-title="Add new image" data-uploader-button-text="<?php _e('Add more image') ?>"><?php _e('Add more image') ?></a>
      <ul id="gallery-metabox-list">
        <?php if ($ids) : foreach ($ids as $key => $value) : $image = wp_get_attachment_image_src($value); ?>
        <li>
           <input type="hidden" name="book_gallery_id[<?php echo $key; ?>]" value="<?php echo $value; ?>">
           <img class="image-preview" src="<?php echo $image[0]; ?>">
           <a class="change-image button button-small" href="#" data-uploader-title="<?php _e('Change image'); ?>" data-uploader-button-text="<?php _e('Change image'); ?>"><?php _e('Change image'); ?></a><br>
           <small><a class="remove-image" href="#"><?php _e('Delete image'); ?></a></small>
        </li>
        <?php endforeach; endif; ?>
     </ul>
  </td></tr>
 </table>
 <?php }
 function gallery_meta_save($post_id) {
  if (!isset($_POST['gallery_meta_nonce']) || !wp_verify_nonce($_POST['gallery_meta_nonce'], basename(__FILE__))) return;
  if (!current_user_can('edit_post', $post_id)) return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

  if(isset($_POST['book_gallery_id'])) {
    update_post_meta($post_id, 'book_gallery_id', $_POST['book_gallery_id']);
  } else {
    delete_post_meta($post_id, 'book_gallery_id');
  }
 }
add_action('save_post', 'gallery_meta_save');

//coupon info
function coupon_info_display() {
	global $post;
	$coupon_value = dsmart_field('coupon_value');
	$coupon_number_use = dsmart_field('coupon_number_use');
	$coupon_status = dsmart_field('coupon_status');
	$coupon_date_begin = dsmart_field('coupon_date_begin');
	$coupon_date_end = dsmart_field('coupon_date_end');
	$coupon_for = dsmart_field('coupon_for');
	$min_order_apply = dsmart_field('min_order_apply');
	$coupon_option = dsmart_field('coupon_option');
	$max_coupon_value = dsmart_field('max_coupon_value');
	$coupon_shipping = dsmart_field('coupon_shipping');
	$coupon_mutiple = dsmart_field('coupon_mutiple');
	wp_nonce_field( 'info_coupon_nonce', 'info_coupon_nonce' ); 
	?>
	<table id="product-info" width="100%">
		<tbody>
			<tr>
				<td><?php echo __("Rabatt","booking-order"); ?></td>
				<td>
					<label for="percent"><input type="radio" id="percent" name="coupon_option" value="percent" <?php if($coupon_option == "" || $coupon_option == "percent"){echo 'checked';} ?>><?php _e('Rabatt nach Prozente %') ?></label>
					<label for="value"><input type="radio" id="value" name="coupon_option" value="value" <?php if($coupon_option == "value"){echo 'checked';} ?>><?php _e('Rabatt nach Geldwert') ?></label>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Rabattwert","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="coupon_value" value="<?php echo $coupon_value; ?>" required/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Maximium des Rabatt","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="max_coupon_value" value="<?php echo $max_coupon_value; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Rabatt Funktionen","booking-order"); ?></td>
				<td>
					<label for="all"><input type="radio" id="all" name="coupon_shipping" value="" <?php if($coupon_shipping == ""){echo 'checked';} ?>><?php _e('Alle') ?></label>
					<label for="shipping"><input type="radio" id="shipping" name="coupon_shipping" value="shipping" <?php if($coupon_shipping == "shipping"){echo 'checked';} ?>><?php _e('Lieferung') ?></label>
					<label for="direct"><input type="radio" id="direct" name="coupon_shipping" value="direct" <?php if($coupon_shipping == "direct"){echo 'checked';} ?>><?php _e('im Laden Abholen') ?></label>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Kombinierbar","booking-order"); ?></td>
				<td>
					<label for="yes"><input type="checkbox" id="yes" name="coupon_mutiple" value="yes" <?php if($coupon_mutiple == "yes"){echo 'checked';} ?>><?php _e('Ja?') ?></label>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Minium Bestellung für Rabatt","booking-order"); ?></td>
				<td>
					<input type="number" class="widefat" name="min_order_apply" value="<?php echo $min_order_apply; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Bestellung status","booking-order"); ?></td>
				<td>
					<select name="coupon_status" class="widefat">
						<option value="processing"><?php _e("Processing") ?></option>
						<option value="completed"><?php _e("Completed") ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Anzahl der Coupons","booking-order"); ?></td>
				<td>
					<input type="number" class="widefat" name="coupon_number_use" value="<?php echo $coupon_number_use; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Beginn Datum","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat datepicker" name="coupon_date_begin" value="<?php echo $coupon_date_begin; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Ende Datum","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat datepicker" name="coupon_date_end" value="<?php echo $coupon_date_end; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Only apply for user","booking-order"); ?></td>
				<td>
					<select class="widefat dsmart-select" name="coupon_for[]" multiple="multiple">
						<?php echo dsmart_option_user($coupon_for); ?>
					</select>
					<a href="#" class="remove-selected-option"><?php _e('Delete option'); ?></a>
				</td>
			</tr>
		</tbody>
	</table>

	<?php
}
add_action('save_post', 'coupon_info_save');
function coupon_info_save($post_id) {
	if ( ! isset( $_POST['info_coupon_nonce'] ) ||
	! wp_verify_nonce( $_POST['info_coupon_nonce'], 'info_coupon_nonce' ) ){
		return;
	}
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;
	
	if (!current_user_can('edit_post', $post_id))
		return;
	$coupon_value = $_POST['coupon_value'];
	update_post_meta( $post_id, 'coupon_value', $coupon_value );
	$coupon_number_use = $_POST['coupon_number_use'];
	update_post_meta( $post_id, 'coupon_number_use', $coupon_number_use );
	$coupon_status = $_POST['coupon_status'];
	update_post_meta( $post_id, 'coupon_status', $coupon_status );
	$coupon_date_begin = $_POST['coupon_date_begin'];
	update_post_meta( $post_id, 'coupon_date_begin', $coupon_date_begin );
	$coupon_date_end = $_POST['coupon_date_end'];
	update_post_meta( $post_id, 'coupon_date_end', $coupon_date_end );
	$coupon_for = $_POST['coupon_for'];
	update_post_meta( $post_id, 'coupon_for', $coupon_for );
	$min_order_apply = $_POST['min_order_apply'];
	update_post_meta( $post_id, 'min_order_apply', $min_order_apply );
	$coupon_option = $_POST['coupon_option'];
	update_post_meta( $post_id, 'coupon_option', $coupon_option );
	$max_coupon_value = $_POST['max_coupon_value'];
	update_post_meta( $post_id, 'max_coupon_value', $max_coupon_value );
	$coupon_shipping = $_POST['coupon_shipping'];
	update_post_meta($post_id,'coupon_shipping',$coupon_shipping);
	$coupon_mutiple = $_POST['coupon_mutiple'];
	update_post_meta($post_id,'coupon_mutiple',$coupon_mutiple);
}

//order in admin
function orders_info_display() {
	global $post;
	$currency = dsmart_field('currency');
    $customer_name1 = dsmart_field('customer_name1');
    $customer_name2 = dsmart_field('customer_name2');
    $customer_email = dsmart_field('customer_email');
    $customer_etage = dsmart_field('customer_etage');
    $customer_zipcode = dsmart_field('customer_zipcode');
    $customer_phone = dsmart_field('customer_phone');
    $more_additional = dsmart_field('more_additional');
    $items = dsmart_field('item');
    $subtotal = dsmart_field('subtotal');
    $coupon = dsmart_field('coupon');
    $coupon_price = dsmart_field('coupon_price');
    $shipping_method = dsmart_field('shipping_method');
    $user_location = dsmart_field('user_location');
    $user_latitude = dsmart_field('user_latitude');
    $user_longitude = dsmart_field('user_longitude');
    $user_time = dsmart_field('user_time');
    $shipping_fee = dsmart_field('shipping_fee');
    $total = dsmart_field('total');
    $status = dsmart_field('status');
    /*$vat7 = dsmart_field('vat7');
    $vat19 = dsmart_field('vat19');
    $taxes = dsmart_field('taxes');*/
    $method = dsmart_field('method');
    $transition_id = dsmart_field('transition_id');
    $reduce = dsmart_field('reduce');
    $reduce_percent = dsmart_field('reduce_percent');   
    $second_order_number = dsmart_field('second_order_number');
    $user_delivery_time = dsmart_field('user_delivery_time');
    $user_delivery_date = dsmart_field('user_delivery_date');
    $show_second_number = get_option('show_second_number');
    $bab = dsmart_field('bab');
    $ar = dsmart_field('ar');
    if(is_array($ar) && isset($ar['ar']) && $ar['ar'] == 1){
    	$ar_id = 1;
    	$r_prefix = $ar['r_prefix'];
    	$r_first_name = $ar['r_first_name'];
    	$r_last_name = $ar['r_last_name'];
    	$r_company = $ar['r_company'];
    	$r_zipcode = $ar['r_zipcode'];
    	$r_city = $ar['r_city'];
    	$r_street = $ar['r_street'];
    }else{
    	$ar_id = 0;
    	$r_prefix = '';
    	$r_first_name = '';
    	$r_last_name = '';
    	$r_company = '';
    	$r_zipcode = '';
    	$r_city = '';
    	$r_street = '';
    }
	wp_nonce_field( 'info_orders_nonce', 'info_orders_nonce' );?>
	<table id="product-info" width="100%">
		<tbody>
			<tr>
				<td><?php echo __("Währung","booking-order"); ?></td>
				<td>
					<select name="currency" class="widefat">
						<option value="1" <?php if($currency == "1"){echo 'selected';} ?>>$</option>
						<option value="2" <?php if($currency == "2"){echo 'selected';} ?>>€</option>
						<option value="3" <?php if($currency == "3"){echo 'selected';} ?>>CHF</option>
					</select>
				</td>
			</tr>
			<?php if($show_second_number == "1"){ ?>
				<tr>
					<td><?php echo __("Bestellnummer","booking-order"); ?></td>
					<td>
						<input type="text" class="widefat" name="second_order_number" value="<?php echo $second_order_number; ?>" required/>
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td><?php echo __("Nachnahme","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="customer_name1" value="<?php echo $customer_name1; ?>" required/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Vorname","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="customer_name2" value="<?php echo $customer_name2; ?>" required/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Email","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="customer_email" value="<?php echo $customer_email; ?>" required/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Etage","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="customer_etage" value="<?php echo $customer_etage; ?>" />
				</td>
			</tr>
			<tr>
				<td><?php echo __("Postleitzahl","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="customer_zipcode" value="<?php echo $customer_zipcode; ?>" />
				</td>
			</tr>
			<tr>
				<td><?php echo __("Telefon","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="customer_phone" value="<?php echo $customer_phone; ?>" required/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Information","booking-order"); ?></td>
				<td>
					<textarea class="widefat" name="more_additional"><?php echo $more_additional; ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2"><b><?php _e("Bewirtungsbeleg als Bon") ?></b></td>
			</tr>
			<tr>
				<td><?php echo __("Bewirtungsbeleg als Bon","booking-order"); ?></td>
				<td>
					<select name="bab" class="widefat">
						<option value="0" <?php if($bab == "0"){echo 'selected';} ?>>Off</option>
						<option value="1" <?php if($bab == "1"){echo 'selected';} ?>>On</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2"><b><?php _e("Rechnungsadresse") ?></b></td>
			</tr>
			<tr>
				<td><?php echo __("Alternative Rechnungsadresse","booking-order"); ?></td>
				<td>
					<select name="ar_id" class="widefat">
						<option value="0" <?php if($ar_id == "0"){echo 'selected';} ?>>Off</option>
						<option value="1" <?php if($ar_id == "1"){echo 'selected';} ?>>On</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Anrede","booking-order"); ?></td>
				<td>
					<select name="r_prefix" class="widefat">
						<option value=""><?php _e("Bitte wählen"); ?></option>
						<option value="0" <?php if($r_prefix == "0"){echo 'selected';} ?>>Herr</option>
						<option value="1" <?php if($r_prefix == "1"){echo 'selected';} ?>>Frau</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Vorname","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="r_first_name" value="<?php echo $r_first_name; ?>" required/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Nachname","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="r_last_name" value="<?php echo $r_last_name; ?>" required/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Firma","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="r_company" value="<?php echo $r_company; ?>" required/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("PLZ","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="r_zipcode" value="<?php echo $r_zipcode; ?>" required/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Stadt","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="r_city" value="<?php echo $r_city; ?>" required/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Straße","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="r_street" value="<?php echo $r_street; ?>" required/>
				</td>
			</tr>

			<?php 
			 if($items){ ?>
				<tr>
					<td colspan="2"><b><?php _e("Produkt") ?></b></td>
				</tr>
				<?php foreach ($items as $key => $value) { 
					if(isset($value['product_id'])):
                        $product_id = intval($value['product_id']);
                    else:
                        $product_id = $key;
                    endif;  
					$meta['sidedish_name'] = dsmart_field('sidedish_name', $product_id);
                    $meta['sidedish_price'] = dsmart_field('sidedish_price', $product_id);  
					$meta['quantity'] = dsmart_field('quantity',$product_id);
                    $meta['varialbe_price'] = dsmart_field('varialbe_price',$product_id);
                    $meta['extra_name'] = dsmart_field('extra_name',$product_id);
                    $meta['extra_price'] = dsmart_field('extra_price',$product_id);
					$isSidedish = isset($value['sidedish_info']) && $value['sidedish_info'] != null && $meta['sidedish_name'] != null && !empty(array_filter($meta['sidedish_name']));
					$isExtra = isset($value['extra_info']) && $value['extra_info'] != null && $meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']));
					$isVariable = isset($value['variable_id']) && $meta['quantity'] != null && !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']));
                     if($isVariable || $isExtra || $isSidedish):
                        $variable_id = intval(explode('_', $value['variable_id'])[1])-1;
                        $variable_text = '<div class="variable-product cart-variable">';
                        if($isVariable){
							$variable_text .=   '<div class="item">';
                        	$variable_text .=       '<h5>'. __( 'AUSGEWÄHLTE PRODUKT', 'dsmart') .'</h5>';
                        	$variable_text .=       '<p>'. $meta['quantity'][$variable_id] .': '. ds_price_format_text($meta['varialbe_price'][$variable_id]) .'</p>';
                        	$variable_text .=   '</div>';
						}
                        if($isExtra):
                            $extra_info = json_decode(stripslashes($value['extra_info'])); 
                            $extra_text = '<div class="item">';
                            $extra_text .= '<h5>'. __('Extra:', 'dsmart') .'</h5>';
                            $extra_text .= '<ul>';
                            foreach ($extra_info as $extra_key => $extra_value) { 
                                $extra_id = intval(explode('_', $extra_value->extra_id)[1])-1;
                                $extra_quantity = $extra_value->extra_quantity; 
                                $extra_text .= '<li>'. $meta['extra_name'][$extra_id] .'(+'. ds_price_format_text($meta['extra_price'][$extra_id]) .') x ' . $extra_quantity .'</li>';                                   
                           } 
                            $extra_text .= '</ul>';
                        else:
                            $extra_text ='';
                        endif;

						if($isSidedish):
                            $sidedish_info = json_decode(stripslashes($value['sidedish_info'])); 
                            $sidedish_text = '<div class="item">';
                            $sidedish_text .= '<h5>'. __('Beilage:', 'dsmart') .'</h5>';
                            $sidedish_text .= '<ul>';
                            foreach ($sidedish_info as $sidedish_key => $sidedish_value) { 
                                $sidedish_id = intval(explode('_', $sidedish_value->sidedish_id)[1])-1;
                                $sidedish_text .= '<li>'. $meta['sidedish_name'][$sidedish_id] . ($meta['sidedish_price'][$sidedish_id] !== "" ? " (".ds_price_format_text($meta['sidedish_price'][$sidedish_id]).")" : "") .'</li>';                                   
                           } 
                            $sidedish_text .= '</ul>';
                        else:
                            $sidedish_text ='';
                        endif;

                        $variable_text = $variable_text . " " . $sidedish_text . ' '. $extra_text;
                    else:
                        $variable_text = '';
                    endif;
					 ?>
					<tr>
						<td><?php echo '<h4 class="dsmart-title"><span>'.$value['quantity'].' x</span> '.$value['title'] .'</h4>';
                            if($variable_text != '' ):
                                echo $variable_text; 
                            endif;  ?>      
                         </td>
						<td>
							<?php echo ds_price_format_text_with_symbol($value['price'],$currency); ?>
						</td>
					</tr>
				<?php } ?>				
			<?php } ?>
			<tr>
				<td><?php echo __("Lieferung oder Abholung","booking-order"); ?></td>
				<td>
					<select name="shipping_method" class="widefat">
						<option value="shipping" <?php if($shipping_method == "shipping"){echo 'selected';} ?>>Lieferung</option>
						<option value="direct" <?php if($shipping_method == "direct"){echo 'selected';} ?>>im Laden Abholen</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Adresse (nur beim Lieferung)","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="user_location" value="<?php echo $user_location; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Datum Lieferung (nur für Lieferung)","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="user_delivery_date" value="<?php echo $user_delivery_date; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Uhrzeit Lieferung (nur für Lieferung)","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="user_delivery_time" value="<?php echo $user_delivery_time; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Uhrzeit im Laden Abholen","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat timepicker" name="user_time" value="<?php echo $user_time; ?>"/>
				</td>
			</tr>			
			<tr>
				<td><?php echo __("Lieferkosten","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="shipping_fee" value="<?php echo $shipping_fee; ?>"/>
				</td>
			</tr>
			<!-- <tr>
				<td><?php echo __("VAT 7%","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="vat7" value="<?php echo $vat7; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("VAT 19%","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="vat19" value="<?php echo $vat19; ?>"/>
				</td>
			</tr> -->
			<!-- <tr>
				<td><?php echo __("Taxes","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="taxes" value="<?php echo $taxes; ?>"/>
				</td>
			</tr> -->
			<tr>
				<td><?php _e("Zwischensumme","booking-order") ?></td>
				<td>
					<input type="text" class="widefat" name="subtotal" value="<?php echo $subtotal; ?>"/>
				</td>
			</tr>
			 <?php 
            if($reduce != ""): ?>
                <tr>
                    <td><?php _e("Rabatt:",'dsmart'); ?> (-<?php echo $reduce_percent; ?>)</td>
                    <td><input type="text" class="widefat" name="reduce" value="<?php echo $reduce; ?>"/></td>
                </tr>
            <?php endif; ?>
            <?php if($coupon != ""):
            	foreach ($coupon as $couponkey => $couponvalue) {?>
					<tr>
						<td><?php echo __("Coupon","booking-order"); ?></td>
						<td>
							<input type="text" class="widefat" name="coupon[]" value="<?php echo $couponkey; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?php echo __("Coupon value","booking-order"); ?></td>
						<td>
							<input type="text" class="widefat" name="coupon_price[]" value="<?php echo $couponvalue; ?>"/>
						</td>
					</tr>
				<?php }
			endif;?>			
			<tr>
				<td><?php echo __("Gesamtsumme","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="total" value="<?php echo $total; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Bezahl Methode","booking-order"); ?></td>
				<td>
					<select name="method" class="widefat" required>
						<option value="" <?php if($method == ""){echo 'selected';} ?>><?php _e("No selected") ?></option>
						<option value="paypal" <?php if($method == "paypal"){echo 'selected';} ?>><?php _e("Paypal") ?></option>
						<option value="klarna" <?php if($method == "klarna"){echo 'selected';} ?>><?php _e("Klarna") ?></option>
						<option value="cash" <?php if($method == "cash"){echo 'selected';} ?>><?php _e("Barzahlung") ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Transaction ID","booking-order"); ?></td>
				<td>
					<input type="text" class="widefat" name="transition_id" value="<?php echo $transition_id; ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo __("Status","booking-order"); ?></td>
				<td>
					<select name="status" class="widefat" required>
						<option value="pending" <?php if($status == "pending"){echo 'selected';} ?>><?php _e("steht aus") ?></option>
						<option value="processing" <?php if($status == "processing"){echo 'selected';} ?>><?php _e("in Bearbeitung") ?></option>
						<option value="completed" <?php if($status == "completed"){echo 'selected';} ?>><?php _e("Fertigstellen") ?></option>
						<option value="cancelled" <?php if($status == "cancelled"){echo 'selected';} ?>><?php _e("Abbrechen") ?></option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>

	<?php
}
add_action('save_post', 'info_orders_save');
function info_orders_save($post_id) {
	if ( ! isset( $_POST['info_orders_nonce'] ) ||
	! wp_verify_nonce( $_POST['info_orders_nonce'], 'info_orders_nonce' ) ){
		return;
	}
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;
	
	if (!current_user_can('edit_post', $post_id))
		return;
	$currency = $_POST['currency'];
	update_post_meta( $post_id, 'currency', $currency );
	$second_order_number = $_POST['second_order_number'];
	update_post_meta( $post_id, 'second_order_number', $second_order_number );
	$customer_name1 = $_POST['customer_name1'];
	update_post_meta( $post_id, 'customer_name1', $customer_name1 );
	$customer_name2 = $_POST['customer_name2'];
	update_post_meta( $post_id, 'customer_name2', $customer_name2 );
	$customer_email = $_POST['customer_email'];
	update_post_meta( $post_id, 'customer_email', $customer_email );
	$customer_zipcode = $_POST['customer_zipcode'];
	update_post_meta( $post_id, 'customer_zipcode', $customer_zipcode );
	$customer_etage = $_POST['customer_etage'];
	update_post_meta( $post_id, 'customer_etage', $customer_etage );
	$customer_phone = $_POST['customer_phone'];
	update_post_meta( $post_id, 'customer_phone', $customer_phone );
	$more_additional = $_POST['more_additional'];
	update_post_meta( $post_id, 'more_additional', $more_additional );
	$subtotal = $_POST['subtotal'];
	update_post_meta( $post_id, 'subtotal', $subtotal );
	/*$vat7 = $_POST['vat7'];
	update_post_meta( $post_id, 'vat7', $vat7 );
	$vat19 = $_POST['vat19'];
	update_post_meta( $post_id, 'vat19', $vat19 );*/
	/*$taxes = $_POST['taxes'];
	update_post_meta( $post_id, 'taxes', $taxes );*/
	$method = $_POST['method'];
	update_post_meta( $post_id, 'method', $method );
	$reduce = $_POST['reduce'];
	update_post_meta( $post_id, 'reduce', $reduce );
	$coupon = $_POST['coupon'];
	$coupon_price = $_POST['coupon_price'];
	if(isset($_POST['coupon'])){
		$coupon = $_POST['coupon'];
		$coupon_price = $_POST['coupon_price'];
		$coupon_array = array();
		foreach ($coupon as $key => $value) {
			$coupon_array[$value] = $coupon_price[$key];
		}
		if(count($coupon_array) == 0){
			$coupon_array = "";
		}
		update_post_meta( $post_id, 'coupon', $coupon_array );
	}
	$shipping_method = $_POST['shipping_method'];
	update_post_meta( $post_id, 'shipping_method', $shipping_method );
	$user_location = $_POST['user_location'];
	update_post_meta( $post_id, 'user_location', $user_location );
	$user_delivery_time = $_POST['user_delivery_time'];
	update_post_meta($post_id,'user_delivery_time',$user_delivery_time);
	$user_delivery_date = $_POST['user_delivery_date'];
	update_post_meta($post_id,'user_delivery_date',$user_delivery_date);
	$user_time = $_POST['user_time'];
	update_post_meta( $post_id, 'user_time', $user_time );
	$shipping_fee = $_POST['shipping_fee'];
	update_post_meta( $post_id, 'shipping_fee', $shipping_fee );
	$status = $_POST['status'];
	update_post_meta( $post_id, 'status', $status );
	$total = $_POST['total'];
	update_post_meta( $post_id, 'total', $total );
	$transition_id = $_POST['transition_id'];
	update_post_meta( $post_id, 'transition_id', $transition_id );
	//update new
	$bab = $_POST['bab'];
	update_post_meta( $post_id, 'bab', $bab );
	$ar_id = $_POST['ar_id'];
	if($ar_id == "1"){
		$ar = array('ar' => 1,'r_prefix' => $_POST['r_prefix'],'r_first_name' => $_POST['r_first_name'],'r_last_name' => $_POST['r_last_name'],'r_company' => $_POST['r_company'],'r_supplement' => $_POST['r_supplement'],'r_zipcode' => $_POST['r_zipcode'],'r_city' => $_POST['r_city'],'r_street' => $_POST['r_street'],'r_housenumber' => $_POST['r_housenumber']);
	}else{
		$ar = array('ar' => 0,'r_prefix' => '','r_first_name' => '','r_last_name' => '','r_company' => '','r_supplement' => '','r_zipcode' => '','r_city' => '','r_street' => '','r_housenumber' => '');
	}
	update_post_meta( $post_id, 'ar', $ar );
}

