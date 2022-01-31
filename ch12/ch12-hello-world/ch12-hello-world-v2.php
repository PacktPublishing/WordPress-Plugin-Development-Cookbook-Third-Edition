<?php
/*
  Plugin Name: Chapter 12 - Hello World V2
  Plugin URI: 
  Description: Companion to recipe 'Making admin page code ready for translation'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

register_activation_hook( __FILE__, 'ch12hw_set_default_options_array' );

function ch12hw_set_default_options_array() {
	if ( false === get_option( 'ch12hw_options' ) ) {
		$new_options = array();
		$new_options['message'] = __( 'Hello World', 'ch12hw_hello_world' );
		add_option( 'ch12hw_options', $new_options );
	}
}

add_action( 'admin_menu', 'ch12hw_settings_menu' );

function ch12hw_settings_menu() {
	add_options_page(
		__( 'Hello World', 'ch12hw_hello_world' ),
		__( 'Hello World', 'ch12hw_hello_world' ),
		'manage_options',
		'ch12hw-hello-world', 'ch12hw_config_page' );
}

function ch12hw_config_page() {
	// Retrieve plugin configuration options from database
	$options = get_option( 'ch12hw_options' );
	?>

	<div id="ch12hw-general" class="wrap">
	<h2><?php _e( 'Hello World Configuration', 'ch12hw_hello_world' ); ?></h2>

	<form method="post" action="admin-post.php">

	 <input type="hidden" name="action"
		value="save_ch12hw_options" />

	 <!-- Adding security through hidden referrer field -->
	 <?php wp_nonce_field( 'ch12hw' ); ?>

	<?php _e( 'Shortcode message', 'ch12hw_hello_world' ); ?>:
	<input type="text" name="message" value="<?php echo esc_html( $options['message'] ); ?>"/><br />
	<input type="submit" value="<?php _e( 'Submit', 'ch12hw_hello_world' ); ?>" class="button-primary"/>
	</form>
	</div>
<?php }

add_action( 'admin_init', 'ch12hw_admin_init' );

function ch12hw_admin_init() {
	add_action( 'admin_post_save_ch12hw_options',
		 'process_ch12hw_options' );
}

function process_ch12hw_options() {
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed' );
	}

	check_admin_referer( 'ch12hw' );

	$options = get_option( 'ch12hw_options' );

	$options['message'] = sanitize_text_field( $_POST['message'] );

	update_option( 'ch12hw_options', $options );
	wp_redirect( add_query_arg( 'page', 'ch12hw-hello-world' , admin_url( 'options-general.php' ) ) );
	exit;
}
