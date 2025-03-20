<?php
//create admin menu
add_action('admin_menu', 'book_menu');
function book_menu()
{
	// add_menu_page('Order Food', 'Order Food', 'edit_posts', 'general-booking-setting', 'general_booking_setting',null,10 );
	// add_submenu_page('general-booking-setting', 'Product Category', 'Product Category', 'manage_options', 'edit-tags.php?taxonomy=product-cat');
	add_submenu_page('edit.php?post_type=product', 'Einstellungen', 'Einstellungen', 'edit_products', 'general-booking-setting', 'general_booking_setting');
	// add_submenu_page('edit.php?post_type=product', 'Bewertungen', 'Bewertungen', 'edit_products', 'list-review', 'list_review');
	add_submenu_page('edit.php?post_type=product', 'Shop Standort', 'Shop Standort', 'manage_options', 'list-shop', 'list_shop');
	/*add_submenu_page( 'general-booking-setting', 'Thêm địa chỉ Shop', 'Thêm địa chỉ Shop','manage_options', 'add-address-shop','add_address_shop');*/
	add_submenu_page('edit.php?post_type=product', 'Bestellung Statistik', 'Bestellung Statistik', 'edit_products', 'dsmart-statistics-order', 'dsmart_statistics_order');
	add_submenu_page('edit.php?post_type=product', 'Umsatz Statistik', 'Umsatz Statistik', 'edit_products', 'dsmart-statistics-all', 'dsmart_statistics_all');
}
function general_booking_setting()
{
	date_default_timezone_set('Europe/Berlin');
	$current_user = wp_get_current_user();
	if (isset($_POST['dsmart-submit'])) {
			$dsmart_theme_style = $_POST['dsmart_theme_style'];
			update_option('dsmart_theme_style', $dsmart_theme_style, 'yes');

			if (isset($_POST['dsmart_thumbnail'])) {
				$dsmart_thumbnail = $_POST['dsmart_thumbnail'];
			} else {
				$dsmart_thumbnail = '';
			}
			update_option('dsmart_thumbnail', $dsmart_thumbnail, 'yes');

			if (isset($_POST['dsmart_buynow'])) {
				$dsmart_buynow = $_POST['dsmart_buynow'];
			} else {
				$dsmart_buynow = '';
			}
			update_option('dsmart_buynow', $dsmart_buynow, 'yes');

			if (isset($_POST['float_cart'])) {
				$float_cart = $_POST['float_cart'];
			} else {
				$float_cart = '';
			}
			update_option('float_cart', $float_cart, 'yes');

			$ds_logo = $_POST['myprefix_image_id'];
			update_option('ds_logo', $ds_logo, 'yes');

			$ds_popup = $_POST['myprefix_image_id_popup'];
			update_option('ds_popup', $ds_popup, 'yes');

			$ds_popup_homepage = $_POST['myprefix_image_id_popup_homepage'];
			update_option('ds_popup_homepage', $ds_popup_homepage, 'yes');

			$dsmart_currency = $_POST['dsmart_currency'];
			update_option('dsmart_currency', $dsmart_currency, 'yes');

			$dsmart_currency_rate = $_POST['dsmart_currency_rate'];
			update_option('dsmart_currency_rate', $dsmart_currency_rate, 'yes');

			$show_second_number = $_POST['show_second_number'];
			update_option('show_second_number', $show_second_number, 'yes');
			
			/*$tax_shipping = $_POST['tax_shipping'];
			update_option('tax_shipping',$tax_shipping,'yes');*/

			$dsmart_google_key = stripslashes_deep($_POST['dsmart_google_key']);
			update_option('dsmart_google_key', $dsmart_google_key, 'yes');

			$place_id_map = stripslashes_deep($_POST['place_id_map']);
			update_option('place_id_map', $place_id_map, 'yes');

			$logo_link = stripslashes_deep($_POST['logo_link']);
			if($logo_link === ""){
				$logo_link = "/";
			}
			update_option('logo_link', $logo_link, 'yes');

			$button_color = $_POST['button_color'];
			update_option('button_color', $button_color, 'yes');

			$sidebar_color = $_POST['sidebar_color'];
			update_option('sidebar_color', $sidebar_color, 'yes');

			$price_color = $_POST['price_color'];
			update_option('price_color', $price_color, 'yes');

			// For API Mobile
			$old_mobile_code = stripslashes_deep(get_option("mobile_code"));
			$mobile_code = $_POST['mobile_code'];

			if (strcmp($old_mobile_code, $mobile_code) != 0) {
				update_option('mobile_code', $mobile_code, 'yes');
				//Generate a random string.
				$token = openssl_random_pseudo_bytes(16);
				//Convert the binary data into hexadecimal representation.
				$token = bin2hex($token);
				update_option('access_token_mobile', $token, 'yes');
			}

			if (isset($_POST['get_distance'])) {
				$get_distance = $_POST['get_distance'];
			} else {
				$get_distance = '';
			}
			update_option('get_distance', $get_distance, 'yes');

			$dsmart_orderby = $_POST['dsmart_orderby'];
			$dsmart_order = $_POST['dsmart_order'];
			update_option('dsmart_order', $dsmart_order, 'yes');
			update_option('dsmart_orderby', $dsmart_orderby, 'yes');

			$time_to_show_alert = $_POST['time_to_show_alert'];
			update_option('time_to_show_alert', $time_to_show_alert, 'yes');

			$back_link_in_cart = $_POST['back_link_in_cart'];
			update_option('back_link_in_cart', $back_link_in_cart, 'yes');

			$redirect_link_shop = $_POST['redirect_link_shop'];
			update_option('redirect_link_shop', $redirect_link_shop, 'yes');
			// payment method
			//=======================================================
			if (isset($_POST['dsmart_paypal'])) {
				$dsmart_paypal = $_POST['dsmart_paypal'];
			} else {
				$dsmart_paypal = '';
			}
			//========================
			if (isset($_POST['dsmart_klarna'])) {
				$dsmart_klarna = $_POST['dsmart_klarna'];
			} else {
				$dsmart_klarna = '';
			}
			$dsmart_barzahlung = $_POST['dsmart_barzahlung'];
			$dsmart_custom_method = $_POST['dsmart_custom_method'];
			//========================
			if (isset($_POST['dsmart_sandbox'])) {
				$dsmart_sandbox = $_POST['dsmart_sandbox'];
			} else {
				$dsmart_sandbox = '';
			}
			$dsmart_paypal_email_address = $_POST['dsmart_paypal_email_address'];
			/*$dsmart_paypal_client_secret = $_POST['dsmart_paypal_client_secret'];
			$dsmart_paypal_client_id = $_POST['dsmart_paypal_client_id'];*/
			$klarna_username = $_POST['klarna_username'];
			$klarna_password = $_POST['klarna_password'];

			update_option('dsmart_paypal', $dsmart_paypal, 'yes');
			update_option('dsmart_klarna', $dsmart_klarna, 'yes');
			update_option('dsmart_barzahlung', $dsmart_barzahlung, 'yes');
			update_option('dsmart_custom_method', $dsmart_custom_method, 'yes');
			update_option('dsmart_sandbox', $dsmart_sandbox, 'yes');
			update_option('dsmart_paypal_email_address', $dsmart_paypal_email_address, 'yes');
			/*update_option('dsmart_paypal_client_id',$dsmart_paypal_client_id,'yes');
			update_option('dsmart_paypal_client_secret',$dsmart_paypal_client_secret,'yes');*/
			update_option('klarna_username', $klarna_username, 'yes');
			update_option('klarna_password', $klarna_password, 'yes');
			$ds_auto_delete_order = $_POST['ds_auto_delete_order'];
			$order_date = $_POST['order_date'];
			update_option('ds_auto_delete_order', $ds_auto_delete_order);
			update_option('order_date', $order_date);
		$enable_pool = $_POST['enable_pool'];
		update_option('enable_pool', $enable_pool, 'yes');

		$homepage_popup = $_POST['homepage_popup'];
		update_option('homepage_popup', $homepage_popup, 'yes');
		
		$dsmart_taxonomy_text = stripslashes_deep($_POST['dsmart_taxonomy_text']);
		$dsmart_min_order = stripslashes_deep($_POST['dsmart_min_order']);
		$dsmart_distance = stripslashes_deep($_POST['dsmart_distance']);

		$ds_mail_name = $_POST['ds_mail_name'];
		$ds_sender_email = $_POST['ds_sender_email'];

		// For API Mobile
		$mobile_code = stripslashes_deep(get_option("mobile_code"));


		// product stock
		//=======================================================
		if (isset($_POST['dsmart_stock'])) {
			$dsmart_stock = $_POST['dsmart_stock'];
		} else {
			$dsmart_stock = '';
		}

		// close shop
		//=======================================================
		if (isset($_POST['close_shop'])) :
			$close_shop = $_POST['close_shop'];
		else :
			$close_shop = null;
		endif;

		$delay_time = $_POST['delay_time'];
		$delay_delivery = $_POST['delay_delivery'];
		$dsmart_min_order = $_POST['dsmart_min_order'];
		$dsmart_min_order_free = $_POST['dsmart_min_order_free'];
		$dsmart_min_order_free_checkbox = $_POST['dsmart_min_order_free_checkbox'];
		$dsmart_shipping_fee = $_POST['dsmart_shipping_fee'];
		$dsmart_shipping_from = $_POST['dsmart_shipping_from'];
		$dsmart_shipping_to = $_POST['dsmart_shipping_to'];
		$dsmart_shipping_cs_fee = $_POST['dsmart_shipping_cs_fee'];
		$dsmart_min_cs_fee = $_POST['dsmart_min_cs_fee'];
		if (isset($_POST['show_notify'])) {
			$show_notify = $_POST['show_notify'];
		} else {
			$show_notify = "";
		}
		if (isset($_POST['show_profile'])) {
			$show_profile = $_POST['show_profile'];
		} else {
			$show_profile = "";
		}
		$notify_text = stripslashes_deep($_POST['notify_text']);
		$close_shop_text = stripslashes_deep($_POST['close_shop_text']);
		//$dsmart_tax = $_POST['dsmart_tax'];

		// shipping method
		//=======================================================
		if (isset($_POST['dsmart_method_ship'])) {
			$dsmart_method_ship = $_POST['dsmart_method_ship'];
		} else {
			$dsmart_method_ship = '';
		}
		if (isset($_POST['dsmart_method_direct'])) {
			$dsmart_method_direct = $_POST['dsmart_method_direct'];
		} else {
			$dsmart_method_direct = '';
		}
		$dsmart_thankyou_text = stripslashes_deep($_POST['dsmart_thankyou_text']);
		$dsmart_term_text = stripslashes_deep($_POST['dsmart_term_text']);
		$dsmart_cart_text = stripslashes_deep($_POST['dsmart_cart_text']);
		$dsmart_cart_color = stripslashes_deep($_POST['dsmart_cart_color']);
		$dsmart_cart_background = stripslashes_deep($_POST['dsmart_cart_background']);
		$next_link_shortcode = stripslashes_deep($_POST['next_link_shortcode']);
		$zipcode_status = stripslashes_deep($_POST['zipcode_status']);

		// For API Mobile
		$mobile_code = stripslashes_deep(get_option("mobile_code"));

		//shop
		//=======================================================
		$dsmart_header_mail_order = $_POST['dsmart_header_mail_order'];
		//=======================================================
		$dsmart_text_mail_order = $_POST['dsmart_text_mail_order'];
		//$dsmart_text_mail_success = $_POST['dsmart_text_mail_success'];
		//$dsmart_text_mail_cancel = $_POST['dsmart_text_mail_cancel'];

		//customer
		//=======================================================
		$dsmart_header_mail_order_cs = $_POST['dsmart_header_mail_order_cs'];
		$dsmart_header_mail_success_cs = $_POST['dsmart_header_mail_success_cs'];
		$dsmart_header_mail_cancel_cs = $_POST['dsmart_header_mail_cancel_cs'];
		//=======================================================
		$dsmart_text_mail_order_cs = $_POST['dsmart_text_mail_order_cs'];
		$dsmart_text_mail_success_cs = $_POST['dsmart_text_mail_success_cs'];
		$dsmart_text_mail_cancel_cs = $_POST['dsmart_text_mail_cancel_cs'];

		//promotion discount
		//=======================================================
		$type_promotion = $_POST['type_promotion'];
		$promotion = $_POST['promotion'];

		$type_promotion_2 = $_POST['type_promotion_2'];
		$promotion_2 = $_POST['promotion_2'];

		$discount_min = $_POST['discount_min'];

		//discount type
		//=======================================================
		if (isset($_POST['discount_cod'])) {
			$discount_cod = $_POST['discount_cod'];
		} else {
			$discount_cod = '';
		}

		if (isset($_POST['discount_shop'])) {
			$discount_shop = $_POST['discount_shop'];
		} else {
			$discount_shop = '';
		}

		

		//custom opening time
		//=======================================================
		$custom_date = $_POST['custom_date'];
		$custom_open_time = $_POST['custom_open_time'];
		$custom_close_time = $_POST['custom_close_time'];
		if ($custom_date != "" && count($custom_date) > 0) {
			$count = 0;
			$array = array();
			foreach ($custom_date as $item) {
				if ($item != "" && $custom_open_time[$count] != "" && $custom_close_time[$count] != "") {
					$array[] = array("date" => $item, "open" => $custom_open_time[$count], "close" => $custom_close_time[$count]);
				}
				$count++;
			}
			if (count($array) > 0) {
				update_option('dsmart_custom_date', $array, 'yes');
			} else {
				update_option('dsmart_custom_date', "", 'yes');
			}
		} else {
			update_option('dsmart_custom_date', "", 'yes');
		}

		// new custom opening time
		$new_custome_date_type = $_POST['new_custome_date_type'];
		$new_custome_date_start_date = $_POST['new_custome_date_start_date'];
		$new_custome_date_end_date = $_POST['new_custome_date_end_date'];
		$new_custome_date_time_type = $_POST['new_custome_date_time_type'];
		$new_custome_date_start_time = $_POST['new_custome_date_start_time'];
		$new_custome_date_end_time = $_POST['new_custome_date_end_time'];
		$new_custome_date_status = $_POST['new_custome_date_status'];
		if ($new_custome_date_type != "" && count($new_custome_date_type) > 0) {
			$count = 0;
			$array = array();
			foreach ($new_custome_date_type as $item) {
				if ($item != "" && $new_custome_date_start_date[$count] != "" && $new_custome_date_time_type[$count] != "" && $new_custome_date_status[$count] != "") {
					$array[] = array(
						"date_type" => $item, 
						"start_date" => $new_custome_date_start_date[$count], 
						"end_date" => $new_custome_date_end_date[$count],
						"time_type" => $new_custome_date_time_type[$count],
						"start_time" => $new_custome_date_start_time[$count],
						"end_time" => $new_custome_date_end_time[$count],
						"status" => $new_custome_date_status[$count],
					);
				}
				$count++;
			}

			if (count($array) > 0) {
				update_option('dsmart_new_custom_date', $array, 'yes');

				// convert to old dsmart_custom_date
				$array2 = array();
				foreach ($array as $item) {
					if($item["date_type"] === "single")
					{
						if($item["status"] === "close")
						{
							$array2[] = array("date" => $item["start_date"], "open" => "00:00", "close" => "00:00");
						}
						else
						{
							if($item["time_type"] === "time_to_time")
							{
								$array2[] = array("date" => $item["start_date"], "open" => $item["start_time"], "close" => $item["end_time"]);
							}
							else
							{
								$array2[] = array("date" => $item["start_date"], "open" => "00:00", "close" => "23:59");
							}
						}
					}
					else
					{
						$begin = new DateTime($item["start_date"]);
						$end = new DateTime($item["end_date"]);
						$end->modify('+1 day');
						$interval = DateInterval::createFromDateString('1 day');
						$period = new DatePeriod($begin, $interval, $end);

						foreach ($period as $dt) {
							$date = $dt->format("d-m-Y");
							if($item["status"] === "close")
							{
								$array2[] = array("date" => $date, "open" => "00:00", "close" => "00:00");
							}
							else
							{
								if($item["time_type"] === "time_to_time")
								{
									$array2[] = array("date" => $date, "open" => $item["start_time"], "close" => $item["end_time"]);
								}
								else
								{
									$array2[] = array("date" => $date, "open" => "00:00", "close" => "23:59");
								}
							}
						}
					}
				}
				update_option('dsmart_custom_date', $array2, 'yes');
			} else {
				update_option('dsmart_new_custom_date', "", 'yes');
				update_option('dsmart_custom_date', "", 'yes');
			}
		} else {
			update_option('dsmart_new_custom_date', "", 'yes');
			update_option('dsmart_custom_date', "", 'yes');
		}

		//Closed time
		//=======================================================
		$closed_time_date = $_POST['closed_time_date'];
		$closed_time_from = $_POST['closed_time_from'];
		$closed_time_to = $_POST['closed_time_to'];
		if ($closed_time_date != "" && count($closed_time_date) > 0) {
			$count = 0;
			$array = array();
			foreach ($closed_time_date as $item) {
				if ($item != "" && $closed_time_from[$count] != "" && $closed_time_to[$count] != "") {
					$array[] = array("date" => $item, "from" => $closed_time_from[$count], "to" => $closed_time_to[$count]);
				}
				$count++;
			}
			if (count($array) > 0) {
				update_option('closed_time', $array, 'yes');
			} else {
				update_option('closed_time', "", 'yes');
			}
		} else {
			update_option('closed_time', "", 'yes');
		}
		$closed_time_date_2 = $_POST['closed_time_date_2'];
		$closed_time_from_2 = $_POST['closed_time_from_2'];
		$closed_time_to_2 = $_POST['closed_time_to_2'];
		if ($closed_time_date_2 != "" && count($closed_time_date_2) > 0) {
			$count = 0;
			$array = array();
			foreach ($closed_time_date_2 as $item) {
				if ($item != "" && $closed_time_from_2[$count] != "" && $closed_time_to_2[$count] != "") {
					$array[] = array("date" => $item, "from" => $closed_time_from_2[$count], "to" => $closed_time_to_2[$count]);
				}
				$count++;
			}
			if (count($array) > 0) {
				update_option('closed_time_2', $array, 'yes');
			} else {
				update_option('closed_time_2', "", 'yes');
			}
		} else {
			update_option('closed_time_2', "", 'yes');
		}

		update_option('ds_mail_name', $ds_mail_name, 'yes');
		update_option('ds_sender_email', $ds_sender_email, 'yes');
		update_option('dsmart_taxonomy_text', $dsmart_taxonomy_text, 'yes');


		//stock setting
		//=======================================================
		update_option('dsmart_stock', $dsmart_stock, 'yes');

		// shipping method
		//=======================================================
		update_option('dsmart_method_direct', $dsmart_method_direct, 'yes');
		update_option('dsmart_method_ship', $dsmart_method_ship, 'yes');


		// get distance between shop and customer
		//=======================================================
		update_option('dsmart_distance', $dsmart_distance, 'yes');

		// close shop
		//=======================================================
		update_option('dsmart_close_shop', $close_shop, 'yes');

		//delay time
		//=======================================================
		update_option('delay_time', $delay_time, 'yes');
		update_option('delay_delivery', $delay_delivery, 'yes');

		//update_option( 'dsmart_tax', $dsmart_tax, 'yes' );

		//=======================================================
		update_option('dsmart_thankyou_text', $dsmart_thankyou_text, 'yes');

		//mon
		//=======================================================
		$time_open_shop_mo = $_POST['time_open_shop_mo'];
		$time_close_shop_mo = $_POST['time_close_shop_mo'];
		update_option('time_open_shop_mo', $time_open_shop_mo, 'yes');
		update_option('time_close_shop_mo', $time_close_shop_mo, 'yes');

		$time_open_shop_2_mo = $_POST['time_open_shop_2_mo'];
		$time_close_shop_2_mo = $_POST['time_close_shop_2_mo'];
		update_option('time_open_shop_2_mo', $time_open_shop_2_mo, 'yes');
		update_option('time_close_shop_2_mo', $time_close_shop_2_mo, 'yes');

		//tue
		//=======================================================
		$time_open_shop_tu = $_POST['time_open_shop_tu'];
		$time_close_shop_tu = $_POST['time_close_shop_tu'];
		update_option('time_open_shop_tu', $time_open_shop_tu, 'yes');
		update_option('time_close_shop_tu', $time_close_shop_tu, 'yes');

		$time_open_shop_2_tu = $_POST['time_open_shop_2_tu'];
		$time_close_shop_2_tu = $_POST['time_close_shop_2_tu'];
		update_option('time_open_shop_2_tu', $time_open_shop_2_tu, 'yes');
		update_option('time_close_shop_2_tu', $time_close_shop_2_tu, 'yes');

		//wed
		//=======================================================
		$time_open_shop_we = $_POST['time_open_shop_we'];
		$time_close_shop_we = $_POST['time_close_shop_we'];
		update_option('time_open_shop_we', $time_open_shop_we, 'yes');
		update_option('time_close_shop_we', $time_close_shop_we, 'yes');

		$time_open_shop_2_we = $_POST['time_open_shop_2_we'];
		$time_close_shop_2_we = $_POST['time_close_shop_2_we'];
		update_option('time_open_shop_2_we', $time_open_shop_2_we, 'yes');
		update_option('time_close_shop_2_we', $time_close_shop_2_we, 'yes');

		//thu
		//=======================================================
		$time_open_shop_th = $_POST['time_open_shop_th'];
		$time_close_shop_th = $_POST['time_close_shop_th'];
		update_option('time_open_shop_th', $time_open_shop_th, 'yes');
		update_option('time_close_shop_th', $time_close_shop_th, 'yes');

		$time_open_shop_2_th = $_POST['time_open_shop_2_th'];
		$time_close_shop_2_th = $_POST['time_close_shop_2_th'];
		update_option('time_open_shop_2_th', $time_open_shop_2_th, 'yes');
		update_option('time_close_shop_2_th', $time_close_shop_2_th, 'yes');

		//fri
		//=======================================================
		$time_open_shop_fr = $_POST['time_open_shop_fr'];
		$time_close_shop_fr = $_POST['time_close_shop_fr'];
		update_option('time_open_shop_fr', $time_open_shop_fr, 'yes');
		update_option('time_close_shop_fr', $time_close_shop_fr, 'yes');

		$time_open_shop_2_fr = $_POST['time_open_shop_2_fr'];
		$time_close_shop_2_fr = $_POST['time_close_shop_2_fr'];
		update_option('time_open_shop_2_fr', $time_open_shop_2_fr, 'yes');
		update_option('time_close_shop_2_fr', $time_close_shop_2_fr, 'yes');

		//sat
		//=======================================================
		$time_open_shop_sa = $_POST['time_open_shop_sa'];
		$time_close_shop_sa = $_POST['time_close_shop_sa'];
		update_option('time_open_shop_sa', $time_open_shop_sa, 'yes');
		update_option('time_close_shop_sa', $time_close_shop_sa, 'yes');

		$time_open_shop_2_sa = $_POST['time_open_shop_2_sa'];
		$time_close_shop_2_sa = $_POST['time_close_shop_2_sa'];
		update_option('time_open_shop_2_sa', $time_open_shop_2_sa, 'yes');
		update_option('time_close_shop_2_sa', $time_close_shop_2_sa, 'yes');

		//sun
		//=======================================================
		$time_open_shop_su = $_POST['time_open_shop_su'];
		$time_close_shop_su = $_POST['time_close_shop_su'];
		update_option('time_open_shop_su', $time_open_shop_su, 'yes');
		update_option('time_close_shop_su', $time_close_shop_su, 'yes');

		$time_open_shop_2_su = $_POST['time_open_shop_2_su'];
		$time_close_shop_2_su = $_POST['time_close_shop_2_su'];
		update_option('time_open_shop_2_su', $time_open_shop_2_su, 'yes');
		update_option('time_close_shop_2_su', $time_close_shop_2_su, 'yes');

		//=======================================================
		update_option('dsmart_min_order', $dsmart_min_order, 'yes');
		update_option('dsmart_min_order_free_checkbox', $dsmart_min_order_free_checkbox, 'yes');

		if($dsmart_min_order_free_checkbox === "0"){
			update_option('dsmart_min_order_free', "", 'yes');
		}else{
			update_option('dsmart_min_order_free', $dsmart_min_order_free, 'yes');
		}
		
		
		update_option('dsmart_shipping_fee', $dsmart_shipping_fee, 'yes');
		update_option('dsmart_shipping_to', $dsmart_shipping_to, 'yes');
		update_option('dsmart_shipping_from', $dsmart_shipping_from, 'yes');
		update_option('dsmart_shipping_cs_fee', $dsmart_shipping_cs_fee, 'yes');
		update_option('dsmart_min_cs_fee', $dsmart_min_cs_fee, 'yes');

		//text mail when customer order
		//=======================================================
		update_option('dsmart_header_mail_order', $dsmart_header_mail_order, 'yes');
		update_option('dsmart_header_mail_order_cs', $dsmart_header_mail_order_cs, 'yes');
		update_option('dsmart_text_mail_order', $dsmart_text_mail_order, 'yes');
		update_option('dsmart_text_mail_order_cs', $dsmart_text_mail_order_cs, 'yes');

		//text mail when change order
		//=======================================================
		update_option('dsmart_header_mail_success_cs', $dsmart_header_mail_success_cs, 'yes');
		update_option('dsmart_header_mail_cancel_cs', $dsmart_header_mail_cancel_cs, 'yes');
		//=======================================================
		//update_option('dsmart_text_mail_success',$dsmart_text_mail_success,'yes');
		//update_option('dsmart_text_mail_cancel',$dsmart_text_mail_cancel,'yes');
		update_option('dsmart_text_mail_success_cs', $dsmart_text_mail_success_cs, 'yes');
		update_option('dsmart_text_mail_cancel_cs', $dsmart_text_mail_cancel_cs, 'yes');
		//=======================================================
		update_option('dsmart_term_text', $dsmart_term_text, 'yes');
		update_option('dsmart_cart_text', $dsmart_cart_text, 'yes');
		update_option('dsmart_cart_color', $dsmart_cart_color, 'yes');
		update_option('dsmart_cart_background', $dsmart_cart_background, 'yes');
		update_option('next_link_shortcode', $next_link_shortcode, 'yes');
		update_option('zipcode_status', $zipcode_status, 'yes');

		//promotion discount
		//=======================================================
		update_option('type_promotion', $type_promotion, 'yes');
		update_option('promotion', $promotion, 'yes');

		update_option('type_promotion_2', $type_promotion_2, 'yes');
		update_option('promotion_2', $promotion_2, 'yes');

		update_option('discount_min', $discount_min, 'yes');

		//discount type
		//=======================================================
		update_option('discount_cod', $discount_cod, 'yes');
		update_option('discount_shop', $discount_shop, 'yes');

		//notify
		//=======================================================
		update_option('show_notify', $show_notify, 'yes');
		update_option('show_profile', $show_profile, 'yes');
		update_option('notify_text', $notify_text, 'yes');
		update_option('close_shop_text', $close_shop_text, 'yes');
		//time discount
		//mon
		//=======================================================
		$time_discount_shop_mo = $_POST['time_discount_shop_mo'];
		update_option('time_discount_shop_mo', $time_discount_shop_mo, 'yes');

		//tue
		//=======================================================
		$time_discount_shop_tu = $_POST['time_discount_shop_tu'];
		update_option('time_discount_shop_tu', $time_discount_shop_tu, 'yes');

		//wed
		//=======================================================
		$time_discount_shop_we = $_POST['time_discount_shop_we'];
		update_option('time_discount_shop_we', $time_discount_shop_we, 'yes');

		//thu
		//=======================================================
		$time_discount_shop_th = $_POST['time_discount_shop_th'];
		update_option('time_discount_shop_th', $time_discount_shop_th, 'yes');
		//fri
		//=======================================================
		$time_discount_shop_fr = $_POST['time_discount_shop_fr'];
		update_option('time_discount_shop_fr', $time_discount_shop_fr, 'yes');
		//sat
		//=======================================================
		$time_discount_shop_sa = $_POST['time_discount_shop_sa'];
		update_option('time_discount_shop_sa', $time_discount_shop_sa, 'yes');
		//sun
		//=======================================================
		$time_discount_shop_su = $_POST['time_discount_shop_su'];
		update_option('time_discount_shop_su', $time_discount_shop_su, 'yes');

		//time discount 2
		//mon
		//=======================================================
		$time_discount_shop_2_mo = $_POST['time_discount_shop_2_mo'];
		update_option('time_discount_shop_2_mo', $time_discount_shop_2_mo, 'yes');

		//tue
		//=======================================================
		$time_discount_shop_2_tu = $_POST['time_discount_shop_2_tu'];
		update_option('time_discount_shop_2_tu', $time_discount_shop_2_tu, 'yes');

		//wed
		//=======================================================
		$time_discount_shop_2_we = $_POST['time_discount_shop_2_we'];
		update_option('time_discount_shop_2_we', $time_discount_shop_2_we, 'yes');

		//thu
		//=======================================================
		$time_discount_shop_2_th = $_POST['time_discount_shop_2_th'];
		update_option('time_discount_shop_2_th', $time_discount_shop_2_th, 'yes');
		//fri
		//=======================================================
		$time_discount_shop_2_fr = $_POST['time_discount_shop_2_fr'];
		update_option('time_discount_shop_2_fr', $time_discount_shop_2_fr, 'yes');
		//sat
		//=======================================================
		$time_discount_shop_2_sa = $_POST['time_discount_shop_2_sa'];
		update_option('time_discount_shop_2_sa', $time_discount_shop_2_sa, 'yes');
		//sun
		//=======================================================
		$time_discount_shop_2_su = $_POST['time_discount_shop_2_su'];
		update_option('time_discount_shop_2_su', $time_discount_shop_2_su, 'yes');

		//custom discount time
		//=======================================================
		$custom_discount_date = $_POST['custom_discount_date'];
		$custom_discount_time = $_POST['custom_discount_time'];
		if ($custom_discount_date != "" && count($custom_discount_date) > 0) {
			$count = 0;
			$array = array();
			foreach ($custom_discount_date as $item) {
				if ($item != "" && $custom_discount_time[$count] != "") {
					$array[] = array("date" => $item, "time" => $custom_discount_time[$count]);
				}
				$count++;
			}
			if (count($array) > 0) {
				update_option('dsmart_custom_discount_date', $array, 'yes');
			} else {
				update_option('dsmart_custom_discount_date', "", 'yes');
			}
		} else {
			update_option('dsmart_custom_discount_date', "", 'yes');
		}
		//zipcode
		if (isset($_POST['zipcode']) && is_array($_POST['zipcode']) && isset($_POST['zipcode_price']) && is_array($_POST['zipcode_price'])) {
			$zipcode = $_POST['zipcode'];
			$zipcode_price = $_POST['zipcode_price'];
			$minium_order = $_POST['minium_order'];
			$zipcode_data = array();
			foreach ($zipcode as $key => $child) {
				$zipcode_data[] = array('zipcode' => $child, 'minium_order' => $minium_order[$key], 'price' => $zipcode_price[$key]);
			}
			update_option('zipcode_data', $zipcode_data);
		} else {
			update_option('zipcode_data', "");
		}
	}
	$ds_auto_delete_order = get_option('ds_auto_delete_order');
	$order_date = get_option('order_date');
	$dsmart_theme_style = get_option('dsmart_theme_style');
	$dsmart_taxonomy_text = get_option('dsmart_taxonomy_text');
	$dsmart_thumbnail = get_option('dsmart_thumbnail');
	$dsmart_buynow = get_option('dsmart_buynow');
	$dsmart_stock = get_option('dsmart_stock');

	// shipping method
	//=======================================================
	$dsmart_method_ship = get_option('dsmart_method_ship');
	$dsmart_method_direct = get_option('dsmart_method_direct');

	// payment method
	//=======================================================
	$dsmart_paypal = get_option('dsmart_paypal');
	$dsmart_klarna = get_option('dsmart_klarna');
	$dsmart_barzahlung = get_option('dsmart_barzahlung');
	$dsmart_custom_method = get_option('dsmart_custom_method');
	$dsmart_sandbox = get_option('dsmart_sandbox');
	$dsmart_paypal_email_address = get_option('dsmart_paypal_email_address');
	$dsmart_paypal_client_id = get_option('dsmart_paypal_client_id');
	$dsmart_paypal_client_secret = get_option('dsmart_paypal_client_secret');
	$klarna_username = get_option('klarna_username');
	$klarna_password = get_option('klarna_password');

	//float cart
	//=======================================================
	$float_cart = get_option('float_cart');

	// close shop
	//=======================================================
	$close_shop = get_option('dsmart_close_shop');

	// delay time
	//=======================================================
	$delay_time = get_option('delay_time');
	$delay_delivery = get_option('delay_delivery');
	//$tax_shipping = get_option('tax_shipping');

	//=======================================================
	$dsmart_custom_date = get_option('dsmart_custom_date');
	$dsmart_new_custom_date = get_option('dsmart_new_custom_date');

	//mon
	//=======================================================
	$time_open_shop_mo = get_option('time_open_shop_mo');
	$time_close_shop_mo = get_option('time_close_shop_mo');

	$time_open_shop_2_mo = get_option('time_open_shop_2_mo');
	$time_close_shop_2_mo = get_option('time_close_shop_2_mo');

	//tue
	//=======================================================
	$time_open_shop_tu = get_option('time_open_shop_tu');
	$time_close_shop_tu = get_option('time_close_shop_tu');

	$time_open_shop_2_tu = get_option('time_open_shop_2_tu');
	$time_close_shop_2_tu = get_option('time_close_shop_2_tu');
	//wed
	//=======================================================
	$time_open_shop_we = get_option('time_open_shop_we');
	$time_close_shop_we = get_option('time_close_shop_we');

	$time_open_shop_2_we = get_option('time_open_shop_2_we');
	$time_close_shop_2_we = get_option('time_close_shop_2_we');

	//thu
	//=======================================================
	$time_open_shop_th = get_option('time_open_shop_th');
	$time_close_shop_th = get_option('time_close_shop_th');

	$time_open_shop_2_th = get_option('time_open_shop_2_th');
	$time_close_shop_2_th = get_option('time_close_shop_2_th');
	//fri
	//=======================================================
	$time_open_shop_fr = get_option('time_open_shop_fr');
	$time_close_shop_fr = get_option('time_close_shop_fr');

	$time_open_shop_2_fr = get_option('time_open_shop_2_fr');
	$time_close_shop_2_fr = get_option('time_close_shop_2_fr');

	//sat
	//=======================================================
	$time_open_shop_sa = get_option('time_open_shop_sa');
	$time_close_shop_sa = get_option('time_close_shop_sa');

	$time_open_shop_2_sa = get_option('time_open_shop_2_sa');
	$time_close_shop_2_sa = get_option('time_close_shop_2_sa');
	//sun
	//=======================================================
	$time_open_shop_su = get_option('time_open_shop_su');
	$time_close_shop_su = get_option('time_close_shop_su');

	$time_open_shop_2_su = get_option('time_open_shop_2_su');
	$time_close_shop_2_su = get_option('time_close_shop_2_su');
	//closed time
	//=======================================================
	$closed_time = get_option('closed_time');

	$closed_time_2 = get_option('closed_time_2');

	// get distance between shop and customer
	//=======================================================
	$get_distance = get_option('get_distance');

	//dsmart distance
	//=======================================================
	$dsmart_distance = get_option('dsmart_distance');

	//google key
	//=======================================================
	$dsmart_google_key = get_option('dsmart_google_key');
	$place_id_map = get_option('place_id_map');
	$logo_link = get_option('logo_link');
	$button_color = get_option('button_color');
	$sidebar_color = get_option('sidebar_color');
	$price_color = get_option('price_color');

	//min order
	//=======================================================
	$dsmart_min_order = get_option('dsmart_min_order');

	//min order free
	//=======================================================
	$dsmart_min_order_free = get_option('dsmart_min_order_free');
	$dsmart_min_order_free_checkbox = get_option('dsmart_min_order_free_checkbox');

	//shipping fee
	//=======================================================
	$dsmart_shipping_fee = get_option('dsmart_shipping_fee');
	$dsmart_shipping_from = get_option('dsmart_shipping_from');
	$dsmart_shipping_to = get_option('dsmart_shipping_to');
	$dsmart_shipping_cs_fee = get_option('dsmart_shipping_cs_fee');
	$dsmart_min_cs_fee = get_option('dsmart_min_cs_fee');

	//currency
	//=======================================================
	$dsmart_currency = get_option('dsmart_currency');

	//currency rate
	//=======================================================
	$dsmart_currency_rate = get_option('dsmart_currency_rate');
	if (!$dsmart_currency_rate) {
		$dsmart_currency_rate = "";
	}
	//$dsmart_tax = get_option('dsmart_tax');
	//text when customer order
	//=======================================================
	$dsmart_header_mail_order 		= get_option('dsmart_header_mail_order');
	$dsmart_header_mail_order_cs 	= get_option('dsmart_header_mail_order_cs');

	$dsmart_text_mail_order 		= get_option('dsmart_text_mail_order');
	$dsmart_text_mail_order_cs 		= get_option('dsmart_text_mail_order_cs');

	//text mail when change status order
	//=======================================================
	$dsmart_header_mail_success_cs	= get_option('dsmart_header_mail_success_cs');
	$dsmart_header_mail_cancel_cs 	= get_option('dsmart_header_mail_cancel_cs');

	//$dsmart_text_mail_success 		= get_option('dsmart_text_mail_success');
	$dsmart_text_mail_cancel 		= get_option('dsmart_text_mail_cancel');
	$dsmart_text_mail_success_cs 	= get_option('dsmart_text_mail_success_cs');
	$dsmart_text_mail_cancel_cs 	= get_option('dsmart_text_mail_cancel_cs');

	//order
	//=======================================================
	$dsmart_thankyou_text 	= get_option('dsmart_thankyou_text');
	$dsmart_order 			= get_option('dsmart_order');
	$dsmart_orderby 		= get_option('dsmart_orderby');
	$dsmart_term_text 		= get_option('dsmart_term_text');
	$ds_mail_name 			= get_option('ds_mail_name');
	$ds_sender_email 		= get_option('ds_sender_email');

	//cart custom text
	//=======================================================
	$dsmart_cart_text 		= get_option('dsmart_cart_text');
	$dsmart_cart_color 		= get_option('dsmart_cart_color');
	$dsmart_cart_background = get_option('dsmart_cart_background');

	//promotion discount
	//=======================================================
	$type_promotion 		= get_option('type_promotion');
	$promotion 				= get_option('promotion');

	$type_promotion_2 		= get_option('type_promotion_2');
	$promotion_2 			= get_option('promotion_2');

	$discount_min 			= get_option('discount_min');

	//discount type
	//=======================================================
	$discount_cod 			= get_option('discount_cod');
	$discount_shop 			= get_option('discount_shop');

	//time discount
	//mon
	//=======================================================
	$time_discount_shop_mo = get_option('time_discount_shop_mo');

	//tue
	//=======================================================
	$time_discount_shop_tu = get_option('time_discount_shop_tu');

	//wed
	//=======================================================
	$time_discount_shop_we = get_option('time_discount_shop_we');

	//thu
	//=======================================================
	$time_discount_shop_th = get_option('time_discount_shop_th');
	//fri
	//=======================================================
	$time_discount_shop_fr = get_option('time_discount_shop_fr');
	//sat
	//=======================================================
	$time_discount_shop_sa = get_option('time_discount_shop_sa');

	//sun
	//=======================================================
	$time_discount_shop_su = get_option('time_discount_shop_su');

	//time discount 2
	//mon
	//=======================================================
	$time_discount_shop_2_mo = get_option('time_discount_shop_2_mo');

	//tue
	//=======================================================
	$time_discount_shop_2_tu = get_option('time_discount_shop_2_tu');

	//wed
	//=======================================================
	$time_discount_shop_2_we = get_option('time_discount_shop_2_we');

	//thu
	//=======================================================
	$time_discount_shop_2_th = get_option('time_discount_shop_2_th');
	//fri
	//=======================================================
	$time_discount_shop_2_fr = get_option('time_discount_shop_2_fr');
	//sat
	//=======================================================
	$time_discount_shop_2_sa = get_option('time_discount_shop_2_sa');
	//sun
	//=======================================================
	$time_discount_shop_2_su = get_option('time_discount_shop_2_su');

	$dsmart_custom_discount_date = get_option('dsmart_custom_discount_date');

	//back link in cart page
	//=========================================================
	$back_link_in_cart = get_option('back_link_in_cart');
	$redirect_link_shop = get_option('redirect_link_shop');
	$notify_text = get_option('notify_text');
	$close_shop_text = get_option('close_shop_text');
	$show_notify = get_option('show_notify');
	$show_profile = get_option('show_profile');
	//second order
	$show_second_number = get_option('show_second_number');
	$enable_pool = get_option('enable_pool');
	$homepage_popup = get_option('homepage_popup');
	$current_date = date('Ymd');
	$total_order_in_date = (get_option('total_order_' . $current_date) != "") ? intval(get_option('total_order_' . $current_date)) : 0;
	$time_to_show_alert = get_option('time_to_show_alert');
	$next_link_shortcode = get_option('next_link_shortcode');
	$zipcode_status = get_option('zipcode_status');
	$zipcode_data = get_option('zipcode_data');

	// For API Mobile
	$mobile_code = stripslashes_deep(get_option("mobile_code"));
?>
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->
	<div class="wrap">
		<h1><?php _e('Einstellungen'); ?></h1>
		<form action="#" class="dsmart-form" method="POST" novalidate>
			<ul class="ds-list-tab">
				<li class="active"><a href="#tab-1">Shop Stil</a></li>
				<li><a href="#tab-2">Allgemeine</a></li>
				<li><a href="#tab-3">Bezahlverfahren</a></li>
				<li><a href="#tab-4">Lieferung & Abholung</a></li>
				<li><a href="#tab-5">Information</a></li>
				<li><a href="#tab-6">Email</a></li>
				<li><a href="#tab-7">Direkt Rabatt</a></li>
				<li><a href="#tab-8">Zipcode Datebase</a></li>
				<li><a href="#tab-9">Bestellungen</a></li>
			</ul>
				<div class="tab-content active" id="tab-1">
					<h2><?php _e("Theme Stil"); ?></h2>
					<div class="form-group">
						<label><input type="radio" name="dsmart_theme_style" value="dark" <?php if ($dsmart_theme_style == "dark") {
																								echo 'checked';
																							} ?>><?php _e('Dunkel (Schwarz)') ?></label>
						<label><input type="radio" name="dsmart_theme_style" value="light" <?php if ($dsmart_theme_style == "light" || $dsmart_theme_style == "") {
																								echo 'checked';
																							} ?>><?php _e('Hell (Weiss)') ?></label>
					</div>
					<h2><?php _e("So schnell wie möglich"); ?></h2>
					<div class="form-group">
						<label><input type="checkbox" name="dsmart_buynow" value="1" <?php if ($dsmart_buynow == "1") {
																							echo 'checked';
																						} ?>><?php _e('So schnell wie möglich?') ?></label>
					</div>
					<h2><?php _e("Vorschaubild"); ?></h2>
					<div class="form-group">
						<label><input type="checkbox" name="dsmart_thumbnail" value="1" <?php if ($dsmart_thumbnail == "1") {
																							echo 'checked';
																						} ?>><?php _e('Vorschaubild Unsichtbar?') ?></label>
					</div>
					<div class="form-group">
						<label><input type="checkbox" name="float_cart" value="on" <?php if ($float_cart == "on") {
																						echo 'checked';
																					} ?>><span><?php _e('Warenkorb Float?'); ?></span></label>
					</div>
					<h2><?php _e("Logo"); ?></h2>
					<div class="form-group">
						<div class="ds-logo-img">
							<?php $image_id = get_option('ds_logo');
							if (intval($image_id) > 0) {
								$image = wp_get_attachment_image($image_id, 'medium', false, array('id' => 'myprefix-preview-image'));
							} else {
								$image = '<img id="myprefix-preview-image" src="' . BOOKING_ORDER_PATH . '/img/no_img.jpg' . '" />';
							}

							echo $image; ?>
						</div>
						<input type="hidden" name="myprefix_image_id" id="myprefix_image_id" value="<?php echo esc_attr($image_id); ?>" class="regular-text" />
						<input type='button' class="button-primary" value="<?php esc_attr_e('Bild aussuchen', 'dsmart'); ?>" id="myprefix_media_manager"/>
					</div>
					<div class="form-group">
							<label><?php _e('Logo link') ?></label>
							<input type="text" name="logo_link" class="widefat" placeholder="<?php _e('Logo link') ?>" value="<?php echo $logo_link; ?>" />
						</div>

						<div class="form-group">
							<label><?php _e('Button Color') ?></label>
							<input type="text" name="button_color" class="coloris" value="<?php echo $button_color; ?>"/>
						</div>

						<div class="form-group">
							<label><?php _e('Sidebar Color') ?></label>
							<input type="text" name="sidebar_color" class="coloris" value="<?php echo $sidebar_color; ?>"/>
						</div>

						<div class="form-group">
							<label><?php _e('Price Color') ?></label>
							<input type="text" name="price_color" class="coloris" value="<?php echo $price_color; ?>"/>
						</div>
				</div>
				<div class="tab-content" id="tab-2">
					<h2><?php _e("Allgemeine"); ?></h2>
					<div class="form-group">
						<label><?php _e('Währung') ?></label>
						<select name="dsmart_currency" class="widefat">
							<option value="1" <?php if ($dsmart_currency == "1") {
													echo 'selected';
												} ?>>$</option>
							<option value="2" <?php if ($dsmart_currency == "2") {
													echo 'selected';
												} ?>>€</option>
							<option value="3" <?php if ($dsmart_currency == "3") {
													echo 'selected';
												} ?>>CHF</option>
						</select>
					</div>
					<div class="form-group">
						<label><?php _e('Währung von $ zu € oder CHF') ?></label>
						<input type="text" name="dsmart_currency_rate" class="widefat" placeholder="<?php _e('Währung von $ zu € oder CHF') ?>" value="<?php echo $dsmart_currency_rate; ?>" />
					</div>
					<h2><?php _e("Zweite Bestellungsnummer"); ?></h2>
					<div class="radio-wrap">
						<label><input type="radio" name="show_second_number" value="0" <?php if ($show_second_number != "1") echo 'checked'; ?>>Aus</label>
						<label><input type="radio" name="show_second_number" value="1" <?php if ($show_second_number == "1") echo 'checked'; ?>>An</label>
					</div>
					
					<div class="form-group">
						<p>Derzeitige Angezeigte-Nummer: <?php echo $total_order_in_date; ?></p>
						<button class="button reset-order" type="button">Zurücksetzen</button>
					</div>
					<!-- <h2><?php _e("Mwst für Lieferung"); ?></h2>
					<div class="form-group">
						<h4><?php _e('Mwst für Lieferung %') ?></h4>
						<label><input type="number" name="tax_shipping" placeholder="<?php _e("Please enter your tax for shipping"); ?>" value="<?php echo $tax_shipping; ?>"/></label>
					</div> -->
					<h2><?php _e("Google map") ?></h2>
					<div class="form-group">
						<div class="form-group">
							<label><?php _e('Google map API-Key') ?></label>
							<input type="text" name="dsmart_google_key" class="widefat" placeholder="<?php _e('Google maps key') ?>" value="<?php echo $dsmart_google_key; ?>" />
						</div>
						<div class="form-group">
							<label><?php _e('Place Id') ?></label>
							<input type="text" name="place_id_map" class="widefat" placeholder="<?php _e('Place id') ?>" value="<?php echo $place_id_map; ?>" />
						</div>
						<div class="form-group">
							<label><input type="checkbox" name="get_distance" value="on" <?php if ($get_distance == "on") {
																								echo 'checked';
																							} ?>><span><?php _e('Entfernung vom Shop zum Kunden Anzeigen?'); ?></span></label>
						</div>
					</div>
					<h2><?php _e("Sortierung") ?></h2>
					<div class="form-group">
						<label><?php _e('Sortierung') ?></label>
						<select name="dsmart_orderby">
							<option value="date" <?php if ($dsmart_orderby == "date") {
														echo 'selected';
													} ?>><?php _e("Date"); ?></option>
							<option value="name" <?php if ($dsmart_orderby == "name") {
														echo 'selected';
													} ?>><?php _e("Name"); ?></option>
							<option value="id" <?php if ($dsmart_orderby == "id") {
													echo 'selected';
												} ?>><?php _e("ID"); ?></option>
							<option value="slug" <?php if ($dsmart_orderby == "slug") {
														echo 'selected';
													} ?>><?php _e("Slug"); ?></option>
							<option value="title_number" <?php if ($dsmart_orderby == "title_number") {
																echo 'selected';
															} ?>><?php _e("Name & number"); ?></option>
						</select>
					</div>
					<div class="form-group">
						<label><?php _e('Sortierart') ?></label>
						<select name="dsmart_order">
							<option value="desc" <?php if ($dsmart_order == "desc") {
														echo 'selected';
													} ?>><?php _e("DESC"); ?></option>
							<option value="asc" <?php if ($dsmart_order == "asc") {
													echo 'selected';
												} ?>><?php _e("ASC"); ?></option>
						</select>
					</div>
					<div class="form-group">
						<label>
							<?php echo __("Einstellung für Alert in \"milisekunden\"", "booking-order"); ?>
						</label>
						<input name="time_to_show_alert" type="number" class="widefat" value="<?php echo $time_to_show_alert; ?>" />
					</div>
					<div class="form-group">
						<h2><?php _e('Weiter Einkaufen button link'); ?></h2>
						<input type="text" class="widefat" name="back_link_in_cart" value="<?php echo $back_link_in_cart; ?>">
					</div>
					<div class="form-group">
						<h2><?php _e('Shop redirect link'); ?></h2>
						<input type="text" class="widefat" name="redirect_link_shop" value="<?php echo $redirect_link_shop; ?>">
					</div>
				</div>
				<div class="tab-content" id="tab-3">
					<h2><?php _e("Zahlungsmethode") ?></h2>
					<div class="form-group">
						<label><input type="checkbox" name="dsmart_paypal" value="on" <?php if ($dsmart_paypal == "on") {
																							echo 'checked';
																						} ?>><?php _e('Paypal') ?></label>
						<label><input type="checkbox" name="dsmart_klarna" value="on" <?php if ($dsmart_klarna == "on") {
																							echo 'checked';
																						} ?>><?php _e('Klarna') ?></label>
						<label><?php _e('Barzahlung Text'); ?></label>
						<label><input type="input" name="dsmart_barzahlung" value="<?php echo $dsmart_barzahlung; ?>"></label>
					</div>
					<div class="form-group">
						<label><?php _e('Benutzerdefiniertes Zahlungsmethode'); ?></label>
						<div class="list-method">
							<?php if (is_array($dsmart_custom_method) && count($dsmart_custom_method) > 0) {
								foreach ($dsmart_custom_method as $value) { ?>
									<div class="method-item">
										<input type="text" name="dsmart_custom_method[]" class="widefat" placeholder="<?php _e('Benutzerdefiniertes Zahlungsmethode') ?>" value="<?php echo $value; ?>" required />
										<span class="remove-row3">x</span>
									</div>
							<?php }
							} ?>
						</div>
						<button type="button" class="button add-new-row3"><?php _e("Neue Zahlungsmethode hinzufugen"); ?></button>
					</div>
					<h2><?php _e('Paypal Einstellung') ?></h2>
					<!-- <div class="form-group">
						<label><?php _e('Paypal Client ID') ?></label>
						<input type="text" name="dsmart_paypal_client_id" class="widefat" placeholder="<?php _e('Paypal Client ID') ?>" value="<?php echo $dsmart_paypal_client_id; ?>"/>
					</div>
					<div class="form-group">
						<label><?php _e('Paypal Client Secret') ?></label>
						<input type="text" name="dsmart_paypal_client_secret" class="widefat" placeholder="<?php _e('Paypal Client Secret') ?>" value="<?php echo $dsmart_paypal_client_secret; ?>"/>
					</div> -->
					<div class="form-group">
						<label><?php _e('Paypal Email-Addresse') ?></label>
						<input type="text" name="dsmart_paypal_email_address" class="widefat" placeholder="<?php _e('Paypal Email-Addresse') ?>" value="<?php echo $dsmart_paypal_email_address; ?>" />
					</div>
					<div class="form-group">
						<label><input type="checkbox" name="dsmart_sandbox" value="yes" <?php if ($dsmart_sandbox == "yes") {
																							echo 'checked';
																						} ?>><?php _e('Sandbox (Paypal Testen)?') ?></label>
					</div>
					<h2><?php _e('Klarna Einstellung') ?></h2>
					<div class="form-group">
						<label><?php _e('Klarna Benutzername') ?></label>
						<input type="text" name="klarna_username" class="widefat" placeholder="<?php _e('Klarna username') ?>" value="<?php echo $klarna_username; ?>" />
					</div>
					<div class="form-group">
						<label><?php _e('Klarna Passwort') ?></label>
						<input type="text" name="klarna_password" class="widefat" placeholder="<?php _e('Klarna password') ?>" value="<?php echo $klarna_password; ?>" />
					</div>
				</div>
			<div class="tab-content" id="tab-4">
			<h2><?php _e("Pool Druckverteilung?"); ?></h2>
					<div class="radio-wrap">
						<label><input type="radio" name="enable_pool" value="0" <?php if ($enable_pool != "1") echo 'checked'; ?>>Aus</label>
						<label><input type="radio" name="enable_pool" value="1" <?php if ($enable_pool == "1") echo 'checked'; ?>>An</label>
					</div>
				<h2><?php _e("Lieferung & Abholung Einstellungen") ?></h2>

				<div class="form-group">
					<label><input type="checkbox" name="dsmart_method_ship" value="on" <?php if ($dsmart_method_ship == "on") {
																							echo 'checked';
																						} ?>><?php _e('Lieferung') ?></label>
					<label><input type="checkbox" name="dsmart_method_direct" value="on" <?php if ($dsmart_method_direct == "on") {
																								echo 'checked';
																							} ?>><?php _e('im Laden Abholen') ?></label>
				</div>
				<div class="form-group">
					<h4><?php _e('Online shop JETZT SCHLIESSEN') ?></h4>
					<label><input type="checkbox" name="close_shop" value="on" <?php if ($close_shop == "on") {
																					echo 'checked';
																				} ?>><span><?php _e('Online Shop wird sofort geschlossen, so dass keine Bestellung mehr möglich ist.'); ?></span></label>
					<span><em><?php _e("Vergiss nicht den Online Shop wieder einzuschalten \"Haken rausnehmen\"!!!") ?></em></span>
				</div>
				<h2><?php _e("Popup Image ( Empfehlung: 1920x1080, 1280x720)"); ?></h2>
				<div class="form-group">
					<div class="ds-logo-img">
						<?php $image_id = get_option('ds_popup');
						if (intval($image_id) > 0) {
							$image = wp_get_attachment_image($image_id, 'medium', false, array('id' => 'myprefix-preview-image-popup'));
						} else {
							$image = '<img id="myprefix-preview-image-popup" src="' . BOOKING_ORDER_PATH . '/img/no_img.jpg' . '" />';
						}

						echo $image; ?>
					</div>
					<input type="hidden" name="myprefix_image_id_popup" id="myprefix_image_id_popup" value="<?php echo esc_attr($image_id); ?>" class="regular-text" />
					<input type='button' class="button-primary" value="<?php esc_attr_e('Bild aussuchen', 'dsmart'); ?>" id="myprefix_media_manager_popup"/>
				</div>
				<h2><?php _e("Show Popup HomePage? (Empfehlung: 1920x1080, 1280x720)"); ?></h2>
				<div class="radio-wrap">
					<label><input type="radio" name="homepage_popup" value="0" <?php if ($homepage_popup == "0") echo 'checked'; ?>>Aus</label>
					<label><input type="radio" name="homepage_popup" value="1" <?php if ($homepage_popup == "1") echo 'checked'; ?>>An (Homepage)</label>
					<label><input type="radio" name="homepage_popup" value="2" <?php if ($homepage_popup == "2") echo 'checked'; ?>>An (Bestellung)</label>
				</div>
				<h2><?php _e(""); ?></h2>
				<div class="form-group">
					<div class="ds-logo-img">
						<?php $image_id_homepage = get_option('ds_popup_homepage');
						if (intval($image_id_homepage) > 0) {
							$image = wp_get_attachment_image($image_id_homepage, 'medium', false, array('id' => 'myprefix-preview-image-popup-homepage'));
						} else {
							$image = '<img id="myprefix-preview-image-popup-homepage" src="' . BOOKING_ORDER_PATH . '/img/no_img.jpg' . '" />';
						}

						echo $image; ?>
					</div>
					<input type="hidden" name="myprefix_image_id_popup_homepage" id="myprefix_image_id_popup_homepage" value="<?php echo esc_attr($image_id_homepage); ?>" class="regular-text" />
					<input type='button' class="button-primary" value="<?php esc_attr_e('Bild aussuchen', 'dsmart'); ?>" id="myprefix_media_manager_popup_homepage"/>
				</div>
				<div id="delay_time_delivery" <?php if ($dsmart_method_ship !== "on") {echo 'style="display:none"';}?> class="form-group">
					<h4><?php _e('Bearbeitungszeit für Lieferung') ?></h4>
					<label><input type="number" name="delay_time" placeholder="<?php _e("Please enter your time which you want to delay(minutes)"); ?>" value="<?php echo $delay_time; ?>" /></label>
				</div>
				<div id="time_delivery_step" <?php if ($dsmart_method_ship !== "on") {echo 'style="display:none"';}?> class="form-group">
					<h4><?php _e('Zeitsabstand für Lieferung') ?></h4>
					<label><input type="number" name="dsmart_delivery_time_step" value="<?php echo $dsmart_delivery_time_step; ?>" /></label>
				</div>
				<div id="delay_time_in_shop" <?php if ($dsmart_method_direct !== "on") {echo 'style="display:none"';}?> class="form-group">
					<h4><?php _e('Bearbeitungszeit für Abholung') ?></h4>
					<label><input type="number" name="delay_delivery" placeholder="<?php _e("Please enter your time which you want to delay(minutes)"); ?>" value="<?php echo $delay_delivery; ?>" /></label>
				</div>
				<div id="time_takeaway_step" <?php if ($dsmart_method_direct !== "on") {echo 'style="display:none"';}?> class="form-group">
					<h4><?php _e('Zeitsabstand für Abholung') ?></h4>
					<label><input type="number" name="dsmart_takeaway_time_step" value="<?php echo $dsmart_takeaway_time_step; ?>" /></label>
				</div>
				<div id="opening_time_delivery" <?php if ($dsmart_method_ship !== "on") {echo 'style="display:none"';}?> class="form-group timepicker-group">
					<h4><?php _e('Öffnungszeiten Lieferung') ?></h4>
					<label>
						<span class="day-of-week"><?php _e("Montag:") ?></span>
						<input type="text" name="time_open_shop_mo" id="time_open_shop_mo" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_mo; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_mo" id="time_close_shop_mo" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_mo; ?>" autocomplete="off" />
					</label>
					<label>
						<span class="day-of-week"><?php _e("Dienstag:") ?></span>
						<input type="text" name="time_open_shop_tu" id="time_open_shop_tu" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_tu; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_tu" id="time_close_shop_tu" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_tu; ?>" autocomplete="off" />
					</label>
					<label>
						<span class="day-of-week"><?php _e("Mittwoch:") ?></span>
						<input type="text" name="time_open_shop_we" id="time_open_shop_we" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_we; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_we" id="time_close_shop_we" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_we; ?>" autocomplete="off" />
					</label>
					<label>
						<span class="day-of-week"><?php _e("Donnerstag:") ?></span>
						<input type="text" name="time_open_shop_th" id="time_open_shop_th" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_th; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_th" id="time_close_shop_th" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_th; ?>" autocomplete="off" />
					</label>
					<label>
						<span class="day-of-week"><?php _e("Freitag:") ?></span>
						<input type="text" name="time_open_shop_fr" id="time_open_shop_fr" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_fr; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_fr" id="time_close_shop_fr" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_fr; ?>" autocomplete="off" />
					</label>
					<label>
						<span class="day-of-week"><?php _e("Samstag:") ?></span>
						<input type="text" name="time_open_shop_sa" id="time_open_shop_sa" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_sa; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_sa" id="time_close_shop_sa" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_sa; ?>" autocomplete="off" />
					</label>
					<label>
						<span class="day-of-week"><?php _e("Sonntag:") ?></span>
						<input type="text" name="time_open_shop_su" id="time_open_shop_su" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_su; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_su" id="time_close_shop_su" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_su; ?>" autocomplete="off" />
					</label>
				</div>
				<div id="opening_time_in_shop" <?php if ($dsmart_method_direct !== "on") {echo 'style="display:none"';}?> class="form-group timepicker-group">
					<h4><?php _e('Öffnungszeiten Abholung') ?></h4>
					<label>
						<span class="day-of-week"><?php _e("Montag:") ?></span>
						<input type="text" name="time_open_shop_2_mo" id="time_open_shop_2_mo" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_2_mo; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_2_mo" id="time_close_shop_2_mo" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_2_mo; ?>" autocomplete="off" />
					</label>
					<label>
						<span class="day-of-week"><?php _e("Dienstag:") ?></span>
						<input type="text" name="time_open_shop_2_tu" id="time_open_shop_2_tu" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_2_tu; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_2_tu" id="time_close_shop_2_tu" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_2_tu; ?>" autocomplete="off" />
					</label>
					<label>
						<span class="day-of-week"><?php _e("Mittwoch:") ?></span>
						<input type="text" name="time_open_shop_2_we" id="time_open_shop_2_we" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_2_we; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_2_we" id="time_close_shop_2_we" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_2_we; ?>" autocomplete="off" />
					</label>
					<label>
						<span class="day-of-week"><?php _e("Donnerstag:") ?></span>
						<input type="text" name="time_open_shop_2_th" id="time_open_shop_2_th" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_2_th; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_2_th" id="time_close_shop_2_th" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_2_th; ?>" autocomplete="off" />
					</label>
					<label>
						<span class="day-of-week"><?php _e("Freitag:") ?></span>
						<input type="text" name="time_open_shop_2_fr" id="time_open_shop_2_fr" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_2_fr; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_2_fr" id="time_close_shop_2_fr" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_2_fr; ?>" autocomplete="off" />
					</label>
					<label>
						<span class="day-of-week"><?php _e("Samstag:") ?></span>
						<input type="text" name="time_open_shop_2_sa" id="time_open_shop_2_sa" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_2_sa; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_2_sa" id="time_close_shop_2_sa" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_2_sa; ?>" autocomplete="off" />
					</label>
					<label>
						<span class="day-of-week"><?php _e("Sonntag:") ?></span>
						<input type="text" name="time_open_shop_2_su" id="time_open_shop_2_su" class="widefat timepicker" placeholder="<?php _e('Öffnungszeit') ?>" value="<?php echo $time_open_shop_2_su; ?>" autocomplete="off" />
						<span><?php _e("to") ?></span>
						<input type="text" name="time_close_shop_2_su" id="time_close_shop_2_su" class="widefat timepicker" placeholder="<?php _e('Schliesszeit') ?>" value="<?php echo $time_close_shop_2_su; ?>" autocomplete="off" />
					</label>
				</div>
				<div id="closing_time_in_shop" <?php if ($dsmart_method_direct !== "on") {echo 'style="display:none"';}?> class="form-group timepicker-group table-custom-closed">
					<h4><?php _e('Schliesszeit Abholung') ?></h4>
					<table class="table">
						<tbody>
							<?php if ($closed_time != "") {
								foreach ($closed_time as $item) { ?>
									<tr>
										<td>
											<select class="widefat" name="closed_time_date[]" required>
												<option value="mo" <?php if ($item['date'] == "mo") {
																		echo 'selected';
																	} ?>><?php _e("Montag") ?></option>
												<option value="tu" <?php if ($item['date'] == "tu") {
																		echo 'selected';
																	} ?>><?php _e("Dienstag") ?></option>
												<option value="we" <?php if ($item['date'] == "we") {
																		echo 'selected';
																	} ?>><?php _e("Mittwoch") ?></option>
												<option value="th" <?php if ($item['date'] == "th") {
																		echo 'selected';
																	} ?>><?php _e("Donnerstag") ?></option>
												<option value="fr" <?php if ($item['date'] == "fr") {
																		echo 'selected';
																	} ?>><?php _e("Freitag") ?></option>
												<option value="sa" <?php if ($item['date'] == "sa") {
																		echo 'selected';
																	} ?>><?php _e("Samstag") ?></option>
												<option value="su" <?php if ($item['date'] == "su") {
																		echo 'selected';
																	} ?>><?php _e("Sonntag"); ?></option>
											</select>
										</td>
										<td><input type="text" name="closed_time_from[]" class="widefat timepicker" value="<?php echo $item['from'] ?>" autocomplete="off" required /></td>
										<td><input type="text" name="closed_time_to[]" class="widefat timepicker" value="<?php echo $item['to'] ?>" autocomplete="off" required /></td>
										<td><span class="button remove-row"><?php _e("Löschen") ?></span></td>
									</tr>
							<?php }
							} ?>
							<tr class="hidden-field">
								<td>
									<select class="widefat" name="closed_time_date[]">
										<option value="mo"><?php _e("Montag") ?></option>
										<option value="tu"><?php _e("Dienstag") ?></option>
										<option value="we"><?php _e("Mittwoch") ?></option>
										<option value="th"><?php _e("Donnerstag") ?></option>
										<option value="fr"><?php _e("Freitag") ?></option>
										<option value="sa"><?php _e("Samstag") ?></option>
										<option value="su"><?php _e("Sonntag"); ?></option>
									</select>
								</td>
								<td><input type="text" name="closed_time_from[]" class="widefat timepicker" autocomplete="off" /></td>
								<td><input type="text" name="closed_time_to[]" class="widefat timepicker" autocomplete="off" /></td>
								<td><span class="button remove-row"><?php _e("Löschen") ?></span></td>
							</tr>
						</tbody>
					</table>
					<button type="button" class="button add-new-row"><?php _e("Neue Zeile hinzufugen") ?></button>
				</div>
				<div id="closing_time_delivery" <?php if ($dsmart_method_ship !== "on") {echo 'style="display:none"';}?> class="form-group timepicker-group table-custom-closed">
					<h4><?php _e('Schliesszeit Lieferung') ?></h4>
					<table class="table">
						<tbody>
							<?php if ($closed_time_2 != "") {
								foreach ($closed_time_2 as $item) { ?>
									<tr>
										<td>
											<select class="widefat" name="closed_time_date_2[]" required>
												<option value="mo" <?php if ($item['date'] == "mo") {
																		echo 'selected';
																	} ?>><?php _e("Montag") ?></option>
												<option value="tu" <?php if ($item['date'] == "tu") {
																		echo 'selected';
																	} ?>><?php _e("Dienstag") ?></option>
												<option value="we" <?php if ($item['date'] == "we") {
																		echo 'selected';
																	} ?>><?php _e("Mittwoch") ?></option>
												<option value="th" <?php if ($item['date'] == "th") {
																		echo 'selected';
																	} ?>><?php _e("Donnerstag") ?></option>
												<option value="fr" <?php if ($item['date'] == "fr") {
																		echo 'selected';
																	} ?>><?php _e("Freitag") ?></option>
												<option value="sa" <?php if ($item['date'] == "sa") {
																		echo 'selected';
																	} ?>><?php _e("Samstag") ?></option>
												<option value="su" <?php if ($item['date'] == "su") {
																		echo 'selected';
																	} ?>><?php _e("Sonntag"); ?></option>
											</select>
										</td>
										<td><input type="text" name="closed_time_from_2[]" class="widefat timepicker" value="<?php echo $item['from'] ?>" autocomplete="off" required /></td>
										<td><input type="text" name="closed_time_to_2[]" class="widefat timepicker" value="<?php echo $item['to'] ?>" autocomplete="off" required /></td>
										<td><span class="button remove-row"><?php _e("Löschen") ?></span></td>
									</tr>
							<?php }
							} ?>
							<tr class="hidden-field">
								<td>
									<select class="widefat" name="closed_time_date_2[]">
										<option value="mo"><?php _e("Montag") ?></option>
										<option value="tu"><?php _e("Dienstag") ?></option>
										<option value="we"><?php _e("Mittwoch") ?></option>
										<option value="th"><?php _e("Donnerstag") ?></option>
										<option value="fr"><?php _e("Freitag") ?></option>
										<option value="sa"><?php _e("Samstag") ?></option>
										<option value="su"><?php _e("Sonntag"); ?></option>
									</select>
								</td>
								<td><input type="text" name="closed_time_from_2[]" class="widefat timepicker" autocomplete="off" /></td>
								<td><input type="text" name="closed_time_to_2[]" class="widefat timepicker" autocomplete="off" /></td>
								<td><span class="button remove-row"><?php _e("Löschen") ?></span></td>
							</tr>
						</tbody>
					</table>
					<button type="button" class="button add-new-row"><?php _e("Neue Zeile hinzufugen") ?></button>
				</div>
				<div class="form-group timepicker-group table-custom-date">
					<h4><?php _e('Benutzerdefinierte Öffnungszeit') ?></h4>
					<h5><?php _e('Feiertage importieren:') ?></h5>

				    <div class="row">
						<select name="holiday_year" class="widefat">
						<?php 
							for($i = 2024; $i <= 2070; $i++){
						?>
								<option value="<?php echo $i ?>"><?php echo $i ?></option>
						<?php
						}
						?>
						</select>

						<select name="holiday_region" class="widefat">
						<?php 
							$region = array(
								"de" => "Deutschland",
								"de-bw" => "Baden-Württemberg",
								"de-by" => "Bayern",
								"de-be" => "Berlin",
								"de-bb" => "Brandenburg",
								"de-hb" => "Bremen",
								"de-hh" => "Hamburg",
								"de-he" => "Hessen",
								"de-mv" => "Mecklenburg-Vorpommern",
								"de-ni" => "Niedersachsen",
								"de-nw" => "Nordrhein-Westfalen",
								"de-rp" => "Rheinland-Pfalz",
								"de-sl" => "Saarland",
								"de-sn" => "Sachsen",
								"de-st" => "Sachsen-Anhalt",
								"de-sh" => "Schleswig-Holstein",
								"de-th" => "Thüringen",
							);

							foreach ($region as $key => $label) {
							
						?>
								<option value="<?php echo $key ?>"><?php echo $label ?></option>
						<?php
						}
						?>
						</select>

						<button type="button" class="button import-holiday"><?php _e("Importieren") ?></button>
					</div>

					<table style="border-spacing: 0 15px;" class="table">
						<thead>
							<tr>
								<th><?php _e("Öffnen/Schliessen") ?></th>
								<th><?php _e("Datum") ?></th>
								<th><?php _e("Zeit") ?></th>
								<th><?php _e("Action") ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if ($dsmart_new_custom_date != "") {
								foreach ($dsmart_new_custom_date as $item) {
									?>
									<tr style="vertical-align: top;">
										<td>
											<select name="new_custome_date_status[]" class="widefat">
												<option value="open" <?php echo $item["status"] === "open" ? "selected" : "" ?>>Open</option>
												<option value="close" <?php echo $item["status"] === "close" ? "selected" : "" ?>>Close</option>
											</select>
										</td>
										<td class="date_choosing">
											<select name="new_custome_date_type[]" class="widefat">
												<option value="single" <?php echo $item["date_type"] === "single" ? "selected" : "" ?> >Date</option>
												<option value="multiple" <?php echo $item["date_type"] === "multiple" ? "selected" : "" ?>>Date Range</option>
											</select>
											<br>
											<input type="text" placeholder="start date" name="new_custome_date_start_date[]" class="widefat datepicker start_date_picker" value="<?php echo $item["start_date"]?>" autocomplete="off" required />
											<br>
											<?php if ($item["date_type"] === "single"){
												?>
													<input type="text" style="display: none;" placeholder="end date" name="new_custome_date_end_date[]" class="widefat datepicker end_date_picker" value="<?php echo $item["end_date"]?>" autocomplete="off" />
												<?php
											}else{
												?>
													<input type="text" placeholder="end date" name="new_custome_date_end_date[]" class="widefat datepicker end_date_picker" value="<?php echo $item["end_date"]?>" autocomplete="off" />
												<?php
											} ?>
										</td>
										<td class="time_choosing">
											<select name="new_custome_date_time_type[]" class="widefat select_time_type">
												<option class="should_disable" <?php echo $item["status"] === "close" ? "disabled" : "" ?> value="time_to_time" <?php echo $item["time_type"] === "time_to_time" ? "selected" : "" ?>>Uhrzeit</option>
												<option value="full_day" <?php echo $item["time_type"] === "full_day" ? "selected" : "" ?>>Ganztägig (00:00 - 23:59)</option>
											</select>
											<?php if ($item["time_type"] === "time_to_time"){
												?>
													<br>
													<input type="text" placeholder="start time" name="new_custome_date_start_time[]" class="widefat timepicker start_time_picker" value="<?php echo $item["start_time"]?>" autocomplete="off"/>
													<br>
													<input type="text" placeholder="end time" name="new_custome_date_end_time[]" class="widefat timepicker end_time_picker" value="<?php echo $item["end_time"]?>" autocomplete="off" />
												<?php
											}else{
												?>
													<br>
													<input type="text" style="display: none;" placeholder="start time" name="new_custome_date_start_time[]" class="widefat timepicker start_time_picker" value="<?php echo $item["start_time"]?>" autocomplete="off"/>
													<br>
													<input type="text" style="display: none;" placeholder="end time" name="new_custome_date_end_time[]" class="widefat timepicker end_time_picker" value="<?php echo $item["end_time"]?>" autocomplete="off" />
												<?php
											} ?>
											
										</td>
										
										<td><span class="button remove-row"><?php _e("Löschen") ?></span></td>
									</tr>
							<?php }
							} ?>
							<tr class="hidden-field">
							<td>
									<select name="new_custome_date_status[]" class="widefat">
											<option value="open" >Open</option>
											<option value="close" >Close</option>
										</select>
									</td>
									<td class="date_choosing">
										<select name="new_custome_date_type[]" class="widefat">
											<option value="single" >Date</option>
											<option value="multiple" >Date Range</option>
										</select>
										<br>
										<input type="text" placeholder="start date" name="new_custome_date_start_date[]" class="widefat datepicker start_date_picker" value="" autocomplete="off" required />
										<br>
										<input type="text" style="display: none;" placeholder="end date" name="new_custome_date_end_date[]" class="widefat datepicker end_date_picker" value="" autocomplete="off" />
									</td>
									<td class="time_choosing">
										<select name="new_custome_date_time_type[]" class="widefat select_time_type">
											<option class="should_disable" value="time_to_time">Uhrzeit</option>
											<option value="full_day">Ganztägig (00:00 - 23:59)</option>
										</select>
										<br>
										<input type="text" placeholder="start time" name="new_custome_date_start_time[]" class="widefat timepicker start_time_picker" value="" autocomplete="off"/>
										<br>
										<input type="text" placeholder="end time" name="new_custome_date_end_time[]" class="widefat timepicker end_time_picker" value="" autocomplete="off" />
									</td>
									
									<td><span class="button remove-row"><?php _e("Löschen") ?></span></td>
							</tr>
						</tbody>
					</table>
					<button type="button" class="button add-new-row"><?php _e("Neue Zeile hinzufugen") ?></button>
				</div>
				<h2><?php _e("Lieferung Einstellung") ?></h2>
				<div class="form-group">
					<label><?php _e('Maximum Auto-Route in KM') ?></label>
					<input type="number" name="dsmart_distance" class="widefat" placeholder="<?php _e('Maximum Auto-Route in KM') ?>" value="<?php echo $dsmart_distance; ?>" />
				</div>
				<!-- <div class="form-group">
					<label><?php _e('Minimum Bestellung für Lieferung') ?></label>
					<input type="number" name="dsmart_min_order" id="dsmart_min_order" class="widefat" placeholder="<?php _e('Minimum Bestellung für Lieferung') ?>" value="<?php echo $dsmart_min_order; ?>" autocomplete="off" />
				</div> -->
				<div class="form-group">
					<label><?php _e('Minimum Bestellung für Kostenlos Lieferung') ?></label>
					<div class="radio-wrap">
						<label><input type="radio" name="dsmart_min_order_free_checkbox" value="0" <?php if ($dsmart_min_order_free_checkbox != "1") echo 'checked'; ?>>Aus</label>
						<label><input type="radio" name="dsmart_min_order_free_checkbox" value="1" <?php if ($dsmart_min_order_free_checkbox == "1") echo 'checked'; ?>>An</label>
					</div>
					<?php if ($dsmart_min_order_free_checkbox == "1") {
						?>
							<input type="number" name="dsmart_min_order_free" id="dsmart_min_order_free" class="widefat" placeholder="<?php _e('Minimum Bestellung für Kostenlos Lieferung') ?>" value="<?php echo $dsmart_min_order_free; ?>" autocomplete="off" />
						<?php
					}else{
						?>
							<input style="display: none;" type="number" name="dsmart_min_order_free" id="dsmart_min_order_free" class="widefat" placeholder="<?php _e('Minimum Bestellung für Kostenlos Lieferung') ?>" value="<?php echo $dsmart_min_order_free; ?>" autocomplete="off" />
						<?php
					} ?>
					
				</div>
				<!-- <div class="form-group">
					<label><?php _e('Standardwert für Lieferkosten') ?></label>
					<input type="text" name="dsmart_shipping_fee" id="dsmart_shipping_fee" class="widefat" placeholder="<?php _e('Standardwert für Lieferkosten') ?>" value="<?php echo $dsmart_shipping_fee; ?>" autocomplete="off" />
				</div> -->
				<div class="form-group">
					<label><?php _e('Benutzerdefinierte Lieferkosten') ?></label>
					<div class="list-shipping-fee">
						<div class="list-shipping-fee-heading">
							<div class="a1"><?php _e("Von (km)", "booking-order"); ?></div>
							<div class="a1"><?php _e("Bis (km)", "booking-order"); ?></div>
							<div class="a1"><?php _e("Lieferkosten", "booking-order"); ?></div>
							<div class="a1"><?php _e("Mindestbestellwert", "booking-order"); ?></div>
						</div>
						<?php if (is_array($dsmart_shipping_from) && count($dsmart_shipping_from) > 0) {
							foreach ($dsmart_shipping_from as $key => $value) { ?>
								<div class="fee-item">
									<input type="number" name="dsmart_shipping_from[]" class="widefat" placeholder="<?php _e('Von') ?>" value="<?php echo $dsmart_shipping_from[$key]; ?>" autocomplete="off" />
									<input type="number" name="dsmart_shipping_to[]" class="widefat" placeholder="<?php _e('Bis') ?>" value="<?php echo $dsmart_shipping_to[$key]; ?>" autocomplete="off" />
									<input type="number" name="dsmart_shipping_cs_fee[]" class="widefat" placeholder="<?php _e('Lieferkosten') ?>" step="0.01" value="<?php echo $dsmart_shipping_cs_fee[$key]; ?>" autocomplete="off" />
									<input type="number" name="dsmart_min_cs_fee[]" class="widefat" placeholder="<?php _e('Mindestbestellwert') ?>" step="0.01" value="<?php echo $dsmart_min_cs_fee[$key]; ?>" autocomplete="off" />
									<?php if ($key > 0) : ?>
										<span class="remove-row2">x</span>
									<?php endif; ?>
								</div>
							<?php }
						} else { ?>
							<div class="fee-item">
								<input type="number" name="dsmart_shipping_from[]" class="widefat" placeholder="<?php _e('Von') ?>" value="" autocomplete="off" />
								<input type="number" name="dsmart_shipping_to[]" class="widefat" placeholder="<?php _e('Bis') ?>" value="" autocomplete="off" />
								<input type="number" name="dsmart_shipping_cs_fee[]" class="widefat" placeholder="<?php _e('Lieferkosten') ?>" step="0.01" value="" autocomplete="off" />
								<input type="number" name="dsmart_min_cs_fee[]" class="widefat" placeholder="<?php _e('Mindestbestellwert') ?>" step="0.01" value="" autocomplete="off" />
							</div>
						<?php } ?>
					</div>
					<button type="button" class="button add-new-row2"><?php _e("Neue Zeile hinzufügen") ?></button>
				</div>
			</div>
			<div class="tab-content" id="tab-5">
				<h2><?php _e("Zusatzstoffe") ?></h2>
				<div class="form-group">
					<textarea name="dsmart_taxonomy_text" class="widefat" placeholder="<?php _e('Zusatzstoffe') ?>" rows="10"><?php echo $dsmart_taxonomy_text; ?></textarea>
				</div>
				<h2><?php _e("Text für Erfolgreicher Bestellungsseite"); ?></h2>
				<div class="form-group">
					<textarea name="dsmart_thankyou_text" rows="5" class="widefat" placeholder="<?php _e('Text für Erfolgreicher Bestellungsseite') ?>"><?php echo $dsmart_thankyou_text; ?></textarea>
				</div>
				<div class="form-group">
					<label><?php _e('AGB Pfad Einstellung') ?></label>
					<textarea name="dsmart_term_text" rows="5" class="widefat" placeholder="<?php _e('AGB Pfad Einstellung') ?>"><?php echo $dsmart_term_text; ?></textarea>
				</div>
				<div class="form-group">
					<label><?php _e('Warenkorb Information Text') ?></label>
					<textarea name="dsmart_cart_text" rows="5" class="widefat" placeholder="<?php _e('Warenkorb Information Text') ?>"><?php echo $dsmart_cart_text; ?></textarea>
				</div>
				<div class="form-group">
					<label><?php _e('Schrift Farbe') ?></label>
					<input type="text" name="dsmart_cart_color" value="<?php echo $dsmart_cart_color; ?>" class="my-color-field" data-default-color="#effeff" />
				</div>
				<div class="form-group">
					<label><?php _e('Hintergrund Farbe') ?></label>
					<input type="text" name="dsmart_cart_background" value="<?php echo $dsmart_cart_background; ?>" class="my-color-field" data-default-color="#effeff" />
				</div>
				<h2><?php _e('Shop Info-Banner'); ?></h2>
				<div class="form-group">
					<label>
						<input type="checkbox" name="show_notify" value="on" <?php echo ($show_notify == 'on') ? 'checked' : ''; ?>>
						<?php echo __("Info-Banner Anzeigen?", "booking-order"); ?>
					</label>
				</div>
				<div class="form-group">
					<label>
						<?php echo __("Banner text", "booking-order"); ?>
					</label>
					<textarea name="notify_text" rows="5" class="widefat"><?php echo $notify_text; ?></textarea>
				</div>
				<div class="form-group">
					<label>
						<?php echo __("Geschlossen Bar", "booking-order"); ?>
					</label>
					<textarea name="close_shop_text" rows="5" class="widefat"><?php echo $close_shop_text; ?></textarea>
				</div>
				<h2><?php _e('Profile'); ?></h2>
				<div class="form-group">
					<label>
						<input type="checkbox" name="show_profile" value="on" <?php echo ($show_profile == 'on') ? 'checked' : ''; ?>>
						<?php echo __("Profil Modus?", "booking-order"); ?>
					</label>
				</div>
			</div>
			<div class="tab-content" id="tab-6">
				<h2><?php _e("Email Allgemeine Informationen") ?></h2>
				<div class="form-group">
					<label><?php _e('Angezeigte Name') ?></label>
					<input type="text" name="ds_mail_name" class="widefat" placeholder="<?php _e('Angezeigte Name') ?>" value="<?php echo $ds_mail_name; ?>" autocomplete="off" />
				</div>
				<div class="form-group">
					<label><?php _e('Emailsender') ?></label>
					<input type="text" name="ds_sender_email" class="widefat" placeholder="<?php _e('Emailsender') ?>" value="<?php echo $ds_sender_email; ?>" autocomplete="off" />
				</div>
				<h2><?php _e("Email für SHOP") ?></h2>
				<div class="form-group">
					<label><?php _e('Email beim erhalt einer Bestellung') ?></label>
					<input type="text" name="dsmart_header_mail_order" class="widefat" placeholder="<?php _e('Email beim erhalt einer Bestellung') ?>" value="<?php echo $dsmart_header_mail_order; ?>" autocomplete="off" />
					<textarea name="dsmart_text_mail_order" rows="5" class="widefat"><?php echo $dsmart_text_mail_order; ?></textarea>
				</div>
				<h2><?php _e("Email für kunden") ?></h2>
				<div class="form-group">
					<label><?php _e('Email an Kunden beim erfolgreichen Bestellung') ?></label>
					<input type="text" name="dsmart_header_mail_order_cs" class="widefat" placeholder="<?php _e('Email an Kunden beim erfolgreichen Bestellung') ?>" value="<?php echo $dsmart_header_mail_order_cs; ?>" autocomplete="off" />
					<textarea name="dsmart_text_mail_order_cs" rows="5" class="widefat"><?php echo $dsmart_text_mail_order_cs; ?></textarea>
				</div>
				<div class="form-group">
					<label><?php _e('Email an Kunden, wenn Bestellung fertiggestellt ist') ?></label>
					<input type="text" name="dsmart_header_mail_success_cs" class="widefat" placeholder="<?php _e('Email an Kunden, wenn Bestellung fertiggestellt ist') ?>" value="<?php echo $dsmart_header_mail_success_cs; ?>" autocomplete="off" />
					<textarea name="dsmart_text_mail_success_cs" rows="5" class="widefat"><?php echo $dsmart_text_mail_success_cs; ?></textarea>
				</div>
				<div class="form-group">
					<label><?php _e('Email an Kunden, wenn Bestellung stoniert wird') ?></label>
					<input type="text" name="dsmart_header_mail_cancel_cs" class="widefat" placeholder="<?php _e('Email an Kunden, wenn Bestellung stoniert wird') ?>" value="<?php echo $dsmart_header_mail_cancel_cs; ?>" autocomplete="off" />
					<textarea name="dsmart_text_mail_cancel_cs" rows="5" class="widefat"><?php echo $dsmart_text_mail_cancel_cs; ?></textarea>
				</div>
			</div>
			<div class="tab-content" id="tab-7">
				<div class="form-group">
					<h2><?php echo __("Direkter Rabatt Abzug", "booking-order"); ?></h2>
					<div class="radio-wrap">
						<label>
							<input type="checkbox" name="discount_cod" value="on" <?php echo ($discount_cod == 'on') ? 'checked' : ''; ?>>
							<?php echo __("Lieferung", "booking-order"); ?>
						</label>
						<label>
							<input type="checkbox" name="discount_shop" value="on" <?php echo ($discount_shop == 'on') ? 'checked' : ''; ?>>
							<?php echo __("Abholung", "booking-order"); ?>
						</label>
					</div>
				</div>
				<div class="form-group">
					<h2><?php echo __("Mindesbestellwert (für direkt Rabatt)", "booking-order"); ?></h2>
					<div class="radio-wrap">
						<label>
							<input type="number" class="widefat" name="discount_min" value="<?php echo $discount_min ?>" >
						</label>
					</div>
				</div>
				<div id="rabatt_delivery_type" <?php if ($discount_cod !== "on") {echo 'style="display:none"';}?> class="form-group">
					<h2><?php echo __("Rabatt Lieferung", "booking-order"); ?></h2>
					<div class="radio-wrap">
						<label>
							<input type="radio" name="type_promotion" value="%" <?php echo ($type_promotion == '%' || $type_promotion == '') ? 'checked' : ''; ?>>
							<?php echo __("Prozente", "booking-order"); ?>
						</label>
						<label>
							<input type="radio" name="type_promotion" value="number" <?php echo ($type_promotion == 'number') ? 'checked' : ''; ?>>
							<?php echo __("Nummer", "booking-order"); ?>
						</label>
					</div>
					<input type="number" class="widefat" name="promotion" value="<?php echo $promotion; ?>">
				</div>
				<div id="rabatt_in_shop_type" <?php if ($discount_shop !== "on") {echo 'style="display:none"';}?> class="form-group">
					<h2><?php echo __("Rabatt Abholung", "booking-order"); ?></h2>
					<div class="radio-wrap">
						<label>
							<input type="radio" name="type_promotion_2" value="%" <?php echo ($type_promotion_2 == '%' || $type_promotion_2 == '') ? 'checked' : ''; ?>>
							<?php echo __("Prozente", "booking-order"); ?>
						</label>
						<label>
							<input type="radio" name="type_promotion_2" value="number" <?php echo ($type_promotion_2 == 'number') ? 'checked' : ''; ?>>
							<?php echo __("Nummer", "booking-order"); ?>
						</label>
					</div>
					<input type="number" class="widefat" name="promotion_2" value="<?php echo $promotion_2; ?>">
				</div>
				<div id="discount-group" <?php if ($discount_cod !== "on") {echo 'style="display:none"';}?> class="form-group timepicker-group rabatt_delivery_time">
					<h4><?php _e('Rabattzeit Lieferung') ?></h4>
					<label id="discount-mon">
						<span class="day-of-week"><?php _e("Montag:") ?></span>
						<input type="text" name="time_discount_shop_mo" id="time_discount_shop_mo" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_mo; ?>" autocomplete="off" />
						<button type="button" class="add-time button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
					<label id="discount-tu">
						<span class="day-of-week"><?php _e("Dienstag:") ?></span>
						<input type="text" name="time_discount_shop_tu" id="time_discount_shop_tu" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_tu; ?>" autocomplete="off" />
						<button type="button" class="add-time button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
					<label id="discount-we">
						<span class="day-of-week"><?php _e("Mittwoch:") ?></span>
						<input type="text" name="time_discount_shop_we" id="time_discount_shop_we" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_we; ?>" autocomplete="off" />
						<button type="button" class="add-time button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
					<label id="discount-th">
						<span class="day-of-week"><?php _e("Donnerstag:") ?></span>
						<input type="text" name="time_discount_shop_th" id="time_discount_shop_th" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_th; ?>" autocomplete="off" />
						<button type="button" class="add-time button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
					<label id="discount-fr">
						<span class="day-of-week"><?php _e("Freitag:") ?></span>
						<input type="text" name="time_discount_shop_fr" id="time_discount_shop_fr" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_fr; ?>" autocomplete="off" />
						<button type="button" class="add-time button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
					<label id="discount-sa">
						<span class="day-of-week"><?php _e("Samstag:") ?></span>
						<input type="text" name="time_discount_shop_sa" id="time_discount_shop_sa" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_sa; ?>" autocomplete="off" />
						<button type="button" class="add-time button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
					<label id="discount-su">
						<span class="day-of-week"><?php _e("Sonntag:") ?></span>
						<input type="text" name="time_discount_shop_su" id="time_discount_shop_su" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_su; ?>" autocomplete="off" />
						<button type="button" class="add-time button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
				</div>
				<div id="discount-group-2" <?php if ($discount_shop !== "on") {echo 'style="display:none"';}?> class="form-group timepicker-group rabatt_in_shop_time">
					<h4><?php _e('Rabattzeit Abholung') ?></h4>
					<label id="discount-mon-2">
						<span class="day-of-week"><?php _e("Montag:") ?></span>
						<input type="text" name="time_discount_shop_2_mo" id="time_discount_shop_2_mo" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_2_mo; ?>" autocomplete="off" />
						<button type="button" class="add-time-2 button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
					<label id="discount-tu-2">
						<span class="day-of-week"><?php _e("Dienstag:") ?></span>
						<input type="text" name="time_discount_shop_2_tu" id="time_discount_shop_2_tu" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_2_tu; ?>" autocomplete="off" />
						<button type="button" class="add-time-2 button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
					<label id="discount-we-2">
						<span class="day-of-week"><?php _e("Mittwoch:") ?></span>
						<input type="text" name="time_discount_shop_2_we" id="time_discount_shop_2_we" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_2_we; ?>" autocomplete="off" />
						<button type="button" class="add-time-2 button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
					<label id="discount-th-2">
						<span class="day-of-week"><?php _e("Donnerstag:") ?></span>
						<input type="text" name="time_discount_shop_2_th" id="time_discount_shop_2_th" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_2_th; ?>" autocomplete="off" />
						<button type="button" class="add-time-2 button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
					<label id="discount-fr-2">
						<span class="day-of-week"><?php _e("Freitag:") ?></span>
						<input type="text" name="time_discount_shop_2_fr" id="time_discount_shop_2_fr" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_2_fr; ?>" autocomplete="off" />
						<button type="button" class="add-time-2 button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
					<label id="discount-sa-2">
						<span class="day-of-week"><?php _e("Samstag:") ?></span>
						<input type="text" name="time_discount_shop_2_sa" id="time_discount_shop_2_sa" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_2_sa; ?>" autocomplete="off" />
						<button type="button" class="add-time-2 button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
					<label id="discount-su-2">
						<span class="day-of-week"><?php _e("Sonntag:") ?></span>
						<input type="text" name="time_discount_shop_2_su" id="time_discount_shop_2_su" class="widefat multi_timepicker" placeholder="<?php _e('Gültige Rabattzeiten') ?>" value="<?php echo $time_discount_shop_2_su; ?>" autocomplete="off" />
						<button type="button" class="add-time-2 button button-primary"><?php _e('Rabatt Zeit einfügen', 'dsmart'); ?></button>
					</label>
				</div>
				<div id="custom-discount-group" class="form-group timepicker-group table-custom-date">
					<h4><?php _e('Benutzerdefinierte Rabatt Zeit') ?></h4>
					<h5><?php _e('Feiertage importieren:') ?></h5>

				    <div class="row">
						<select name="holiday_year" class="widefat">
						<?php 
							for($i = 2024; $i <= 2070; $i++){
						?>
								<option value="<?php echo $i ?>"><?php echo $i ?></option>
						<?php
						}
						?>
						</select>

						<select name="holiday_region" class="widefat">
						<?php 
							$region = array(
								"de" => "Deutschland",
								"de-bw" => "Baden-Württemberg",
								"de-by" => "Bayern",
								"de-be" => "Berlin",
								"de-bb" => "Brandenburg",
								"de-hb" => "Bremen",
								"de-hh" => "Hamburg",
								"de-he" => "Hessen",
								"de-mv" => "Mecklenburg-Vorpommern",
								"de-ni" => "Niedersachsen",
								"de-nw" => "Nordrhein-Westfalen",
								"de-rp" => "Rheinland-Pfalz",
								"de-sl" => "Saarland",
								"de-sn" => "Sachsen",
								"de-st" => "Sachsen-Anhalt",
								"de-sh" => "Schleswig-Holstein",
								"de-th" => "Thüringen",
							);

							foreach ($region as $key => $label) {
							
						?>
								<option value="<?php echo $key ?>"><?php echo $label ?></option>
						<?php
						}
						?>
						</select>

						<button type="button" class="button import-holiday"><?php _e("Importieren") ?></button>
					</div>
					<table class="table">
						<thead>
							<tr>
								<th><?php _e("Datum") ?></th>
								<th><?php _e("Rabatt Zeit") ?></th>
								<th><?php _e("Zeit hinzufügen") ?></th>
								<th><?php _e("Action") ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if ($dsmart_custom_discount_date != "") {
								$index = 1;
								foreach ($dsmart_custom_discount_date as $item) { ?>
									<tr id="custom-time<?php echo $index; ?>">
										<td><input type="text" name="custom_discount_date[]" class="widefat datepicker" value="<?php echo $item['date']; ?>" autocomplete="off" required /></td>
										<td><input type="text" name="custom_discount_time[]" class="widefat multi_timepicker" value="<?php echo $item['time'] ?>" autocomplete="off" required /></td>
										<td><button type="button" class="add-time button button-primary"><?php _e('Neue Gültige Rabattzeiten', 'dsmart'); ?></button></td>
										<td><span class="button remove-row"><?php _e("Löschen") ?></span></td>
									</tr>
							<?php $index++;
								}
							} ?>
							<tr class="hidden-field">
								<td><input type="text" name="custom_discount_date[]" autocomplete="off" class="widefat datepicker start_date_picker" /></td>
								<td><input type="text" name="custom_discount_time[]" class="widefat multi_timepicker" value="" autocomplete="off" required /></td>
								<td><button type="button" class="add-time button button-primary"><?php _e('Neue Gültige Rabattzeiten', 'dsmart'); ?></button></td>
								<td><span class="button remove-row"><?php _e("Löschen") ?></span></td>
							</tr>
						</tbody>
					</table>
					<button type="button" class="button add-new-row"><?php _e("Rabatt Zeit einfügen") ?></button>
				</div>
			</div>
			<div class="tab-content" id="tab-8">
				<div class="form-group">
					<h2><?php _e("PLZ Funktion"); ?></h2>
					<select name="zipcode_status" class="widefat">
						<option value="on" <?php echo ($zipcode_status == "on") ? 'selected' : ''; ?>><?php _e("An"); ?></option>
						<option value="off" <?php echo ($zipcode_status != "on") ? 'selected' : ''; ?>><?php _e("Aus"); ?></option>
					</select>
				</div>
				<div class="form-group">
					<h2><?php _e("Link für Shortcode"); ?></h2>
					<input type="text" class="widefat" name="next_link_shortcode" value="<?php echo $next_link_shortcode; ?>" />
				</div>
				<div class="form-group">
					<table class="table table-zipcode">
						<thead>
							<tr>
								<th><?php _e("PLZ") ?></th>
								<th><?php _e("Mindestbestellwert") ?></th>
								<th><?php _e("Lieferkosten") ?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php if ($zipcode_data != "") {
								foreach ($zipcode_data as $item) { ?>
									<tr>
										<td><input type="text" name="zipcode[]" class="widefat" value="<?php echo $item['zipcode']; ?>" autocomplete="off" required /></td>
										<td><input type="text" name="minium_order[]" class="widefat" value="<?php echo $item['minium_order'] ?>" autocomplete="off" required /></td>
										<td><input type="text" name="zipcode_price[]" class="widefat" value="<?php echo $item['price'] ?>" autocomplete="off" required /></td>
										<td><span class="button remove-row"><?php _e("Löschen") ?></span></td>
									</tr>
							<?php }
							} ?>
						</tbody>
					</table>
					<button type="button" class="button add-new-row-zipcode"><?php _e("Neue Zeile hinzufugen") ?></button>
				</div>
			</div>
			<?php if (in_array('administrator', (array) $current_user->roles)) { ?>
				<div class="tab-content" id="tab-9">
					<div class="form-group">
						<h2><?php _e("Automatisch Bestellungen Löschen"); ?></h2>
						<div class="form-group">
							<label><input type="radio" name="ds_auto_delete_order" value="on" <?php if ($ds_auto_delete_order == "on") {
																									echo 'checked';
																								} ?>><?php _e('An') ?></label>
							<label><input type="radio" name="ds_auto_delete_order" value="off" <?php if ($ds_auto_delete_order == "off" || $ds_auto_delete_order == "") {
																									echo 'checked';
																								} ?>><?php _e('Aus') ?></label>
						</div>
						<p>in Tagen: <input type="number" name="order_date" id="order-date" value="<?php echo $order_date; ?>" /> tage</p>
						<h2><?php _e("Delete all orders"); ?></h2>
						<button type="button" class="button button-primary delete-all-order">Alle Löschen</button>
					</div>
				</div>
			<?php } ?>

			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$('.my-color-field').wpColorPicker();
					$(".ds-list-tab li a").on("click", function(e) {
						e.preventDefault();
						var href = $(this).attr("href");
						$(this).parents("li").addClass("active").siblings("li").removeClass("active");
						$(".tab-content").removeClass("active");
						$(href).addClass("active");
					})
				});
			</script>
			<button class="button button-primary button-large" name="dsmart-submit" type="submit"><?php _e('Speichern'); ?></button>
		</form>
		<div class="add-time-form">
			<h4 class="text-center"><?php _e('Rabattzeit hinzufügen', 'dsmart'); ?></h4>
			<p class="message hidden"></p>
			<input type="hidden" name="object" class="object" value="">
			<input type="text" name="discount_form_mo" class="timepicker" value="" placeholder="<?php _e('Rabatt strart time', 'dsmart'); ?>">
			<input type="text" name="discount_to_mo" class="timepicker" value="" placeholder="<?php _e('Rabatt end time', 'dsmart'); ?>">
			<button type="button" class="button button-primary btn-ok"><?php _e('Ok', 'dsmart'); ?></button>
			<button type="button" class="button button-primary btn-cancel"><?php _e('Close', 'dsmart'); ?></button>
		</div>
		<div class="add-time-form-2">
			<h4 class="text-center"><?php _e('Rabattzeit hinzufügen', 'dsmart'); ?></h4>
			<p class="message hidden"></p>
			<input type="hidden" name="object" class="object" value="">
			<input type="text" name="discount_form_mo" class="timepicker" value="" placeholder="<?php _e('Rabatt strart time', 'dsmart'); ?>">
			<input type="text" name="discount_to_mo" class="timepicker" value="" placeholder="<?php _e('Rabatt end time', 'dsmart'); ?>">
			<button type="button" class="button button-primary btn-ok"><?php _e('Ok', 'dsmart'); ?></button>
			<button type="button" class="button button-primary btn-cancel"><?php _e('Close', 'dsmart'); ?></button>
		</div>
	</div>
<?php }
function list_shop()
{
	global $wpdb;
	if (isset($_POST['edit-shop'])) {
		global $wpdb;
		$id_shop = stripslashes_deep($_POST['id_shop']);
		$name = stripslashes_deep($_POST['shop_name']);
		$latitude = $_POST['latitude'];
		$longitude = $_POST['longitude'];
		$email = $_POST['shop_email'];
		$address = stripslashes_deep($_POST['shop_address']);
		$tablename = $wpdb->prefix . "shop_address";
		$check = $wpdb->get_var("SELECT COUNT(*) FROM {$tablename} WHERE id = " . $id_shop);
		if ($check > 0  && $latitude != "" && $longitude != "") {
			$wpdb->update($tablename, array('shop_name' => $name, 'email' => $email, 'shop_address' => $address, 'latitude' => $latitude, 'longitude' => $longitude), array('id' => $id_shop));
			$notify = __('Die Shop-Adresse wurde geändert.');
			$check_notify = "1";
		} elseif ($latitude == "" || $longitude == "") {
			$notify =  __('Die Shop-Adresse ist bei Google nicht vorhanden.');
			$check_notify = "0";
		} else {
			$notify =  __('Shop-Adresse existiert nicht.');
			$check_notify = "0";
		}
	}
	if (isset($_POST['delete-shop'])) {
		global $wpdb;
		$id_shop = stripslashes_deep($_POST['id_shop']);
		$tablename = $wpdb->prefix . "shop_address";
		$check = $wpdb->get_var("SELECT COUNT(*) FROM {$tablename} WHERE id = " . $id_shop);
		if ($check > 0) {
			$wpdb->delete($tablename, array('id' => $id_shop));
			$notify = __('Shop address has been deleted.');
			$check_notify = "1";
		} else {
			$notify =  __('Shop address not exists.');
			$check_notify = "0";
		}
	} ?>
	<div class="wrap">
		<?php if (!isset($_GET['id-delete']) && !isset($_GET['id-edit'])) : ?>
			<h1><?php _e('Shop') ?></h1>
			<?php if (isset($check_notify) && $check_notify == "0") { ?>
				<p class="dsmart-error"><?php echo $notify ?></p>
			<?php } elseif (isset($check_notify) && $check_notify == "1") { ?>
				<p class="dsmart-success"><?php echo $notify ?></p>
			<?php } ?>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php _e('Shop name') ?></th>
						<th><?php _e('Shop addresse') ?></th>
						<th><?php _e('Email') ?></th>
						<th><?php _e('Action'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$tablename = $wpdb->prefix . "shop_address";
					$data = $wpdb->get_results("SELECT * FROM {$tablename}");
					if ($data) {
						foreach ($data as $item) { ?>
							<tr>
								<td><?php echo $item->shop_name; ?></td>
								<td><?php echo $item->shop_address; ?></td>
								<td><?php echo $item->email; ?></td>
								<td>
									<a href="<?php echo ds_merge_querystring(remove_query_arg(array('paged', 'id-delete', 'id-edit')), '?id-edit=' . $item->id); ?>" class="button button-primary button-large" name="edit-shop"><?php _e('Bearbeiten'); ?></a>
								</td>
							</tr>
					<?php }
					} ?>
				</tbody>
			</table>
		<?php elseif (isset($_GET['id-edit']) && $_GET['id-edit'] != "") : ?>
			<h1><?php _e('Edit shop') ?></h1>
			<?php if (isset($check_notify) && $check_notify == "0") { ?>
				<p class="dsmart-error"><?php echo $notify ?></p>
			<?php } elseif (isset($check_notify) && $check_notify == "1") { ?>
				<p class="dsmart-success"><?php echo $notify ?></p>
			<?php }
			$id_edit = $_GET['id-edit'];
			$tablename = $wpdb->prefix . "shop_address";
			$data = $wpdb->get_row("SELECT * FROM {$tablename} WHERE id = " . $id_edit); ?>
			<form action="#" class="dsmart-form" method="POST">
				<div class="form-group">
					<label><?php _e('Shop Name') ?></label>
					<input type="text" name="shop_name" class="widefat" placeholder="<?php _e('Shop Name') ?>" value="<?php echo $data->shop_name; ?>" required />
				</div>
				<div class="form-group">
					<label><?php _e('Shop Adresse') ?></label>
					<input type="text" name="shop_address" id="shop-address" class="widefat" placeholder="<?php _e('Shop Adresse') ?>" value="<?php echo $data->shop_address; ?>" autocomplete="off" required />
				</div>
				<div class="form-group">
					<label><?php _e('Email-Addresse') ?></label>
					<input type="text" name="shop_email" class="widefat" placeholder="<?php _e('Email-Adresse') ?>" value="<?php echo $data->email; ?>" autocomplete="new-password" required />
				</div>
				<input type="hidden" name="latitude" value="<?php echo $data->latitude; ?>" />
				<input type="hidden" name="longitude" value="<?php echo $data->longitude; ?>" />
				<input type="hidden" name="id_shop" value="<?php echo $data->id; ?>">
				<button class="button button-primary button-large" name="edit-shop" type="submit"><?php _e('Speichern'); ?></button>
			</form>
			<script>
				function initMap() {
					var startLocation = document.getElementById('shop-address');
					new google.maps.places.Autocomplete(startLocation);
					var geocoder = new google.maps.Geocoder();
					autocomplete = new google.maps.places.Autocomplete(jQuery("#shop-address").get(0));
					google.maps.event.addListener(autocomplete, 'place_changed', function() {
						var address = jQuery("#shop-address").val();
						geocoder.geocode({
							'address': address
						}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
								var latitude = results[0].geometry.location.lat();
								var longitude = results[0].geometry.location.lng();
								jQuery("input[name=latitude]").val(latitude);
								jQuery("input[name=longitude]").val(longitude);
							}
						});
					});
				}
			</script>
			<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_google_map_key(); ?>&libraries=places&callback=initMap" async defer></script>
		<?php elseif (isset($_GET['id-delete']) && $_GET['id-delete'] != "") : ?>
			<h1><?php _e('Delete shop') ?></h1>
			<?php if (isset($check_notify) && $check_notify == "0") { ?>
				<p class="dsmart-error"><?php echo $notify ?></p>
			<?php } elseif (isset($check_notify) && $check_notify == "1") { ?>
				<p class="dsmart-success"><?php echo $notify ?></p>
			<?php }
			$id_delete = $_GET['id-delete'];
			$tablename = $wpdb->prefix . "shop_address";
			$data = $wpdb->get_row("SELECT * FROM {$tablename} WHERE id = " . $id_delete); ?>
			<form action="<?php echo ds_merge_querystring(remove_query_arg(array('paged', 'id-delete', 'id-edit'))); ?>" class="dsmart-form" method="POST">
				<p><?php _e('Do you want to delete shop "' . $data->shop_name . '" with address ' . $data->shop_address . '?') ?></p>
				<input type="hidden" name="id_shop" value="<?php echo $data->id; ?>">
				<button class="button button-primary button-large" name="delete-shop" type="submit"><?php _e('Delete'); ?></button>
			</form>
		<?php endif; ?>
	</div>
<?php }
function add_address_shop()
{
	if (isset($_POST['add-new-shop'])) {
		global $wpdb;
		$name = $_POST['shop_name'];
		$address = $_POST['shop_address'];
		$latitude = $_POST['latitude'];
		$longitude = $_POST['longitude'];
		$time = date('Y-m-d H:i:s');
		$tablename = $wpdb->prefix . "shop_address";
		$check = $wpdb->get_var("SELECT COUNT(*) FROM {$tablename} WHERE shop_name LIKE '" . $address . "' AND shop_address LIKE '" . $name . "'");
		if ($check == 0 && $latitude != "" && $longitude != "") {
			$wpdb->insert($tablename, array('shop_name' => $name, 'shop_address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'time' => $time));
			$notify = __('Added shop.');
			$check_notify = "1";
		} elseif ($latitude == "" || $longitude == "") {
			$notify =  __('Shop address not exists.');
			$check_notify = "0";
		} else {
			$notify =  __('Shop address exists.');
			$check_notify = "0";
		}
	} ?>
	<div class="wrap">
		<h1><?php _e('Add new shop') ?></h1>
		<?php if (isset($check_notify) && $check_notify == "0") { ?>
			<p class="dsmart-error"><?php echo $notify ?></p>
		<?php } elseif (isset($check_notify) && $check_notify == "1") { ?>
			<p class="dsmart-success"><?php echo $notify ?></p>
		<?php } ?>
		<form action="#" class="dsmart-form" method="POST">
			<div class="form-group">
				<label><?php _e('Shop name') ?></label>
				<input type="text" name="shop_name" class="widefat" placeholder="<?php _e('Shop name') ?>" required />
			</div>
			<div class="form-group">
				<label><?php _e('Shop address') ?></label>
				<input type="text" name="shop_address" id="shop-address" class="widefat" placeholder="<?php _e('Shop address') ?>" autocomplete="off" required />
			</div>
			<input type="hidden" name="latitude" />
			<input type="hidden" name="longitude" />
			<button class="button button-primary button-large" name="add-new-shop" type="submit"><?php _e('Add new'); ?></button>
		</form>
	</div>
	<script>
		function initMap() {
			var startLocation = document.getElementById('shop-address');
			new google.maps.places.Autocomplete(startLocation);
			var geocoder = new google.maps.Geocoder();
			autocomplete = new google.maps.places.Autocomplete(jQuery("#shop-address").get(0));
			google.maps.event.addListener(autocomplete, 'place_changed', function() {
				var address = jQuery("#shop-address").val();
				geocoder.geocode({
					'address': address
				}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						var latitude = results[0].geometry.location.lat();
						var longitude = results[0].geometry.location.lng();
						jQuery("input[name=latitude]").val(latitude);
						jQuery("input[name=longitude]").val(longitude);
					}
				});
			});
		}
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_google_map_key(); ?>&libraries=places&callback=initMap" async defer></script>
	<?php }

function list_review()
{
	global $wpdb;
	if (isset($_POST['edit-rating'])) {
		$id_review = stripslashes_deep($_POST['id_review']);
		$comment = stripslashes_deep($_POST['comment']);
		$rating = stripslashes_deep($_POST['rating']);
		$tablename = $wpdb->prefix . "rating_product";
		$check = $wpdb->get_row("SELECT * FROM {$tablename} WHERE id = " . $id_review);
		if (count($check) > 0) {
			$wpdb->update($tablename, array('comment' => $comment, 'rating' => $rating), array('id' => $id_review));
			$notify = __('Review has been changed.');
			$check_notify = "1";
			$post_id = $check->post_id;
			$data = $wpdb->get_results("SELECT * FROM {$tablename} WHERE post_id = " . $post_id);
			$total = 0;
			$avg = 0;
			if ($data) {
				foreach ($data as $item) {
					$rating = intval($item->rating);
					$total = $total + $rating;
				}
				$avg = $total / count($data);
			}
			update_post_meta($post_id, 'avg_rating', $avg);
		} else {
			$notify =  __('review not exists.');
			$check_notify = "0";
		}
	}
	if (isset($_POST['delete-rating'])) {
		$id_review = stripslashes_deep($_POST['id_review']);
		$tablename = $wpdb->prefix . "rating_product";
		$check = $wpdb->get_row("SELECT * FROM {$tablename} WHERE id = " . $id_review);
		if (count($check) > 0) {
			$wpdb->delete($tablename, array('id' => $id_review));
			$notify = __('Review has been deleted.');
			$check_notify = "1";
			$post_id = $check->post_id;
			$data = $wpdb->get_results("SELECT * FROM {$tablename} WHERE post_id = " . $post_id);
			$total = 0;
			$avg = 0;
			if ($data) {
				foreach ($data as $item) {
					$rating = intval($item->rating);
					$total = $total + $rating;
				}
				$avg = $total / count($data);
			}
			update_post_meta($post_id, 'avg_rating', $avg);
		} else {
			$notify =  __('Review not exists.');
			$check_notify = "0";
		}
	}
	$tablename = $wpdb->prefix . "rating_product";
	if (isset($_GET['id-edit']) && $_GET['id-edit'] != "") { ?>
		<div class="wrap">
			<h1><?php _e("Edit review") ?></h1>
			<?php if (isset($check_notify) && $check_notify == "0") { ?>
				<p class="dsmart-error"><?php echo $notify ?></p>
			<?php } elseif (isset($check_notify) && $check_notify == "1") { ?>
				<p class="dsmart-success"><?php echo $notify ?></p>
			<?php }
			$id_edit = $_GET['id-edit'];
			$tablename = $wpdb->prefix . "rating_product";
			$data = $wpdb->get_row("SELECT * FROM {$tablename} WHERE id = " . $id_edit); ?>
			<form action="<?php echo ds_merge_querystring(remove_query_arg(array('paged', 'id-delete', 'id-edit'))); ?>" class="dsmart-form" method="POST">
				<div class="form-group">
					<label></label>
					<textarea name="comment" class="widefat" required><?php echo $data->comment ?></textarea>
				</div>
				<div class="form-group">
					<input type="number" name="rating" class="widefat" required min="1" max="5" value="<?php echo $data->rating; ?>" />
				</div>
				<input type="hidden" name="id_review" value="<?php echo $data->id; ?>">
				<button class="button button-primary button-large" name="edit-rating" type="submit"><?php _e('Update'); ?></button>
			</form>
		</div>
	<?php } elseif (isset($_GET['id-delete']) && $_GET['id-delete'] != "") { ?>
		<div class="wrap">
			<h1><?php _e("Delete rating") ?></h1>
			<?php if (isset($check_notify) && $check_notify == "0") { ?>
				<p class="dsmart-error"><?php echo $notify ?></p>
			<?php } elseif (isset($check_notify) && $check_notify == "1") { ?>
				<p class="dsmart-success"><?php echo $notify ?></p>
			<?php }
			$id_delete = $_GET['id-delete'];
			$tablename = $wpdb->prefix . "rating_product";
			$data = $wpdb->get_row("SELECT * FROM {$tablename} WHERE id = " . $id_delete); ?>
			<form action="<?php echo ds_merge_querystring(remove_query_arg(array('paged', 'id-delete', 'id-edit'))); ?>" class="dsmart-form" method="POST">
				<p><?php _e('Do you want to delete review "' . $data->comment . '"?') ?></p>
				<input type="hidden" name="id_review" value="<?php echo $data->id; ?>">
				<button class="button button-primary button-large" name="delete-rating" type="submit"><?php _e('Delete'); ?></button>
			</form>
		</div>
	<?php } else {
		$where = "";
		if (isset($_GET['paged']) && $_GET['paged'] != "") {
			$paged = intval($_GET['paged']);
			$start = intval($_GET['paged']) - 1;
			$limit = " LIMIT " . $start . ",10";
		} else {
			$limit = " LIMIT 0,10";
			$paged = 1;
		}
		if (isset($_GET['user_id']) && $_GET['user_id'] != "") {
			$get_user_id = $_GET['user_id'];
			if ($where == "") {
				$where .= " WHERE user_id = " . $get_user_id;
			} else {
				$where .= " AND user_id = " . $get_user_id;
			}
		} else {
			$get_user_id = "";
		}
		if (isset($_GET['post_id']) && $_GET['post_id'] != "") {
			$get_post_id = $_GET['post_id'];
			if ($where == "") {
				$where .= " WHERE post_id = " . $get_post_id;
			} else {
				$where .= " AND post_id = " . $get_post_id;
			}
		} else {
			$get_post_id = "";
		}
		if ($where != "") {
			$data = $wpdb->get_results("SELECT * FROM {$tablename} " . $where . $limit);
			$total = $wpdb->get_results("SELECT id FROM {$tablename} " . $where);
		} else {
			$data = $wpdb->get_results("SELECT * FROM {$tablename} " . $limit);
			$total = $wpdb->get_results("SELECT id FROM {$tablename}");
		}
		$count_data = count($data);
		$count_total = count($total);
		$count = ceil($count_total / 10);
		$count = intval($count); ?>
		<div class="wrap">
			<h1><?php _e("List review") ?></h1>
			<?php if (isset($check_notify) && $check_notify == "0") { ?>
				<p class="dsmart-error"><?php echo $notify ?></p>
			<?php } elseif (isset($check_notify) && $check_notify == "1") { ?>
				<p class="dsmart-success"><?php echo $notify ?></p>
			<?php } ?>
			<div class="tablenav">
				<div class="actions">
					<form method="GET" action="<?php echo ds_merge_querystring(remove_query_arg(array('paged', 'id-delete', 'id-edit'))); ?>">
						<select name="user_id">
							<option value=""><?php _e("All user"); ?></option>
							<?php $users = get_users(array('fields' => array('ID')));
							foreach ($users as $user_id) {
								if ($user_id->ID == intval($get_user_id)) {
									$select = "selected";
								} else {
									$select = "";
								}
								$userdata = get_userdata($user_id->ID);
								echo '<option value="' . $user_id->ID . '" ' . $select . '>' . $userdata->user_login . '( ID : ' . $user_id->ID . ' )' . '</option>';
							} ?>
						</select>
						<select name="post_id">
							<option value=""><?php _e("All product"); ?></option>
							<?php $tablename2 = $wpdb->prefix . "posts";
							$data_post = $wpdb->get_results("SELECT ID,post_title FROM {$tablename2} WHERE post_type = 'product' AND post_status = 'publish'");
							if ($data_post) {
								foreach ($data_post as $data_item) {
									if ($data_item->ID == intval($get_post_id)) {
										$select = "selected";
									} else {
										$select = "";
									}
									echo '<option value="' . $data_item->ID . '" ' . $select . '>' . $data_item->post_title . '( ID : ' . $data_item->ID . ' )' . '</option>';
								}
							} ?>
						</select>
						<input type="hidden" name="page" value="list-review" />
						<button type="submit" class="button button-large"><?php _e("Filter") ?></button>
					</form>
				</div>
			</div>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php _e('Product') ?></th>
						<th><?php _e('User') ?></th>
						<th><?php _e('Review'); ?></th>
						<th><?php _e('Rating star'); ?></th>
						<th><?php _e('Action'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($data) {
						foreach ($data as $item) {
							$userdata  = get_userdata($item->user_id);
							if ($userdata->display_name == "")
								$display_name = $userdata->user_login;
							else
								$display_name = $userdata->display_name; ?>
							<tr>
								<td><?php echo get_the_title($item->post_id) . ' (' . $item->post_id . ')'; ?></td>
								<td><?php echo $display_name . ' (' . $item->user_id . ')'; ?></td>
								<td><?php echo $item->comment; ?></td>
								<td><?php echo $item->rating; ?></td>
								<td>
									<a href="<?php echo ds_merge_querystring(remove_query_arg(array('paged', 'id-delete', 'id-edit')), '?id-edit=' . $item->id); ?>" class="button button-primary button-large"><?php _e('Edit'); ?></a>
									<a href="<?php echo ds_merge_querystring(remove_query_arg(array('paged', 'id-delete', 'id-edit')), '?id-delete=' . $item->id); ?>" class="button button-large"><?php _e('Delete'); ?></a>
								</td>
							</tr>
					<?php }
					} ?>
				</tbody>
			</table>
			<?php echo paginate_links(array(
				'base' => add_query_arg('paged', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => $count,
				'current' => $paged
			)); ?>
		</div>
	<?php }
}
function dsmart_statistics_order()
{
	$date_query = array();
	$meta_query = array();
	if (isset($_GET['date_from']) && $_GET['date_from'] != "") {
		$date_from = $_GET['date_from'];
		$date_from_data = explode('-', $date_from);
		$date_query['after'] = array(
			'year'  => $date_from_data[2],
			'month' => $date_from_data[1],
			'day'   => $date_from_data[0],
		);
	} else {
		$date_from = "";
	}
	if (isset($_GET['date_to']) && $_GET['date_to'] != "") {
		$date_to = $_GET['date_to'];
		$date_to_data = explode('-', $date_to);
		$date_query['before'] = array(
			'year'  => $date_to_data[2],
			'month' => $date_to_data[1],
			'day'   => $date_to_data[0],
		);
	} else {
		$date_to = "";
	}
	if (count($date_query) > 0) {
		$date_query['inclusive'] = true;
		$date_query['relation'] = 'AND';
	} else {
		$date_query = null;
	}
	if (isset($_GET['status']) && $_GET['status'] != "") {
		$status = $_GET['status'];
		$meta_query[] = array(
			'key'  => 'status',
			'value'		=> $status,
			'compare' => 'LIKE',
		);
	} else {
		$status = "";
	}
	if (count($meta_query) > 0) {
		$relations = array('relation' => 'AND');
	} else {
		$relations = array('relation' => 'OR');
		$meta_query = null;
	}
	global $wp_query; ?>
	<div class="wrap">
		<h1><?php _e('Bestellung Statistik'); ?></h1>
		<div class="tablenav top">
			<div class="actions alignleft">
				<form method="GET" action="<?php echo ds_merge_querystring(remove_query_arg(array('paged', 'date_from', 'date_to'))); ?>">
					<input type="text" name="date_from" class="datepicker" placeholder="<?php _e("Von") ?>" value="<?php echo $date_from ?>" autocomplete="off" />
					<input type="text" name="date_to" class="datepicker" placeholder="<?php _e("Bis") ?>" value="<?php echo $date_to ?>" autocomplete="off" />
					<select name="status">
						<option value=""><?php _e("Alle") ?></option>
						<option value="processing" <?php if ($status == "processing") {
														echo 'selected';
													} ?>><?php _e("in Bearbeitung"); ?></option>
						<option value="completed" <?php if ($status == "completed") {
														echo 'selected';
													} ?>><?php _e("Fertiggestellt"); ?></option>
						<option value="cancelled" <?php if ($status == "cancelled") {
														echo 'selected';
													} ?>><?php _e("Abgebrochen"); ?></option>
					</select>
					<input type="hidden" name="page" value="dsmart-statistics-order" />
					<input type="hidden" name="post_type" value="product" />
					<button type="submit" class="button button-large"><?php _e("Filter") ?></button>
				</form>
			</div>
			<button type="button" id="export-excel" class="button alignright"><?php _e("Export"); ?></button>
		</div>
		<div class="">
			<?php if (isset($_GET['paged']) && $_GET['paged'] != "") {
				$paged = $_GET['paged'];
			} else {
				$paged = 1;
			}
			$wp_query  = new WP_Query(array(
				'post_type'      => 'orders',
				'post_status'   => 'publish',
				'posts_per_page' => 20,
				'order'          => 'desc',
				'orderby' => 'date',
				'paged' => $paged,
				'date_query' => $date_query,
				'meta_query' => array(
					$meta_query,
					$relations
				),
			));
			if ($wp_query->have_posts()) : ?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php _e('Bestellung') ?></th>
							<th><?php _e('Name') ?></th>
							<th><?php _e('Telefon') ?></th>
							<th><?php _e('Email') ?></th>
							<th><?php _e('Gesamtsumme') ?></th>
							<th><?php _e('Status') ?></th>
							<th><?php _e('Erstelltes Datum') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php while ($wp_query->have_posts()) : $wp_query->the_post();
							$currency = dsmart_field('currency');
							$customer_name1 = dsmart_field('customer_name1');
							$customer_name2 = dsmart_field('customer_name2');
							$customer_email = dsmart_field('customer_email');
							$customer_phone = dsmart_field('customer_phone');
							$total = dsmart_field('total');
							$status = dsmart_field('status'); ?>
							<tr data-item="<?php the_ID(); ?>">
								<td><?php the_title(); ?></td>
								<td><?php echo $customer_name1 . ' ' . $customer_name2; ?></td>
								<td><?php echo $customer_phone; ?></td>
								<td><?php echo $customer_email; ?></td>
								<td><?php echo ds_price_format_text_with_symbol($total, $currency); ?></td>
								<td>
									<?php if ($status == "processing") { ?>
										<span class="processing"><?php _e("Processing") ?></span>
									<?php } elseif ($status == "cancelled") { ?>
										<span class="cancelled"><?php _e("Cancelled") ?></span>
									<?php } elseif ($status == "completed") { ?>
										<span class="completed"><?php _e("Completed") ?></span>
									<?php } ?>
								</td>
								<td><?php echo get_the_date(); ?></td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
				<div class="pagination">
					<?php
					echo paginate_links(array(
						'base'         => str_replace('#038;', '', str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999)))),
						'total'        => $wp_query->max_num_pages,
						'current'      => $paged,
						'format'       => '?paged=%#%',
						'show_all'     => false,
						'type'         => 'plain',
						'end_size'     => 2,
						'mid_size'     => 1,
						'prev_next'    => true,
						'prev_text'    => sprintf('<i></i> %1$s', __('Prev', 'text-domain')),
						'next_text'    => sprintf('%1$s <i></i>', __('Next', 'text-domain')),
						'add_args'     => false,
						'add_fragment' => '',
					));
					?>
				</div>
			<?php wp_reset_query();
			else : ?>
				<p><?php _e('No order'); ?></p>
			<?php endif; ?>
		</div>
	<?php }

function dsmart_statistics_all()
{
	$date_query = array();
	$meta_query = array();
	if (isset($_GET['date_from']) && $_GET['date_from'] != "") {
		$date_from = $_GET['date_from'];
		$date_from_data = explode('-', $date_from);
		$date_query['after'] = array(
			'year'  => $date_from_data[2],
			'month' => $date_from_data[1],
			'day'   => $date_from_data[0],
		);
	} else {
		$date_from = "";
	}
	if (isset($_GET['date_to']) && $_GET['date_to'] != "") {
		$date_to = $_GET['date_to'];
		$date_to_data = explode('-', $date_to);
		$date_query['before'] = array(
			'year'  => $date_to_data[2],
			'month' => $date_to_data[1],
			'day'   => $date_to_data[0],
		);
	} else {
		$date_to = "";
	}
	if (count($date_query) > 0) {
		$date_query['inclusive'] = true;
		$date_query['relation'] = 'AND';
	} else {
		$date_query = null;
	}
	if (isset($_GET['order-status']) && $_GET['order-status'] != "") {
		$status = $_GET['order-status'];
		$meta_query[] = array('key' => 'status', 'value' => $status, 'compare' => 'LIKE');
	} else {
		$status = "";
		$meta_query = null;
	}
	global $wp_query; ?>
		<div class="wrap">
			<h1><?php _e('Bestellung Statistik'); ?></h1>
			<div class="tablenav top">
				<div class="actions">
					<form method="GET" action="<?php echo ds_merge_querystring(remove_query_arg(array('date_from', 'date_to'))); ?>">
						<input type="text" name="date_from" class="datepicker" placeholder="<?php _e("Von") ?>" value="<?php echo $date_from ?>" autocomplete="off" />
						<input type="text" name="date_to" class="datepicker" placeholder="<?php _e("Bis") ?>" value="<?php echo $date_to ?>" autocomplete="off" />
						<select name="order-status" class="widefat">
							<option value="">Alle</option>
							<option value="completed" <?php echo ($status == "completed") ? 'selected' : ''; ?>>Fertiggestellt</option>
							<option value="processing" <?php echo ($status == "processing") ? 'selected' : ''; ?>>in Bearbeitung</option>
						</select>
						<input type="hidden" name="page" value="dsmart-statistics-all" />
						<input type="hidden" name="post_type" value="product" />
						<button type="submit" class="button button-large"><?php _e("Filter") ?></button>
					</form>
				</div>
			</div>
			<?php
			$wp_query  = new WP_Query(array(
				'post_type'      => 'orders',
				'post_status'   => 'publish',
				'posts_per_page' => -1,
				'order'          => 'desc',
				'orderby' => 'date',
				'date_query' => $date_query,
				'meta_query' => $meta_query
			));
			$total_all = 0;
			$post_count = $wp_query->post_count;
			if ($wp_query->have_posts()) :
				while ($wp_query->have_posts()) : $wp_query->the_post();
					$currency = dsmart_field('currency');
					$total = dsmart_field('total');
					$status = dsmart_field('status');
					$price_after_change = ds_convert_currency_price($currency, $total);
					$total_all = $total_all + $price_after_change;
				endwhile;
				wp_reset_query();
			endif; ?>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th></th>
						<th><?php _e('Bestellungen') ?></th>
						<th><?php _e('Gesamtsumme') ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php _e("Umsatz") ?></td>
						<td><?php _e($post_count . ' orders'); ?></td>
						<td><?php echo ds_price_format_text_with_symbol($total_all, "2"); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php }
