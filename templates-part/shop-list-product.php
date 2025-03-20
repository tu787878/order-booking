<?php global $wp_query;
	$dsmart_thumbnail = get_option('dsmart_thumbnail');
	$tax_query = array();
	$meta_query = array();
	if(isset($_GET['search']) && $_GET['search'] != ""){
		$search = $_GET['search'];
	}else{
		$search = "";
	}
	if(isset($_GET['cat']) && $_GET['cat'] != ""){
		$cat = $_GET['cat'];
		$tax_query[] = array(
			'taxonomy'  => 'product-cat',
        	'field'     => 'term_id',
        	'terms'		=> $cat,
		);
	}else{
		$cat = "";
	}
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	if(isset($_GET['status']) && $_GET['status'] != ""){
		$status = $_GET['status'];
		$meta_query[] = array(
			'key'  => 'status',
        	'value'		=> $status,
        	'compare' => 'LIKE',
		);
	}else{
		$status = "";

	}
	if(count($tax_query)>0){
        $relation = array('relation' => 'AND');
    }else {
        $relation = array('relation' => 'OR');
    }
    if(count($meta_query)>0){
        $relations = array('relation' => 'AND');
    }else {
        $relations = array('relation' => 'OR');
    }
    if(count($tax_query) > 0 && count($meta_query) > 0){
    	$wp_query = new WP_Query(array(
        	's' => $search,
            'post_type' => 'product',
            'orderby' => 'date',
            'order' => 'DESC', 
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'paged' => $paged,
            'tax_query' => array(
                $relation,
                $tax_query
            ),
            'meta_query' => array(
            	$relations,
            	$meta_query
            )             
        ));
    }elseif(count($tax_query) == 0 && count($meta_query) > 0){
    	$wp_query = new WP_Query(array(
        	's' => $search,
            'post_type' => 'product',
            'orderby' => 'date',
            'order' => 'DESC', 
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'paged' => $paged,
            'meta_query' => array(
            	$relations,
            	$meta_query
            )             
        ));
    }elseif(count($tax_query) > 0 && count($meta_query) == 0){
    	$wp_query = new WP_Query(array(
        	's' => $search,
            'post_type' => 'product',
            'orderby' => 'date',
            'order' => 'DESC', 
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'paged' => $paged,
            'tax_query' => array(
                $relation,
                $tax_query
            ),         
        ));
    }else{
        $wp_query = new WP_Query(array(
        	's' => $search,
            'post_type' => 'product',
            'orderby' => 'date',
            'order' => 'DESC', 
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'paged' => $paged,            
        ));
    }
?>
<div class="dsmart-filter">
	<form action="<?php the_permalink(); ?>" method="GET">
		<div class="dsmart-wrap">
			<input type="text" name="search" class="dsmart-field" placeholder="<?php _e("Enter keyword") ?>" value="<?php echo $search; ?>"/>
		</div>
		<div class="dsmart-wrap">
			<select class="dsmart-field" name="cat">
				<option value=""><?php _e("All category") ?></option>
				<?php $terms = get_terms( array(
	                'taxonomy' => 'product-cat',
	                'hide_empty' => false,
	            ) );
	            if($terms):
	                foreach ($terms as $term) {
	                    if(intval($cat) == $term->term_id){
	                        $class = "selected";
	                    }else{
	                        $class = "";
	                    } ?>
	                    <option value="<?php echo $term->term_id ?>" <?php echo $class; ?>><?php echo $term->name; ?></option>
	                <?php }
	            endif; ?>
			</select>
		</div>
		<div class="dsmart-wrap">
			<select class="dsmart-field" name="status">
				<option value=""><?php _e("All status") ?></option>
				<option value="instock" <?php if($status == "instock"){echo 'selected';} ?>><?php _e("Instock"); ?></option>
				<option value="outstock" <?php if($status == "outstock"){echo 'selected';} ?>><?php _e("Out of stock"); ?></option>
			</select>
		</div>
		<button type="submit" class="dsmart-button"><?php _e("Filter") ?></button>
	</form>
</div>
<div class="dsmart-table">
	<table class="table">
		<thead>
			<tr>
				<?php if(has_post_thumbnail() && $dsmart_thumbnail != "1"): ?><th class="product-thumbnail"><?php _e("Thumnail") ?></th><?php endif;?>
				<th class="product-name"><?php _e("Product name") ?></th>
				<th class="product-cat"><?php _e("Product category") ?></th>
				<th class="product-status"><?php _e("Product status") ?></th>
			</tr>
		</thead>
			<?php if($wp_query->have_posts()): 
				echo '<tbody>';
				while($wp_query->have_posts()): the_post();
					if(has_post_thumbnail()):
                        $url_img =  wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
                    else:
                        $url_img =  plugin_dir_url( __FILE__ ).'img/no_img.jpg';
                    endif;
                    $array_name = array();
                    $post_terms = wp_get_post_terms( get_the_ID(), 'product-cat');
                    $product_status = dsmart_field('status',get_the_ID());
                    $sku = dsmart_field('sku',get_the_ID());
                    $viewed_product = dsmart_field('viewed_product');
                    if($viewed_product == ""){
                    	$class= "active";
                    }else{
                    	$class = "";
                    }
                    if($post_terms){
                        foreach ($post_terms as $item) {
                            $array_name[] = $item->name;
                        }
                    } ?>
					<tr class="<?php echo $class; ?>">
						<?php if(has_post_thumbnail() && $dsmart_thumbnail != "1"): ?><td class="product-thumbnail"><img src="<?php echo $url_img; ?>"></td><?php endif; ?>
						<td class="product-name">
							<h4><?php the_title(); ?></h4>
							<?php if($sku != ""): ?><p class="sku"><?php echo __('SKU: ').$sku; ?></p><?php endif; ?>
						</td>
						<td class="product-cat">
							<?php echo implode(',', $array_name); ?>
						</td>
						<td class="product-status">
							<?php if($product_status == "instock"){
								echo '<span class="instock">'.__("Instock").'</span>';
							}else{
								echo '<span class="outstock">'.__("Out of stock").'</span>';
							} ?>
						</td>
					</tr>
				<?php endwhile;
				echo '</tbody>';
				echo '<tfoot>';
				echo '<tr>';
				echo '<td colspan="4">';
				echo get_the_posts_pagination( array( 'mid_size' => 2,'prev_text' => __('Bisherige',"dsmart"),'next_text' => __('Nächste',"dsmart") ));
				echo '</td>';
				echo '</tr>';
				echo '</tfoot>';
				wp_reset_query();
			endif;?>
	</table>
</div>