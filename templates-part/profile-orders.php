<?php global $wp_query;
$current_user = wp_get_current_user();
$meta_query = array();
if(isset($_GET['search']) && $_GET['search'] != ""){
    $search = $_GET['search'];
}else{
    $search = "";
}
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
if(isset($_GET['status']) && $_GET['status'] != ""){
    $status = $_GET['status'];
    $meta_query[] = array(
        'key'  => 'status',
        'value'     => $status,
        'compare' => 'LIKE',
    );
}else{
    $status = "";

}
if(count($meta_query)>0){
    $meta_query['relation'] = "AND";
}else {
    $meta_query = null;
}
$wp_query = new WP_Query(array(
    's' => $search,
    'post_type' => 'orders',
    'orderby' => 'date',
    'order' => 'DESC', 
    'post_status'    => 'publish',
    'posts_per_page' => 10,
    'paged' => $paged,
    'author' => $current_user->ID,
    'meta_query' => $meta_query,          
));?>
<h3 class="text-center title-page"><?php _e("Bestellungen"); ?></h3>
<div class="notify"></div>
<div class="dsmart-filter">
    <form action="<?php the_permalink(); ?>" method="GET">
        <div class="dsmart-wrap">
            <input type="text" name="search" class="dsmart-field" placeholder="<?php _e("Bestellcode eingeben") ?>" value="<?php echo $search; ?>"/>
        </div>
        <div class="dsmart-wrap">
            <select class="dsmart-field" name="status">
                <option value=""><?php _e("Alle") ?></option>
                <option value="processing" <?php if($status == "processing"){echo 'selected';} ?>><?php _e("Verarbeitung"); ?></option>
                <option value="completed" <?php if($status == "completed"){echo 'selected';} ?>><?php _e("Abgeschlossen"); ?></option>
                <option value="cancelled" <?php if($status == "cancelled"){echo 'selected';} ?>><?php _e("Abgebrochen"); ?></option>
            </select>
        </div>
        <input type="hidden" name="action" value="orders">
        <button type="submit" class="dsmart-button"><?php _e("Filter") ?></button>
    </form>
</div>
<div class="dsmart-table">
    <table class="table">
        <thead>
            <tr>
                <th class="order-title"><?php _e("Bestellung") ?></th>
                <th class="order-name"><?php _e("Vollständiger Name") ?></th>
                <th class="order-price"><?php _e("Gesamt") ?></th>
                <th class="order-status"><?php _e("Status") ?></th>
                <th class="order-action"><?php _e("Aktion") ?></th>
            </tr>
        </thead>
        <?php if($wp_query->have_posts()): 
            echo '<tbody>';
            while($wp_query->have_posts()): the_post();
                $currency = dsmart_field('currency');
                $customer_name = dsmart_field('customer_name');
                $customer_email = dsmart_field('customer_email');
                $customer_phone = dsmart_field('customer_phone');
                $total = dsmart_field('total');
                $order_status = dsmart_field('status');?>
                <tr data-item="<?php the_ID(); ?>">
                    <td class="order-title"><h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4></td>
                    <td class="order-name"><?php echo $customer_name; ?></td>
                    <td class="order-price">
                        <?php echo ds_price_format_text_with_symbol($total,$currency);?>
                    </td>
                    <td class="order-status">
                        <?php if($order_status == "processing"){
                            echo '<span class="processing">'.__("Verarbeitung").'</span>';
                        }elseif($order_status == "completed"){
                            echo '<span class="completed">'.__("Abgeschlossen").'</span>';
                        }else{
                            echo '<span class="cancelled">'.__("Abgebrochen").'</span>';
                        } ?>
                    </td>
                    <td class="order-action">
                        <div class="list-button">
                            <div class="list-button">
                                <a href="<?php the_permalink() ?>" class="dsmart-button"><?php _e("Detail") ?></a>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endwhile;
            echo '</tbody>';
            echo '<tfoot>';
            echo '<tr>';
            echo '<td colspan="7">';
            echo get_the_posts_pagination( array( 'mid_size' => 2,'prev_text' => __('Bisherige',"dsmart"),'next_text' => __('Nächste',"dsmart") ));
            echo '</td>';
            echo '</tr>';
            echo '</tfoot>';
            wp_reset_query();
        endif;?>
    </table>
</div>