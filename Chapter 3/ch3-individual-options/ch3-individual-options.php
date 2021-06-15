<?php
/*
  Plugin Name: Chapter 3 - Individual Options
  Plugin URI: 
  Description: Companion to recipe 'Creating default user settings on plugin initialization'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */
 
// Register function to be called when plugin is activated
register_activation_hook( __FILE__, 'ch3io_set_default_options' );

// Function to check if option exist and create a new option if it does not
function ch3io_set_default_options() {
	if ( false === get_option( 'ch3io_ga_account_name' ) ) {
		add_option( 'ch3io_ga_account_name', 'UA-0000000-0' );
	}
}