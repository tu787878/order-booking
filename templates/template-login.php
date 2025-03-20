<?php 
if(is_user_logged_in() || get_option('show_profile') !== "on"){
	wp_redirect(home_url());
	exit;
}
get_header();?>
<div class="container">
	<form class="account-form login-form">
		<div class="notify"></div>
		<div class="form-group">
			<input type="text" name="username" placeholder="<?php _e("Benutzername *") ?>">
		</div>
		<div class="form-group">
			<input type="password" name="password" placeholder="<?php _e("Passwort *") ?>">
		</div>
		<div class="submit-button">
			<p class="ds-note text-center"><?php echo sprintf("Sie haben kein Konto? <a href=\"%s\">Jetzt registrieren.</a>",get_permalink(get_page_id_by_template('templates/template-register.php'))) ?></p>
			<?php wp_nonce_field('login_nonce','login_nonce'); ?>
			<button type="submit"><?php _e("Anmeldung"); ?></button>
			<p class="ds-note text-center"><?php echo sprintf("<a href=\"%s\">Passwort vergessen?</a>",get_permalink(get_page_id_by_template('templates/template-lostpass.php'))) ?></p>
		</div>
	</form>
</div>
<div class="book-loading"><img src="<?php echo plugin_dir_url( __FILE__ ).'img/loading.gif'; ?>"></div>
<?php get_footer();?>