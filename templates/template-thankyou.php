<?php 
/*use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersAuthorizeRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;*/
if(isset($_GET['PayerID']) && isset($_GET['order']) && isset($_GET['token-success'])){
    $order_id = $_GET['order'];
    $check_token = get_post_meta($order_id,'paypal_token',true);
    if($check_token != "" && $check_token == $_GET['token-success']){
        /*$clientId = get_option('dsmart_paypal_client_id');
        $clientSecret = get_option('dsmart_paypal_client_secret');
        $dsmart_sandbox = get_option('dsmart_sandbox');
        if($dsmart_sandbox == "yes"){
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        }else{
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        }
        $client = new PayPalHttpClient($environment);
        $request = new OrdersCaptureRequest($_GET['token']);
        $request->prefer('return=representation');
        try {
            $response = $client->execute($request);
            if($response->result->status == "COMPLETED"){
                update_post_meta($order_id,'transition_id',$_GET['PayerID']);
                update_post_meta($order_id,'status','processing');
                update_post_meta($order_id,'paypal_token','');
            }
        }catch (HttpException $ex) {
            print_r($ex->getMessage());
        }*/
        update_post_meta($order_id,'transition_id',$_GET['PayerID']);
        update_post_meta($order_id,'status','processing');
        update_post_meta($order_id,'paypal_token','');
        wp_redirect(get_permalink($order_id));
        exit;
    }
}elseif(isset($_COOKIE['checkout_data']) && $_COOKIE['checkout_data'] != ""){
	$checkout_data = json_decode(stripcslashes($_COOKIE['checkout_data']),true);
	$method = $checkout_data['method'];
	if($method == "paypal" && isset($_GET['amt']) && isset($_GET['item_name']) && isset($_GET['st']) && isset($_GET['tx']) && str_replace('ORDER ', '',$_GET['item_name']) == $checkout_data['order_code']){
        $order_id = post_exists($checkout_data['order_code']);
        if($order_id != "" && $order_id > 0){
    		//$order_id = create_new_order($checkout_data,strtolower($_GET['st']),$_GET['tx']);
            update_post_meta($order_id,'transition_id',$transaction_id);
            update_post_meta($order_id,'status',$_GET['tx']);
    		wp_redirect(get_permalink($order_id));
    		exit;
        }
	}elseif($method == "klarna" && isset($_GET['token']) && $_GET['token'] != ""){
        require_once BOOKING_ORDER_PATH2  . 'klarna/autoload.php';
        $checkout_data = json_decode(stripcslashes($_COOKIE['checkout_data']),true);
        $order_id = post_exists($checkout_data['order_code']);
        if($order_id != "" && $order_id > 0){
            $dsmart_sandbox = get_option('dsmart_sandbox');
            if($dsmart_sandbox == "yes"){
                $klarna_url = EU_TEST_BASE_URL;
                $apiEndpoint = Klarna\Rest\Transport\ConnectorInterface::EU_TEST_BASE_URL;
            }else{
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
            $authorization_token = $_GET['token'];
            $data = json_decode(stripcslashes($_COOKIE['data']),true);
            try {
                $order = new Klarna\Rest\Payments\Orders($connector, $authorization_token);
                $data = $order->create($data);
                if($data['fraud_status'] == 'ACCEPTED'){
                    update_post_meta($order_id,'transition_id',$data['order_id']);
                    update_post_meta($order_id,'status','processing');
                }else{
                    update_post_meta($order_id,'transition_id',$data['order_id']);
                    update_post_meta($order_id,'status','pending');
                    //$order_id = create_new_order($checkout_data,'pending',$data['order_id']);
                }
                wp_redirect(get_permalink($order_id));
                exit;
            } catch (Exception $e) {
                echo 'Caught exception: ' . $e->getMessage() . "\n";
            }
        }else{
            wp_redirect(home_url());
            exit;
        }
	}
}else{
	wp_redirect(home_url());
	exit;
}