<?php
/*use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersAuthorizeRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;*/
//get shipping fee
function get_shipping_fee()
{
	$check = false;
	date_default_timezone_set('Europe/Berlin');
	$tax 				= get_option('dsmart_tax');
	$shop_id 			= get_shop_id();
	$shipping_method 	= $_REQUEST['shipping_method'];
	if ($shipping_method == "shipping") {
		$user_location 	= $_REQUEST['user_location'];
		$user_latitude 	= $_REQUEST['latitude'];
		$user_longitude = $_REQUEST['longitude'];
		$delivery_time = $_REQUEST['delivery_time'];
		$delivery_date = $_REQUEST['delivery_date'];
		$has_discount = is_discount_time($delivery_time, $delivery_date, $shipping_method);
	} else {
		$shipping_time = $_REQUEST['shipping_time'];
		$has_discount = is_discount_time($shipping_time, null, $shipping_method);
	}
	$dsmart_distance 		= get_option('dsmart_distance');
	$close_shop 			= get_option('dsmart_close_shop');
	$dsmart_min_order 		= ds_convert_price(get_option('dsmart_min_order'));
	$dsmart_min_order_free = get_option('dsmart_min_order_free') != "" ? ds_convert_price(get_option('dsmart_min_order_free')) : "";
	$dsmart_shipping_fee 	= "";
	$dsmart_shipping_from 	= get_option('dsmart_shipping_from');
	$dsmart_shipping_to 	= get_option('dsmart_shipping_to');
	$dsmart_shipping_cs_fee = get_option('dsmart_shipping_cs_fee');
	$dsmart_min_cs_fee = get_option('dsmart_min_cs_fee');
	$min = null;
	$shipping_fee 			= 0;
	$total_cart 			= ds_get_cart_total_item();
	$total_cart_use_coupon  = ds_get_cart_total_item_use_coupon();
	$type_promotion 		= get_option('type_promotion');
	$promotion 				= get_option('promotion');
	$type_promotion_2 		= get_option('type_promotion_2');
	$promotion_2 			= get_option('promotion_2');
	$discount_cod 			= get_option('discount_cod');
	$discount_shop 			= get_option('discount_shop');
	$tax_shipping 			= get_option('tax_shipping');
	$check_zipcode 		    = (get_option('zipcode_status') == "on") ? true : false;
	$has_reduce				= false;
	$coupon_price_all = 0;
	$check_coupon = true;
	$check_promotion = true;
	$coupon_array = array();
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
	if ($shop_id == "" || ($shop_id != "" && check_shop_id($shop_id) == false)) {
		echo json_encode(array(
			'check' => $check,
			'message' => __("Shop does not exists.", 'dsmart'),
		));
	} elseif ($close_shop == "on") {
		echo json_encode(array(
			'check' => $check,
			'message' => __("Currently, shop does not accept orders.", 'dsmart'),
		));
	} else {
		$shop = get_shop_data_by_id($shop_id);
		if (!isset($delivery_time) || $delivery_time == "") {
			$current_time = date('H:i');
		} else {
			$current_time = $delivery_time;
		}
		if ($shipping_method == "shipping") {
			$check_time = check_time_with_time_shop($current_time, null, null, $shipping_method);
			if ($check_time == true) :
				if ($check_zipcode == true) {
					if (isset($_POST['filled_zipcode']) && $_POST['filled_zipcode'] != "") {
						$zipcode = $_POST['filled_zipcode'];
					} else {
						$zipcode = $_COOKIE['filled_zipcode'];
					}
					$zipcode_get = get_data_zipcode($zipcode);
					if ($zipcode_get !== false) {
						setcookie('filled_zipcode', $zipcode, time() + 2592000, '/', NULL, 0);
						$zipcode = $zipcode_get['zipcode'];
						$minium_order = intval($zipcode_get['minium_order']);
						$zipcode_price = floatval($zipcode_get['price']);
						if ($minium_order > $total_cart) {
							$shipping = 0;
							$total_all = $total_cart;
							$total_all_use_coupon = $total_cart_use_coupon;
							if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
								$coupon = explode(',', $_COOKIE['coupon']);
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
							$total_all = $total_all + $shipping;
							if ($total_all < 0) {
								$total_all = 0;
							}
							echo json_encode(array(
								'check' => false,
								'shipping' 			=> ds_price_format_text_no_convert($shipping),
								'coupon'	 		=> $coupon_array,
								'total' 			=> ds_price_format_text_no_convert($total_all),
								'message' => __("Mindestbestellwert für die PLZ ist " . ds_price_format_text_no_convert($minium_order)),
							));
						} else {
							$shipping = $zipcode_price;
							$total_all = $total_cart;
							$total_all_use_coupon = $total_cart_use_coupon;
							$reduce = '';
							$reduce_percent = '';
							$discount_min = get_option('discount_min');
							if ($check_promotion == true && $promotion != null && $has_discount && $discount_cod == 'on' && floatval($discount_min) <= $total_all) :
								$has_reduce	= true;
								if ($type_promotion == '%') :
									$reduce_percent = $promotion;
									$temp_total 	= $total_all_use_coupon * floatval($promotion) / 100;
									$total_all 		= $total_all - $temp_total;
									$total_all_use_coupon = $total_all_use_coupon - $temp_total;
									$reduce 		= ds_price_format_text_no_convert($temp_total);
								else :
									$reduce_percent = round($promotion / floatval($total_all_use_coupon) * 100);
									$total_all 		= $total_all - floatval($promotion);
									$total_all_use_coupon = $total_all_use_coupon - floatval($promotion);
									$reduce 		= ds_price_format_text_no_convert($promotion);
								endif;
							endif;
							if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
								$coupon = explode(',', $_COOKIE['coupon']);
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
							$total_all = $total_all + $shipping;
							if ($total_all < 0) {
								$total_all = 0;
							}
							echo json_encode(array(
								'check' 			=> true,
								'has_reduce' 		=> $has_reduce,
								'reduce' 			=> $reduce,
								'reduce_percent' 	=> $reduce_percent . '%',
								'dsmart_shipping_fee' => $dsmart_shipping_fee,
								'shippingzz'		=> $shipping,
								'shipping' 			=> ds_price_format_text_no_convert($shipping),
								'coupon' 			=> $coupon_array,
								'total' 			=> ds_price_format_text_no_convert($total_all),
								"debug"				=> $shipping
							));
						}
					} else {
						$shipping = 0;
						$total_all = $total_cart;
						$total_all_use_coupon = $total_cart_use_coupon;
						if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
							$coupon = explode(',', $_COOKIE['coupon']);
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
						$total_all = $total_all + $shipping;
						if ($total_all < 0) {
							$total_all = 0;
						}
						echo json_encode(array(
							'check' => false,
							'shipping' 			=> ds_price_format_text_no_convert($shipping),
							'coupon'	 		=> $coupon_array,
							'total' 			=> ds_price_format_text_no_convert($total_all),
							'message' => __("Wir liefern nicht zu der gewünschten PLZ."),
						));
					}
				} else {
					$distance = get_distance_from_customer_to_shop($shop->latitude, $shop->longitude, $user_latitude, $user_longitude);
					if (count($dsmart_shipping_from) > 0) {
						foreach ($dsmart_shipping_from as $key => $value) {
							if ($distance >= intval($dsmart_shipping_from[$key])*1000 && $distance <= intval($dsmart_shipping_to[$key])*1000) {
								$dsmart_shipping_fee 	= ds_convert_price($dsmart_shipping_cs_fee[$key]);
								if ($dsmart_min_cs_fee[$key] != "") $min = $dsmart_min_cs_fee[$key];
							}
						}
					}
					if ($min !== null && $min > $total_cart) {
						$shipping = 0;
						$total_all = $total_cart;
						$total_all_use_coupon = $total_cart_use_coupon;
						if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
							$coupon = explode(',', $_COOKIE['coupon']);
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
						echo json_encode(array(
							'check' => $check,
							'shipping' 			=> ds_price_format_text_no_convert($shipping),
							'coupon'	 		=> $coupon_array,
							'total' 			=> ds_price_format_text_no_convert($total_all),
							'message' => __("Der Mindestbestellwert für Lieferung beträgt ab ", 'dsmart') . ds_price_format_text_no_convert($min),
						));
					} elseif ($dsmart_distance != "" && $distance > floatval($dsmart_distance)*1000) {
						$shipping = 0;
						$total_all = $total_cart;
						$total_all_use_coupon = $total_cart_use_coupon;
						if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
							$coupon = explode(',', $_COOKIE['coupon']);
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
						$total_all = $total_all + $shipping;
						if ($total_all < 0) {
							$total_all = 0;
						}
						echo json_encode(array(
							'check' => $check,
							'shipping' 			=> ds_price_format_text_no_convert($shipping),
							'coupon'	 		=> $coupon_array,
							'total' 			=> ds_price_format_text_no_convert($total_all),
							'message' => __("Die maximale Lieferentfernung ist ", 'dsmart') . dsmart_format_number($dsmart_distance) . "km (" . __("Die Entfernung zu Ihre Adresse beträgt: ", 'dsmart') . dsmart_format_number($distance/1000) . 'km)',
						));
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
						$total_all = $total_cart;
						$total_all_use_coupon = $total_cart_use_coupon;
						$reduce = '';
						$reduce_percent = '';
						$discount_min = get_option('discount_min');
						if ($check_promotion == true && $promotion != null && $has_discount && floatval($discount_min) <= $total_all) :
							$has_reduce	= true;
							if ($type_promotion == '%') :
								$reduce_percent = $promotion;
								$temp_total 	= $total_all_use_coupon * floatval($promotion) / 100;
								$total_all 		= $total_all - $temp_total;
								$total_all_use_coupon = $total_all_use_coupon - $temp_total;
								$reduce 		= ds_price_format_text_no_convert($temp_total);
							else :
								$reduce_percent = round($promotion / floatval($total_all_use_coupon) * 100);
								$total_all 		= $total_all - floatval($promotion);
								$total_all_use_coupon = $total_all_use_coupon - floatval($promotion);
								$reduce 		= ds_price_format_text_no_convert($promotion);
							endif;
						endif;
						if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
							$coupon = explode(',', $_COOKIE['coupon']);
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
						$total_all = $total_all + $shipping;
						if ($total_all < 0) {
							$total_all = 0;
						}
						echo json_encode(array(
							'check' 			=> true,
							'has_reduce' 		=> $has_reduce,
							'reduce' 			=> $reduce,
							'reduce_percent' 	=> $reduce_percent . '%',
							'dsmart_shipping_fee' => $dsmart_shipping_fee,
							'shippingzz'		=> $shipping,
							'shipping' 			=> ds_price_format_text_no_convert($shipping),
							'coupon' 			=> $coupon_array,
							'total' 			=> ds_price_format_text_no_convert($total_all),
							'debug'				=> $dsmart_min_order_free
						));
					}
				}
			else :
				$shipping = 0;
				$total_all = $total_cart;
				$total_all_use_coupon = $total_cart_use_coupon;
				if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
					$coupon = explode(',', $_COOKIE['coupon']);
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
				$total_all = $total_all + $shipping;
				if ($total_all < 0) {
					$total_all = 0;
				}
				echo json_encode(array(
					'shipping' 			=> ds_price_format_text_no_convert($shipping),
					'coupon'	 		=> $coupon_array,
					'total' 			=> ds_price_format_text_no_convert($total_all),
					'check' => false,
					'message' => __("Sie bestellen gerade vor der Laden Öffnungszeiten.", 'dsmart'),
				));
			endif;
		} else {
			$shipping_time 	= $_REQUEST['shipping_time'];
			$check_time 	= check_time_with_time_shop($shipping_time, $current_time);
			$has_discount 	= is_discount_time($shipping_time, null, $shipping_method);
			$total_all  = 0;
			if ($check_time == true) {
				$shipping = 0;
				$vat7 = $vat['vat7'];
				$vat19 = $vat['vat19'];
				$taxes = $vat['taxes'];
				if ($tax_shipping != "") {
					$shipping_vat = $shipping - round($shipping / (1 + $tax_shipping / 100), 2);
					if ($tax_shipping == "7") {
						$vat7 = $vat7 + $shipping_vat;
					} elseif ($tax_shipping == "19") {
						$vat19 = $vat19 + $shipping_vat;
					} else {
						$taxes = $taxes + $shipping_vat;
					}
				} else {
					$shipping_vat = 0;
				}
				$total_all = $total_cart;
				$total_all_use_coupon = $total_cart_use_coupon;
				$reduce = '';
				$reduce_percent = '';
				$discount_min = get_option('discount_min');
				if ($promotion_2 != null && $has_discount && floatval($discount_min) <= $total_all) :
					$has_reduce			= true;
					if ($type_promotion_2 == '%') :
						$reduce_percent = $promotion_2;
						$temp_total 	= $total_all_use_coupon * floatval($promotion_2) / 100;
						$total_all 		= $total_all - $temp_total;
						$total_all_use_coupon = $total_all_use_coupon - $temp_total;
						$reduce 		= ds_price_format_text_no_convert($temp_total);
					else :
						$reduce_percent = round($promotion_2 / floatval($total_all_use_coupon) * 100);
						$total_all 		= $total_all - floatval($promotion_2);
						$total_all_use_coupon = $total_all_use_coupon - floatval($promotion_2);
						$reduce 		= ds_price_format_text_no_convert($promotion_2);
					endif;
				endif;
				if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
					$coupon = explode(',', $_COOKIE['coupon']);
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
				$total_all = $shipping + $shipping_vat + $total_all;
				//$total_all = $total_all - $coupon_price_all;
				if ($total_all < 0) {
					$total_all = 0;
				}
				echo json_encode(array(
					'has_discount' 		=> $has_discount,
					'check' 			=> true,
					'has_reduce' 		=> $has_reduce,
					'reduce' 			=> $reduce,
					'reduce_percent' 	=> $reduce_percent . '%',
					'shipping' 			=> ds_price_format_text_no_convert($shipping),
					'coupon'	 		=> $coupon_array,
					'total' 			=> ds_price_format_text_no_convert($total_all),
				));
			} else {
				$shipping = 0;
				if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "" && check_coupon_available($_COOKIE['coupon']) == 1) {
					$coupon_price = get_price_of_coupon($_COOKIE['coupon'], $total_all);
				} else {
					$coupon_price = 0;
				}
				$total_all = $total_all - $coupon_price;
				$total_all = $shipping + $total_all;
				if ($total_all < 0) {
					$total_all = 0;
				}
				echo json_encode(array(
					'check' => $check,
					'shipping' 			=> ds_price_format_text_no_convert($shipping),
					'taxes' 			=> ds_price_format_text_no_convert($vat['vat7'] + $vat['vat19'] + $vat['taxes']),
					'coupon'	 		=> '- ' . ds_price_format_text_no_convert($coupon_price),
					'total' 			=> ds_price_format_text_no_convert($total_all),
					'message' => __("Derzeit akzeptiert der Shop keine Bestellungen oder Ihre Zeit ist falsch.", 'dsmart'),
				));
			}
		}
	}
	exit;
}
add_action('wp_ajax_get_shipping_fee', 'get_shipping_fee');
add_action('wp_ajax_nopriv_get_shipping_fee', 'get_shipping_fee');

//update cart
function dsmart_update_cart()
{
	$items = $_REQUEST['items'];
	if (isset($_COOKIE['cart']) && $_COOKIE['cart'] != "") {
		$cart = unserialize(base64_decode($_COOKIE['cart']));
	} else {
		$cart = array();
	}
	foreach ($items as $key => $value) {
		$cart[$key]['quantity'] = intval($value);
	}
	setcookie('cart', base64_encode(serialize($cart)), time() + 2592000, '/', NULL, 0);
	exit;
}
add_action('wp_ajax_dsmart_update_cart', 'dsmart_update_cart');
add_action('wp_ajax_nopriv_dsmart_update_cart', 'dsmart_update_cart');

//delete_item_in_cart
function dsmart_delete_item_cart()
{
	$id = $_REQUEST['id'];
	if (isset($_COOKIE['cart']) && $_COOKIE['cart'] != "") {
		$cart = unserialize(base64_decode($_COOKIE['cart']));
	} else {
		$cart = array();
	}
	unset($cart[$id]);
	setcookie('cart', base64_encode(serialize($cart)), time() + 2592000, '/', NULL, 0);
	exit;
}
add_action('wp_ajax_dsmart_delete_item_cart', 'dsmart_delete_item_cart');
add_action('wp_ajax_nopriv_dsmart_delete_item_cart', 'dsmart_delete_item_cart');


//add coupon and remove coupon
function dsmart_add_coupon_in_cart()
{
	$coupon_code = $_REQUEST['id'];
	$check_add_coupon = true;
	$list_coupon = array();
	$list_coupon1 = array();
	$list_coupon2 = array();
	$data = array();
	$shipping_method = $_REQUEST['shipping_method'];
	if ($shipping_method == "shipping") {
		$data['user_location']  = $_REQUEST['user_location'];
		$data['user_latitude']  = $_REQUEST['latitude'];
		$data['user_longitude'] = $_REQUEST['longitude'];
		$data['delivery_date'] = $_REQUEST['delivery_date'];
		$data['delivery_time'] = $_REQUEST['delivery_time'];
	} else {
		$data['shipping_time'] = $_REQUEST['shipping_time'];
	}
	if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
		$list_coupon = explode(',', $_COOKIE['coupon']);
		foreach ($list_coupon as $item) {
			$coupon_id = get_coupon_id_from_code($item);
			$coupon_option = dsmart_field('coupon_option', $coupon_id);
			$coupon_mutiple = dsmart_field('coupon_mutiple', $coupon_id);
			$coupon_shipping = dsmart_field('coupon_shipping', $coupon_id);
			if ($coupon_option == "percent") {
				$list_coupon1[] = $item;
			} else {
				$list_coupon2[] = $item;
			}
			if ($coupon_mutiple != "yes") {
				$check_add_coupon = false;
				setcookie('coupon_notify', "7", time() + 2592000, '/', NULL, 0);
				echo json_encode(array(
					'check' => false,
					'message2' => __('Coupon kann nicht mit anderen Coupons verwendet werden.', 'dsmart'),
					'message' => '<div class="alert alert-danger">' . __('Coupon kann nicht mit anderen Coupons verwendet werden.', 'dsmart') . '</div>',
					'html' => get_total_cart($shipping_method, $data),
				));
				exit;
			} elseif ($shipping_method != "" && $coupon_shipping != "" && $shipping_method != $coupon_shipping) {
				$check_add_coupon = false;
				//setcookie('coupon_notify', "7", time()+2592000, '/', NULL, 0);
				echo json_encode(array(
					'check' => false,
					'message2' => __('Coupon kann nicht mit dieser Versandart genutzt werden.', 'dsmart'),
					'message' => '<div class="alert alert-danger">' . __('Coupon kann nicht mit dieser Versandart genutzt werden.', 'dsmart') . '</div>',
					'html' => get_total_cart($shipping_method, $data),
				));
				exit;
			}
		}
	}
	if ($coupon_code != "") {
		$coupon_id = get_coupon_id_from_code($coupon_code);
		$main_coupon_mutiple = dsmart_field('coupon_mutiple', $coupon_id);
		$coupon_shipping = dsmart_field('coupon_shipping', $coupon_id);
		if ($main_coupon_mutiple != "yes" && count($list_coupon) > 0) {
			//setcookie('coupon_notify', "7", time()+2592000, '/', NULL, 0);
			echo json_encode(array(
				'check' => false,
				'message2' => __('Coupon kann nicht mit anderen Coupons verwendet werden.', 'dsmart'),
				'message' => '<div class="alert alert-danger">' . __('Coupon kann nicht mit anderen Coupons verwendet werden.', 'dsmart') . '</div>',
				'html' => get_total_cart($shipping_method, $data),
			));
			exit;
		} elseif ($shipping_method != "" && $coupon_shipping != "" && $shipping_method != $coupon_shipping) {
			echo json_encode(array(
				'check' => false,
				'message2' => __('Coupon kann nicht in dieser Versandart verwendet werden.', 'dsmart'),
				'message' => '<div class="alert alert-danger">' . __('Coupon kann nicht in dieser Versandart verwendet werden.', 'dsmart') . '</div>',
				'html' => get_total_cart($shipping_method, $data),
			));
			exit;
		} elseif (in_array($coupon_code, $list_coupon)) {
			echo json_encode(array(
				'check' => false,
				'message2' => __('Coupon kann nicht mit anderen Coupons verwendet werden.', 'dsmart'),
				'message' => '<div class="alert alert-danger">' . __('Coupon kann nicht mit anderen Coupons verwendet werden.', 'dsmart') . '</div>',
				'html' => get_total_cart($shipping_method, $data),
			));
			exit;
		}
		$coupon_notify = check_coupon_available($coupon_code);
		if ($coupon_notify == 1) {
			$coupon_option = dsmart_field('coupon_option', $coupon_id);
			if ($coupon_option == "percent") {
				$list_coupon1[] = $coupon_code;
			} else {
				$list_coupon2[] = $coupon_code;
			}
			$result = $list_coupon1 + $list_coupon2;
			setcookie('coupon', implode(',', $result), time() + 2592000, '/', NULL, 0);
			echo json_encode(array(
				'check' => true,
				'message' => '<div class="alert alert-success">' . __('Rabattcode hinzugefügt.', 'dsmart') . '</div>',
				'html' => get_total_cart($shipping_method, $data, implode(',', $result)),
			));
			exit;
		} else {
			echo json_encode(array(
				'check' => false,
				'message2' => __('Fehler mit Coupon.', 'dsmart'),
				'message' => '<div class="alert alert-danger">' . __('Fehler mit Coupon.', 'dsmart') . '</div>',
				'html' => get_total_cart($shipping_method, $data),
			));
			exit;
		}
	}
}
add_action('wp_ajax_dsmart_add_coupon_in_cart', 'dsmart_add_coupon_in_cart');
add_action('wp_ajax_nopriv_dsmart_add_coupon_in_cart', 'dsmart_add_coupon_in_cart');

function dsmart_remove_coupon_in_cart()
{
	$coupon = $_REQUEST['coupon'];
	$data = array();
	$shipping_method = $_REQUEST['shipping_method'];
	if ($shipping_method == "shipping") {
		$data['user_location']  = $_REQUEST['user_location'];
		$data['user_latitude']  = $_REQUEST['latitude'];
		$data['user_longitude'] = $_REQUEST['longitude'];
		$data['delivery_date'] = $_REQUEST['delivery_date'];
		$data['delivery_time'] = $_REQUEST['delivery_time'];
	} else {
		$data['shipping_time'] = $_REQUEST['shipping_time'];
	}
	$list_coupon = array();
	if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
		$list_coupon = explode(',', $_COOKIE['coupon']);
		foreach ($list_coupon as $key => $item) {
			if ($item == $coupon) {
				unset($list_coupon[$key]);
			}
		}
	}
	if (count($list_coupon) > 0) {
		setcookie('coupon', implode(',', $list_coupon), time() + 2592000, '/', NULL, 0);
	} else {
		setcookie('coupon', null, time() - 2592000, '/', NULL, 0);
	}
	echo json_encode(array(
		'check' => true,
		'message2' => __('Coupon wurde gelöscht.', 'dsmart'),
		'message' => '<div class="alert alert-success">' . __('Coupon wurde gelöscht.', 'dsmart') . '</div>',
		'html' => get_total_cart($shipping_method, $data, implode(',', $list_coupon), true),
	));
	exit;
}
add_action('wp_ajax_dsmart_remove_coupon_in_cart', 'dsmart_remove_coupon_in_cart');
add_action('wp_ajax_nopriv_dsmart_remove_coupon_in_cart', 'dsmart_remove_coupon_in_cart');

function check_can_shipping_or_not()
{
	$check = false;
	date_default_timezone_set('Europe/Berlin');
	$shop_id = get_shop_id();
	$shipping_method = $_REQUEST['shipping_method'];
	if ($shipping_method == "shipping") {
		$user_location 	= $_REQUEST['user_location'];
		$user_latitude 	= $_REQUEST['latitude'];
		$user_longitude = $_REQUEST['longitude'];
		$delivery_time = $_REQUEST['delivery_time'];
		$delivery_date = $_REQUEST['delivery_date'];
	} else {
		$shipping_time 	= $_REQUEST['shipping_time'];
	}
	$dsmart_distance 		= get_option('dsmart_distance');
	$close_shop 			= get_option('dsmart_close_shop');
	$dsmart_min_order 		= ds_convert_price(get_option('dsmart_min_order'));
	$dsmart_min_order_free = get_option('dsmart_min_order_free') != "" ? ds_convert_price(get_option('dsmart_min_order_free')) : "";
	$dsmart_shipping_fee 	= "";
	$shipping_fee 			= 0;
	$total_cart 			= ds_get_cart_total_item();
	$check_zipcode 		    = (get_option('zipcode_status') == "on") ? true : false;
	/*if(isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "" && check_coupon_available($_COOKIE['coupon']) == 1){
		$coupon_price = get_price_of_coupon($_COOKIE['coupon']);
	}else{
		$coupon_price = 0;
	}*/
	if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
		$coupon = explode(',', $_COOKIE['coupon']);
		foreach ($coupon as $key => $item) {
			$coupon_id = get_coupon_id_from_code($item);
			$coupon_mutiple = dsmart_field('coupon_mutiple', $coupon_id);
			$coupon_shipping = dsmart_field('coupon_shipping', $coupon_id);
			if ($shipping_method != "" && $coupon_shipping != "" && $shipping_method != $coupon_shipping) {
				unset($coupon[$key]);
				setcookie('coupon', implode(',', $coupon), time() + 2592000, '/', NULL, 0);
			} else {
				if (count($coupon) > 1 && $coupon_mutiple != "yes") {
					$check_coupon = false;
				}
				if ($coupon_mutiple != "yes") {
					$check_promotion = false;
					$has_discount = false;
				}
				if (check_coupon_available($item) == 1) {
					$coupon_price_all += $coupon_price = get_price_of_coupon($item, $total_all);
					$coupon_array[$item]['price'] = '- ' . ds_price_format_text_no_convert($coupon_price);
					$coupon_array[$item]['type'] = dsmart_field('coupon_shipping', $coupon_id);
					$total_all = $total_all - $coupon_price;
				}
			}
		}
	} else {
		$coupon_price = 0;
		$coupon_price_all = 0;
	}
	if ($shop_id == "" || ($shop_id != "" && check_shop_id($shop_id) == false)) {
		echo json_encode(array(
			'check' 	=> $check,
			'message' 	=> __("Shop existiert nicht.", 'dsmart'),
		));
	} elseif ($close_shop == "on") {
		echo json_encode(array(
			'check' 	=> $check,
			'message' 	=> __("Derzeit akzeptiert der Shop keine Bestellungen. Bitte versuchen Sie es später noch einmal.", 'dsmart'),
		));
	} else {
		$checkout_id = get_page_id_by_template('templates/checkout-page.php');
		$shop = get_shop_data_by_id($shop_id);
		$current_time = date('H:i', strtotime(date('H:i')));
		if ($shipping_method == "shipping") {
			if ($check_zipcode == true) {
				if (isset($_POST['filled_zipcode']) && $_POST['filled_zipcode'] != "") {
					$zipcode = $_POST['filled_zipcode'];
				} else {
					$zipcode = $_COOKIE['filled_zipcode'];
				}
				//if(isset($_COOKIE['filled_zipcode']) || $_COOKIE['filled_zipcode'] != ""){
				$zipcode_get = get_data_zipcode($zipcode);
				if ($zipcode_get !== false) {
					setcookie('filled_zipcode', $zipcode, time() + 2592000, '/', NULL, 0);
					$zipcode = $zipcode_get['zipcode'];
					$minium_order = intval($zipcode_get['minium_order']);
					$zipcode_price = floatval($zipcode_get['price']);
					if ($minium_order > $total_cart) {
						echo json_encode(array(
							'check' => false,
							'message' => __("Minium order for your zipcode: " . ds_price_format_text_no_convert($minium_order)),
						));
					} else {
						$shipping_info = array(
							"shop" 				=> $shop_id,
							"shipping_method" 	=> $shipping_method,
							"zipcode" 			=> $zipcode,
							"delivery_time" 	=> $delivery_time,
							"delivery_date" 	=> $delivery_date
						);
						setcookie('shipping_info', base64_encode(serialize($shipping_info)), time() + 2592000, '/', NULL, 0);
						echo json_encode(array(
							'check' 			=> true,
							'redirect_url' => get_permalink($checkout_id),
						));
					}
				} else {
					echo json_encode(array(
						'check' => false,
						'message' => __("No shipping for your zipcode."),
					));
				}
			} else {
				$check_time = check_time_with_time_shop($delivery_time, null, $delivery_date, $shipping_method);
				if ($check_time == true) {
					$distance = get_distance_from_customer_to_shop($shop->latitude, $shop->longitude, $user_latitude, $user_longitude);
					if ($dsmart_distance != "" && $distance > floatval($dsmart_distance)*1000) {
						echo json_encode(array(
							'check' => $check,
							'message' => __("Die maximale Lieferentfernung ist ", 'dsmart') . dsmart_format_number($dsmart_distance) . "km (" . __("Die Entfernung zu Ihre Adresse beträgt: ", 'dsmart') . dsmart_format_number($distance/1000) . 'km )',
						));
					} else {
						$shipping_info = array(
							"shop" 				=> $shop_id,
							"shipping_method" 	=> $shipping_method,
							"location" 			=> $user_location,
							"latitude" 			=> $user_latitude,
							"longitude" 		=> $user_longitude,
							"delivery_time" 	=> $delivery_time,
							"delivery_date" 	=> $delivery_date
						);
						setcookie('shipping_info', base64_encode(serialize($shipping_info)), time() + 2592000, '/', NULL, 0);
						echo json_encode(array(
							'check' => true,
							'redirect_url' => get_permalink($checkout_id),
						));
					}
				} else {
					echo json_encode(array(
						'check' => $check,
						'message' => __("Derzeit akzeptiert der Shop keine Bestellungen. Bitte versuchen Sie es später noch einmal.", 'dsmart'),
						'z' => $current_time,
						'date' => $delivery_date,
						'zxcz' => $delivery_time
					));
				}
			}
		} else {
			if ($shipping_time == "") {
				echo json_encode(array(
					'check' => false,
					'message' => __("Bitte wählen Sie die Zeit.", 'dsmart'),
				));
			} else {
				$check_time = check_time_with_time_shop($shipping_time, $current_time, $delivery_date, $shipping_method);
				if ($check_time == true) {
					$shipping_info = array(
						"shop" 				=> $shop_id,
						"shipping_method" 	=> "direct",
						"time" 				=> $shipping_time
					);
					setcookie('shipping_info', base64_encode(serialize($shipping_info)), time() + 2592000, '/', NULL, 0);
					echo json_encode(array(
						'check' => true,
						'redirect_url' => get_permalink($checkout_id),
					));
				} else {
					echo json_encode(array(
						'check' => false,
						'message' => __("Der Shop funktioniert nicht oder es ist keine Zeit, ein ungeeignetes Produkt zu bekommen. Bitte versuche es erneut.", 'dsmart'),
						'zxc' => array($shipping_time, $current_time, $delivery_date, $shipping_method)
					));
				}
			}
		}
	}
	exit;
}
add_action('wp_ajax_check_can_shipping_or_not', 'check_can_shipping_or_not');
add_action('wp_ajax_nopriv_check_can_shipping_or_not', 'check_can_shipping_or_not');


function check_time_available()
{
	date_default_timezone_set('Europe/Berlin');
	$check 					= false;
	$time 					= $_REQUEST['time'];
	$ztime 					= $_REQUEST['time'];
	$current_time 			= date('H:i');
	$time_data 				= date('H:i', strtotime($time));
	$time 					= new DateTime($time);
	$close_shop 			= get_option('dsmart_close_shop');
	$current_date_text 		= custom_date();
	$current_date 			= date('d-m-Y');
	$method = $_REQUEST['method'];
	if ($method == "shipping") {
		$time_open_shop 		= get_option('time_open_shop_' . $current_date_text);
		$time_close_shop 		= get_option('time_close_shop_' . $current_date_text);
		$time_open_shop_text 	= get_option('time_open_shop_' . $current_date_text);
		$time_close_shop_text 	= get_option('time_close_shop_' . $current_date_text);
	} else {
		$time_open_shop 		= get_option('time_open_shop_2_' . $current_date_text);
		$time_close_shop 		= get_option('time_close_shop_2_' . $current_date_text);
		$time_open_shop_text 	= get_option('time_open_shop_2_' . $current_date_text);
		$time_close_shop_text 	= get_option('time_close_shop_2_' . $current_date_text);
	}
	$dsmart_custom_date 	= get_option('dsmart_custom_date');
	$total_cart 			= ds_get_cart_total_item();
	$total_cart_use_coupon 	= ds_get_cart_total_item_use_coupon();
	$has_discount 			= is_discount_time($ztime, null, $method);
	$type_promotion 		= get_option('type_promotion');
	$promotion 				= get_option('promotion');

	$type_promotion_2 		= get_option('type_promotion_2');
	$promotion_2 			= get_option('promotion_2');
	$vat 					= ds_get_vat_total_item();
	$tax_shipping 		    = get_option('tax_shipping');
	$shipping = 0;
	$coupon_price_all = 0;
	$check_coupon = true;
	$check_promotion = true;
	$coupon_array = array();
	$vat7 = $vat['vat7'];
	$vat19 = $vat['vat19'];
	$taxes = $vat['taxes'];
	if ($tax_shipping != "") {
		$shipping_vat = $shipping - round($shipping / (1 + $tax_shipping / 100), 2);
		if ($tax_shipping == "7") {
			$vat7 = $vat7 + $shipping_vat;
		} elseif ($tax_shipping == "19") {
			$vat19 = $vat19 + $shipping_vat;
		} else {
			$taxes = $taxes + $shipping_vat;
		}
	} else {
		$shipping_vat = 0;
	}
	$total_all = $total_cart;
	$reduce = '';
	$reduce_percent = '';
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
	
	$discount_min = get_option('discount_min');
	if ($discount_min != '' && $discount_min != 0 && $discount_min != '0' && floatval($discount_min) > $total_all) {
		$has_discount = false;
	}else{
		if ($check_promotion == true && $promotion_2 != null && $has_discount) :
			$has_reduce			= true;
			if ($type_promotion == '%') :
				$reduce_percent = $promotion_2;
				$temp_total 	= $total_cart_use_coupon * floatval($promotion_2) / 100;
				$total_all 		= $total_all - $temp_total;
				$total_cart_use_coupon = $total_cart_use_coupon - $temp_total;
				$reduce 		= ds_price_format_text_no_convert($temp_total);
			else :
				$reduce_percent = round($promotion_2 / floatval($total_cart_use_coupon) * 100);
				$total_all 		= $total_all - floatval($promotion_2);
				$total_cart_use_coupon = $total_cart_use_coupon - floatval($promotion_2);
				$reduce 		= ds_price_format_text_no_convert($promotion_2);
			endif;
		endif;
	}
		
	
	if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
		$coupon = explode(',', $_COOKIE['coupon']);
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
			if (check_coupon_available($item) == 1 && ($method == "" || ($method != "" && $method == $coupon_shipping))) {
				$coupon_price_all += $coupon_price = get_price_of_coupon($item, $total_cart_use_coupon);
				$coupon_array[$item]['price'] = '- ' . ds_price_format_text_no_convert($coupon_price);
				$coupon_array[$item]['type'] = dsmart_field('coupon_shipping', $coupon_id);
				$total_all = $total_all - $coupon_price;
				$total_cart_use_coupon = $total_cart_use_coupon - $coupon_price;
			}
		}
	} else {
		$coupon_price = 0;
		$coupon_price_all = 0;
	}
	$total_all = $shipping + $shipping_vat + $total_all;
	//$total_all = $total_all - $coupon_price_all;
	if ($total_all < 0) {
		$total_all = 0;
	}
	if ($dsmart_custom_date != "" && count($dsmart_custom_date) > 0) {
		foreach ($dsmart_custom_date as $key => $value) {
			if ($key == $current_date) {
				$time_open_shop = $value['open'];
				$time_close_shop = $value['close'];
				$time_open_shop_text = $value['open'];
				$time_close_shop_text = $value['close'];
			}
		}
	}
	// $closed_time = get_option('closed_time');
	$closed_time = get_option('closed_time_2');
	$time_shop_array = array();
	if ($dsmart_custom_date != "" && count($dsmart_custom_date) > 0) {
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
	if ($check_coupon == false) {
		echo json_encode(array(
			'check' => false,
			'has_discount' 		=> $has_discount,
			'has_reduce' 		=> $has_reduce,
			'reduce' 			=> $reduce,
			'reduce_percent' 	=> $reduce_percent . '%',
			'shipping' 			=> ds_price_format_text_no_convert($shipping),
			'vat7' 				=> ds_price_format_text_no_convert($vat7),
			'vat19' 			=> ds_price_format_text_no_convert($vat19),
			'taxes' 			=> ds_price_format_text_no_convert($taxes),
			'coupon'	 		=> $coupon_array,
			'total' 			=> ds_price_format_text_no_convert($total_all),
			'message' => __("Einige Coupon können nicht mit anderen Coupon verwendet werden.", 'dsmart'),
		));
	} elseif ($close_shop == "on") {
		echo json_encode(array(
			'check' => false,
			'has_discount' 		=> $has_discount,
			'has_reduce' 		=> $has_reduce,
			'reduce' 			=> $reduce,
			'reduce_percent' 	=> $reduce_percent . '%',
			'shipping' 			=> ds_price_format_text_no_convert($shipping),
			'vat7' 				=> ds_price_format_text_no_convert($vat7),
			'vat19' 			=> ds_price_format_text_no_convert($vat19),
			'taxes' 			=> ds_price_format_text_no_convert($taxes),
			'coupon'	 		=> $coupon_array,
			'total' 			=> ds_price_format_text_no_convert($total_all),
			'message' => __("Derzeit akzeptiert der Shop keine Bestellungen. Bitte versuchen Sie es später noch einmal.", 'dsmart'),
		));
	} elseif ($time_data < date('H:i', strtotime($current_time))) {
		echo json_encode(array(
			'check' => false,
			'has_discount' 		=> $has_discount,
			'has_reduce' 		=> $has_reduce,
			'reduce' 			=> $reduce,
			'reduce_percent' 	=> $reduce_percent . '%',
			'shipping' 			=> ds_price_format_text_no_convert($shipping),
			'vat7' 				=> ds_price_format_text_no_convert($vat7),
			'vat19' 			=> ds_price_format_text_no_convert($vat19),
			'taxes' 			=> ds_price_format_text_no_convert($taxes),
			'coupon'	 		=> $coupon_array,
			'total' 			=> ds_price_format_text_no_convert($total_all),
			'message' => __("Please choose shipping time after " . $current_time . ". Please try again later.", 'dsmart'),
		));
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
			if ($check_data == true) {
				echo json_encode(array(
					'check' 			=> true,
					'has_discount' 		=> $has_discount,
					'has_reduce' 		=> $has_reduce,
					'reduce' 			=> $reduce,
					'reduce_percent' 	=> $reduce_percent . '%',
					'shipping' 			=> ds_price_format_text_no_convert($shipping),
					'vat7' 				=> ds_price_format_text_no_convert($vat7),
					'vat19' 			=> ds_price_format_text_no_convert($vat19),
					'coupon'	 		=> $coupon_array,
					'total' 			=> ds_price_format_text_no_convert($total_all),
				));
			} else {
				echo json_encode(array(
					'check' => false,
					'has_discount' 		=> $has_discount,
					'has_reduce' 		=> $has_reduce,
					'reduce' 			=> $reduce,
					'reduce_percent' 	=> $reduce_percent . '%',
					'shipping' 			=> ds_price_format_text_no_convert($shipping),
					'vat7' 				=> ds_price_format_text_no_convert($vat7),
					'vat19' 			=> ds_price_format_text_no_convert($vat19),
					'coupon'	 		=> $coupon_array,
					'total' 			=> ds_price_format_text_no_convert($total_all),
					'message' => __("Der Laden ist vorübergehend geschlossen. Bitte versuchen Sie es später noch einmal.", 'dsmart'),
				));
			}
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
				echo json_encode(array(
					'check' => false,
					'has_discount' 		=> $has_discount,
					'has_reduce' 		=> $has_reduce,
					'reduce' 			=> $reduce,
					'reduce_percent' 	=> $reduce_percent . '%',
					'shipping' 			=> ds_price_format_text_no_convert($shipping),
					'vat7' 				=> ds_price_format_text_no_convert($vat7),
					'vat19' 			=> ds_price_format_text_no_convert($vat19),
					'coupon'	 		=> $coupon_array,
					'total' 			=> ds_price_format_text_no_convert($total_all),
					'message' => __("Der Laden ist vorübergehend geschlossen. Bitte versuchen Sie es später noch einmal.", 'dsmart'),
				));
			} else {
				if ($time_open_shop == "" && $time_close_shop == "") {
					return true;
				} elseif ($time_open_shop == "" && $time_close_shop != "") {
					$time_close_shop = new DateTime($time_close_shop);
					if ($time <= $time_close_shop) {
						echo json_encode(array(
							'check' 			=> true,
							'has_discount' 		=> $has_discount,
							'has_reduce' 		=> $has_reduce,
							'reduce' 			=> $reduce,
							'reduce_percent' 	=> $reduce_percent . '%',
							'shipping' 			=> ds_price_format_text_no_convert($shipping),
							'vat7' 				=> ds_price_format_text_no_convert($vat7),
							'vat19' 			=> ds_price_format_text_no_convert($vat19),
							'coupon'	 		=> $coupon_array,
							'total' 			=> ds_price_format_text_no_convert($total_all),
						));
					} else {
						echo json_encode(array(
							'check' => false,
							'has_discount' 		=> $has_discount,
							'has_reduce' 		=> $has_reduce,
							'reduce' 			=> $reduce,
							'reduce_percent' 	=> $reduce_percent . '%',
							'shipping' 			=> ds_price_format_text_no_convert($shipping),
							'vat7' 				=> ds_price_format_text_no_convert($vat7),
							'vat19' 			=> ds_price_format_text_no_convert($vat19),
							'coupon'	 		=> $coupon_array,
							'total' 			=> ds_price_format_text_no_convert($total_all),
							'message' => __("Der Zeitpunkt, zu dem Sie einen Termin vereinbaren, stimmt nicht überein. Bitte wählen Sie eine Zeit vor ", 'dsmart') . $time_close_shop_text,
						));
					}
				} elseif ($time_open_shop != "" && $time_close_shop == "") {
					$time_open_shop = new DateTime($time_open_shop);
					if ($time >= $time_open_shop) {
						echo json_encode(array(
							'check' 			=> true,
							'has_discount' 		=> $has_discount,
							'has_reduce' 		=> $has_reduce,
							'reduce' 			=> $reduce,
							'reduce_percent' 	=> $reduce_percent . '%',
							'shipping' 			=> ds_price_format_text_no_convert($shipping),
							'vat7' 				=> ds_price_format_text_no_convert($vat7),
							'vat19' 			=> ds_price_format_text_no_convert($vat19),
							'coupon'	 		=> $coupon_array,
							'total' 			=> ds_price_format_text_no_convert($total_all),
						));
					} else {
						echo json_encode(array(
							'check' => false,
							'has_discount' 		=> $has_discount,
							'has_reduce' 		=> $has_reduce,
							'reduce' 			=> $reduce,
							'reduce_percent' 	=> $reduce_percent . '%',
							'shipping' 			=> ds_price_format_text_no_convert($shipping),
							'vat7' 				=> ds_price_format_text_no_convert($vat7),
							'vat19' 			=> ds_price_format_text_no_convert($vat19),
							'coupon'	 		=> $coupon_array,
							'total' 			=> ds_price_format_text_no_convert($total_all),
							'message' => __("Der Zeitpunkt, zu dem Sie einen Termin vereinbaren, stimmt nicht überein. Bitte wählen Sie eine Zeit danach ", 'dsmart') . $time_open_shop_text,
						));
					}
				} else {
					$time_open_shop = new DateTime($time_open_shop);
					$time_close_shop = new DateTime($time_close_shop);
					if ($time <= $time_close_shop && $time >= $time_open_shop) {
						echo json_encode(array(
							'check' 			=> true,
							'has_discount' 		=> $has_discount,
							'has_reduce' 		=> $has_reduce,
							'reduce' 			=> $reduce,
							'reduce_percent' 	=> $reduce_percent . '%',
							'shipping' 			=> ds_price_format_text_no_convert($shipping),
							'vat7' 				=> ds_price_format_text_no_convert($vat7),
							'vat19' 			=> ds_price_format_text_no_convert($vat19),
							'coupon'	 		=> $coupon_array,
							'total' 			=> ds_price_format_text_no_convert($total_all),
						));
					} else {
						echo json_encode(array(
							'check' => false,
							'has_discount' 		=> $has_discount,
							'has_reduce' 		=> $has_reduce,
							'reduce' 			=> $reduce,
							'reduce_percent' 	=> $reduce_percent . '%',
							'shipping' 			=> ds_price_format_text_no_convert($shipping),
							'vat7' 				=> ds_price_format_text_no_convert($vat7),
							'vat19' 			=> ds_price_format_text_no_convert($vat19),
							'coupon'	 		=> $coupon_array,
							'total' 			=> ds_price_format_text_no_convert($total_all),
							'message' => __("Die Uhrzeit, zu der Sie einen Termin vereinbaren, stimmt nicht überein. Bitte wählen Sie eine Zeit vor ", 'dsmart') . $time_open_shop_text . __(" to ", 'dsmart') . $time_close_shop_text,
						));
					}
				}
			}
		} else {
			if ($time_open_shop == "" && $time_close_shop == "") {
				echo json_encode(array(
					'check' 			=> true,
					'has_discount' 		=> $has_discount,
					'has_reduce' 		=> $has_reduce,
					'reduce' 			=> $reduce,
					'reduce_percent' 	=> $reduce_percent . '%',
					'shipping' 			=> ds_price_format_text_no_convert($shipping),
					'vat7' 				=> ds_price_format_text_no_convert($vat7),
					'vat19' 			=> ds_price_format_text_no_convert($vat19),
					'coupon'	 		=> $coupon_array,
					'total' 			=> ds_price_format_text_no_convert($total_all),
				));
			} elseif ($time_open_shop == "" && $time_close_shop != "") {
				$time_close_shop = new DateTime($time_close_shop);
				if ($time <= $time_close_shop) {
					echo json_encode(array(
						'check' 			=> true,
						'has_discount' 		=> $has_discount,
						'has_reduce' 		=> $has_reduce,
						'reduce' 			=> $reduce,
						'reduce_percent' 	=> $reduce_percent . '%',
						'shipping' 			=> ds_price_format_text_no_convert($shipping),
						'vat7' 				=> ds_price_format_text_no_convert($vat7),
						'vat19' 			=> ds_price_format_text_no_convert($vat19),
						'coupon'	 		=> $coupon_array,
						'total' 			=> ds_price_format_text_no_convert($total_all),
					));
				} else {
					echo json_encode(array(
						'check' => false,
						'has_discount' 		=> $has_discount,
						'has_reduce' 		=> $has_reduce,
						'reduce' 			=> $reduce,
						'reduce_percent' 	=> $reduce_percent . '%',
						'shipping' 			=> ds_price_format_text_no_convert($shipping),
						'vat7' 				=> ds_price_format_text_no_convert($vat7),
						'vat19' 			=> ds_price_format_text_no_convert($vat19),
						'coupon'	 		=> $coupon_array,
						'total' 			=> ds_price_format_text_no_convert($total_all),
						'message' => __("Die Uhrzeit, zu der Sie einen Termin vereinbaren, stimmt nicht überein. Bitte wählen Sie eine Zeit vor ", 'dsmart') . $time_close_shop_text,
					));
				}
			} elseif ($time_open_shop != "" && $time_close_shop == "") {
				$time_open_shop = new DateTime($time_open_shop);
				if ($time >= $time_open_shop) {
					echo json_encode(array(
						'check' => true,
						'has_discount' 		=> $has_discount,
						'has_reduce' 		=> $has_reduce,
						'reduce' 			=> $reduce,
						'reduce_percent' 	=> $reduce_percent . '%',
						'shipping' 			=> ds_price_format_text_no_convert($shipping),
						'vat7' 				=> ds_price_format_text_no_convert($vat7),
						'vat19' 			=> ds_price_format_text_no_convert($vat19),
						'coupon'	 		=> $coupon_array,
						'total' 			=> ds_price_format_text_no_convert($total_all),
					));
				} else {
					echo json_encode(array(
						'check' => false,
						'has_discount' 		=> $has_discount,
						'has_reduce' 		=> $has_reduce,
						'reduce' 			=> $reduce,
						'reduce_percent' 	=> $reduce_percent . '%',
						'shipping' 			=> ds_price_format_text_no_convert($shipping),
						'vat7' 				=> ds_price_format_text_no_convert($vat7),
						'vat19' 			=> ds_price_format_text_no_convert($vat19),
						'coupon'	 		=> $coupon_array,
						'total' 			=> ds_price_format_text_no_convert($total_all),
						'message' => __("Die Uhrzeit, zu der Sie einen Termin vereinbaren, stimmt nicht überein. Bitte wählen Sie eine Uhrzeit nach " . $time_open_shop_text),
					));
				}
			} else {
				$time_open_shop = new DateTime($time_open_shop);
				$time_close_shop = new DateTime($time_close_shop);
				if ($time <= $time_close_shop && $time >= $time_open_shop) {
					echo json_encode(array(
						'check' => true,
						'has_discount' 		=> $has_discount,
						'has_reduce' 		=> $has_reduce,
						'reduce' 			=> $reduce,
						'reduce_percent' 	=> $reduce_percent . '%',
						'shipping' 			=> ds_price_format_text_no_convert($shipping),
						'vat7' 				=> ds_price_format_text_no_convert($vat7),
						'vat19' 			=> ds_price_format_text_no_convert($vat19),
						'coupon'	 		=> $coupon_array,
						'total' 			=> ds_price_format_text_no_convert($total_all),
					));
				} else {
					echo json_encode(array(
						'check' => false,
						'has_discount' 		=> $has_discount,
						'has_reduce' 		=> $has_reduce,
						'reduce' 			=> $reduce,
						'reduce_percent' 	=> $reduce_percent . '%',
						'shipping' 			=> ds_price_format_text_no_convert($shipping),
						'vat7' 				=> ds_price_format_text_no_convert($vat7),
						'vat19' 			=> ds_price_format_text_no_convert($vat19),
						'coupon'	 		=> $coupon_array,
						'total' 			=> ds_price_format_text_no_convert($total_all),
						'message' => __("Die von Ihnen gewählte Zeit stimmt nicht überein. Bitte wählen Sie eine Zeit von ", 'dsmart') . $time_open_shop_text . __(" bis ", 'dsmart') . $time_close_shop_text,
					));
				}
			}
		}
	}
	exit;
}
add_action('wp_ajax_check_time_available', 'check_time_available');
add_action('wp_ajax_nopriv_check_time_available', 'check_time_available');

//checkout cart
function checkout_cart()
{
	$customer_name1 = $_REQUEST['customer_name1'];
	$customer_name2 = $_REQUEST['customer_name2'];
	$customer_phone = $_REQUEST['customer_phone'];
	$customer_email = $_REQUEST['customer_email'];
	$more_additional = $_REQUEST['more_additional'];
	$customer_etage = $_REQUEST['customer_etage'];
	$customer_address = $_REQUEST['customer_address'];
	$customer_zipcode = $_REQUEST['customer_zipcode'];
	$method = $_REQUEST['method'];
	$bab = (isset($_REQUEST['bab']) && intval($_REQUEST['bab']) == 1) ? 1 : 0;
	$ar = json_decode(stripcslashes($_REQUEST['ar']), true);
	$tax = get_option('dsmart_tax');
	$currency = ds_cs_price_symbol2();
	$total_all = 0;
	$total_all_use_coupon = 0;
	$total_cart = 0;
	$tax_price = 0;
	$vat = 0;
	$taxes = 0;
	$shop_id = get_shop_id();
	$shop = get_shop_data_by_id($shop_id);
	$address = $shop->shop_address;
	$checkout_page = get_page_id_by_template('templates/checkout-page.php');
	$thankyou_page = get_page_id_by_template('templates/template-thankyou.php');
	$dsmart_custom_method = get_option('dsmart_custom_method');
	$check_zipcode = (get_option('zipcode_status') == "on") ? true : false;
	$coupon_array = array();
	$coupon_price_all = 0;
	$check_coupon = true;
	if (is_user_logged_in()) {
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
	} else {
		$user_id = 0;
	}
	if (isset($_COOKIE['cart']) && $_COOKIE['cart'] != "") {
		$cart = unserialize(base64_decode($_COOKIE['cart']));
	}

	$all_category_not_open = get_all_category_not_open();
	$isHacked = false;
	if(count($all_category_not_open) > 0 && count($cart) > 0){
		foreach ($cart as $key_item => $value_item) {
			$cats = wp_get_post_terms($value_item['product_id'],'product-cat', array( 'fields' => 'ids' ));
			$check_cat = true;
			if(count($cats) > 0){
				foreach ($cats as $cat) {
					if(in_array($cat, $all_category_not_open)){
						$isHacked = true;
						break;
					}
				}
			}
		}
	}
	if($isHacked){
		die(json_encode(array(
			'check' => false,
			'message' => __("Bitte aktualisieren Sie die Seite und versuchen Sie es erneut!", 'dsmart'),
		)));
	}

	if ($customer_name1 != "" && $customer_name2 != "" && $customer_email != "" && $customer_phone != "" && ($method == "paypal" || $method == "klarna" || $method == "cash" || in_array($method, $dsmart_custom_method))) {
		if (isset($cart) && count($cart) > 0) {
			$order_code = ds_random_order_code();
			$checkout_data = array('order_code' => $order_code, 'customer_name1' => $customer_name1, 'customer_name2' => $customer_name2, 'customer_phone' => $customer_phone, 'customer_email' => $customer_email, 'customer_address' => $customer_address, 'more_additional' => $more_additional, 'customer_etage' => $customer_etage, 'customer_zipcode' => $customer_zipcode, 'method' => $method, 'cart' => $cart, 'bab' => $bab, 'ar' => $ar);
			setcookie('checkout_data', json_encode($checkout_data), time() + 2592000, '/', NULL, 0);
			foreach ($cart as $key_item => $value_item) {
				$product_id = intval($value_item['product_id']);
				$quantity = $value_item['quantity'];
				$meta['quantity'] = dsmart_field('quantity', $product_id);
				$meta['varialbe_price'] = dsmart_field('varialbe_price', $product_id);

				$meta['extra_name'] = dsmart_field('extra_name', $product_id);
				$meta['extra_price'] = dsmart_field('extra_price', $product_id);

				$meta['sidedish_name'] = dsmart_field('sidedish_name', $product_id);
				$meta['sidedish_price'] = dsmart_field('sidedish_price', $product_id);

				$sidedish_price = 0;
				if (isset($value_item['sidedish_info']) && $value_item['sidedish_info'] != null && $meta['sidedish_name'] != null && !empty(array_filter($meta['sidedish_name']))) :
					$sidedish_info = json_decode(stripslashes($value_item['sidedish_info']));
					foreach ($sidedish_info as $sidedish_key => $sidedish_value) {
						$sidedish_id = intval(explode('_', $sidedish_value->sidedish_id)[1]) - 1;
						$temp_price = $meta['sidedish_price'][$sidedish_id];
						if(isset($temp_price) && $temp_price!= null && $temp_price !== ""){
							$sidedish_price = floatval($temp_price);
						}
						else{
							$sidedish_price = 0;
						}
					}
				endif;

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
				endif;

				if (isset($value_item['variable_id']) && $meta['quantity'] != null &&  !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']))) :
					$variable_id = intval(explode('_', $value_item['variable_id'])[1]) - 1;
					$price_item =  floatval($meta['varialbe_price'][$variable_id]) + $extra_price + $sidedish_price;
					$price 		= $price_item * intval($quantity);
				else :
					$variable_id = '';
					$price_item = dsmart_field('price', $product_id);
					$price 		= ds_caculate_item_price($product_id, $quantity) + ($extra_price + $sidedish_price) * $quantity;
				endif;

				$status_item = dsmart_field('status', $product_id);
				$vat_item = dsmart_field('vat', $product_id);
				$taxes_item = dsmart_field('taxes', $product_id);
				if ($status_item == "instock" && $price_item != "") {
					// $price = ds_caculate_item_price($key_item,$quantity);
					$total_all = $total_all + $price;
					if (check_product_can_use_coupon_or_not($product_id) == true) {
						$total_all_use_coupon = $total_all_use_coupon + $price;
					}
					if ($vat_item != "") {
						$vat_price = $price - round($price / (1 + $vat_item / 100), 2);
						$vat = $vat + $vat_price;
					}
					if ($taxes_item != "") {
						$taxes_price 	= $price - round($price / (1 + $taxes_item / 100), 2);
						$taxes = $taxes + $taxes_price;
					}
				}
			}
			if (isset($_COOKIE['shipping_info']) && $_COOKIE['shipping_info'] != "") {
				$shipping_info = unserialize(base64_decode($_COOKIE['shipping_info']));
				$shipping_method = $shipping_info['shipping_method'];
				$user_location = "";
				$user_latitude = "";
				$user_longitude = "";
				$user_time = "";
				$shop_id = $shipping_info['shop'];
				if ($shipping_method == "shipping") {
					$address = $user_location = $shipping_info['location'];
					$user_latitude = $shipping_info['latitude'];
					$user_longitude = $shipping_info['longitude'];
					$delivery_time = $shipping_info['delivery_time'];
					$delivery_date = $shipping_info['delivery_date'];

					$has_discount = is_discount_time($delivery_time, $delivery_date, $shipping_method);
				} else {
					$user_time = $shipping_info['time'];
					$has_discount = is_discount_time($user_time, null, $shipping_method);
				}
				$shipping_data = check_shipping_available($shipping_info);
				$shipping_fee = $shipping_data['price'];
				$shipping_check = $shipping_data['check'];
			} else {
				$shipping_fee = 0;
			}
			$total_all 			= $total_all;
			if ($shipping_method == "shipping") {
				$type_promotion 	= get_option('type_promotion');
				$promotion = get_option('promotion');
			} else {
				$type_promotion 	= get_option('type_promotion_2');
				$promotion = get_option('promotion_2');
			}
			$discount_min = get_option('discount_min');
			if ($discount_min != '' && $discount_min != 0 && $discount_min != '0' && floatval($discount_min) > $total_all) {
				$has_discount = false;
			}else{
				if ($promotion != null && $has_discount) :
					if ($type_promotion == '%') :
						$temp_total = $total_all_use_coupon * floatval($promotion) / 100;
						$total_all = $total_all - $temp_total;
						$total_all_use_coupon = $total_all_use_coupon - $temp_total;
					else :
						$total_all = $total_all - floatval($promotion);
						$total_all_use_coupon = $total_all_use_coupon - floatval($promotion);
					endif;
				endif;
			}
				
			if (isset($_COOKIE['coupon']) && $_COOKIE['coupon'] != "") {
				$coupon = explode(',', $_COOKIE['coupon']);
				foreach ($coupon as $key_coupon => $item_coupon) {
					$coupon_id = get_coupon_id_from_code($item_coupon);
					$coupon_mutiple = dsmart_field('coupon_mutiple', $coupon_id);
					$coupon_shipping = dsmart_field('coupon_shipping', $coupon_id);
					if ($shipping_method != "" && $coupon_shipping != "" && $shipping_method != $coupon_shipping) {
						unset($coupon[$key_coupon]);
						setcookie('coupon', implode(',', $coupon), time() + 2592000, '/', NULL, 0);
					} else {
						if (count($coupon) > 1 && $coupon_mutiple != "yes") {
							$check_coupon = false;
						}
						if (check_coupon_available($item_coupon) == 1) {
							$coupon_price_all += $coupon_price = get_price_of_coupon($item_coupon, $total_all_use_coupon);
							$coupon_array[$item_coupon] = $coupon_price;
							$total_all = $total_all - $coupon_price;
							$total_all_use_coupon = $total_all_use_coupon - $coupon_price;
						}
					}
				}
			} else {
				$coupon_price = 0;
				$coupon_price_all = 0;
			}
			if ($check_coupon == false) {
				die(json_encode(array(
					'check' => false,
					'message' => __("Fehler!", 'dsmart'),
				)));
			}
			$total_cart = $total_all + $shipping_fee;
			if ($total_cart < 0) {
				$total_cart = 0;
			}
			$total_send = $total_cart * 100;
			if ($total_cart > 0) {
				$dsmart_sandbox = get_option('dsmart_sandbox');
				if ($method == "paypal") {
					$data = array();
					$order_id = create_new_order($checkout_data, 'pending');
					$token = generateRandomString();
					update_post_meta($order_id, 'paypal_token', $token);
					$dsmart_sandbox = get_option('dsmart_sandbox');
					$paypal_email = get_option('dsmart_paypal_email_address');
					if ($dsmart_sandbox == "yes") {
						$data['cmd'] = '_xclick';
						$data['item_name'] = 'ORDER ' . $order_code;
						$data['return'] = get_permalink($thankyou_page) . '?token-success=' . $token . '&order=' . $order_id;
						$data['cancel_return'] = get_permalink($checkout_page);
						$data['business'] = $paypal_email;
						$data['currency_code'] = $currency;
						$data['amount'] = $total_cart;
						$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
					} else {
						$data['cmd'] = '_xclick';
						$data['item_name'] = 'ORDER ' . $order_code;
						$data['return'] = get_permalink($thankyou_page) . '?token=' . $token . '&order=' . $order_id;
						$data['cancel_return'] = get_permalink($checkout_page);
						$data['business'] = $paypal_email;
						$data['currency_code'] = $currency;
						$data['amount'] = $total_cart;
						$url = "https://www.paypal.com/cgi-bin/webscr";
					}
					$response = wp_remote_post(
						$url,
						array(
							'method' => 'POST',
							'timeout' => 45,
							'redirection' => 5,
							'httpversion' => '1.1',
							'sslverify' => false,
							'blocking' => true,
							'body' => $data,
						)
					);
					if (is_wp_error($response)) {
						die(json_encode(array(
							'check' => false,
							'message' => __("Error!", 'dsmart'),
						)));
					}
					echo wp_json_encode(
						array(
							'check' => true,
							'redirect_url' => $response['http_response']->get_response_object()->url
						)
					);
					/*$clientId = get_option('dsmart_paypal_client_id');
					$clientSecret = get_option('dsmart_paypal_client_secret');
					$dsmart_sandbox = get_option('dsmart_sandbox');
					if($dsmart_sandbox == "yes"){
						$environment = new SandboxEnvironment($clientId, $clientSecret);
					}else{
						$environment = new ProductionEnvironment($clientId, $clientSecret);
					}
					$client = new PayPalHttpClient($environment);

					$request = new OrdersCreateRequest();
					$request->prefer('return=representation');
					$request->body = [
	                     "intent" => "CAPTURE",
	                     "purchase_units" => [[
	                         "reference_id" => 'ORDER '.$order_code,
	                         "amount" => [
	                             "value" => $total_cart,
	                             "currency_code" => $currency
	                         ]
	                     ]],
	                     "application_context" => [
	                          "cancel_url" => home_url(),
	                          "return_url" => get_permalink($thankyou_page).'?order='.$order_id.'&token-success='.$token
	                     ] 
	                ];

					try {
					    $response = $client->execute($request);
					    $payment_link = $response->result->links[1]->href;
					    echo json_encode(array(
				    		'check' => true,
				    		'redirect_url' => $payment_link,
					    ));
					}catch (HttpException $ex) {
					    echo json_encode(array(
							'check' => false,
							'message' => $ex->getMessage(),
						));
					}*/
				} elseif ($method == "klarna") {
					$order_id = create_new_order($checkout_data, 'pending');
					require_once BOOKING_ORDER_PATH2  . 'klarna/autoload.php';
					$image_id = get_option('ds_logo');
					if ($image_id != null) {
						$image = wp_get_attachment_image_src($image_id, 'full')[0];
					} else {
						$image = BOOKING_ORDER_PATH . 'img/no_img.jpg';
					}
					if ($dsmart_sandbox == "yes") {
						$klarna_url = EU_TEST_BASE_URL;
						$apiEndpoint = Klarna\Rest\Transport\ConnectorInterface::EU_TEST_BASE_URL;
					} else {
						$klarna_url = EU_BASE_URL;
						$apiEndpoint = Klarna\Rest\Transport\ConnectorInterface::EU_BASE_URL;
					}
					$merchantId = get_option('klarna_username');
					$sharedSecret = get_option('klarna_password');


					$connector = Klarna\Rest\Transport\CURLConnector::create(
						$merchantId,
						$sharedSecret,
						$apiEndpoint
					);
					$address = [
						"given_name" => $customer_name1,
						"family_name" => $customer_name2,
						"email" => $customer_email,
						"title" => "",
						"street_address" => $address,
						"street_address2" => "",
						"postal_code" => '20038',
						"city" => "Munich",
						"region" => "",
						"phone" => $customer_phone,
						"country" => "DE"
					];

					$data = [
						"billing_address" => $address,
						"shipping_address" => $address,
						"purchase_country" => "DE",
						"purchase_currency" => "EUR",
						"locale" => "de-DE",
						"order_amount" => $total_send,
						"order_tax_amount" => 0,
						"order_lines" => [
							[
								"type" => "physical",
								"reference" => $order_code,
								"name" => $order_code,
								"quantity" => 1,
								"quantity_unit" => "unit",
								"unit_price" => $total_send,
								"tax_rate" => 0,
								"total_amount" => $total_send,
								"total_tax_amount" => 0
							]
						],
					];
					$session = new Klarna\Rest\Payments\Sessions($connector);
					$session->create($data);
					$sessionId = $session->getId();
					$session = [
						"merchant_urls" => [
							"cancel" => get_permalink($checkout_page),
							"failure" => get_permalink($checkout_page),
							"privacy_policy" => home_url(),
							"success" => get_permalink($thankyou_page) . "?token={{authorization_token}}",
							"terms" => home_url()
						],
						"options" => [
							"background_images" => [
								[
									"url" => "https://example.com/bgimage.jpg",
									"width" => 1200
								]
							],
							"logo_url" => $image,
							"page_title" => "Complete your purchase",
							"payment_method_category" => "pay_later",
							"purchase_type" => "buy"
						],
						"payment_session_url" => "https://api.klarna.com/payments/v1/sessions/$sessionId"
					];

					try {
						$hpp = new Klarna\Rest\HostedPaymentPage\Sessions($connector);
						$sessionData = $hpp->create($session);
						setcookie('data', json_encode($checkout_data), time() + 2592000, '/', NULL, 0);
						die(json_encode(array(
							'check' => true,
							'redirect_url' => $sessionData['redirect_url'],
						)));
					} catch (Exception $e) {
						die(json_encode(array(
							'check' => false,
							'message' => $session,
						)));
					}
				} else {
					setcookie('cart', null, time() - 2592000, '/', NULL, 0);
					$order_id = create_new_order($checkout_data);
					echo json_encode(array(
						'check' => true,
						'redirect_url' => get_permalink($order_id),
					));
				}
			} else {
				setcookie('cart', null, time() - 2592000, '/', NULL, 0);
				$order_id = create_new_order($checkout_data);
				echo json_encode(array(
					'check' => true,
					'redirect_url' => get_permalink($order_id),
				));
			}
		} else {
			echo json_encode(array(
				'check' => false,
				'message' => __("Ihr Warenkorb ist leer.", 'dsmart'),
			));
		}
	} else {
		echo json_encode(array(
			'check' => false,
			'message' => __("Bitte füllen Sie alle Informationen aus.", 'dsmart'),
		));
	}
	exit;
}
add_action('wp_ajax_checkout_cart', 'checkout_cart');
add_action('wp_ajax_nopriv_checkout_cart', 'checkout_cart');

//complete order in frontend
function ajax_complete_order()
{
	$id = $_REQUEST['id'];
	$field_status = dsmart_field('status', $id);
	if ($field_status == "processing") {
		update_post_meta($id, 'status', "completed");
		wp_send_mail_order($id, $field_status, "completed");
		echo json_encode(array(
			'check' => true,
		));
	} else {
		echo json_encode(array(
			'check' => false,
			'z' => $field_status,
			'message' => __("Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut", 'dsmart'),
		));
	}
	exit;
}
add_action('wp_ajax_ajax_complete_order', 'ajax_complete_order');
add_action('wp_ajax_nopriv_ajax_complete_order', 'ajax_complete_order');
//cancel order in frontend
function ajax_cancel_order()
{
	$id = $_REQUEST['id'];
	$field_status = dsmart_field('status', $id);
	if ($field_status == "processing" || $field_status == "pending") {
		update_post_meta($id, 'status', "cancelled");
		wp_send_mail_order($id, $field_status, "cancelled");
		echo json_encode(array(
			'check' => true,
		));
	} else {
		echo json_encode(array(
			'check' => false,
			'message' => __("Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut", 'dsmart'),
		));
	}
	exit;
}
add_action('wp_ajax_ajax_cancel_order', 'ajax_cancel_order');
add_action('wp_ajax_nopriv_ajax_cancel_order', 'ajax_cancel_order');
//processing order in frontend
function ajax_processing_order()
{
	$id = $_REQUEST['id'];
	$field_status = dsmart_field('status', $id);
	if ($field_status == "pending") {
		update_post_meta($id, 'status', "processing");
		wp_send_mail_order($id, $field_status, "processing");
		echo json_encode(array(
			'check' => true,
		));
	} else {
		echo json_encode(array(
			'check' => false,
			'message' => __("Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut", 'dsmart'),
		));
	}
	exit;
}
add_action('wp_ajax_ajax_processing_order', 'ajax_processing_order');
add_action('wp_ajax_nopriv_ajax_processing_order', 'ajax_processing_order');

function ajax_add_to_cart()
{
	$check 				= false;
	$error 				= false;
	$message 			= '';
	$id 				= $_REQUEST['id'];
	$pro_status = dsmart_field('status', $id);
	if (isset($_REQUEST['total_quantity'])) {
		$total_quantity = $_REQUEST['total_quantity'];
	} else {
		$total_quantity = 1;
	}
	if (isset($_REQUEST['variable_id'])) {
		$variable_id = $_REQUEST['variable_id'];
	} else {
		$variable_id = null;
	}
	if($variable_id === "") $variable_id = null;
	if (isset($_REQUEST['extra_info'])) {
		$extra_info 	= json_decode(stripslashes($_REQUEST['extra_info']), true);
	} else {
		$extra_info 	= null;
	}
	if (empty($extra_info)) {
		$extra_info = null;
	}
	if (isset($_REQUEST['sidedish_info'])) {
		$sidedish_info 	= json_decode(stripslashes($_REQUEST['sidedish_info']), true);
	} else {
		$sidedish_info 	= null;
	}
	if (empty($sidedish_info)) {
		$sidedish_info = null;
	}
	$cart_id = get_page_id_by_template('templates/cart-page.php');
	if ($id != "") {
		if ($pro_status == "instock") {
			if (isset($_COOKIE['cart']) && $_COOKIE['cart'] != "") {
				$cart 	= unserialize(base64_decode($_COOKIE['cart']));
			} else {
				$cart 	= array();
			}
			if (count($cart) > 0) {
				$new_pos = null;
				$post_terms = wp_get_post_terms($id, 'product-cat', array('fields' => 'ids'));
				if (isset($post_terms[0])) {
					$new_position = (intval(get_term_meta($post_terms[0], 'category_pos', true)) > 0) ? intval(get_term_meta($post_terms[0], 'category_pos', true)) : 0;
				} else {
					$new_position = 0;
				}
				$pos_id = 0;
				$beforeZZ = "test";
				$afterZZ = "test";
				foreach ($cart as $key => $value) {
					$cart_item_terms = wp_get_post_terms($value['product_id'], 'product-cat', array('fields' => 'ids'));
					if (isset($cart_item_terms[0])) {
						$check_postion = (intval(get_term_meta($cart_item_terms[0], 'category_pos', true)) > 0) ? intval(get_term_meta($cart_item_terms[0], 'category_pos', true)) : 0;
						if ($check_postion > $new_position) {
							if ($new_pos === null)
								$new_pos = $pos_id;
						}
					}
					if (intval($id) === $value['product_id']) {
						$check 					= true;
						$variable_id_current = $cart[$key]['variable_id'];
						$extra_info_current = json_decode(stripslashes($value['extra_info']), true);
						$sidedish_info_current = json_decode(stripslashes($value['sidedish_info']), true);
						
						$isSameVariable = ($variable_id === $variable_id_current);
						$isSameExtra = ($extra_info === $extra_info_current);
						$isSameSidedish = (($sidedish_info == null && $sidedish_info_current == null) || $sidedish_info === $sidedish_info_current);

						if($isSameVariable && $isSameExtra && $isSameSidedish){
							$check 					= true;
							$beforeZZ = $cart[$key]['quantity'];
							$cart[$key]['quantity'] = intval($cart[$key]['quantity']) + intval($total_quantity);
							$afterZZ = $cart[$key]['quantity'];
							$testest = intval($cart[$key]['quantity']) + intval($total_quantity);
							break;
						}
						else{
							$check 					= false;
						}
					}
					$pos_id++;
				}

				if ($check == false) {
					$error = false;
					if ($new_pos !== null) {
						$new_cart = array();
						$pos_id = 0;
						foreach ($cart as $key => $value) {
							if ($pos_id == $new_pos) {
								$extra_id = '';
								if($extra_info != null){
									foreach ($extra_info as $extra_key => $extra_value) {
										$extra_id .= '_' . $extra_value['extra_id'];
									}
								}

								$sidedish_id = '';
								if($sidedish_info != null){
									foreach ($sidedish_info as $sidedish_key => $sidedish_value) {
										$sidedish_id .= '_' . $sidedish_value['sidedish_id'];
									}
								}
								$temp = [];
								$temp["product_id"] = intval($id);
								$temp["quantity"] = intval($total_quantity);
								if($variable_id != null) $temp["variable_id"] = $variable_id;
								if($extra_info != null) $temp["extra_info"] = addslashes(json_encode($extra_info));
								if($sidedish_info != null) $temp["sidedish_info"] = addslashes(json_encode($sidedish_info));
								$new_cart[$id . ($variable_id != null ? '_' : '') . $variable_id . $extra_id . $sidedish_id] = $temp;
							}
							$new_cart[$key] = $value;
							$pos_id++;
						}
						$cart = $new_cart;
					} else {
						$extra_id = '';
						if($extra_info != null){
							foreach ($extra_info as $extra_key => $extra_value) {
								$extra_id .= '_' . $extra_value['extra_id'];
							}
						}

						$sidedish_id = '';
						if($sidedish_info != null){
							foreach ($sidedish_info as $sidedish_key => $sidedish_value) {
								$sidedish_id .= '_' . $sidedish_value['sidedish_id'];
							}
						}

						$temp = [];
						$temp["product_id"] = intval($id);
						$temp["quantity"] = intval($total_quantity);
						if($variable_id != null) $temp["variable_id"] = $variable_id;
						if($extra_info != null) $temp["extra_info"] = addslashes(json_encode($extra_info));
						if($sidedish_info != null) $temp["sidedish_info"] = addslashes(json_encode($sidedish_info));
						$cart[$id . ($variable_id != null ? '_' : '') . $variable_id . $extra_id . $sidedish_id] = $temp;
					}
				}
			} else {
				$error 					= false;
				$extra_id = '';
				if($extra_info != null){
					foreach ($extra_info as $extra_key => $extra_value) {
						$extra_id .= '_' . $extra_value['extra_id'];
					}
				}

				$sidedish_id = '';
				if($sidedish_info != null){
					foreach ($sidedish_info as $sidedish_key => $sidedish_value) {
						$sidedish_id .= '_' . $sidedish_value['sidedish_id'];
					}
				}

				$temp = [];
				$temp["product_id"] = intval($id);
				$temp["quantity"] = intval($total_quantity);
				if($variable_id != null) $temp["variable_id"] = $variable_id;
				if($extra_info != null) $temp["extra_info"] = addslashes(json_encode($extra_info));
				if($sidedish_info != null) $temp["sidedish_info"] = addslashes(json_encode($sidedish_info));
				$cart[$id . ($variable_id != null ? '_' : '') . $variable_id . $extra_id . $sidedish_id] = $temp;
			}
			if ($error == false) {
				setcookie('cart', base64_encode(serialize($cart)), time() + 2592000, '/', NULL, 0);
				echo json_encode(array(
					'check' => $error,
					'message' => __('Produkt "' . get_the_title($id) . '" wurde hinzugefügt.<a class="go-to-cart" href="' . get_permalink($cart_id) . '">Zum Warenkorb</a>'),
					'total' => ds_get_cart_total_quantity($cart),
					'price' => ds_price_format_text_no_convert(ds_get_cart_total_item($cart)),
					'debug' =>  $variable_id,
					'after' =>  $variable_id_current,
					'before' =>  $isSameVariable,
					'zzz' => $testest

				));
			} else {
				echo json_encode(array(
					'check' => $error,
					'message' => $message,
				));
			}
		} else {
			$error 					= true;
			echo json_encode(array(
				'check' => $error,
				'message' => __("Das Produkt ist nicht mehr vorrätig. Bitte wählen Sie ein anderes Produkt", 'dsmart'),
			));
		}
	} else {
		$error 					= true;
		echo json_encode(array(
			'check' => $error,
			'message' => __("Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut", 'dsmart'),
		));
	}
	exit;
}
add_action('wp_ajax_ajax_add_to_cart', 'ajax_add_to_cart');
add_action('wp_ajax_nopriv_ajax_add_to_cart', 'ajax_add_to_cart');

//print order
function ajax_print_order()
{
	$id = $_REQUEST['id'];
	require plugin_dir_path(__FILE__) . 'fpdf181/fpdf.php';
	// class PDF extends FPDF{
	// 	// Page header
	// 	function Header()
	// 	{
	// 	    // Arial bold 15
	// 	    $this->SetFont('Arial','B',15);
	// 	    // Move to the right
	// 	    $this->Cell(80);
	// 	    // Title
	// 	    $this->Cell(30,10,'Order #'.get_the_title($id),1,0,'C');
	// 	    // Line break
	// 	    $this->Ln(20);
	// 	}

	// 	// Page footer
	// 	function Footer()
	// 	{
	// 	    // Position at 1.5 cm from bottom
	// 	    $this->SetY(-15);
	// 	    // Arial italic 8
	// 	    $this->SetFont('Arial','I',8);
	// 	    // Page number
	// 	    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	// 	}
	// }
	class PDF_MC_Table extends FPDF
	{
		var $widths;
		var $aligns;

		function Header()
		{
			// Arial bold 15
			$this->AddFont('Oswald-Bold', '', 'Oswald-Bold.php');

			$this->SetFont('Oswald-Bold', '', 15);
			// Move to the right
			$this->Cell(80);
			// Title
			$this->Cell(30, 10, 'Order #' . get_the_title($_REQUEST['id']), 1, 0, 'C');
			// Line break
			$this->Ln(20);
		}

		// Page footer
		function Footer()
		{
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			// Arial italic 8
			$this->AddFont('Oswald-RegularItalic', '', 'Oswald-RegularItalic.php');
			$this->SetFont('Oswald-RegularItalic', '', 8);
			// Page number
			$this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
		}

		function SetWidths($w)
		{
			//Set the array of column widths
			$this->widths = $w;
		}

		function SetAligns($a)
		{
			//Set the array of column alignments
			$this->aligns = $a;
		}

		function Row($data)
		{
			//Calculate the height of the row
			$nb = 0;
			for ($i = 0; $i < count($data); $i++)
				$nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
			$h = 5 * $nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row
			for ($i = 0; $i < count($data); $i++) {
				$w = $this->widths[$i];
				$a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
				//Save the current position
				$x = $this->GetX();
				$y = $this->GetY();
				//Draw the border
				$this->Rect($x, $y, $w, $h);
				//Print the text
				$this->MultiCell($w, 5, $data[$i], 0, $a);
				//Put the position to the right of the cell
				$this->SetXY($x + $w, $y);
			}
			//Go to the next line
			$this->Ln($h);
		}

		function CheckPageBreak($h)
		{
			//If the height h would cause an overflow, add a new page immediately
			if ($this->GetY() + $h > $this->PageBreakTrigger)
				$this->AddPage($this->CurOrientation);
		}

		function NbLines($w, $txt)
		{
			//Computes the number of lines a MultiCell of width w will take
			$cw = &$this->CurrentFont['cw'];
			if ($w == 0)
				$w = $this->w - $this->rMargin - $this->x;
			$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
			$s = str_replace("\r", '', $txt);
			$nb = strlen($s);
			if ($nb > 0 and $s[$nb - 1] == "\n")
				$nb--;
			$sep = -1;
			$i = 0;
			$j = 0;
			$l = 0;
			$nl = 1;
			while ($i < $nb) {
				$c = $s[$i];
				if ($c == "\n") {
					$i++;
					$sep = -1;
					$j = $i;
					$l = 0;
					$nl++;
					continue;
				}
				if ($c == ' ')
					$sep = $i;
				$l += $cw[$c];
				if ($l > $wmax) {
					if ($sep == -1) {
						if ($i == $j)
							$i++;
					} else
						$i = $sep + 1;
					$sep = -1;
					$j = $i;
					$l = 0;
					$nl++;
				} else
					$i++;
			}
			return $nl;
		}
	}

	$currency = dsmart_field('currency', $id);
	$customer_name1 = dsmart_field('customer_name1', $id);
	$customer_name2 = dsmart_field('customer_name2', $id);
	$customer_email = dsmart_field('customer_email', $id);
	$customer_phone = dsmart_field('customer_phone', $id);
	$more_additional = dsmart_field('more_additional', $id);
	$items = dsmart_field('item', $id);
	$subtotal = dsmart_field('subtotal', $id);
	$coupon = dsmart_field('coupon', $id);
	$coupon_price = dsmart_field('coupon_price', $id);
	$shipping_method = dsmart_field('shipping_method', $id);
	$user_location = dsmart_field('user_location', $id);
	$user_latitude = dsmart_field('user_latitude', $id);
	$user_longitude = dsmart_field('user_longitude', $id);
	$user_time = dsmart_field('user_time', $id);
	$shipping_fee = dsmart_field('shipping_fee', $id);
	$total = dsmart_field('total', $id);
	$status = dsmart_field('status', $id);
	/*$vat = dsmart_field('vat',$id);*/
	$reduce = dsmart_field('reduce', $id);
	$reduce_percent = dsmart_field('reduce_percent', $id);
	$total_after_tax = 100 - intval($vat);
	if ($currency == "€") {
		$currency = 'EURO';
	}
	if ($status == "processing") {
		$status_text = '<span class="processing">' . __("in Bearbeitung", 'dsmart') . '</span>';
	} elseif ($status == "completed") {
		$status_text = '<span class="completed">' . __("Fertig", 'dsmart') . '</span>';
	} else {
		$status_text = '<span class="cancelled">' . __("Abgebrochen", 'dsmart') . '</span>';
	}
	$pdf = new PDF_MC_Table();
	$pdf->AddFont('Oswald-Regular', '', 'Oswald-Regular.php');
	$pdf->AddPage();
	$pdf->SetFont('Oswald-Regular', '', 24);
	$pdf->SetFillColor(255, 51, 0);
	$pdf->SetTextColor(0);
	$fill = false;
	$pdf->MultiCell(0, 20, 'Bestellung #' . get_the_title($id), 0, 'C', false);
	$pdf->SetFont('Oswald-Regular', '', 14);
	$pdf->SetFillColor(224, 235, 255);
	$pdf->MultiCell(0, 10, 'Kundeninformationen', 0, 'C', false);
	$pdf->SetFillColor(255, 0, 0);
	$pdf->SetDrawColor(128, 0, 0);
	$pdf->SetLineWidth(.3);
	$pdf->Cell('Nachname', 10, 'Nachname', 'LTRB', 0, 'L', $fill);
	$pdf->Cell('', 10, iconv('UTF-8', 'windows-1252', $customer_name1), 'TRB', 0, 'R', $fill);
	$pdf->Ln();

	$pdf->Cell('Vorname', 10, 'Vorname', 'LTRB', 0, 'L', $fill);
	$pdf->Cell('', 10, iconv('UTF-8', 'windows-1252', $customer_name2), 'TRB', 0, 'R', $fill);
	$pdf->Ln();

	$pdf->Cell('Email', 10, 'Email', 'LBR', 0, 'L', $fill);
	$pdf->Cell('', 10, $customer_email, 'LBR', 0, 'R', $fill);
	$pdf->Ln();

	$pdf->Cell('Telefonnummer', 10, 'Telefonnummer', 'LBR', 0, 'L', $fill);
	$pdf->Cell('', 10, $customer_phone, 'LBR', 0, 'R', $fill);
	$pdf->Ln();

	if ($shipping_method == "shipping") {
		$pdf->Cell('Versandart', 10, 'Versandart', 'LBR', 0, 'L', $fill);
		$pdf->Cell('Ship COD', 10, 'Ship COD', 'LBR', 0, 'R', $fill);
		$pdf->Ln();
		$pdf->Cell('Lieferanschrift', 10, 'Lieferanschrift', 'LBR', 0, 'L', $fill);
		$pdf->Cell('', 10, iconv('UTF-8', 'windows-1252', $user_location), 'LBR', 0, 'R', $fill);
		$pdf->Ln();
	} else {
		$pdf->Cell('Versandart', 10, 'Versandart', 'LBR', 0, 'L', $fill);
		$pdf->Cell('Go to shop', 10, 'Go to shop', 'LBR', 0, 'R', $fill);
		$pdf->Ln();
		$pdf->Cell('Zeit', 10, 'Zeit', 'LBR', 0, 'L', $fill);
		$pdf->Cell('', 10, $user_time, 'LBR', 0, 'R', $fill);
		$pdf->Ln();
	}

	$pdf->Cell('', 10, 'Bestellnotiz', 'LBR', 0, 'L', $fill);
	$pdf->Cell('', 10, iconv('UTF-8', 'windows-1252', $more_additional), 'LBR', 0, 'R', $fill);
	$pdf->Ln();

	$pdf->Cell(12, 0, '', 'T');

	$pdf->MultiCell(0, 10, 'Bestellinformationen', 0, 'C', false);

	$pdf->Cell('Produkt', 10, 'Produkt', 'LTBR', 0, 'L', $fill);
	$pdf->Cell('Gesamtsumme', 10, 'Gesamtsumme', 'LTBR', 0, 'R', $fill);
	$pdf->Ln();

	if ($items) {
		foreach ($items as $key => $value) {
			if (isset($value['product_id'])) :
				$product_id = intval($value['product_id']);
			else :
				$product_id = $key;
			endif;
			$meta['quantity'] = dsmart_field('quantity', $product_id);
			$meta['varialbe_price'] = dsmart_field('varialbe_price', $product_id);
			$meta['extra_name'] = dsmart_field('extra_name', $product_id);
			$meta['extra_price'] = dsmart_field('extra_price', $product_id);

			if (isset($value['variable_id']) && $meta['quantity'] != null && !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']))) :
				$variable_id = intval(explode('_', $value['variable_id'])[1]) - 1;
				$variable_text = __("AUSGEWÄHLTE PRODUKT: ", "dsmart") . $meta['quantity'][$variable_id] . ' - ' . cs_ds_price_format_text_with_symbol($meta['varialbe_price'][$variable_id], $currency) . '.';
			else :
				$variable_text = '';
			endif;
			if (isset($value['extra_info']) && $value['extra_info'] != null && $meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']))) :
				$extra_info = json_decode(stripslashes($value['extra_info']));
				$extra_text = __('Extra: ', 'dsmart');
				$a = 1;
				foreach ($extra_info as $extra_key => $extra_value) {
					$extra_id = intval(explode('_', $extra_value->extra_id)[1]) - 1;
					$extra_quantity = $extra_value->extra_quantity;
					if ($a == 1) :
						$extra_text .=  $meta['extra_name'][$extra_id] . '(' . cs_ds_price_format_text_with_symbol($meta['extra_price'][$extra_id], $currency) . ') x ' . $extra_quantity;
					else :
						$extra_text .= ', ' . $meta['extra_name'][$extra_id] . '(' . cs_ds_price_format_text_with_symbol($meta['extra_price'][$extra_id], $currency) . ') x ' . $extra_quantity;
					endif;
					$a++;
				}
			else :
				$extra_text = '';
			endif;
			if ($variable_text != '' || $extra_text != '') :
				$text = "\n" . " (" . $variable_text . "\n\n" . $extra_text . ")";
			else :
				$text = '';
			endif;
			$product_title = "\n" . $value['quantity'] . ' x ' . $value['title'] . "\n" . $text . "\n\n";
			$product_price = "\n\n" .  cs_ds_price_format_text_with_symbol($value['price'], $currency) . "\n\n";
			$pdf->AddFont('Oswald-Regular', '', 'Oswald-Regular.php');
			$pdf->SetFont('Oswald-Regular', '', 14);
			$pdf->SetWidths(array(150, 40));
			$pdf->Row(array(iconv('UTF-8', 'windows-1252', $product_title), $product_price));
			// $pdf->MultiCell('',10,cs_ds_price_format_text_with_symbol($value['price'],$cur),'LR',0,'R',$fill);
			// $pdf->Ln();
		}
	}

	$pdf->Cell('Lieferungskosten', 10, 'Lieferungskosten', 'LBR', 0, 'L', $fill);
	$pdf->Cell('', 10, cs_ds_price_format_text_with_symbol($shipping_fee, $currency), 'LBR', 0, 'R', $fill);
	$pdf->Ln();
	/*if($vat != ""){
    	$pdf->Cell('Mehrwertsteuer',10,'Mehrwertsteuer','LBR',0,'L',$fill);
	    $pdf->Cell('',10,cs_ds_price_format_text_with_symbol($vat),'LBR',0,'R',$fill);
	    $pdf->Ln();
    }*/
	$pdf->Cell('Zwischensumme', 10, 'Zwischensumme', 'LBR', 0, 'L', $fill);
	$pdf->Cell('', 10, cs_ds_price_format_text_with_symbol($subtotal, $currency), 'LBR', 0, 'R', $fill);
	$pdf->Ln();
	if ($reduce != "") {
		$pdf->Cell(iconv('UTF-8', 'windows-1252', 'Rabatt'), 10, iconv('UTF-8', 'windows-1252', 'Rabatt') . '(-' . $reduce_percent . ')', 'LBR', 0, 'L', $fill);
		$pdf->Cell('', 10, '- ' . cs_ds_price_format_text_with_symbol($reduce, $currency), 'LBR', 0, 'R', $fill);
		$pdf->Ln();
	}
	//cs_ds_price_format_text_with_symbol(floatval($subtotal - $total - $coupon_price)
	if ($coupon != "" && $coupon_price != "") {
		$pdf->Cell('', 10, "Rabattcode: (" . $coupon . ")", 'LBR', 0, 'L', $fill);
		$pdf->Cell('', 10, '- ' . cs_ds_price_format_text_with_symbol($coupon_price, $currency), 'LBR', 0, 'R', $fill);
		$pdf->Ln();
	}

	$pdf->Cell('Gesamtsumme', 10, 'Gesamtsumme', 'LBR', 0, 'L', $fill);
	$pdf->Cell('', 10, cs_ds_price_format_text_with_symbol($total, $currency), 'LBR', 0, 'R', $fill);
	$pdf->Ln();

	$pdf->Cell(12, 0, '', 'T');

	$pdf->Output(F, plugin_dir_path(__FILE__) . 'order-detail.pdf');

	echo json_encode(array(
		'check' => true,
		'url' => plugin_dir_url(__FILE__) . 'order-detail.pdf',
	));
	exit;
}
add_action('wp_ajax_ajax_print_order', 'ajax_print_order');
add_action('wp_ajax_nopriv_ajax_print_order', 'ajax_print_order');

//get current google maps
function get_api_google_maps()
{
	$latitude = $_REQUEST['latitude'];
	$longtitude = $_REQUEST['longtitude'];
	$dsmart_google_key = get_option('dsmart_google_key');
	if ($latitude != "" && $longtitude != "") {
		$link = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=' . $latitude . ',' . $longtitude . '&rankby=distance&key=' . $dsmart_google_key;
		$content = file_get_contents($link);
		$result = json_decode($content);
		if ($result->status == "OK") {
			$data = $result->results;
			if ($data) {
				$content = "";
				foreach ($data as $item) {
					$lat = $item->geometry->location->lat;
					$lng = $item->geometry->location->lng;
					$address = $item->vicinity;
					$content .= '<li class="item" data-lat="' . $lat . '" data-lng="' . $lng . '" data-address="' . $address . '">' . $address . '</li>';
				}
			}
			echo json_encode(array(
				'check' => true,
				'result' => $content,
			));
		} else {
			echo json_encode(array(
				'check' => false,
				'message' => __("Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut", 'dsmart'),
			));
		}
	} else {
		echo json_encode(array(
			'check' => false,
			'message' => __("Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut", 'dsmart'),
		));
	}
	exit;
}
add_action('wp_ajax_get_api_google_maps', 'get_api_google_maps');
add_action('wp_ajax_nopriv_get_api_google_maps', 'get_api_google_maps');

// Ajax action to refresh the user image
add_action('wp_ajax_myprefix_get_image', 'myprefix_get_image');
function myprefix_get_image()
{
	if (isset($_GET['id'])) {
		$image = wp_get_attachment_image(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT), 'medium', false, array('id' => 'myprefix-preview-image'));
		$data = array(
			'image'    => $image,
		);
		wp_send_json_success($data);
	} else {
		wp_send_json_error();
	}
}

// Ajax action to refresh the user image
add_action('wp_ajax_myprefix_get_image_popup_homepage', 'myprefix_get_image_popup_homepage');
function myprefix_get_image_popup_homepage()
{
	if (isset($_GET['id'])) {
		$image = wp_get_attachment_image(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT), 'medium', false, array('id' => 'myprefix-preview-image-popup-homepage'));
		$data = array(
			'image'    => $image,
		);
		wp_send_json_success($data);
	} else {
		wp_send_json_error();
	}
}

// Ajax action to refresh the user image
add_action('wp_ajax_myprefix_get_image_popup', 'myprefix_get_image_popup');
function myprefix_get_image_popup()
{
	if (isset($_GET['id'])) {
		$image = wp_get_attachment_image(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT), 'medium', false, array('id' => 'myprefix-preview-image-popup'));
		$data = array(
			'image'    => $image,
		);
		wp_send_json_success($data);
	} else {
		wp_send_json_error();
	}
}
//reset your second order number
function reset_your_order_number()
{
	date_default_timezone_set('Europe/Berlin');
	$current_date = date('Ymd');
	update_option('total_order_' . $current_date, '0', 'yes');
	exit;
}
add_action('wp_ajax_reset_your_order_number', 'reset_your_order_number');
function export_excel()
{
	require_once BOOKING_ORDER_PATH2 . "inc/PHPExcel/Classes/PHPExcel.php";

	$filename = "Export-" . date('d-m-Y');	
	$header1 = [ 'Order ID' => 'string',
	'Full Name' => 'string',
	'Phone' => 'string',
	'Email' => 'string',
	'Address' => 'string' ];

	$data = array(
		array('Order ID','Full Name','Phone','Email','Address','Total','Date'),
	);

	$wp_query  = new WP_Query(array(
		'post_type'      => 'orders',
		'post_status'   => 'publish',
		'posts_per_page' => -1,
		'order'          => 'desc',
		'orderby' => 'date',
	));

	if ($wp_query->have_posts()) :
		while ($wp_query->have_posts()) : $wp_query->the_post();
			$customer_name1 = dsmart_field('customer_name1');
			$customer_name2 = dsmart_field('customer_name2');
			$customer_email = dsmart_field('customer_email');
			$customer_phone = dsmart_field('customer_phone');
			$user_location = dsmart_field('user_location');
			$data[] = array('#' . get_the_ID(), $customer_name1 . ' ' . $customer_name2, $customer_phone, $customer_email, $user_location,dsmart_field('total'),  get_the_date());
		endwhile;
		wp_reset_query();
	endif;
	
	$writer = new XLSXWriter();
	$writer->writeSheet($data);
	$writer->writeToFile(BOOKING_ORDER_PATH2 . 'inc/exceldata/' . $filename . '.xlsx');
	
	echo json_encode(array(
		'check' => true,
		'url' => BOOKING_ORDER_PATH . 'inc/exceldata/' . $filename . '.xlsx',
	));
	exit;
}
add_action('wp_ajax_export_excel', 'export_excel');

function get_zipcode_availabe_or_not()
{
	$zipcode = $_REQUEST['zipcode'];
	$zipcode_data = get_option('zipcode_data');
	$next_link_shortcode = (get_option('next_link_shortcode') != "") ? get_option('next_link_shortcode') : home_url();
	if ($zipcode_data != "" && is_array($zipcode_data) && count($zipcode_data) > 0) {
		$check = false;
		foreach ($zipcode_data as $value) {
			if ($value['zipcode'] == $zipcode) {
				$check = true;
			}
		}
		if ($check == true) {
			//setcookie('filled_zipcode', $zipcode, time()+2592000, '/', NULL, 0);
			echo json_encode(array(
				'check' => true,
				'link' => $next_link_shortcode
			));
		} else {
			echo json_encode(array(
				'check' => false,
				'message' => __("Leider liefern wir nicht in Ihren Ortschaft")
			));
		}
	} else {
		echo json_encode(array(
			'check' => false,
			'message' => __("Leider liefern wir nicht in Ihren Ortschaft")
		));
	}
	exit;
}
add_action('wp_ajax_get_zipcode_availabe_or_not', 'get_zipcode_availabe_or_not');
add_action('wp_ajax_nopriv_get_zipcode_availabe_or_not', 'get_zipcode_availabe_or_not');

function save_zipcode_availabe_or_not()
{
	$zipcode = $_REQUEST['zipcode'];
	$zipcode_data = get_option('zipcode_data');
	$next_link_shortcode = (get_option('next_link_shortcode') != "") ? get_option('next_link_shortcode') : home_url();
	if ($zipcode_data != "" && is_array($zipcode_data) && count($zipcode_data) > 0) {
		$check = false;
		foreach ($zipcode_data as $value) {
			if ($value['zipcode'] == $zipcode) {
				$check = true;
			}
		}
		if ($check == true) {
			setcookie('filled_zipcode', $zipcode, time() + 2592000, '/', NULL, 0);
			echo json_encode(array(
				'check' => true,
				'link' => $next_link_shortcode
			));
		} else {
			setcookie('filled_zipcode', '', time() - 2592000, '/', NULL, 0);
			echo json_encode(array(
				'check' => false,
				'message' => __("Leider liefern wir nicht in Ihren Ortschaft")
			));
		}
	} else {
		setcookie('filled_zipcode', '', time() - 2592000, '/', NULL, 0);
		echo json_encode(array(
			'check' => false,
			'message' => __("Leider liefern wir nicht in Ihren Ortschaft")
		));
	}
	exit;
}
add_action('wp_ajax_save_zipcode_availabe_or_not', 'save_zipcode_availabe_or_not');
add_action('wp_ajax_nopriv_save_zipcode_availabe_or_not', 'save_zipcode_availabe_or_not');

//register
function ds_register()
{
	global $wpdb;
	$username = trim($_REQUEST['username']);
	$firstname = $_REQUEST['firstname'];
	$lastname = $_REQUEST['lastname'];
	$phone = $_REQUEST['phone'];
	$etage = $_REQUEST['etage'];
	$street = $_REQUEST['street'];
	$city = $_REQUEST['city'];
	$zipcode = $_REQUEST['zipcode'];
	$password = apply_filters('pre_user_pass', $_REQUEST['password']);
	$email    = $_REQUEST['email'];
	$_wpnonce = $_REQUEST['_wpnonce'];
	$display_name = $firstname . " " . $lastname;
	if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'register_nonce')) {
		echo json_encode(array(
			'check' => false,
			'message' => "Fehler-Token."
		));
		exit;
	}
	if ($email != "" && email_exists($email)) {
		$error = __("Die E-Mail-Adresse ist bereits vorhanden.");
		echo json_encode(array(
			'check' => false,
			'message' => $error
		));
		exit;
	}
	if ($username != "" && username_exists($username)) {
		$error = __("Benutzername ist bereits vorhanden.");
		echo json_encode(array(
			'check' => false,
			'message' => $error
		));
		exit;
	} else {
		$user_id = wp_create_user($username, $password, $email);
		$member = new WP_User($user_id);
		$member->set_role('subscriber');
		$args = array(
			'ID'         => $user_id,
			'user_email' => esc_attr($email),
			'display_name' => $display_name,
			'first_name' => $firstname,
			'last_name' => $lastname
		);
		wp_update_user($args);
		$address = array('customer_name1' => $firstname, 'customer_name2' => $lastname, 'customer_phone' => $phone, 'customer_email' => $email, 'customer_etage' => $etage, 'customer_street' => $street, 'customer_city' => $city, 'customer_zipcode' => $zipcode);
		update_user_meta($user_id, 'address', $address);
		$title1 = 'Willkommen Herr/Frau ' . $username;
		$body1 = 'Sie haben sich erfolgreich registiert. <strong>Benutzername:</strong> ' . $username . ' - <strong>Email:</strong> ' . $email . ' - <strong>Passwort:</strong> ' . $password . ' . <a href="' . home_url() . '"><strong>Hier zurück zur Homepage</strong></a>';
		' <a href="' . get_edit_user_link($user->ID) . '">' . _('Zurück zu Ihrem Profil') . '</a>';
		wp_mail($email, $title1, $body1);
	}
	echo json_encode(array(
		'check' => true,
		'message' => 'Registrierung erfolgreich.',
		'link' => get_permalink(get_page_id_by_template('templates/template-login.php')),
	));
	exit;
}
add_action('wp_ajax_nopriv_ds_register', 'ds_register');

//check login
function ds_login()
{
	global $wpdb;
	$username = trim($_REQUEST['username']);
	$password = apply_filters('pre_user_pass', $_REQUEST['password']);
	$_wpnonce = $_REQUEST['_wpnonce'];
	if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'login_nonce')) {
		echo json_encode(array(
			'check' => false,
			'message' => "Fehler-Token."
		));
		exit;
	}
	$creds = array(
		'user_login'    => $username,
		'user_password' => $password,
		'remember'      => true
	);
	$user = wp_signon($creds, false);
	if (!is_wp_error($user)) {
		wp_set_current_user($user->ID, $username);
		wp_set_auth_cookie($user->ID, true);
		echo json_encode(array(
			'check' => true,
			'message' => "Anmeldung erfolgreich.",
			'link' => home_url(),
		));
	} else {
		echo json_encode(array(
			'check' => false,
			'message' => "Benutzername oder Passwort falsch."
		));
	}
	exit;
}
add_action('wp_ajax_nopriv_ds_login', 'ds_login');

//lost password
function ds_lostpass()
{
	global $wpdb;
	$error = "";
	$username = $_REQUEST['username'];
	$_wpnonce = $_REQUEST['_wpnonce'];
	if (is_user_logged_in()) {
		$error = 'Bitte melden Sie sich ab, bevor Sie Ihr Passwort anfordern.';
		echo json_encode(array(
			'check' => false,
			'message' => $error
		));
		exit;
	}
	if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'lostpass_nonce')) {
		echo json_encode(array(
			'check' => false,
			'message' => "Fehler-Token."
		));
		exit;
	}
	if ($username != "" && (email_exists($username) || username_exists($username))) {
		if (strpos($username, '@') !== false) {
			$user = get_user_by('email', $username);
		} else {
			$user = get_user_by('login', $username);
		}
		$user_email = $user->user_email;
		$username = $user->user_login;
		$adt_rp_key = get_password_reset_key($user);
		$title = 'Passwort für Konto abfragen: ' . $username . ' bei ' . get_option('blogname');
		$body = 'Passwort für Konto abfragen: <strong>' . $username . '</strong>. Bitte klicken Sie <a href="' . get_permalink(get_page_id_by_template('templates/template-lostpass.php')) . '?key=' . $adt_rp_key . '&login=' . rawurlencode($username) . '">hier</a>, um Ihr Passwort zurückzusetzen. Wenn nicht Sie das angefordert haben, klicken Sie nicht auf diesen Link.</a>';
		wp_mail($user_email, $title, $body);
		echo json_encode(array(
			'check' => true,
			'message' => "Bitte prüfen Sie Ihre E-Mail, um Ihr Passwort zurückzusetzen."
		));
	} else {
		echo json_encode(array(
			'check' => false,
			'message' => "Das Konto mit dem Benutzernamen oder der E-Mail Adresse ist nicht vorhanden."
		));
	}
	exit;
}
add_action('wp_ajax_nopriv_ds_lostpass', 'ds_lostpass');

//reset password
function ds_resetpass()
{
	global $wpdb;
	if (is_user_logged_in()) {
		echo json_encode(array(
			'check' => false,
			'message' => 'Bitte melden Sie sich ab, bevor Sie Ihr Passwort anfordern.'
		));
		exit;
	}
	$password = $_REQUEST['password'];
	$login = $_REQUEST['login'];
	$key = $_REQUEST['key'];
	$_wpnonce = $_REQUEST['_wpnonce'];
	if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'resetpass_nonce')) {
		echo json_encode(array(
			'check' => false,
			'message' => "Fehler-Token."
		));
		exit;
	}
	$user_data = get_user_by('login', $login);
	$user = check_password_reset_key($key, $user_data->user_login);
	if (is_wp_error($user)) {
		echo json_encode(array(
			'check' => false,
			'message' => 'Error.'
		));
		exit;
	} else {
		wp_set_password($password, $user->ID);
		echo json_encode(array(
			'check' => true,
			'message' => sprintf('Passwort zurücksetzen erfolgreich.<a href="%s">Klicken Sie hier, um sich jetzt anzumelden</a>', get_permalink(get_page_id_by_template('templates/template-login.php')))
		));
		exit;
	}
	echo $error;
	exit;
}
add_action('wp_ajax_nopriv_ds_resetpass', 'ds_resetpass');

function ds_update_profile()
{
	if (!is_user_logged_in()) {
		echo json_encode(array(
			'check' => false,
			'message' => 'Bitte melden Sie sich zuerst an.'
		));
		exit;
	}
	$user_id     = get_current_user_id();
	$userdata    = get_user_by('id', $user_id);
	$firstname  = $_POST['firstname'];
	$lastname   = $_POST['lastname'];
	$_wpnonce = $_REQUEST['_wpnonce'];
	if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'profile_nonce')) {
		echo json_encode(array(
			'check' => false,
			'message' => "Fehler-Token."
		));
		exit;
	}
	if ($firstname == "" || $lastname == "") {
		echo json_encode(array(
			'check' => false,
			'message' => "Bitte geben Sie alle Informationen an."
		));
		exit;
	}
	wp_update_user(array('ID' => $user_id, 'first_name' => $firstname, 'last_name' => $lastname, 'display_name' => $firstname . ' ' . $lastname));
	echo json_encode(array(
		'check' => true,
		'message' => "Update erfolgreich.",
	));
	exit;
}
add_action('wp_ajax_ds_update_profile', 'ds_update_profile');

function ds_change_password()
{
	global $wpdb;
	$check       = false;
	$user_id     = get_current_user_id();
	$userdata    = get_user_by('id', $user_id);
	$oldpassword    = $_POST['oldpassword'];
	$password    = $_POST['password'];
	$_wpnonce = $_REQUEST['_wpnonce'];
	if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'profile_nonce')) {
		echo json_encode(array(
			'check' => false,
			'message' => "Fehler-Token."
		));
		exit;
	}

	if (!is_null($oldpassword) && !is_null($password)) {
		$result = wp_check_password($oldpassword, $userdata->user_pass, $user_id); //var_dump($result);
		if ($result == true) {
			wp_set_password($password, $user_id);
		} else {
			echo json_encode(array(
				'check' => false,
				'message' => "Altes Passwort falsch."
			));
			exit;
		}
	}
	echo json_encode(array(
		'check' => true,
		'message' => "Das Passwort wurde erfolgreich geändert."
	));
	exit;
}
add_action('wp_ajax_ds_change_password', 'ds_change_password');

function ds_update_avatar()
{
	global $wpdb;
	$check       = false;
	$user_id     = get_current_user_id();
	$userdata    = get_user_by('id', $user_id);
	$_wpnonce = $_REQUEST['profile_nonce'];
	if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'profile_nonce')) {
		echo json_encode(array(
			'check' => false,
			'message' => "Fehler-Token."
		));
		exit;
	}
	if (!isset($_FILES["avatar"])) {
		echo json_encode(array(
			'check' => false,
			'message' => "Bitte wählen Sie ein Bild zum Hochladen."
		));
		exit;
	}
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	$uploadedfile = $_FILES["avatar"];
	if ($uploadedfile['name'] == null) {
		echo json_encode(array(
			'check' => false,
			'message' => "Please choose image to upload."
		));
		exit;
	}
	$imageFileType = strtolower(pathinfo($uploadedfile['name'], PATHINFO_EXTENSION));
	if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
		echo json_encode(array(
			'check' => false,
			'message' => "Bitte laden Sie ein JPG/PNG/JPEG-Bild hoch."
		));
		exit;
	}
	if ($uploadedfile['size'] > 1024000) {
		echo json_encode(array(
			'check' => false,
			'message' => "Bild zu groß. Bitte wählen Sie ein Bild mit weniger als 1 MB."
		));
		exit;
	}
	$file = array(
		'name'     => $uploadedfile['name'],
		'type'     => $uploadedfile['type'],
		'tmp_name' => $uploadedfile['tmp_name'],
		'error'    => $uploadedfile['error'],
		'size'     => $uploadedfile['size']
	);
	$movefile = wp_handle_upload($file, array('test_form' => false));
	if ($movefile) {
		$wp_upload_dir = wp_upload_dir();
		$attachment = array(
			'guid' => $wp_upload_dir['url'] . '/' . basename($movefile['file']),
			'post_mime_type' => $movefile['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($movefile['file'])),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment($attachment, $movefile['file']);
		$attach_data = wp_generate_attachment_metadata($attach_id, $movefile['file']);
		wp_update_attachment_metadata($attach_id,  $attach_data);
		update_user_meta($user_id, 'wp_user_avatar', $attach_id);
		echo json_encode(array(
			'check' => true,
			'message' => "Upload erfolgreich."
		));
	} else {
		echo json_encode(array(
			'check' => false,
			'message' => "Kann nicht in dieses Laufwerk schreiben."
		));
	}
	exit;
}
add_action('wp_ajax_ds_update_avatar', 'ds_update_avatar');
//update address
function ds_update_address()
{
	$user = wp_get_current_user();
	$user_id = $user->ID;
	$_wpnonce = $_REQUEST['profile_nonce'];
	$address_action = $_REQUEST['address_action'];
	if (!isset($_wpnonce) || !wp_verify_nonce($_wpnonce, 'profile_nonce')) {
		echo json_encode(array(
			'check' => false,
			'message' => "Fehler-Token."
		));
		exit;
	}
	switch ($address_action) {
		case 'edit':
			$customer_name1 = $_REQUEST['customer_name1'];
			$customer_name2 = $_REQUEST['customer_name2'];
			$customer_phone = $_REQUEST['customer_phone'];
			$customer_email = $_REQUEST['customer_email'];
			$customer_etage = $_REQUEST['customer_etage'];
			$customer_street = $_REQUEST['customer_street'];
			$customer_city = $_REQUEST['customer_city'];
			$customer_zipcode = $_REQUEST['customer_zipcode'];
			if ($customer_name1 == "" || $customer_name2 == "" || $customer_phone == "" || $customer_email == "" || $customer_street == "" || $customer_city == "" || $customer_zipcode == "") {
				echo json_encode(array(
					'check' => false,
					'message' => "Bitte geben Sie alle Informationen an."
				));
				exit;
			}
			$address = (is_array(get_user_meta($user_id, 'address', true))) ? get_user_meta($user_id, 'address', true) : array();
			$address['customer_name1'] = $customer_name1;
			$address['customer_name2'] = $customer_name2;
			$address['customer_phone'] = $customer_phone;
			$address['customer_email'] = $customer_email;
			$address['customer_etage'] = $customer_etage;
			$address['customer_street'] = $customer_street;
			$address['customer_city'] = $customer_city;
			$address['customer_zipcode'] = $customer_zipcode;
			update_user_meta($user_id, 'address', $address);
			echo json_encode(array(
				'check' => true,
				'message' => "Adresse bearbeiten erfolgreich.",
			));
			exit;
			break;
		default:
			echo json_encode(array(
				'check' => false,
				'message' => "Fehleraktion",

			));
			exit;
			break;
	}
}
add_action('wp_ajax_ds_update_address', 'ds_update_address');

//delete orders
function d_delete_orders()
{
	global $wpdb;
	$type = $_REQUEST['type'];
	if ($type == "all") {
		// $sql = "DELETE a,b,c
		//     FROM wp_posts a
		//     LEFT JOIN wp_term_relationships b
		//         ON (a.ID = b.object_id)
		//     LEFT JOIN wp_postmeta c
		//         ON (a.ID = c.post_id)
		//     WHERE a.post_type = 'orders'";
		// // $results = $wpdb->ex($sql);
		// $wpdb->query($wpdb->prepare($sql));
		$allposts= get_posts( array('post_type'=>'orders','numberposts'=>-1) );
		foreach ($allposts as $eachpost) {
			wp_delete_post( $eachpost->ID, true );
		}
		echo json_encode(array(
			'check' => true,
			'message' => "Done!",
		));
	}
	exit;
}
add_action('wp_ajax_d_delete_orders', 'd_delete_orders');
