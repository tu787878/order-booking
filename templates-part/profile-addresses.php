<?php
$user = wp_get_current_user(); ?>
<?php if(isset($_GET['edit'])): 
    $address = ds_get_address($user->ID); ?>
    <h3 class="text-center title-page"><?php _e("Edit address"); ?></h3>
    <div class="notify"></div>
    <form method="POST" action="#" class="account-form address-form"> 
        <div class="form-group">
            <label><?php _e("Vornamen"); ?></label>
            <input type="text" name="customer_name1" value="<?php echo $address['customer_name1'] ?>">
        </div>
        <div class="form-group">
            <label><?php _e("Nachname"); ?></label>
            <input type="text" name="customer_name2" value="<?php echo $address['customer_name2'] ?>">
        </div>
        <div class="form-group">
            <label><?php _e("Telefon"); ?></label>
            <input type="number" name="customer_phone" value="<?php echo $address['customer_phone'] ?>">
        </div>
        <div class="form-group">
            <label><?php _e("E-Mail"); ?></label>
            <input type="text" name="customer_email" value="<?php echo $address['customer_email'] ?>">
        </div>
        <div class="form-group">
            <label><?php _e("Etage"); ?></label>
            <input type="text" name="customer_etage" value="<?php echo $address['customer_etage'] ?>">
        </div>
        <div class="form-group">
            <label><?php _e("Adresse"); ?></label>
            <input type="text" name="customer_street" value="<?php echo $address['customer_street'] ?>">
        </div>
        <div class="form-group">
            <label><?php _e("Stadt"); ?></label>
            <input type="text" name="customer_city" value="<?php echo $address['customer_city'] ?>">
        </div>
        <div class="form-group">
            <label><?php _e("PLZ"); ?></label>
            <input type="text" name="customer_zipcode" value="<?php echo $address['customer_zipcode'] ?>">
        </div>
        <div class="submit-button">
            <?php wp_nonce_field('profile_nonce','profile_nonce'); ?>
            <input type="hidden" name="address_action" value="edit"/>
            <button type="submit"><?php _e("Update"); ?></button>
        </div>
    </form>
<?php else: 
    $address = ds_get_address($user->ID);
    $full_address = "";
    // var_dump($address);
    if($address['customer_etage'] != ""){
        $full_address = $address['customer_etage'].", ";
    }
    $full_address .= $address['customer_street'].', '.$address['customer_city'].', '.$address['customer_zipcode']; ?>
    <h3 class="text-center title-page"><?php _e("Addresses"); ?></h3>
    <div class="notify"></div>
    <h4 class="text-center"><?php _e("Standardadresse"); ?></h4>
    <div class="list-address main-address">
        <?php if($address != false): ?>
            <div class="address-item" data-id="<?php echo $default_address['id']; ?>">
                <div class="address-wrap">
                    <ul class="list-data">
                        <li><?php echo $address['customer_name1'].' '.$address['customer_name2']; ?></li>
                        <li><?php echo $address['customer_phone']; ?></li>
                        <li><?php echo $address['customer_email']; ?></li>
                        <li><?php echo $full_address; ?></li>
                    </ul>
                </div>
                <div class="address-action">
                    <a href="<?php echo ds_merge_querystring(remove_query_arg(array('edit','add-new')), '?edit'); ?>"><?php _e("Bearbeiten"); ?></a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger"><?php _e("Keine Adresse"); ?></div>
        <?php endif; ?>
    </div>
    <?php if($address == null): ?>
        <a href="<?php echo ds_merge_querystring(remove_query_arg(array('edit','add-new')), '?edit'); ?>" class="dsmart-button"><?php _e("Neu hinzufügen"); ?></a>
    <?php endif;?>
    <?php wp_nonce_field('profile_nonce','profile_nonce'); ?>
<?php endif; ?>