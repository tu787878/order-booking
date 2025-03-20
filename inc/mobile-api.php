<?php

// Test
add_action('rest_api_init', function () {

    register_rest_route('ordertcg/v1', '/manage/change_option', array(
        'methods' => 'POST',
        'callback' => 'manage_change_option'
    ));
});
function manage_change_option()
{

    // authentication 
    $data = file_get_contents('php://input');

    parse_str($data, $data);
    $code = $data["code"];
    $code = base64_decode($code);
    $arr = explode(".",$code);
    
    $check = wp_authenticate_username_password( NULL, $arr[0], $arr[1] );

    if(!is_wp_error( $check )){
        $result = array('status' => 'success', 'code'=>0, 'data'=>$data["data"]);
        update_option($data["option"], $data["data"]);

        return $result;
    }
    $result = array('status' => 'fail', 'code'=>1);
    return $result;
} 

add_action('rest_api_init', function () {

    register_rest_route('ordertcg/v1', '/manage/save_popup', array(
        'methods' => 'POST',
        'callback' => 'manage_save_popup'
    ));
});
function manage_save_popup()
{

    // authentication 
    $data = file_get_contents('php://input');

    parse_str($data, $data);
    $code = $data["code"];
    $base64 = $data["base64"];

    $code = base64_decode($code);
    $arr = explode(".",$code);
    
    $check = wp_authenticate_username_password( NULL, $arr[0], $arr[1] );

    if(!is_wp_error( $check )){
        $image_id = save_image2($base64, '');
		$pos  = strpos($base64, ';');
		$type = explode(':', substr($base64, 0, $pos))[1];
        $output = "";
        if (intval($image_id) > 0) {
            update_option('ds_popup', $image_id);
            $url = wp_get_attachment_image_src( $image_id, 'medium', false );
            $output = is_ssl() ? preg_replace( "^http:", "https:", $url[0] ) : $url[0] ;
            $image = '<img id="myprefix-preview-image-popup" src="' . $output . '" />';
        } else {
			$image_id2 = get_option('ds_popup');
            $url = wp_get_attachment_image_src( $image_id2, 'medium', false );
            $output = is_ssl() ? preg_replace( "^http:", "https:", $url[0] ) : $url[0] ;
            $image = '<img id="myprefix-preview-image-popup" src="' . $output . '" />';
        }
        $result = array('status' => 'success', 'code'=>0, 'data' => $image, 'debug'=>explode("/", $type)[1]);
        return $result;
    }
    $result = array('status' => 'fail', 'code'=>1);
    return $result;
}

add_action('rest_api_init', function () {

    register_rest_route('ordertcg/v1', '/manage/get_version_plugin', array(
        'methods' => 'GET',
        'callback' => 'manage_get_version_plugin'
    ));
});
function manage_get_version_plugin()
{

    // authentication 
    $code = $_GET['code'];

    $code = base64_decode($code);
    $arr = explode(".",$code);
    
    $check = wp_authenticate_username_password( NULL, $arr[0], $arr[1] );

    if(!is_wp_error( $check )){
        $result = array('status' => 'success', 'code'=>0, 'data' => getVersion());
        return $result;
    }

    $result = array('status' => 'fail', 'code'=>1);
    return $result;
}

// Test
add_action('rest_api_init', function () {

    register_rest_route('ordertcg/v1', '/manage/get_popup', array(
        'methods' => 'GET',
        'callback' => 'manage_get_popup'
    ));
});
function manage_get_popup()
{

    // authentication 
    $code = $_GET['code'];
    $option = $_GET['option'];

    $code = base64_decode($code);
    $arr = explode(".",$code);
    
    $check = wp_authenticate_username_password( NULL, $arr[0], $arr[1] );

    if(!is_wp_error( $check )){
        $image_id = get_option($option);
        $output = "";
        if (intval($image_id) > 0) {
            $url = wp_get_attachment_image_src( $image_id, 'medium', false );
            $output = is_ssl() ? preg_replace( "^http:", "https:", $url[0] ) : $url[0] ;
            $image = '<img id="myprefix-preview-image-popup" src="' . $output . '" />';
        } else {
            $image = '<img id="myprefix-preview-image-popup" src="' . BOOKING_ORDER_PATH . '/img/no_img.jpg' . '" />';
        }
        $result = array('status' => 'success', 'code'=>0, 'data' => $image);
        return $result;
    }
    $result = array('status' => 'fail', 'code'=>1);
    return $result;
}

// Test
add_action('rest_api_init', function () {

    register_rest_route('ordertcg/v1', '/manage/get_option', array(
        'methods' => 'GET',
        'callback' => 'manage_get_option'
    ));
});
function manage_get_option()
{

    // authentication 
    $code = $_GET['code'];
    $option = $_GET['option'];

    $code = base64_decode($code);
    $arr = explode(".",$code);
    
    $check = wp_authenticate_username_password( NULL, $arr[0], $arr[1] );

    if(!is_wp_error( $check )){
        $data = get_option($option);
        $result = array('status' => 'success', 'code'=>0, 'data'=>$data);

        return $result;
    }
    $result = array('status' => 'fail', 'code'=>1);
    return $result;
}

//////////////////////////////////////////////////////////////////////

// Test
add_action('rest_api_init', function () {

    register_rest_route('ordertcg/v1', '/admin/load_appointment', array(
        'methods' => 'GET',
        'callback' => 'load_appointment'
    ));
});
function load_appointment()
{
    $data[] = [
        'id'              => $row->id,
        'title'           => $row->customer_name,
        'start'           => $row->start_time,
        'end'             => $row->end_time,
        'color'           => $row->color,
        'textColor'       => $row->text_color
    ];

    return $data;
}

//////////////////////////////////////////////////////////////////////


add_action('rest_api_init', function () {

    register_rest_route('ordertcg/v1', '/mobile/done/order', array(
        'methods' => 'GET',
        'callback' => 'done_order'
    ));
});

function done_order()
{
    $result = [];
    $token = $_GET['token'];
    $orderId = $_GET['orderId'];
    $result = array('status' => '', 'data' => [], 'message' => '');

    $data_token = get_option("access_token_mobile");

    if (strcmp($token, $data_token) == 0) {
        $result['status'] = 'success';
        $result['message'] = 'You got data!';

        update_post_meta($orderId, "status", "completed");

        $result['data'] = NULL;
    } else {
        $result['status'] = 'failed';
        $result['message'] = 'Your token is incorrect!';
        $result['data'] = $token;
    }
    return $result;
}

add_action('rest_api_init', function () {

    register_rest_route('ordertcg/v1', '/mobile/cancel/order', array(
        'methods' => 'GET',
        'callback' => 'cancel_order'
    ));
});

function cancel_order()
{
    $result = [];
    $token = $_GET['token'];
    $orderId = $_GET['orderId'];
    $result = array('status' => '', 'data' => [], 'message' => '');

    $data_token = get_option("access_token_mobile");

    if (strcmp($token, $data_token) == 0) {
        $result['status'] = 'success';
        $result['message'] = 'Bestellung wurde abgelehnt';

        $field_status = dsmart_field('status', $orderId);
        if ($field_status == "processing" || $field_status == "pending") {
            update_post_meta($orderId, 'status', "cancelled");
            wp_send_mail_order($orderId, $field_status, "cancelled");
        } else {
            // $result['status'] = 'failed';
            // $result['message'] = 'Bestellung wurde vorher abgelehnt!';
            // $result['data'] = null;
            // return $result;
        }
        $result['data'] = NULL;
    } else {
        $result['status'] = 'failed';
        $result['message'] = 'Your token is incorrect!';
        $result['data'] = $token;
    }
    
    return $result;
}

add_action('rest_api_init', function () {

    register_rest_route('ordertcg/v1', '/mail/cancel/order', array(
        'methods' => 'GET',
        'callback' => 'cancel_order_mail'
    ));
});

function cancel_order_mail()
{
    $result = [];
    $token = $_GET['token'];
    $orderId = $_GET['orderId'];
    $result = array('status' => '', 'data' => [], 'message' => '');

    $data_token = get_option("access_token_mobile");

    if (strcmp($token, $data_token) == 0) {
        $result['status'] = 'success';
        $result['message'] = 'Bestellung wurde abgelehnt';

        $field_status = dsmart_field('status', $orderId);
        if ($field_status == "processing" || $field_status == "pending") {
            update_post_meta($orderId, 'status', "cancelled");
            wp_send_mail_order($orderId, $field_status, "cancelled");
        } else {
            $result['status'] = 'failed';
            $result['message'] = 'Bestellung wurde vorher abgelehnt!';
            $result['data'] = null;
            return $result;
        }
        $result['data'] = NULL;
    } else {
        $result['status'] = 'failed';
        $result['message'] = 'Your token is incorrect!';
        $result['data'] = $token;
        return $result;
    }
    wp_redirect( home_url() );
    exit();
    // return $result;
}
