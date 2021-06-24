<?php
/*
  Plugin Name: Chapter 2 - Favicon
  Plugin URI:
  Description: Companion to recipe 'Using WordPress path utility functions'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

add_action( 'wp_head', 'ch2fi_page_header_output' );

function ch2fi_page_header_output() {
	$site_icon_url = get_site_icon_url();
	if ( !empty( $site_icon_url ) ) {
		wp_site_icon();
	} else {
		$icon = plugins_url( 'favicon.ico', __FILE__ );
?>

    <link rel="shortcut icon" href="<?php echo $icon; ?>" />
 <?php  
	}
}


