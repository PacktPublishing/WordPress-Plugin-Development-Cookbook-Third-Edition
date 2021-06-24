<?php
/*
  Plugin Name: Chapter 3 - Multi-Level Menu V2
  Plugin URI:
  Description: Companion to recipe 'Splitting admin code from the main plugin file to optimize site performance'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

define( 'ch3mlm', 1 );

if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . 'ch3-multi-level-menu-admin-functions.php';
}