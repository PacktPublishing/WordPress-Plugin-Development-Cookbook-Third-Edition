<?php
/*
Plugin Name: Chapter 6 - Block Template
Plugin URI:
Description: Declares a plugin that will be visible in the WordPress admin interface
Version: 1.0
Author: Yannick Lefebvre
Author URI: http://ylefebvre.ca
License: GPLv2
*/

add_action( 'init', 'ch6bt_register_block' );

function ch6bt_register_block() {
	if ( !function_exists( 'register_block_type' ) ) {
		return;
	}
    
	$asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');
    
	wp_register_script(
		'ch6bt-twitter-feed',
		plugins_url( 'build/index.js', __FILE__ ),
		$asset_file['dependencies'],
		$asset_file['version']
	);
    
	register_block_type( 'ch6bt/twitter-feed', array(
		'editor_script' => 'ch6bt-twitter-feed',
	) );
}
    