<?php
/*
Plugin Name: Chapter 6 - Blockquote block
Plugin URI:
Description: Declares a plugin that will be visible in the WordPress admin interface
Version: 1.0
Author: Yannick Lefebvre
Author URI: http://ylefebvre.ca
License: GPLv2
*/

add_action( 'init', 'ch6bb_register_block' );

function ch6bb_register_block() {
	if ( !function_exists( 'register_block_type' ) ) {
		return;
	}

    $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

	wp_register_script(
		'ch6bb-blockquote-block',
		plugins_url( 'build/index.js', __FILE__ ),
		$asset_file['dependencies'],
		$asset_file['version']
	);

	wp_register_style(
		'ch6bb-blockquote-style',
		plugins_url( 'css/style.css', __FILE__ ),
		array( ),
		filemtime( plugin_dir_path( __FILE__ ) . '/css/style.css' )
	);

	register_block_type( 'ch6bb-blockquote-block/blockquote',
    array(
			'style' => 'ch6bb-blockquote-style',
			'editor_script' => 'ch6bb-blockquote-block',
	) );
}

