<?php
/*
Plugin Name: Zendy Speed: Query Strings
Plugin URI: https://kauai.zendy.net/wordpress/plugins/zendy-speed/
Description: Remove query strings from static resources; improve your website performance and your performace grade on YSlow, Google PageSpeed, and Pingdom.
Author: Zendy Web Studio
Version: 2.0.1
Author URI: https://kauai.zendy.net
*/

/**
 * Strip URI from 'ver' param
 * @param $uri string
 * @return $stripped_uri string
 */ 
if( ! function_exists( 'zendy_speed_query_strings' ) ){

	function zendy_speed_query_strings( $uri ){
	
		// Strip version number from resource (ver param first on GET params list)
		if( strpos( $uri, '?ver' ) !== false ){
			$uri_parts = explode( '?ver', $uri );
			$stripped_uri = $uri_parts[0];	
			
		// Strip version number from resource (ver param *not* first on GET params list)
		}elseif( strpos( $uri, '&ver' ) !== false ){
			$uri_parts = explode( '&ver', $uri );
			$stripped_uri = $uri_parts[0];		
		}
		
		// If stripped URI has been set, return it
		// Otherwise, just return the full URI
		return $stripped_uri ? $stripped_uri : $uri;
		
	}
	
}

// Hook it all up (not for admins though)
if( ! is_admin() ) {

	// Filter JS
	add_filter( 'script_loader_src', 'zendy_speed_query_strings', 15, 1 );
	
	// Filter CSS
	add_filter( 'style_loader_src', 'zendy_speed_query_strings', 15, 1 );

}

?>