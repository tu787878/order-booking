<?php 
if(is_user_logged_in()){
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$user = get_userdata( $user_id );
	if ( in_array( 'shop', (array) $user->roles )) {
	    $viewed_product = dsmart_field('viewed_product');
	    if($viewed_product == ""){
	    	update_post_meta( get_the_ID(), 'viewed_product', "1" );
	    }
	}
}
$dsmart_thumbnail = get_option('dsmart_thumbnail');
$dsmart_stock = get_option('dsmart_stock');
$notify = add_to_cart();
$comment_notify = submit_comment_form();
$price = dsmart_field('price');
$ds_status_product = dsmart_field('status');
$book_gallery_ids = dsmart_field('book_gallery_id');
$avg_rating = dsmart_field('avg_rating');
if($avg_rating == ""){
    $rating_percent = 0;
}else{
    $rating_percent = floatval($avg_rating)*20;
}
$desc = dsmart_field('desc',get_the_ID());
$sharp = intval(dsmart_field('sharp',get_the_ID()));
get_header();
$current_img = ""; ?>
<div class="container">
	<?php add_to_cart_notify($notify);
	submit_comment_notify($comment_notify); ?>
	<div class="dsmart-notify"></div>
	<div class="listing-inner">
        <div class="menu-menucard">
            <div class="menu-meals">
            	<div class="row top-content">
            		<?php if(has_post_thumbnail()):
					    $current_img =  wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
					endif; ?>
            		<?php if($dsmart_thumbnail != "1" && $current_img != ""){ ?>
		            	<div class="col-md-5 left">
		            		<?php if(has_post_thumbnail()):
							    $current_img =  wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
							else:
								$current_img =  plugin_dir_url( __FILE__ ).'img/no_img.jpg';
							endif; ?>
		            		<div class="main-img">
		            			<img src="<?php echo $current_img; ?>" alt="<?php the_title(); ?>">
		            			<?php if($book_gallery_ids != "" && count($book_gallery_ids) > 0){ ?>
		            				<?php foreach ($book_gallery_ids as $book_gallery_id) {
		            					$id = wp_get_attachment_url($book_gallery_id); ?>
		            					<img src="<?php echo $id ?>" alt=""/>
		            				<?php } ?>
		            		<?php } ?>
		            		</div>
		            		<?php if($book_gallery_ids != "" && count($book_gallery_ids) > 0){ ?>
		            			<div class="list-img">
		            				<div class="item" style="background-image: url(<?php echo $current_img; ?>)"></div>
		            				<?php foreach ($book_gallery_ids as $book_gallery_id) {
		            					$id = wp_get_attachment_url($book_gallery_id); ?>
		            					<div class="item" style="background-image: url(<?php echo $id; ?>);"></div>
		            				<?php } ?>
		            			</div>
		            		<?php } ?>
		            	</div>
		            	<div class="col-md-7 right">
		                    <div class="info">
		                        <h2 class="title"><?php the_title(); ?></h2> 
		                    </div>		                   
		                    <?php if(($dsmart_stock == "1" || ($dsmart_stock != "1" && $ds_status_product == "instock")) && $price != ""): ?>
			                    <form class="add-to-cart-form" action="#" method="POST">
			                    	<div class="form-group">
			                    		<div class="price">
			                    			<?php _e("Price: ",'dsmart'); ?><span><?php echo ds_price_format_text($price); ?></span>
			                    		</div>
			                    		<div class="quantity">
								        	<button type="button" class="minus-product"><i class="fa fa-minus" aria-hidden="true"></i></button>
								        	<input type="number" name="quantity-input" value="1"/>
								        	<button type="button" class="plus-product"><i class="fa fa-plus" aria-hidden="true"></i></button>
								        </div>
			                    	</div>
			                    	<div class="list-button">
			                    		<button class="add-to-cart" name="add-to-cart" type="submit"><?php _e("+",'dsmart') ?></button>
			                    	</div>
			                    </form>
			                <?php else: ?>
			                	<p class="out-of-stock"><?php _e('Produkt ist ausverkauft.','dsmart') ?></p>
			                <?php endif;?>
			                <?php if($desc != ""){ ?>
			                	<p><?php echo __("Zusatzstoffe: ",'dsmart').$desc; ?></p>
			                <?php } ?>
			                <?php if($sharp != 0){ ?>
			                	<p>
			                		<?php _e("Schärfe: ",'dsmart');
			                		if($sharp == 1){
										echo 'leicht scharf';
									}elseif($sharp == 2){
										echo 'mittelscharf';
									}elseif($sharp == 3){
										echo 'sehr scharf';
									} ?>
			                	</p>
			                <?php } ?>
		                </div>
		            <?php }else{ ?>
		            	<div class="col-md-12 right">
		                    <div class="info">
		                        <h2 class="title"><?php the_title(); ?></h2> 
		                    </div>		                    
		                    <?php if(($dsmart_stock == "1" || ($dsmart_stock != "1" && $ds_status_product == "instock")) && $price != ""): ?>
			                    <form class="add-to-cart-form" action="#" method="POST">
			                    	<div class="form-group">
			                    		<div class="price">
			                    			<?php _e("Price: ",'dsmart'); ?><span><?php echo ds_price_format_text($price); ?></span>
			                    		</div>
			                    		<div class="quantity">
								        	<button type="button" class="minus-product"><i class="fa fa-minus" aria-hidden="true"></i></button>
								        	<input type="number" name="quantity-input" value="1"/>
								        	<button type="button" class="plus-product"><i class="fa fa-plus" aria-hidden="true"></i></button>
								        </div>
			                    	</div>
			                    	<div class="list-button">
			                    		<button class="add-to-cart" name="add-to-cart" type="submit"><?php _e("+",'dsmart') ?></button>
			                    	</div>
			                    </form>
			                <?php else: ?>
			                	<p class="out-of-stock"><?php _e('Produkt ist ausverkauft.','dsmart') ?></p>
			                <?php endif;?>
			                <?php if($desc != ""){ ?>
			                	<p><?php echo $desc; ?></p>
			                <?php } ?>
			                <?php if($sharp != 0){ ?>
			                	<p>
			                		<?php _e("Schärfe: ");
			                		if($sharp == 1){
										echo __('leicht scharf','dsmart');
									}elseif($sharp == 2){
										echo __('mittelscharf','dsmart');
									}elseif($sharp == 3){
										echo __('sehr scharf','dsmart');
									} ?>
			                	</p>
			                <?php } ?>
		                </div>
		            <?php }?>
	            </div>
                <div class="desc">
                	<?php while ( have_posts() ) : the_post();
						the_content();
					endwhile; ?>
				</div>
            </div>
            <div class="menu-categories-wrapper">
                <div class="menu-categories">
                	<h3 class="menu-title"><?php _e("Product Category",'dsmart') ?></h3>
                    <?php $terms = get_terms( array(
					    'taxonomy' => 'product-cat',
					    'hide_empty' => false,
					) );
					if($terms): ?>
	                    <ul class="menu-category-list">
	                        <?php foreach ($terms as $term) { ?>
	                        	<li><a href="<?php echo get_term_link($term->term_id); ?>" class="menu-category"><?php echo $term->name; ?></a></li>
	                        <?php } ?>
	                    </ul>
	                <?php endif;?>
                </div>
            </div>
        </div>
	</div>
</div>
<?php get_footer(); ?>