<?php
$user = wp_get_current_user();
$userdata  = get_userdata($user->ID);
if($userdata->display_name=="")
	$display_name = $userdata->user_login;
else
	$display_name = $userdata->display_name;?>
<form method="POST" action="#" class="account-form password-form"> 
    <h3 class="text-center title-page"><?php _e("Passwort ändern"); ?></h3>
    <div class="notify"></div>
    <div class="form-group">
        <label for="old-pass"><?php _e("Altes Passwort"); ?></label>
        <input id="old-pass" name="old-pass" type="password">
    </div>
    <div class="form-group ">
        <label for="new-pass"><?php _e("Neues Passwort"); ?></label>
        <input id="new-pass" name="new-pass" type="password">
    </div>
    <div class="form-group">
        <label for="confirm-pass"><?php _e("Bestätigen Sie das Passwort"); ?></label>
        <input id="confirm-pass" name="confirm-pass" type="password">
    </div>
    <div class="submit-button">
        <?php wp_nonce_field('profile_nonce','profile_nonce'); ?>
        <button type="submit"><?php _e("Update"); ?></button>
    </div>
</form>