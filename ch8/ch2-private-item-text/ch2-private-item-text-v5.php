<?php
/*
  Plugin Name: Chapter 2 - Private Item Text V5
  Plugin URI: 
  Description: Companion to recipe 'Display new user data in user list page'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

// Utility function for CSS validation
function ch2pit_validate_css( $css ) {
    require_once plugin_dir_path( __FILE__ ) . '/csstidy/class.csstidy.php';
    $csstidy = new csstidy();
    $csstidy->set_cfg( 'optimise_shorthands', 2 );
    $csstidy->set_cfg( 'template', 'low' );
    $csstidy->set_cfg( 'discard_invalid_properties', true );

    // Parse the CSS
    $csstidy->parse( $css );
    return $csstidy->print->plain();
}

// Declare enclosing shortcode 'private' with associated function
add_shortcode( 'private', 'ch2pit_private_shortcode' );

// Function that is called when the 'private' shortcode is found
function ch2pit_private_shortcode( $atts, $content = null ) {
	if ( is_user_logged_in() ) {
		return '<div class="private">' . $content . '</div>';
	} else {
        $output = '<div class="register">';
		$output .= 'You need to become a member to access ';
		$output .= 'this content.</div>';
		return $output;	
	}
}

// Associate function with wp_head action hook to be called
// when the page header is being rendered
add_action( 'wp_head', 'ch2pit_page_header_output' );

// Function to add code to page header
function ch2pit_page_header_output() { ?>
	<style type='text/css'>
	<?php
		$options = ch2pit_get_options();
		echo esc_html( $options['stylesheet'] );
	?>
	</style>
<?php }

// Assign function to be called when plugin is activated or upgraded
register_activation_hook( __FILE__, 'ch2pit_get_options' );

// Function to create default options if they don't exist upon activation
function ch2pit_get_options() {
    $options = get_option( 'ch2pit_options', array() );

    $stylesheet_location = plugin_dir_path( __FILE__ ) . 'stylesheet.css';
    $new_options['stylesheet'] = ch2pit_validate_css( file_get_contents( $stylesheet_location ) );    
 
    $merged_options = wp_parse_args( $options, $new_options );
    $compare_options = array_diff_key( $new_options, $options );
    if ( empty( $options ) || !empty( $compare_options ) ) {
        update_option( 'ch2pit_options', $merged_options );
    }
    return $merged_options;
}

// Assign function to be called when admin page starts being displayed
add_action( 'admin_init', 'ch2pit_admin_init' );

// Register function to be called when user submits options
function ch2pit_admin_init() {
	add_action( 'admin_post_save_ch2pit_options', 'process_ch2pit_options' );
}

// Function to process user data submission
function process_ch2pit_options() {
		// Check that user has proper security level
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( 'Not allowed' );
		}

		// Check if nonce field is present
		check_admin_referer( 'ch2pit' );

		// Retrieve original plugin options array
		$options = ch2pit_get_options( 'ch2pit_options' );

		if ( isset($_POST['resetstyle'] ) ) {
			$stylesheet_location = plugin_dir_path( __FILE__ ) . 'stylesheet.css';
			$options['stylesheet'] = ch2pit_validate_css( file_get_contents( $stylesheet_location ) );

			$message = 2;
		} elseif ( !empty( $_POST ) ) {
			// Cycle through all text form fields and store their values
			// in the options array
			foreach ( array( 'stylesheet' ) as $option_name ) {
				if ( isset( $_POST[$option_name] ) ) {
					$options[$option_name] = ch2pit_validate_css( $_POST[$option_name] );
				}
			}
			$message = 1;
		}

		// Store updated options array to database
		update_option( 'ch2pit_options', $options );

		// Redirect the page to the configuration form that was
		// processed
		wp_redirect( add_query_arg( array( 'page' => 'ch2pit-private-item-text', 'message' => $message ), admin_url( 'options-general.php' ) ) );
		exit();
}

// Assign function to be called when admin menu is constructed
add_action( 'admin_menu', 'ch2pit_settings_menu' );

// Function to add item to Settings menu and specify function to display
// options page content
function ch2pit_settings_menu() {
	add_options_page( 'Private Item Text Configuration', 'Private Item Text', 'manage_options', 'ch2pit-private-item-text', 'ch2pit_config_page' );
}

// Function to display options page content
function ch2pit_config_page() {
	// Retrieve plugin configuration options from database
	$options = ch2pit_get_options( 'ch2pit_options' );
	?>

	<div id="ch2pit-general" class="wrap">
	<h2>Private Item Text</h2>

	<!-- Code to display confirmation messages when settings are
			saved or reset -->
	<?php if ( isset( $_GET['message'] ) && $_GET['message'] == '1' ):?>
	<div id='message' class='updated fade'><p><strong>Settings Saved</strong></p></div>
	<?php elseif ( isset( $_GET['message'] ) && $_GET['message'] == '2' ):?>
		<div id='message' class='updated fade'><p><strong>Stylesheet reverted to original</strong></p></div>
	<?php endif; ?>

	<form name="ch2pit_options_form" method="post" action="admin-post.php">

	<input type="hidden" name="action" value="save_ch2pit_options" />
	<?php wp_nonce_field( 'ch2pit' ); ?>

	Stylesheet<br />
	<textarea name="stylesheet" rows="10" cols="40" id="fancy-textarea" style="font-family:Consolas,Monaco,monospace">
<?php echo esc_html( $options['stylesheet'] ); ?></textarea><br />

	<input type="submit" value="Submit" class="button-primary" />
	<input type="submit" value="Reset" name="resetstyle" class="button-primary" />
	</form>
	</div>
	
	<script type="text/javascript">
        jQuery(document).ready(function() {
            wp.codeEditor.initialize( jQuery('#fancy-textarea'), cm_settings );
        });
	</script>
<?php }

add_action( 'admin_enqueue_scripts', 'add_page_scripts_enqueue_script' );

function add_page_scripts_enqueue_script( $hook ) {
    if( 'settings_page_ch2pit-private-item-text' === $hook ) {
        $cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
        wp_localize_script( 'jquery', 'cm_settings', $cm_settings );
        
        wp_enqueue_script( 'wp-theme-plugin-editor' );
        wp_enqueue_style( 'wp-codemirror' );
    }
}

/********************************************************************************
 Code from recipe 'Adding custom fields to the user editor'
 ********************************************************************************/

global $user_levels;
$user_levels = array( 'regular' => 'Regular', 'paid' => 'Paid' );

add_action( 'edit_user_profile', 'ch2pit_show_user_profile' );
add_action( 'user_new_form', 'ch2pit_show_user_profile' );

function ch2pit_show_user_profile( $user ) {
	global $user_levels;
	$current_user_level = '';
	
	if ( !empty( $user ) && isset( $user->data->ID ) ) {
		$user_id = $user->data->ID;
		$current_user_level = get_user_meta( $user_id, 'user_level', true );
	} ?>
	
	<h3>Site membership level</h3>

	<table class="form-table">
		<tr>
			<th>Level</th>
			<td>
				<select name="user_level" id="user_level">
				<?php foreach( $user_levels as $user_level_idx => $user_level ) { ?>
					<option value="<?php echo $user_level_idx; ?>"
					<?php selected( $current_user_level, $user_level_idx ); ?>>
					<?php echo $user_level; ?></option>
				<?php } ?>
				</select>
			</td>
		</tr>
	</table>
	   
<?php }

/********************************************************************************
 Code from recipe 'Processing and storing user custom data'
 ********************************************************************************/

add_action( 'profile_update', 'ch2pit_save_user_data' );
add_action( 'user_register', 'ch2pit_save_user_data' );

function ch2pit_save_user_data( $user_id ) {
	global $user_levels;
	
    if ( isset( $_POST['user_level'] ) && !empty( $_POST['user_level'] ) && array_key_exists( $_POST['user_level'], $user_levels ) ) {
        update_user_meta( $user_id, 'user_level', sanitize_text_field( $_POST['user_level'] ) );
    } else {
		update_user_meta( $user_id, 'user_level', 'regular' );
	}
}

/********************************************************************************
 Code from recipe 'Display new user data in user list page'
 ********************************************************************************/

add_filter( 'manage_users_columns', 'ch2pit_add_user_columns' );

function ch2pit_add_user_columns( $columns ) {
    $new_columns = array_slice( $columns, 0, 2, true ) + 
	               array( 'level' => 'User Level' ) + 
				   array_slice( $columns, 2, NULL, true );
    return $new_columns;
}

add_filter( 'manage_users_custom_column', 'ch2pit_display_user_columns_data', 10, 3 );

function ch2pit_display_user_columns_data( $val, $column_name, $user_id ) {
	global $user_levels;
	$val = '';
	if ( 'level' == $column_name ) {
		$current_user_level = get_user_meta( $user_id, 'user_level', true );
		if ( !empty( $current_user_level ) ) {
			$val = $user_levels[$current_user_level];
		}
    }
    return esc_html( $val );
}

add_action( 'restrict_manage_users', 'ch2pit_add_user_filter' );

function ch2pit_add_user_filter() {
	global $user_levels;
	$filter_value = '';
	
    if ( isset( $_GET['user_level'] ) ) {
		$filter_value = sanitize_text_field( $_GET['user_level'] );
	} ?>
    
	<select name="user_level" class="user_level" style="float:none;">
	<option value="">No filter</option>
	<?php foreach( $user_levels as $user_level_idx => $user_level ) { ?>
        <option value="<?php echo $user_level_idx; ?>" 
		<?php selected( $filter_value, $user_level_idx ); ?>>
		<?php echo $user_level; ?></option>
    <?php } ?>
    <input type="submit" class="button" value="Filter">
<?php }

add_action( 'admin_footer', 'ch2pit_user_filter_js' );

function ch2pit_user_filter_js() {
	global $current_screen;
    if ( 'users' !== $current_screen->id ) {
        return;
    } ?>
	
	<script type="text/javascript">
		jQuery( document ).ready( function() {
			jQuery( '.user_level' ).first().change( function() {
				var field_val = jQuery( this ).val()
				jQuery( '.user_level' ).last().val( field_val );
			});
			
			jQuery( '.user_level' ).last().change( function() {
				jQuery( '.user_level' ).first().val( jQuery( this ).val() );
			});
		});
	</script>res
<?php }

add_filter( 'pre_get_users', 'ch2pit_filter_users' );

function ch2pit_filter_users( $query ) {
    global $pagenow, $user_levels;

    if ( is_admin() && 'users.php' == $pagenow && 
         isset( $_GET['user_level'] ) ) {
		$filter_value = sanitize_text_field( $_GET['user_level'] );
		if ( !empty( $filter_value ) && array_key_exists( $filter_value, $user_levels ) ) {
			$query->set( 'meta_key', 'user_level' );
			$query->set( 'meta_query', array(
							array( 'key' => 'user_level',
								   'value' => $filter_value ) ) );
		}
    }
}