<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<?php global $post;
$current_user = wp_get_current_user();
// Restrict access to administrators only
if ( ! in_array( 'administrator', (array) $current_user->roles ) ) {
    wp_redirect( home_url() );
    exit;
}
$back_link_in_cart 		= get_option('back_link_in_cart');
$currency = dsmart_field('currency');
$customer_name1 = dsmart_field('customer_name1');
$customer_name2 = dsmart_field('customer_name2');
$customer_email = dsmart_field('customer_email');
$customer_etage = dsmart_field('customer_etage');
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
$user_delivery_time = dsmart_field('user_delivery_time');
$user_delivery_date = dsmart_field('user_delivery_date');
$user_time = dsmart_field('user_time');
$shipping_fee = dsmart_field('shipping_fee');
$total = dsmart_field('total');
$status = dsmart_field('status');
$reduce = dsmart_field('reduce');
$reduce_percent = dsmart_field('reduce_percent');   
$second_order_number = dsmart_field('second_order_number');
$show_second_number = get_option('show_second_number');
$dsmart_barzahlung  = (get_option('dsmart_barzahlung') != "") ? get_option('dsmart_barzahlung') : 'Barzahlung';
if(strtolower($status) == "processing"){
    $status_text = '<span class="processing">'.__("in Bearbeitung",'dsmart').'</span>';
}elseif(strtolower($status) == "completed"){
    $status_text = '<span class="completed">'.__("Fertig",'dsmart').'</span>';
}elseif(strtolower($status) == "pending"){
    $status_text = '<span class="processing">'.__("steht aus",'dsmart').'</span>';
}else{
    $status_text = '<span class="cancelled">'.__("Abgebrochen",'dsmart').'</span>';
}
$dsmart_thankyou_text = get_option('dsmart_thankyou_text');
$method = dsmart_field('method');
switch ($method) {
    case 'paypal':
        $method_text = "Paypal";
        break;
    case 'klarna':
        $method_text = "Klarna";
        break;
    case 'cash':
        $method_text = $dsmart_barzahlung;
        break;
    default:
        $method_text = $method;
        break;
}
$data_item = "";
$order_date = get_the_date('d-m-Y');
get_header(); ?>
    <div class="order_page">
        <!-- <div class="container"> -->
            <div class="notify"></div>
            <?php if($dsmart_thankyou_text != ""): ?><div class="ds-thankyou"><?php echo $dsmart_thankyou_text; ?></div><?php endif; ?>
            <div id="div-to-print">
                <style media="print">
                 @media print {
                    a[href]:after {
                        content: none !important;
                    }
                    .list-button{
                        display: none;
                    }
                    .container-wrapper{
                        padding: 0;
                    }
                    .container{
                        width: 100%;
                        padding: 0;
                        margin: 0;
                    }
                    .ds-thankyou,.action-order,.footer-section,.title-container-outer-wrap,.outer-wrap{
                        display: none;
                    }
                }
                </style>
                <div class="promotion_user">
                    <h3 class="title_user"><?php _e("Kundeninformationen",'dsmart'); ?></h3>
                    <table id="promotion_table">
                        <tbody>
                            <?php if($show_second_number == "1"){?>
                                <tr>
                                    <td><?php _e("Bestellnummer:",'dsmart'); ?></td>
                                    <td><?php echo $second_order_number; ?></td>
                                </tr>
                            <?php }?>
                            <tr>
                                <td><?php _e("Nachname:",'dsmart'); ?></td>
                                <td><?php echo $customer_name1; ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Vorname:",'dsmart'); ?></td>
                                <td><?php echo $customer_name2; ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Email Adresse:",'dsmart') ?></td>
                                <td><?php echo $customer_email; ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Etage:",'dsmart') ?></td>
                                <td><?php echo $customer_etage; ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Telefonnummer:",'dsmart'); ?></td>
                                <td><?php echo $customer_phone; ?></td>
                            </tr>
                            <?php if($shipping_method == "shipping"){ ?>
                                <tr>
                                    <td><?php _e("Versandart:",'dsmart') ?></td>
                                    <td><?php _e("Lieferung",'dsmart'); ?></td>
                                </tr>
                                <tr>
                                    <td><?php _e("Lieferanschrift:",'dsmart') ?></td>
                                    <td><?php echo $user_location; ?></td>
                                </tr>
                                <tr>
                                    <td><?php _e("Tag:",'dsmart') ?></td>
                                    <td>
                                        <?php if($order_date == $user_delivery_date): ?>
                                            <span style="font-size: 16px;font-weight: 700;color: #446f3a;line-height: 21px;"><?php echo $user_delivery_date; ?></span>
                                        <?php else: ?>
                                            <span style="font-size: 16px;font-weight: 700;color: #000;line-height: 21px;"><?php echo __('ACHTUNG++').$user_delivery_date.__('++ACHTUNG'); ?></span>
                                        <?php endif;?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e("Liefer- / Abholzeit:",'dsmart') ?></td>
                                    <td><?php echo $user_delivery_time; ?></td>
                                </tr>
                            <?php }else{ ?>
                                <tr>
                                    <td><?php _e("Versandart:",'dsmart') ?></td>
                                    <td><?php _e("im Laden Abholen",'dsmart'); ?></td>
                                </tr>
                                <tr>
                                    <td><?php _e("Liefer- / Abholzeit:",'dsmart') ?></td>
                                    <td><?php echo $user_time; ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td><?php _e("Bestellnotiz:",'dsmart'); ?></td>
                                <td><?php echo $more_additional; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="oder_infor">
                    <h3 class="title_order"><?php _e("Bestellinformationen",'dsmart'); ?></h3>
                    <table id="oder_table">
                        <thead>
                            <tr>
                                <th><span><?php _e("Produkt",'dsmart'); ?></span></th>
                                <th><span><?php _e("Gesamtsumme",'dsmart'); ?></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($items){ ?>
                                <?php foreach ($items as $key => $value) { 
                                    if(isset($value['product_id'])):
                                        $product_id = intval($value['product_id']);
                                    else:
                                        $product_id = $key;
                                    endif;    
                                    $meta['quantity'] = dsmart_field('quantity',$product_id);
                                    $meta['varialbe_price'] = dsmart_field('varialbe_price',$product_id);
                                    $meta['extra_name'] = dsmart_field('extra_name',$product_id);
                                    $meta['extra_price'] = dsmart_field('extra_price',$product_id);
                                    $meta['sidedish_name'] = dsmart_field('sidedish_name',$product_id);
                                    $meta['sidedish_price'] = dsmart_field('sidedish_price',$product_id);
                                    $sidedish_text = dsmart_field('sidedish_text', $product_id);
                                    if($sidedish_text == null || $sidedish_text == '')
                                    {
                                        $sidedish_text = "Beilage";
                                    }
                                    $data_item .= '<div class="section" style="margin-bottom: 10px;padding-bottom: 10px;border-bottom: 1px dashed #000;">';
                                    $data_item .= '<p style="line-height: 1.3;margin: 0;text-align: center;"><b>Produkt: '.$value['quantity'].' x '.$value['title'].'</b></p>';
                                    $isSidedish = isset($value['sidedish_info']) && $value['sidedish_info'] != null && $meta['sidedish_name'] != null && !empty(array_filter($meta['sidedish_name']));
                                    $isExtra = isset($value['extra_info']) && $value['extra_info'] != null && $meta['extra_name'] != null && !empty(array_filter($meta['extra_name'])) && $meta['extra_price'] != null && !empty(array_filter($meta['extra_price']));
                                    $isVariable = isset($value['variable_id']) && $meta['quantity'] != null && !empty(array_filter($meta['quantity'])) && $meta['varialbe_price'] != null && !empty(array_filter($meta['varialbe_price']));
                                    if($isVariable || $isExtra || $isSidedish):
                                        $data_item .= '<p style="line-height: 1.3;margin: 0;text-align: center;">AUSGEWÄHLTE PRODUKT</p>';
                                        $variable_id = intval(explode('_', $value['variable_id'])[1])-1;
                                        $variable_text = '<div class="variable-product cart-variable">';
                                        if($isVariable){
                                            $variable_text .=   '<div class="item">';
                                            $variable_text .=       '<h5>'. __( 'AUSGEWÄHLTE PRODUKT', 'dsmart') .'</h5>';
                                            $variable_text .=       '<p>'. $meta['quantity'][$variable_id] .': '. ds_price_format_text($meta['varialbe_price'][$variable_id]) .'</p>';
                                            $variable_text .=   '</div>';
                                            $data_item .= '<p style="line-height: 1.3;margin: 0;text-align: center;">'.$meta['quantity'][$variable_id].': '.ds_price_format_text($meta['varialbe_price'][$variable_id]).'</p>';
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
                                            $sidedish_text .= '<h5>'. __( $sidedish_text . ':', 'dsmart') .'</h5>';
                                            $sidedish_text .= '<ul>';
                                            foreach ($sidedish_info as $sidedish_key => $sidedish_value) { 
                                                $sidedish_id = intval(explode('_', $sidedish_value->sidedish_id)[1])-1;
                                                $sidedish_text .= '<li>'. $meta['sidedish_name'][$sidedish_id] . (isset($meta['sidedish_price'][$sidedish_id]) && $meta['sidedish_price'][$sidedish_id] !== "" ? " (+".ds_price_format_text($meta['sidedish_price'][$sidedish_id]).")" : "") .'</li>';                                   
                                           } 
                                            $sidedish_text .= '</ul>';
                                        else:
                                            $sidedish_text ='';
                                        endif;
                
                                        $variable_text = $variable_text . " " . $sidedish_text . ' '. $extra_text;
                                    else:
                                        $variable_text = '';
                                    endif;
                                    $data_item .= '</div>';?>
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
                        </tbody>
                        <tfoot>                       
                            <tr>
                                <td><?php _e("Lieferungskosten:",'dsmart');  ?></td>
                                <td><?php echo ($shipping_fee != '') ? ds_price_format_text_with_symbol($shipping_fee,$currency):''; ?></td>
                            </tr>
                             <tr>
                                <td><?php _e("Zwischensumme:",'dsmart'); ?></td>
                                <td><?php echo ds_price_format_text_with_symbol($subtotal); ?></td>
                            </tr>
                            <?php 
                            if($reduce != ""): ?>
                                <tr>
                                    <td><?php _e("Rabatt:",'dsmart'); ?> (-<?php echo $reduce_percent; ?>)</td>
                                    <td>- <?php echo (strpos($reduce, ds_price_symbol($currency)) !== false) ? $reduce : ds_price_format_text_with_symbol($reduce,$currency); ?></td>
                                </tr>
                            <?php endif;
                            if($coupon != ""):
                                foreach ($coupon as $couponkey => $couponvalue) { ?>
                                    <tr>
                                       <td><?php _e("Rabattcode: (".$couponkey.")",'dsmart'); ?></td> 
                                       <td><?php echo '- '.ds_price_format_text_with_symbol($couponvalue,$currency); ?></td>
                                    </tr>
                                <?php }?>
                            <?php endif;?>
                            <tr>
                                <td><?php _e("Gesamtsumme:",'dsmart'); ?></td>
                                <td>
                                    <div class="price">
                                        <span><?php echo (strpos($total, ds_price_symbol($currency)) !== false) ? $total : ds_price_format_text_with_symbol($total,$currency); ?></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e("Bezahlverfahren:",'dsmart'); ?></td>
                                <td><div class="status"><?php echo ucfirst($method_text); ?></div></td>
                            </tr>
                            <tr>
                                <td><?php _e("Status:",'dsmart'); ?></td>
                                <td><div class="status"><?php echo $status_text; ?></div></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php if((in_array( 'administrator', (array) $current_user->roles ) || in_array( 'shop', (array) $current_user->roles ))){ ?>
                <div class="action-order">
                    <div class="list-button">
                        <?php if($status == "processing"): ?>
                            <button type="button" class="dsmart-button complete-order" data-item="<?php the_ID(); ?>"><?php _e("Fertigstellen",'dsmart') ?></button>
                        <?php endif; ?>
                        <button type="button" class="dsmart-button print-order" data-item="<?php the_ID(); ?>"><?php _e("Print order",'dsmart'); ?></button>
                        <?php if($status == "processing"): ?>
                            <button type="button" class="dsmart-button cancel-order" data-item="<?php the_ID(); ?>"><?php _e("Cancel",'dsmart') ?></button>
                        <?php endif; ?>
                        <?php if($back_link_in_cart != ''): ?>
                            <a style="margin: 0 auto;display: block;text-align: center;width: 100px;" href="<?php echo $back_link_in_cart; ?>" class="dsmart-button"><?php _e("Zurück",'dsmart'); ?></a>
                        <?php endif; ?>	
                    </div>
                </div>
                <div id="cancel-order" class="modal fade" role="dialog">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php _e("Cancel order?",'dsmart') ?></h4>
                      </div>
                      <div class="modal-body">
                        <div class="dsmart-modal-notify"></div>
                        <p><?php _e("Do you want to cancel this order?",'dsmart') ?></p>
                      </div>
                      <div class="modal-footer">
                        <input type="hidden" name="order-id" value=""/>
                        <button type="button" class="btn btn-danger cancel-button"><?php _e("Cancel order",'dsmart') ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e("Return",'dsmart') ?></button>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="complete-order" class="modal fade" role="dialog">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php _e("Complete order?",'dsmart') ?></h4>
                      </div>
                      <div class="modal-body">
                        <div class="dsmart-modal-notify"></div>
                        <p><?php _e("Do you want to complete this order?",'dsmart') ?></p>
                      </div>
                      <div class="modal-footer">
                        <input type="hidden" name="order-id" value=""/>
                        <button type="button" class="btn btn-primary complete-button"><?php _e("Complete order",'dsmart') ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e("Return",'dsmart') ?></button>
                        <button type="button" class="btn btn-default" ><?php _e("Return",'dsmart') ?></button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="book-loading"><img src="<?php echo plugin_dir_url( __FILE__ ).'img/loading.gif'; ?>"></div>
            <?php }else{ ?>
                <?php if($back_link_in_cart != ''): ?>
                    <a style="margin: 0 auto;display: block;text-align: center;width: 100px;" href="<?php echo $back_link_in_cart; ?>" class="dsmart-button"><?php _e("Zurück",'dsmart'); ?></a>
                <?php endif; ?>	
            <?php }?>
        <!-- </div> -->
    </div>
<?php get_footer(); ?>