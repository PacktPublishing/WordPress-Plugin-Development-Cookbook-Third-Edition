<?php
/*
  Plugin Name: Chapter 9 - Pop-Up Dialog v2
  Plugin URI: 
  Description: Companion to recipe 'Controlling pop-up dialog display using shortcodes'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */
 
global $load_scripts;
$load_scripts = false;

// Register function responsible for loading scripts
add_action( 'wp_enqueue_scripts', 'ch9pud_load_scripts' );

// Function to load jQuery and Thickbox scripts
function ch9pud_load_scripts() {
	// Only load scripts is variable is set to true
	global $load_scripts;

	if ( $load_scripts ) {
		wp_enqueue_script( 'jquery' );
		add_thickbox();
	}
}

// Register function to add output to page footer
add_action( 'wp_footer', 'ch9pud_footer_code' );

// Footer display function
function ch9pud_footer_code() { 
	// Only load scripts is keyword is found on page
	global $load_scripts;
	if ( $load_scripts ) { ?>

	<script type="text/javascript">
		jQuery( document ).ready( function() {
			setTimeout(
				function(){
					tb_show( 'Pop-Up Message', '<?php echo plugins_url('content.html?width=420&height=220&modal=true', __FILE__ )?>', null );
		}, 2000 );
		});
	</script>

<?php } 
}

// Add filter function to parse all posts before pages are displayed
add_filter( 'the_posts', 'ch9pud_conditionally_add_scripts_and_styles' );

// the_posts filter function
function ch9pud_conditionally_add_scripts_and_styles( $posts ) {
	// Exit function immediately if no posts are present
	if ( empty( $posts ) ) {
		return $posts;
	}

	// Global variable to indicate if scripts should be loaded
	global $load_scripts;

	// Cycle through posts and set flag true if keyword is found
	foreach ( $posts as $post ) {		
		$shortcode_pos = stripos( $post->post_content, '[popup]', 0 );
		if ( $shortcode_pos !== false ) {
			$load_scripts = true;
			return $posts;
		}
	}

	// Return posts array unchanged
	return $posts;
}

add_shortcode( 'popup', 'ch9pud_popup_shortcode' );

function ch9pud_popup_shortcode() {
	return;
}