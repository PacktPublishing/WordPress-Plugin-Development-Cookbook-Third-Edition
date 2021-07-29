<?php
/*
  Plugin Name: Chapter 9 - Load jQuery
  Plugin URI: 
  Description: Companion to recipe 'Safely loading jQuery onto WordPress web pages'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

// Register function to be called when scripts are being queued
add_action( 'wp_enqueue_scripts', 'ch9lj_front_facing_pages' );

// Function to request to load jquery script on front-facing pages
function ch9lj_front_facing_pages() {
	wp_enqueue_script( 'jquery' );
}