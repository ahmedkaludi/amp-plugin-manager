<?php 
/*
Plugin Name: AMPforWP Plugin Manager
Plugin URI: https://wordpress.org/plugins/accelerated-mobile-pages/
Description: MU Plugin Creator for Accelerated Mobile Pages
Version: 1.1
Author: Ahmed Kaludi, Mohammed Kaludi
Author URI: https://ampforwp.com/
Donate link: https://www.paypal.me/Kaludi/5
License: GPL2
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('AMP_MU_CURRENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define('AMP_MU_PLUGIN_TARGET_FILE', AMP_MU_CURRENT_PLUGIN_DIR . 'plugin/ampforwp-plugin-supporter.php' );
define('AMPFORWP_PLUGIN_MANAGER_VERSION', '1.1');

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'AMP_PLUGIN_MANAGER_STORE_URL', 'https://accounts.ampforwp.com/' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'AMP_PLUGIN_MANAGER_ITEM_NAME', 'AMP Plugin Manager' );

// the download ID. This is the ID of your product in EDD and should match the download ID visible in your Downloads list (see example below)
//define( 'AMPFORWP_ITEM_ID', 2502 );
// the name of the settings page for the license input to be displayed
define( 'AMP_PLUGIN_MANAGER_LICENSE_PAGE', 'amp-plugin-manager' );
if(! defined('AMP_PLUGIN_MANAGER_ITEM_FOLDER_NAME')){
    $folderName = basename(__DIR__);
    define( 'AMP_PLUGIN_MANAGER_ITEM_FOLDER_NAME', $folderName );
}


register_activation_hook( __FILE__, 'ampforwp_plugin_supporter_activator' );
//Run this function on activation
function ampforwp_plugin_supporter_activator() {
	//If MU plugins directory is not available, create it
	if (!file_exists(WPMU_PLUGIN_DIR)) {
		@mkdir(WPMU_PLUGIN_DIR);
	}
	//If it is already created
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
	// Delete the option from options table after deactivating this plugin
	delete_option( 'ampforwp_activated_plugins_list' );

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
// Get all the active plugins
function get_the_active_plugins() {
    $active_plugins = array();
    $active_plugins = get_option ( 'active_plugins' );
    // Flip the array to display the names of the plugins
    $active_plugins = array_flip($active_plugins);
    return $active_plugins;
}
// Get the list of activated plugins to show in AMP options panel
function list_of_activated_plugins(){
    $activated_plugins_list = array();
    $all_active_plugins = array();
    $all_active_plugins = get_the_active_plugins();
    if( $all_active_plugins){
	    foreach ($all_active_plugins as $key => $value) {
	        $activated_plugins_list[ $key] =  $key;
	    }
	}
    return $activated_plugins_list;
}
// Add a section for Plugin manager in AMP Options
add_filter("redux/options/redux_builder_amp/sections", 'ampforwp_settings_to_disable_plugins');

if ( ! function_exists( 'ampforwp_settings_to_disable_plugins' ) ) {
			function ampforwp_settings_to_disable_plugins($sections){

		$sections[] = array(
	        'title' => __('Plugin Manager', 'redux-framework-demo'),
	        'icon'  => 'el el-magic',
			'desc'  => 'You can Disable Plugins only in AMP which are causing AMP validation errors',

	        'fields' =>  ampforwp_create_controls_for_plugin_manager(),
	        );

return $sections;
}
}
//Create controls for Plugin manager option panel
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
                        'title'    => __('Select plugins to Disable only in AMP', 'accelerated-mobile-pages'),
                        'required' => array('amp-plugin-manager-switch', '=', 1),
                        'options'     => list_of_activated_plugins(),
                    );

         return $controls;  
 }
 //Update the options table by adding activated plugins list 
add_action('redux/options/redux_builder_amp/saved', 'update_options_plugins_list');
function update_options_plugins_list(){
	global $redux_builder_amp; 
	$get_data_from_redux = array();
	$get_data_from_redux = $redux_builder_amp['amp-pm']; 
	 update_option('ampforwp_activated_plugins_list', $get_data_from_redux);
}
// Remove Plugin Manager section from AMP Options Panel after activation
add_action('after_setup_theme', function(){
	Redux::removeSection( 'redux_builder_amp','opt-plugins-manager');
});


/*
	Plugin Update Method
 */
require_once dirname( __FILE__ ) . '/updater/EDD_SL_Plugin_Updater.php';

// Check for updates
function amp_plugin_manager_plugin_updater() {

    // retrieve our license key from the DB
    
    // setup the updater
    $edd_updater = new AMP_PLUGIN_MANAGER_EDD_SL_Plugin_Updater( AMP_PLUGIN_MANAGER_STORE_URL, __FILE__, array(
            'version'   => AMPFORWP_PLUGIN_MANAGER_VERSION,                // current version number
            'license'   => '',                        // license key (used get_option above to retrieve from DB)
            'license_status'=> 'valid',
            'item_name' => AMP_PLUGIN_MANAGER_ITEM_NAME,          // name of this plugin
            'author'    => 'Mohammed Kaludi',                   // author of this plugin
            'beta'      => false,
        )
    );
}
add_action( 'admin_init', 'amp_plugin_manager_plugin_updater', 0 );