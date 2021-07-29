<?php
/*
  Plugin Name: Chapter 9 - Calendar Picker v2
  Plugin URI: 
  Description: Companion to recipe 'Adding tooltips to admin page form fields using the TipTip plugin'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

// Register function responsible for loading scripts
add_action( 'admin_enqueue_scripts', 'ch9cp_admin_scripts' );

// Load core jQuery script, UI script, datepicker script and datepicker style
// Load tooltip script and style
function ch9cp_admin_scripts() {
	$screen = get_current_screen();
	if ( 'post' == $screen->base && 'post' == $screen->post_type ) {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'datepickercss', 
			plugins_url( 'css/jquery-ui.min.css', __FILE__ ), array(), '1.12.1' );
		wp_enqueue_script( 'tiptipjs', plugins_url( 'tiptip/jquery.tipTip.minified.js', __FILE__ ), array(), '1.3' );
		wp_enqueue_style( 'tiptipcss', plugins_url( 'tiptip/tipTip.css', __FILE__ ), array(), '1.3' );
	}
}

// Register callback to be executed when meta boxes are being created
add_action( 'add_meta_boxes', 'ch9cp_register_meta_box' );

// Create meta box to display date selection field
function ch9cp_register_meta_box() {
	add_meta_box('ch9cp_datepicker_box', 'Assign Date',
		'ch9cp_date_meta_box', 'post', 'normal');
}

// Display meta box contents
function ch9cp_date_meta_box( $post ) { ?>
	<input type="text" class="ch9cp_tooltip" title="Please enter a date" id="ch9cp_date" name="ch9cp_date" />

	<!-- Javascript function to display calendar button -->
	<!-- and associate date selection with field -->
	<!-- and display help tooltips -->
	<script type='text/javascript'>
		jQuery( document ).ready( function() {
			jQuery( '#ch9cp_date' ).datepicker( { minDate: '+0',
				dateFormat: 'yy-mm-dd', showOn: 'both',
				constrainInput: true } );

			jQuery( '.ch9cp_tooltip' ).each( function() {
					jQuery( this ).tipTip();
				}
			);
		});
	</script>
<?php }