<?php
/*
  Plugin Name: Chapter 5 - Hide Custom Fields
  Plugin URI: 
  Description: Companion to recipe 'Hiding the Custom Field section in the post editor'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

// Register function to be called when administration menu is built
add_action( 'add_meta_boxes', 'ch5_hcf_remove_custom_fields_metabox' );

// Function to remove custom fields meta box
function ch5_hcf_remove_custom_fields_metabox() {
	remove_meta_box( 'postcustom', 'post', 'normal' );
	remove_meta_box( 'postcustom', 'page', 'normal' );
}