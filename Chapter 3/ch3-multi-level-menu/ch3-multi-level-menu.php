<?php
/*
  Plugin Name: Chapter 3 - Multi-Level Menu
  Plugin URI:
  Description: Companion to recipe 'Creating a multi-level administration menu'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

// Register function to be called when the admin menu is constructed
add_action( 'admin_menu', 'ch3mlm_admin_menu' );

// Add two menu items to the admin menu, with one being a top-level menu item and the other being a sub-menu
function ch3mlm_admin_menu() {
	// Create top-level menu item
	add_menu_page( 'My Complex Plugin Configuration Page',
		'My Complex Plugin', 'manage_options',
		'ch3mlm-main-menu', 'ch3mlm_my_complex_main',
		'dashicons-menu-alt3' );

	// Create a sub-menu under the top-level menu
	add_submenu_page( 'ch3mlm-main-menu',
		'My Complex Menu Sub-Config Page', 'Sub-Config Page',
		'manage_options', 'ch3mlm-sub-menu',
		'ch3mlm_my_complex_submenu' );
}