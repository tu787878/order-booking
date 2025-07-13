<?php
if(is_user_logged_in() || get_option('show_profile') !== "on"){
	wp_redirect(home_url());
	exit;
}
get_header()?>
<div class="container">
	<?php if(isset($_GET['key']) && isset($_GET['login']) && $_GET['key'] != "" && $_GET['login'] != ""):
		$user = check_password_reset_key($_GET['key'], $_GET['login']);
		if ( is_wp_error( $user ) ) {
		    if ( $user->get_error_code() === 'expired_key' )
		        echo '<div classs="alert alert-danger">Abgelaufene Taste.</div>';
		    else
		        echo '<div classs="alert alert-danger">Taste ist nicht vorhanden.</div>';
		}else{?>
			<form class="account-form reset-form" action="#" method="POST">
				<div class="notify"></div>	
				<div class="form-group">
					<input type="password" name="password" placeholder="<?php _e("Passwort eingeben *"); ?>">
				</div>
				<div class="form-group">
					<input type="password" name="password2" placeholder="<?php _e("Passwort erneut eingeben *"); ?>">
				</div>
				<div class="submit-button">
					<input type="hidden" name="key" value="<?php echo $_GET['key']; ?>">
					<input type="hidden" name="login" value="<?php echo $_GET['login']; ?>">
					<?php wp_nonce_field('resetpass_nonce','resetpass_nonce'); ?>
					<button type="submit"><?php _e("Passwort aktualisieren"); ?></button>
				</div>
			</form>
		<?php }	
	else:?>		
		<form class="account-form lostpass-form" action="#" method="POST">
			<div class="notify"></div>						
			<div class="form-group">
				<input type="text" name="username" placeholder="Benutzername oder E-Mail Adresse*">
			</div>
			<div class="submit-button">
				<p class="ds-note"><?php echo sprintf("<a href=\"%s\">Nie vermint. Ich habe es mir gemerkt.</a>",get_permalink(get_page_id_by_template('templates/template-login.php'))) ?></p>
				<?php wp_nonce_field('lostpass_nonce','lostpass_nonce'); ?>
				<button type="submit"><?php _e("Rücksetzanforderung senden"); ?></button>
			</div>
		</form>
	<?php endif;?>
</div>
<div class="book-loading"><img src="<?php echo plugin_dir_url( __FILE__ ).'img/loading.gif'; ?>"></div>
<?php get_footer(); ?>
