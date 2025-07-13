<?php
/**
 * Archive orders
 *
 */

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
<?php
$mtheme_pagelayout_type="two-column";
?>
<div class="contents-wrap float-left two-column">
<?php
	if ( have_posts() )
		the_post();
?>
	<?php
		rewind_posts();
		get_template_part( 'loop', 'archive' );
	?>

</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
