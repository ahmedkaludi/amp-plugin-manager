<?php
/*
Plugin Name: AMPforWP 3rd Party Plugin Supporter 
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
function ampforwp_api_request_disable_plugin( $plugins ) {

	$plugins_not_needed = array(
		'squirrly-seo/squirrly.php'
	);

	foreach ( $plugins_not_needed as $plugin ) {
		$key = array_search( $plugin, $plugins );
		if ( false !== $key ) {
			unset( $plugins[ $key ] );
		}
	}

	return $plugins;
}