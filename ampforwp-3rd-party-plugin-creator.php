<?php 
/*
Plugin Name: AMPforWP Plugin Manager
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

if ( defined( 'AMPFORWP_PLUGIN_DIR' ) ) { 
	add_filter( 'plugin_action_links', 'ampforwp_plugin_creator_settings_link', 10, 5 );
} else {

	add_filter( 'plugin_action_links', 'ampforwp_plugin_creator_activation_link', 10, 5 );

	// Add Activate Parent Plugin button in settings page
	if ( ! function_exists( 'ampforwp_plugin_creator_activation_link' ) ) {
		function ampforwp_plugin_creator_activation_link( $actions, $plugin_file ) {
			static $plugin;
			if (!isset($plugin))
				$plugin = plugin_basename(__FILE__);
				if ($plugin == $plugin_file) {
						$settings = array('settings' => '<a href="plugin-install.php?s=accelerated+mobile+pages&tab=search&type=term">' . __('Please Activate the Parent Plugin.', 'ampforwp_plugin_creator') . '</a>');
						$actions = array_merge($settings , $actions );
					}
				return $actions;
		}
	}
	// Return if Parent plugin is not active, and don't load the below code.
	return;
}

// Add settings Icon in the plugin activation page
if ( ! function_exists( 'ampforwp_plugin_creator_settings_link' ) ) {
	function ampforwp_plugin_creator_settings_link( $actions, $plugin_file )  {
			static $plugin;
			if (!isset($plugin))
				$plugin = plugin_basename(__FILE__);
				if ($plugin == $plugin_file) {
						$settings = array('settings' => '<a href="admin.php?page=amp_options&tab=8">' . __('Settings', 'ampforwp_plugin_creator') . '</a>');
			  		$actions = array_merge( $actions , $settings);
					}
				return $actions;
	}
}
function get_the_active_plugins() {
    $plugins = array();
    $plugins = get_option ( 'active_plugins' );
    $plugins = array_flip($plugins);
    return $plugins;
}
function list_the_plugins_names(){
    $new_array = array();
    $arrray = get_the_active_plugins();
    foreach ($arrray as $key => $value) {
        $new_array[ $key] =  $key;
    }
    return $new_array;
}
add_filter("redux/options/redux_builder_amp/sections", 'ampforwp_settings_to_disable_plugins');

if ( ! function_exists( 'ampforwp_settings_to_disable_plugins' ) ) {
			function ampforwp_settings_to_disable_plugins($sections){

		$sections[] = array(
	        'title' => __('AMPforWP Plugin Manager', 'redux-framework-demo'),
	        'icon' => 'el el-th-large',
					'desc'  => 'Disable Plugins in AMP',
	        'fields' =>  ampforwp_create_controls_for_plugin_manager(),
	        );

return $sections;
}
}

function ampforwp_create_controls_for_plugin_manager(){
	  $controls[]  = 	array(
                        'id'        =>'amp-plugin-manager-switch',
                        'type'      => 'switch',
                        'title'     => __('Plugin Manager', 'accelerated-mobile-pages'),
                        'default'   => 0, 
                        'true'      => 'true',
                        'false'     => 'false',
                    );

       $controls[]  =    array(
                        'id'       => 'amp-pm',
                        'type'     => 'checkbox',
                        'title'    => __('Select plugins to disable', 'accelerated-mobile-pages'),
                        'required' => array('amp-plugin-manager-switch', '=', 1),
                        'options'     => list_the_plugins_names(),
                    );

         return $controls;  
 }
add_action('redux/options/redux_builder_amp/saved', 'update_plugin_data');
function update_plugin_data(){
	global $redux_builder_amp; 
	$get_data_from_redux = $redux_builder_amp['amp-pm']; 
	 update_option('new_mu_plugins', $get_data_from_redux);
}