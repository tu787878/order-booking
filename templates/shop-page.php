<?php 

if(!is_user_logged_in()){
	wp_redirect(home_url());
}else{
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$user = get_userdata( $user_id );
	if ( in_array( 'shop', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) {
	    
	}else{
		wp_redirect(home_url());
	}
}
get_header(); ?>
	<div class="container">
		<div class="dsmart-row">
			<div class="dsmart-col-3 shop-sidebar">
				<ul class="shop-menu">
					<li class="<?php if(!isset($_GET['section']) || (isset($_GET['section']) && $_GET['section'] == "")){echo 'active';} ?>"><a href="<?php echo get_permalink(); ?>"><?php _e("List products") ?></a></li>
					<li class="<?php if(isset($_GET['section']) && $_GET['section'] == "list-order"){echo 'active';} ?>"><a href="<?php echo get_permalink().'?section=list-order'; ?>"><?php _e("List orders") ?></a></li>
					<li class="<?php if(isset($_GET['section']) && $_GET['section'] == "setting"){echo 'active';} ?>"><a href="<?php echo get_permalink().'?section=setting'; ?>"><?php _e("Setting") ?></a></li>
					<li class="<?php if(isset($_GET['section']) && $_GET['section'] == "statistics"){echo 'active';} ?>"><a href="<?php echo get_permalink().'?section=statistics'; ?>"><?php _e("Statistic") ?></a></li>
				</ul>
			</div>
			<div class="dsmart-col-9 shop-content">
				<?php if(!isset($_GET['section']) || (isset($_GET['section']) && $_GET['section'] == "")){
					echo dsmart_get_template( 'shop-list-product.php' );
				}elseif(isset($_GET['section']) && $_GET['section'] == "list-order"){
					echo dsmart_get_template( 'shop-list-order.php' );
				}elseif(isset($_GET['section']) && $_GET['section'] == "setting"){
					echo dsmart_get_template( 'shop-setting.php' );
				}elseif(isset($_GET['section']) && $_GET['section'] == "statistics"){
					echo dsmart_get_template( 'shop-statistics.php' );
				}?>
			</div>
		</div>
		<div class="book-loading"><img src="<?php echo plugin_dir_url( __FILE__ ).'img/loading.gif'; ?>"></div>
		<div class="book-overlay"></div>
	</div>
<?php get_footer(); ?>