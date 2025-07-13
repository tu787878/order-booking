<?php $current_user = wp_get_current_user();
if($current_user->display_name=="")
    $display_name = $current_user->user_login;
else
    $display_name = $current_user->display_name;?>
<div class="profile-thumb"><a href="<?php echo get_the_permalink(); ?>?action=avatar"><?php echo get_avatar($current_user->ID, 200)?></a></div>
<div class="profile-usertitle"><?php echo $display_name; ?></div>
<div class="profile-usermenu">
    <ul class="nav">
        <li class="<?php if(!isset($_GET['action']) || isset($_GET['action']) && $_GET['action']=="info") echo 'active'; ?>">
            <a href="<?php echo get_the_permalink(); ?>?action=info">
                <?php _e("Informationen"); ?>
            </a>
        </li>
        <li class="<?php if(isset($_GET['action']) && $_GET['action']=="changepassword") echo 'active'; ?>">
            <a href="<?php echo get_the_permalink(); ?>?action=changepassword">
                <?php _e("Passwort ändern"); ?>
            </a>
        </li>
        <li class="<?php if(isset($_GET['action']) && $_GET['action']=="avatar") echo 'active'; ?>">
            <a href="<?php echo get_the_permalink(); ?>?action=avatar">
                <?php _e("Avatar"); ?>
            </a>
        </li>
        <li class="<?php if(isset($_GET['action']) && $_GET['action']=="addresses") echo 'active'; ?>">
            <a href="<?php echo get_the_permalink(); ?>?action=addresses">
                <?php _e("Adresse"); ?>
            </a>
        </li>
        <li class="<?php if(isset($_GET['action']) && $_GET['action']=="orders") echo 'active'; ?>">
            <a href="<?php echo get_the_permalink(); ?>?action=orders">
                <?php _e("Bestellungen"); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo wp_logout_url(home_url() );?>">
                <?php _e("Abmelden"); ?>
            </a>
        </li>
    </ul>
</div>