<?php $current_user = wp_get_current_user();?>
<form method="POST" action="#" class="account-form avatar-form" enctype="multipart/form-data"> 
	<h3 class="text-center title-page"><?php _e("Avatar ändern"); ?></h3>
	<div class="notify"></div>
	<div class="avatar">
		<input type="file" name="avatar" value="" class="avatar-file" accept="image/*">
		<label for="avatar-file" class="btn-avatar">      			
      		<?php echo get_avatar($current_user->ID, 200); ?>
  		</label>
	</div>
	<div class="submit-button">
        <?php wp_nonce_field('profile_nonce','profile_nonce'); ?>
        <button type="submit"><?php _e("Update"); ?></button>
    </div>
</div>