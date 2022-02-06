<?php
/*
  Plugin Name: Chapter 2 - Email Page Link
  Plugin URI: Companion to recipe 'Adding text after each item's content using filters'
  Description: Write your description for the plugin.
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

add_filter( 'the_content', 'ch2epl_email_page_filter' );

function ch2epl_email_page_filter ( $the_content ) { 
     
    // build url to mail message icon
    $mail_icon_url = plugins_url( 'mailicon.png', __FILE__ ); 
 
    // Set value of $new_content variable to previous content 
    $new_content = $the_content; 

    // Append image with mailto link after content, including 
    // the item title and permanent URL
	$new_content .= '<div class="email_link">';
    $new_content .= '<a title="Email article link"';
    $new_content .= 'href="mailto:someone@somewhere.com?';
    $new_content .= 'subject=Check out this interesting ';
    $new_content .= 'article entitled ';
    $new_content .= get_the_title();
    $new_content .= '&body=Hi!%0A%0AYou might ';
    $new_content .= 'enjoy this article entitled ';
    $new_content .= get_the_title() . '.%0A%0A';
    $new_content .= get_permalink();
    $new_content .= '%0A%0AEnjoy!">';

    $new_content .= '<img alt="Email icon" src="';
    $new_content .= esc_url( $mail_icon_url );
    $new_content .= '" /></a></div>';

    // Return filtered content for display on the site 
    return $new_content; 
} 