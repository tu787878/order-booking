<?php
/**
 * Template Name: Profile
 */
if(  !is_user_logged_in() || get_option('show_profile') !== "on" ){
	wp_redirect(home_url());
	exit();
}
get_header();?>
<div id="profile-page" class="container">	
	<div class="main-side">
		<div class="left-side">
			<?php echo dsmart_get_template( 'profile-sidebar.php' );?>
		</div>
		<div class="right-side">
			<?php 
				if(!isset($_GET['action']) || isset($_GET['action']) && $_GET['action']=="info"):
					echo dsmart_get_template( 'profile-info.php' );
				elseif(isset($_GET['action']) && $_GET['action']=="changepassword"):
					echo dsmart_get_template( 'profile-changepassword.php' );
				elseif(isset($_GET['action']) && $_GET['action']=="avatar"):
					echo dsmart_get_template( 'profile-avatar.php' );
				elseif(isset($_GET['action']) && $_GET['action']=="addresses"):
					echo dsmart_get_template( 'profile-addresses.php' );
				elseif(isset($_GET['action']) && $_GET['action']=="orders"):
					echo dsmart_get_template( 'profile-orders.php' );
				endif;?>
		</div>
	</div>
</div>
<div class="book-loading"><img src="<?php echo plugin_dir_url( __FILE__ ).'img/loading.gif'; ?>"></div>
<?php
get_footer();