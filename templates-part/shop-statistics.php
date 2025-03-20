<?php $date_query = array();
$meta_query = array();
if(isset($_GET['date_from']) && $_GET['date_from'] != ""){
	$date_from = $_GET['date_from'];
	$date_from_data = explode('-', $date_from);
	$date_query['after'] = array(
      'year'  => $date_from_data[2],                  
      'month' => $date_from_data[1],                     
      'day'   => $date_from_data[0],                    
    );
}else{
	$date_from = "";
}
if(isset($_GET['date_to']) && $_GET['date_to'] != ""){
	$date_to = $_GET['date_to'];
	$date_to_data = explode('-', $date_to);
	$date_query['before'] = array(
      'year'  => $date_to_data[2],                  
      'month' => $date_to_data[1],                     
      'day'   => $date_to_data[0],                    
    );
}else{
	$date_to = "";
}
if(count($date_query) > 0){
	$date_query['inclusive'] = true;
	$date_query['relation'] = 'AND';
}else{
	$date_query = null;
}
global $wp_query;?>
<h4 class="dsmart-title"><?php _e("Statistic"); ?></h4>
<div class="dsmart-filter">
	<form method="GET" action="<?php echo ds_merge_querystring(remove_query_arg(array('date_from','date_to'))); ?>">
		<input type="text" name="date_from" class="dsmart-field dsmart-datepicker" placeholder="<?php _e("From date") ?>" value="<?php echo $date_from ?>" autocomplete="off" />
		<input type="text" name="date_to" class="dsmart-field dsmart-datepicker" placeholder="<?php _e("to date") ?>" value="<?php echo $date_to ?>" autocomplete="off" />
		<input type="hidden" name="section" value="statistics"/>
		<button type="submit" class="dsmart-button"><?php _e("Filter") ?></button>
	</form>
</div>
<?php 
$wp_query  = new WP_Query( array(
    'post_type'      => 'orders',
    'post_status'   => 'publish',
    'posts_per_page' => -1,
    'order'          => 'desc',
    'orderby' => 'date',
    'date_query' => $date_query,
    'meta_query' => array(
    	array(
    		'key'  => 'status',
        	'value'		=> 'completed',
        	'compare' => 'LIKE',
    	)
    )
));
$total_all = 0;
if ($wp_query->have_posts()):
	while ($wp_query->have_posts()) : $wp_query->the_post(); 
		$currency = dsmart_field('currency');
        $total = dsmart_field('total');
        $status = dsmart_field('status');
	    $price_after_change = ds_convert_currency_price($currency,$total);  
	    $total_all = $total_all + $price_after_change;      
	endwhile;wp_reset_query();
endif; ?>
<div class="dsmart-table">
	<table class="table">
		<thead>
			<tr>
                <th></th>
                <th><?php _e('Total orders') ?></th>
                <th><?php _e('Total') ?></th>
            </tr>
		</thead>
		<tbody>
			<tr>
				<td><?php _e("Revenue") ?></td>
				<td><?php _e($wp_query->post_count.' orders'); ?></td>
				<td><?php echo ds_price_format_text_with_symbol($total_all,"1"); ?></td>
			</tr>
		</tbody>
	</table>
</div>