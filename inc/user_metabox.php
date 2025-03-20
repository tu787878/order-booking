<?php
add_action('show_user_profile', 'add_custom_user_profile_fields');
add_action('personal_options_update', 'save_custom_user_profile_fields');

// for admins
add_action('edit_user_profile', 'add_custom_user_profile_fields');
add_action('edit_user_profile_update', 'save_custom_user_profile_fields');

function add_custom_user_profile_fields($user) {?>
    <h3><?php _e("User Image") ?></h3>
	<table class="form-table">
		<tr>
			<th><label for="location"><?php _e("Choose image");?></label></th>
			<td>
			  	<div class="user-avatar"><?php echo get_avatar($user->ID, 200)?></div>
			  	<input type="hidden" name="wp_user_avatar" value="<?php echo get_user_meta($user->ID,'wp_user_avatar',true); ?>">
			  	<input type="button" class="user-add-button button" value="<?php _e("Change"); ?>" />
				<input type="button" class="user-remove-button button" value="<?php _e("Remove"); ?>" />
			</td>
		</tr>
	</table>
	<style type="text/css">
		.user-avatar img{
			width: 200px;
			height: 200px;
			overflow: hidden;
			object-fit: cover;
		}
	</style>
	<script type="text/javascript">
		jQuery(function($){
		    $('body').on('click', '.user-add-button', function(e){
		        e.preventDefault();

		            var button = $(this),
		            	wrap = $(".user-avatar"),
		                custom_uploader = wp.media({
		            title: 'Insert image',
		            library : {
		                type : 'image'
		            },
		            button: {
		                text: 'Use this image'
		            },
		            multiple: false
		        }).on('select', function() {
		            var attachment = custom_uploader.state().get('selection').first().toJSON();
		            wrap.html('<img src="' + attachment.url + '" />');
		            $("input[name=wp_user_avatar]").val(attachment.id);
		        })
		        .open();
		    });
		    $('body').on('click', '.user-remove-button', function(){
		    	$(".user-avatar").html("");
		    	$("input[name=wp_user_avatar]").val("");
		        return false;
		    });

		});
	</script>
<?php }
function save_custom_user_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id))
        return FALSE;

    $wp_user_avatar = ( isset($_POST['wp_user_avatar']) ) ? $_POST['wp_user_avatar'] : '';

    // human readable value and id
    update_user_meta($user_id, 'wp_user_avatar', $wp_user_avatar);
    //update_user_meta($user_id, 'location_id', $location->term_id);
}