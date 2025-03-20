<?php 
global $wpdb;
$table_name = $wpdb->prefix . "shop_address"; 
$charset_collate = $wpdb->get_charset_collate();
$sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  shop_name text NOT NULL,
  shop_address text NOT NULL,
  email text NOT NULL,
  latitude text NOT NULL,
  longitude text NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );
$table_name1 = $wpdb->prefix . "rating_product"; 
$sql1 = "CREATE TABLE $table_name1 (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  user_id int NOT NULL,
  post_id int NOT NULL,
  comment text NOT NULL,
  rating int NOT NULL,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;";
dbDelta( $sql1 );