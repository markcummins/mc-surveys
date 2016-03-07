<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

/*
Plugin Name: Surveys
Description: Creates Surveys
Version: 2.0
Author: Mark Cummins
*/

include_once plugin_dir_path( __FILE__ ) . "core/init.php";

function mc_surveys_activate(){
    
    global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'mc_survey_fields';

    $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `post_id` int(10) unsigned NOT NULL,
              `type` varchar(128) NOT NULL,
              `order` smallint(6) NOT NULL DEFAULT '0',
              `attributes` text NOT NULL, PRIMARY KEY (`id`)) {$charset_collate};";
    
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}
register_activation_hook(__FILE__, 'mc_surveys_activate');