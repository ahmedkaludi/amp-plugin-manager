<?php
/*
Plugin Name: AMPforWP Plugin Manager Supporter 
Plugin URI: https://wordpress.org/plugins/accelerated-mobile-pages/
Description: Makes the AMP pages valid by disabling error causing plugins on AMP pages.
Version: 1.0
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$listener_term = '/amp/';
$current_url   = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '';
//Disable the plugins only if it's AMP endpoint
if ( strstr( $current_url, $listener_term ) ||  strstr( $current_url, '?amp') ) {
	add_filter( 'option_active_plugins', 'ampforwp_api_request_disable_plugin' , 100);
}
//Get the plugins list from options
function get_plugins_to_deactivate(){
	$list_of_plugins = array();
	$list_of_selected_plugins = '';
	$list_of_selected_plugins = get_option('ampforwp_activated_plugins_list');
	if($list_of_selected_plugins){	
		$list_of_selected_plugins = array_filter($list_of_selected_plugins);	
		foreach ($list_of_selected_plugins as $key => $value) {
			$list_of_plugins[] =  $key;
		}
	}
	return $list_of_plugins;
}
//Function to disable the plugins in AMP
function ampforwp_api_request_disable_plugin( $plugins ) {
	$plugins_not_needed = array();
	$plugins_not_needed = get_plugins_to_deactivate();
	if($plugins_not_needed){
		foreach ( $plugins_not_needed as $plugin_not_needed ) {
			$plugins_to_deactivate = array_search( $plugin_not_needed, $plugins );
			if ( false !== $plugins_to_deactivate ) {
				//Deactivate the selected plugins
				unset( $plugins[ $plugins_to_deactivate ] );
			}
		}
	}
	return $plugins;
}

