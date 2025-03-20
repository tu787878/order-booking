<?php 
//add to cart action
function add_to_cart(){
	if(isset($_POST['add-to-cart'])){
		$check = false;
		$id = get_the_ID();
		$status = 1;
		$pro_status = dsmart_field('status');
		$quantity = intval($_POST['quantity-input']);
		/*if($pro_status == "instock"){*/
			if(isset($_COOKIE['cart']) && $_COOKIE['cart'] != ""){
				$cart = unserialize(base64_decode($_COOKIE['cart']));
			}else{
				$cart = array();
			}
			if(count($cart) > 0){
				foreach ($cart as $key => $value) {
					if($key == $id){
						$check = true;
						$cart[$key]['quantity'] = intval($cart[$key]['quantity']) + $quantity;
					}
				}
				if($check == false){
					$cart[$id] = array('quantity' => $quantity);
				}
			}else{
				$cart[$id] = array('quantity' => $quantity);
			}
			setcookie('cart', base64_encode(serialize($cart)), time()+2592000, '/', NULL, 0);
			$status = 2;
		/*}else{
			$status = 3;
		}*/
		return $status;
	}else{
		return false;
	}
}
//add to cart notify
function add_to_cart_notify($notify = null){
	$id = get_the_ID();
	$cart_id = get_page_id_by_template('templates/cart-page.php'); 
	if($notify == 1){ ?>
		<div class="alert alert-danger"><?php _e('Ein Fehler ist aufgetreten. Bitte versuche es erneut.','dsmart'); ?></div>
	<?php }elseif($notify == 2){ ?>
		<div class="alert alert-success"><?php _e('Produkt "'.get_the_title($id).'" wurde Ihrem Warenkorb hinzugefügt.','dsmart'); ?><a class="go-to-cart" href="<?php echo get_permalink($cart_id); ?>"><?php _e('Warenkorb ansehen','dsmart') ?></a></div>
	<?php }elseif($notify == 3){ ?>
		<div class="alert alert-danger"><?php _e('Das Produkt ist ausverkauft. Bitte versuchen Sie es später noch einmal.','dsmart'); ?></div>
	<?php }
}