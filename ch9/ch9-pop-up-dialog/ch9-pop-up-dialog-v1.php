<?php
/*
  Plugin Name: Chapter 9 - Pop-Up Dialog v1
  Plugin URI: 
  Description: Companion to recipe 'Displaying a pop-up dialog using the built-in ThickBox plugin'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

// Register function responsible for loading scripts
add_action( 'wp_enqueue_scripts', 'ch9pud_load_scripts' );

// Function to load jQuery and Thickbox scripts
function ch9pud_load_scripts() {
	wp_enqueue_script( 'jquery' );
	add_thickbox();
}

// Register function to add output to page footer
add_action( 'wp_footer', 'ch9pud_footer_code' );

// Footer display function
function ch9pud_footer_code() { ?>
	<script type="text/javascript">
		jQuery( document ).ready( function() {
			setTimeout(
				function(){
					tb_show( 'Pop-Up Message', '<?php echo plugins_url( 'content.html?width=420&height=220', __FILE__ )?>', null );
		}, 2000 );
		});

</script>
<?php
}