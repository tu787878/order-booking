<?php 
if(is_user_logged_in() || get_option('show_profile') !== "on"){
	wp_redirect(home_url());
	exit;
}
get_header();?>
<div class="container">
	<form class="account-form register-form">
		<div class="notify"></div>
		<div class="form-group">
			<input type="text" name="firstname" placeholder="<?php _e("Vornamen *") ?>">
		</div>
		<div class="form-group">
			<input type="text" name="lastname" placeholder="<?php _e("Nachname *") ?>">
		</div>
		<div class="form-group">
			<input type="text" name="username" placeholder="<?php _e("Benutzername *") ?>">
		</div>
		<div class="form-group">
			<input type="text" name="email" placeholder="<?php _e("Email address *") ?>">
		</div>
		<div class="form-group">
			<input type="number" name="phone" placeholder="<?php _e("Telefon *") ?>">
		</div>
		<div class="form-group">
			<input type="text" name="etage" placeholder="<?php _e("Etage") ?>">
		</div>
		<div class="form-group">
			<input type="text" name="street" placeholder="<?php _e("Strasse *") ?>">
		</div>
		<div class="form-group">
			<input type="text" name="city" placeholder="<?php _e("Stadt *") ?>">
		</div>
		<div class="form-group">
			<input type="text" name="zipcode" placeholder="<?php _e("PLZ *") ?>">
		</div>
		<div class="form-group">
			<input type="password" name="password" placeholder="<?php _e("Passwort *") ?>">
		</div>
		<div class="form-group">
			<input type="password" name="repassword" placeholder="<?php _e("Neu-Passwort *") ?>">
		</div>
		<div class="submit-button">
			<p class="ds-note"><?php echo sprintf("Sie haben ein Konto? <a href=\"%s\">Jetzt anmelden.</a>",get_permalink(get_page_id_by_template('templates/template-login.php'))) ?></p>
			<?php wp_nonce_field('register_nonce','register_nonce'); ?>
			<button type="submit"><?php _e("Registrieren"); ?></button>
		</div>
	</form>
</div>
<div class="book-loading"><img src="<?php echo plugin_dir_url( __FILE__ ).'img/loading.gif'; ?>"></div>
<?php get_footer();?>