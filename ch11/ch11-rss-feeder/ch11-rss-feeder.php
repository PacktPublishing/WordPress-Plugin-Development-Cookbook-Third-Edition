<?php
/*
Plugin Name: Chapter 11 - RSS Feeder
Plugin URI:
Description: Declares a plugin that will be visible in the WordPress admin interface
Version: 1.0
Author: Yannick Lefebvre
Author URI: http://ylefebvre.ca
License: GPLv2
*/

add_action( 'admin_menu', 'ch11rf_settings_menu' ); 

function ch11rf_settings_menu() {
	$options_page = add_options_page( 'RSS Feeder', 'RSS Feeder', 'manage_options', 'ch11rf-rss-feeder', 'ch11rf_config_page' );
} 

function ch11rf_config_page() {
	$rss_feed_list = get_option( 'ch11rf_rss_feed_list' ); ?>
		
	<div id="ch11rf-general" class="wrap">
	<h2>RSS Feeder</h2><br />
	
	<?php if ( isset( $_GET['message'] ) && $_GET['message'] == '1' ) { ?>
		<div id='message' class='updated fade'><p><strong>Modifications Saved</strong></p></div>
	<?php } ?>
		
	<form method="post" id="rss_feed_form" action="<?php echo admin_url( 'admin-post.php' ); ?>">
	<input type="hidden" name="action" value="save_ch11rf_options" />
	<?php wp_nonce_field( 'ch11rf' ); ?>

	<table>       
	<?php if ( !empty( $rss_feed_list ) ) {
		foreach ( $rss_feed_list as $rss_feed ) { ?>
			<tr><td><input type="text" size="80" name="rss_feed[]" value="<?php echo $rss_feed; ?>"></td>
			<td><span style="cursor: pointer" class="dashicons dashicons-trash"></span></td></tr>
	<?php } } ?>
		<tr><td>
			<input type="text" size="80" name="rss_feed[]" value="" placeholder="RSS Feed URL"></td>
			<td><input type="submit" value="Save" class="button-primary"/></td></tr>
	</table>    
	</form>
	</div>

	<script type="text/javascript">
	jQuery( document ).ready( function() {
		jQuery( '.dashicons-trash' ).click( function() {
			jQuery( this ).closest( 'tr' ).remove();
			jQuery( '#rss_feed_form' ).submit();
		});
	});
	</script>
<?php }

add_action( 'admin_init', 'ch11rf_admin_init' );

function ch11rf_admin_init() {
	add_action( 'admin_post_save_ch11rf_options', 'process_ch11rf_options' );
}

function process_ch11rf_options() {
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed' );
	}
	check_admin_referer( 'ch11rf' );
		
	if ( isset( $_POST['rss_feed'] ) && !empty( $_POST['rss_feed'] ) ) {
		$rss_feed_array = array();
		foreach ( $_POST['rss_feed'] as $rss_feed ) {
			if ( !empty( $rss_feed ) ) {
				$rss_feed_array[] = esc_url_raw( $rss_feed );
			}            
		}
		update_option( 'ch11rf_rss_feed_list', $rss_feed_array );        
	}
	
	wp_redirect( add_query_arg( array( 'page' => 'ch11rf-rss-feeder', 'message' => '1' ), admin_url( 'options-general.php' ) ) );    
	exit;
}

add_shortcode( 'rss-feeder', 'ch11rf_show_feed_list' );

function ch11rf_show_feed_list() {
	$rss_feed_list = get_option( 'ch11rf_rss_feed_list' );
	$output = '';
	if ( empty( $rss_feed_list ) ) { return; }
	
	$output .= '<div class="rss_feeder">';
	foreach ( $rss_feed_list as $rss_feed ) {
		$rss = fetch_feed( $rss_feed );
		if ( is_wp_error( $rss ) ) { return; }
		$feed_items = $rss->get_items( 0, 5 );                
		if ( $feed_items ) {
			foreach ( $feed_items as $index => $item ) {
				if ( 0 == $index ) {
					$output .= '<h3>' . $item->get_feed()->get_title() . '</h3>';
				}
				$output .= '<div class="item">';
				$output .= '<h4><a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></h4>';
				$output .= '<div class="item_content">' . wp_trim_words( $item->get_description(), 40 );
				$output .= '</div></div>';
			}
		}
	}
	$output .= '</div>';
	return $output;
}