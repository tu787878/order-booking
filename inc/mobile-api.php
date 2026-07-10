<?php

/**
 * 256-bit symmetric key shared with the calling site.
 * The secret string MUST be byte-for-byte identical on both sites.
 */
function myplugin_auth_key()
{
    $secret = '8764ecfe65173f70cd341552ab5d0bae6d5a0f09d2e532723e9f13c4a645ba42';
    return hash('sha256', $secret, true);
}

/**
 * Decrypt an auth code produced by the calling site.
 * Returns array('u' => username, 'p' => password) on success, or false on
 * failure / tampering.
 *
 * Code format (before base64url): iv(12 bytes) || gcm_tag(16 bytes) || ciphertext
 * Encoding: base64url (+/ -> -_, '=' padding stripped)
 * Plaintext: JSON {"u":"<username>","p":"<password>"}
 */
function myplugin_decrypt_code($code)
{
    $blob = base64_decode(strtr($code, '-_', '+/'));
    if ($blob === false || strlen($blob) < 28) {
        return false;
    }

    $iv         = substr($blob, 0, 12);
    $tag        = substr($blob, 12, 16);
    $ciphertext = substr($blob, 28);
    $key        = myplugin_auth_key();

    $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($plaintext === false) {
        return false;
    }

    $data = json_decode($plaintext, true);
    if (!is_array($data) || !isset($data['u'], $data['p'])) {
        return false;
    }

    return $data;
}

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
    $creds = myplugin_decrypt_code($code);
    
    $check = $creds
        ? wp_authenticate_username_password( NULL, $creds['u'], $creds['p'] )
        : new WP_Error( 'invalid_code', 'Invalid authentication code' );

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
// Decode a base64 data-URI (e.g. "data:image/png;base64,....") and create a
// WordPress attachment. Returns the attachment ID on success, or 0 on failure.
if ( ! function_exists( 'save_image2' ) ) {
    function save_image2( $base64, $title = '' )
    {
        if ( empty( $base64 ) ) {
            return 0;
        }

        // Split off the "data:image/xxx;base64," prefix when present.
        if ( strpos( $base64, ',' ) !== false ) {
            list( $meta, $content ) = explode( ',', $base64, 2 );
        } else {
            $meta    = '';
            $content = $base64;
        }

        // Determine the file extension from the mime type in the prefix.
        $ext = 'png';
        if ( preg_match( '#data:image/([a-zA-Z0-9\.\+\-]+);base64#', $meta, $m ) ) {
            $ext = strtolower( $m[1] );
            if ( 'jpeg' === $ext ) {
                $ext = 'jpg';
            }
        }

        $decoded = base64_decode( $content, true );
        if ( false === $decoded ) {
            return 0;
        }

        $filename = ( $title ? sanitize_file_name( $title ) : 'popup-' . uniqid() ) . '.' . $ext;

        // Write the file into the uploads directory.
        $upload = wp_upload_bits( $filename, null, $decoded );
        if ( ! empty( $upload['error'] ) ) {
            return 0;
        }

        require_once ABSPATH . 'wp-admin/includes/image.php';

        $filetype   = wp_check_filetype( $upload['file'], null );
        $attachment = array(
            'guid'           => $upload['url'],
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $upload['file'] ) ),
            'post_content'   => '',
            'post_status'    => 'inherit',
        );

        $attach_id = wp_insert_attachment( $attachment, $upload['file'] );
        if ( is_wp_error( $attach_id ) || ! $attach_id ) {
            return 0;
        }

        $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }
}

function manage_save_popup()
{

    // authentication
    $data = file_get_contents('php://input');

    parse_str($data, $data);
    $code = $data["code"];
    $base64 = $data["base64"];

    $creds = myplugin_decrypt_code($code);
    
    $check = $creds
        ? wp_authenticate_username_password( NULL, $creds['u'], $creds['p'] )
        : new WP_Error( 'invalid_code', 'Invalid authentication code' );

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

    $creds = myplugin_decrypt_code($code);
    
    $check = $creds
        ? wp_authenticate_username_password( NULL, $creds['u'], $creds['p'] )
        : new WP_Error( 'invalid_code', 'Invalid authentication code' );

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

    $creds = myplugin_decrypt_code($code);
    
    $check = $creds
        ? wp_authenticate_username_password( NULL, $creds['u'], $creds['p'] )
        : new WP_Error( 'invalid_code', 'Invalid authentication code' );

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

    $creds = myplugin_decrypt_code($code);
    
    $check = $creds
        ? wp_authenticate_username_password( NULL, $creds['u'], $creds['p'] )
        : new WP_Error( 'invalid_code', 'Invalid authentication code' );

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
