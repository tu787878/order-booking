<?php $current_user = wp_get_current_user();
if(isset($_GET['id']) && $_GET['id'] != "" && is_user_logged_in() && (in_array( 'administrator', (array) $current_user->roles ) || in_array( 'shop', (array) $current_user->roles )) ){
    $id = base64_decode($_GET['id']);
    $url = BOOKING_ORDER_PATH . 'inc/pdfdata/'.$id.'.pdf';
    $url2 = BOOKING_ORDER_PATH2 . 'inc/pdfdata/'.$id.'.pdf';
    if(!file_exists($url2)){
        wp_redirect(home_url());
        exit;
    }
}else{
    wp_redirect(home_url());
    exit;
}
get_header();?>
<div class="text-center" style="text-align: center;">
    <button type="button" class="dsmart-button" onclick="printJS({printable:'<?php echo $url; ?>', type:'pdf', showModal:true})">
        <?php _e("Print again"); ?>
    </button>
</div>
<?php get_footer();?>
<script type="text/javascript">
    printJS({printable:'<?php echo $url; ?>', type:'pdf', showModal:true});
</script>