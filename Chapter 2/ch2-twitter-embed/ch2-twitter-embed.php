<?php

/*
  Plugin Name: Chapter 2 - Twitter Embed
  Plugin URI: 
  Description: Companion to recipe 'Creating a new shortcode with parameters'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

add_shortcode( 'twitterfeed', 'ch2te_twitter_embed_shortcode' );

function ch2te_twitter_embed_shortcode( $atts ) {
    extract( shortcode_atts( array(
        'user_name' => 'ylefebvre'
    ), $atts ) );
	
	if ( empty( $user_name ) ) {
        $user_name = 'ylefebvre';
    } else {
		$user_name = sanitize_text_field( $user_name );
	}
    
	$output = '<a class="twitter-timeline" href="';
	$output .= esc_url( 'https://twitter.com/' . $user_name );
	$output .= '">Tweets by ' . esc_html( $user_name );
	$output .= '</a><script async ';
	$output .= 'src="//platform.twitter.com/widgets.js" ';
	$output .= 'charset="utf-8"></script>';
    
    return $output;
}