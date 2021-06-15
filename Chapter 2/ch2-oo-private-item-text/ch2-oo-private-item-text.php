<?php

/*
  Plugin Name: Chapter 2 - Object-Oriented - Private Item Text
  Plugin URI: 
  Description: Companion to recipe 'Writing plugins using object-oriented PHP'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

class CH2_OO_Private_Item_Text {

	function __construct() {
		add_shortcode( 'private', array( $this, 'ch2pit_private_shortcode' ) );
		add_action( 'init', array( $this, 'ch2pit_queue_stylesheet' ) );
	}

	function ch2pit_private_shortcode( $atts, $content = null ) {
		if ( is_user_logged_in() )
			return '<div class="private">' . $content . '</div>';
		else {
			$output = '<div class="register">';
			$output .= 'You need to become a member to access ';
			$output .= 'this content.</div>';
			return $output;
		}			
	}

	function ch2pit_queue_stylesheet() {
		wp_enqueue_style( 'privateshortcodestyle', plugins_url( 'stylesheet.css', __FILE__ ) );
	}

}

$my_ch2_oo_private_item_text = new CH2_OO_Private_Item_Text();