<?php 
/*
Plugin Name: AMPforWP 3rd Party Plugin Creator 
Plugin URI: https://wordpress.org/plugins/accelerated-mobile-pages/
Description: MU Plugin Creator for Accelerated Mobile Pages
Version: 1.0
Author: Ahmed Kaludi, Mohammed Kaludi
Author URI: https://ampforwp.com/
Donate link: https://www.paypal.me/Kaludi/5
License: GPL2
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('AMP_MU_CURRENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define('AMP_MU_PLUGIN_TARGET_FILE', AMP_MU_CURRENT_PLUGIN_DIR . 'plugin/ampforwp-plugin-supporter.php' );

register_activation_hook( __FILE__, 'ampforwp_plugin_supporter_activator' );

function ampforwp_plugin_supporter_activator() {
	if (!file_exists(WPMU_PLUGIN_DIR)) {
		@mkdir(WPMU_PLUGIN_DIR);
	}
	
	if ( file_exists( AMP_MU_PLUGIN_TARGET_FILE )) {
		@copy( AMP_MU_PLUGIN_TARGET_FILE , WPMU_PLUGIN_DIR . "/ampforwp-plugin-supporter.php");
	}

}

register_deactivation_hook( __FILE__, 'ampforwp_plugin_supporter_deactivation_hook' );
function  ampforwp_plugin_supporter_deactivation_hook() {

	// This code deletes the plugin file from MU folder on deactivation.
	if ( file_exists( WPMU_PLUGIN_DIR . "/ampforwp-plugin-supporter.php" ) ) {
		@unlink( WPMU_PLUGIN_DIR . "/ampforwp-plugin-supporter.php" );
	}

}