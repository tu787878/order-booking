<?php
$user = wp_get_current_user();
$userdata  = get_userdata($user->ID);
if($userdata->display_name=="")
	$display_name = $userdata->user_login;
else
	$display_name = $userdata->display_name;
$phone = get_user_meta($user->ID,'phone',true); ?>
<form method="POST" action="#" class="account-form profile-form">
	<h3 class="text-center title-page"><?php _e("Ihre Informationen"); ?></h3>
	<div class="notify"></div>
	<div class="form-group">
		<label for="email"><?php _e("E-Mail"); ?></label>
		<input type="text" name="email" id="email" value="<?php echo $user->user_email; ?>" readonly>
	</div>
	<div class="form-group">
		<label for="fullname"><?php _e("Vornamen"); ?></label>	
		<input type="text" name="firstname" id="firstname" value="<?php echo $user->first_name; ?>" required>		
	</div>
	<div class="form-group">
		<label for="fullname"><?php _e("Nachname"); ?></label>	
		<input type="text" name="lastname" id="lastname" value="<?php echo $user->last_name; ?>" required>		
	</div>
	<div class="submit-button">
		<?php wp_nonce_field('profile_nonce','profile_nonce'); ?>
		<button type="submit"><?php _e("Update"); ?></button>
	</div>
</form>