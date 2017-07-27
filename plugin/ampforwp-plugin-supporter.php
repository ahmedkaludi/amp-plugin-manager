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

if ( strstr( $current_url, $listener_term ) ) {
	add_filter( 'option_active_plugins', 'ampforwp_api_request_disable_plugin' , 100);
}


function get_active_plugins_list(){

	$list_of_plugins = array();
	$list_of_selected_plugins = '';

	$list_of_selected_plugins = get_option('new_mu_plugins');
		
	$list_of_selected_plugins = array_filter($list_of_selected_plugins);
		
	
	foreach ($list_of_selected_plugins as $key => $value) {
		$list_of_plugins[] =  $key;
	}
	return $list_of_plugins;
}


function ampforwp_api_request_disable_plugin( $plugins ) {




	$plugins_not_needed = get_active_plugins_list();

	foreach ( $plugins_not_needed as $plugin ) {
		$key = array_search( $plugin, $plugins );
		if ( false !== $key ) {
			unset( $plugins[ $key ] );
		}
	}

	return $plugins;
}

