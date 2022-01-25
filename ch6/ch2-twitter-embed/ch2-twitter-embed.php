<?php
/*
  Plugin Name: Chapter 2 - Twitter Embed
  Plugin URI: 
  Description: Companion to recipe 'Managing multiple sets of user settings from a single admin page'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

// Declare shortcode 'twitterfeed' with associated function
add_shortcode( 'twitterfeed', 'ch2te_twitter_embed_shortcode' );

// Function that is called when the 'twitterfeed' shortcode is found
function ch2te_twitter_embed_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'user_name' => 'ylefebvre',
		'option_id' => 1
	), $atts ) );
	
	if ( intval( $option_id ) < 1 || intval( $option_id ) > 5 ) {          
        $option_id = 1; 
    }	
	$options = ch2te_get_options( $option_id );
	
	if ( empty( $user_name ) ) {
		$user_name = 'ylefebvre';
	} else {
		$user_name = sanitize_text_field( $user_name );
	}
 
	if ( !empty( $user_name ) ) {
		$output = '<p><a class="twitter-timeline" href="'; 
        $output .= esc_url( 'https://twitter.com/' . $user_name );
        $output .= '" data-width="' . intval( $options['width'] );
		$output .= '" data-tweet-limit="';
		$output .= intval( $options['number_of_tweets'] );
        $output .= '">' . 'Tweets by ' . esc_html( $user_name );
        $output .= '</a></p><script async ';
        $output .= 'src="//platform.twitter.com/widgets.js"';
        $output .= ' charset="utf-8"></script>';
	} else {
		$output = '';
	}
	return $output;
}

// Assign function to be called when plugin is activated or upgraded
register_activation_hook( __FILE__, 'ch2te_set_default_options_array' ); 

// Function to create default options if they don't exist upon activation
function ch2te_set_default_options_array() { 
    ch2te_get_options();
}

function ch2te_get_options( $id = 1 ) {
    $options = get_option( 'ch2te_options_' . $id, array() );

    $new_options['setting_name'] = 'Default'; 
    $new_options['width'] = 560; 
    $new_options['number_of_tweets'] = 3; 
	
    $merged_options = wp_parse_args( $options, $new_options ); 

    $compare_options = array_diff_key( $new_options, $options );   
    if ( empty( $options ) || !empty( $compare_options ) ) {
        update_option( 'ch2te_options_' . $id, $merged_options );
    }
    return $merged_options;
}

// Assign function to be called when admin page starts being displayed
add_action( 'admin_init', 'ch2te_admin_init' ); 

// Register function to be called when user submits options
function ch2te_admin_init() { 
  add_action( 'admin_post_save_ch2te_options', 'process_ch2te_options' ); 
}

// Function to process user data submission
function process_ch2te_options() { 
    // Check that user has proper security level 
    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( 'Not allowed' ); 
    }
     
    // Check that nonce field is present 
    check_admin_referer( 'ch2te' ); 
	
    // Check if option_id field was present  
    if ( isset( $_POST['option_id'] ) ) {
	    $option_id = intval( $_POST['option_id'] ); 
	} else {
        $option_id = 1;
    }
	
    // Build option name and retrieve options 
    $options = ch2te_get_options( $option_id ); 
         
    // Cycle through all text fields and store their values 
    foreach ( array( 'setting_name' ) as $param_name ) { 
        if ( isset( $_POST[$param_name] ) ) { 
            $options[$param_name] = sanitize_text_field( $_POST[$param_name] ); 
        } 
    } 
         
    // Cycle through all numeric fields, convert to int and store
    foreach ( array( 'width', 'number_of_tweets' ) as $param_name ) { 
        if ( isset( $_POST[$param_name] ) ) { 
            $options[$param_name] = intval( $_POST[$param_name] ); 
        } 
    } 
             
    // Store updated options array to database 
	$options_name = 'ch2te_options_' . $option_id;
    update_option( $options_name, $options ); 
	 
    $cleanaddress = 
        add_query_arg( array( 'message' => 1,  
                              'option_id' => $option_id, 
                              'page' => 'ch2te-twitter-embed' ), 
                       admin_url( 'options-general.php' ) ); 
    wp_redirect( $cleanaddress ); 
    exit; 
}

// Assign function to be called when admin menu is constructed
add_action( 'admin_menu', 'ch2te_settings_menu' ); 

// Function to add item to Settings menu and specify function to display
// options page content
function ch2te_settings_menu() { 
    add_options_page( 'Twitter Embed Configuration', 
        'Twitter Embed', 'manage_options', 
        'ch2te-twitter-embed',
        'ch2te_config_page' ); 
}

// Function to display options page content
function ch2te_config_page() { 
    // Retrieve plugin configuration options from database 
    if ( isset( $_GET['option_id'] ) ) {
        $option_id = intval( $_GET['option_id'] ); 
    } else {
        $option_id = 1;
    }
 
    $options = ch2te_get_options( $option_id ); ?> 
 
    <div id="ch2te-general" class="wrap"> 
    <h2>Twitter Embed</h2> 
 
    <!-- Display message when settings are saved -->
     <?php if ( isset( $_GET['message'] ) && $_GET['message'] == '1' ) { ?>
 <div id='message' class='updated fade'><p><strong>Settings Saved
 </strong></p></div>
     <?php } ?>

     <!-- Option selector -->
     <div id="icon-themes" class="icon32"><br></div>
     <h2 class="nav-tab-wrapper">
     <?php for ( $counter = 1; $counter <= 5; $counter++ ) {
         $temp_options = ch2te_get_options( $counter ); 
         $class = ( $counter == $option_id ) ? ' nav-tab-active' : '';?> 
 
    <a class="nav-tab<?php echo esc_html( $class ); ?>" href="<?php echo 
 add_query_arg( array( 'page' => 'ch2te-twitter-embed', 'option_id' => $counter ), admin_url( 'options-general.php' ) ); ?>"><?php echo intval( $counter ); ?><?php if ( $temp_options !== false ) echo ' (' . esc_html( $temp_options['setting_name'] ) . ')'; else echo ' (Empty)'; ?></a>
     <?php } ?>
     </h2><br />    
     
    <!-- Main options form --> 
    <form name="ch2te_options_form" method="post" action="admin-post.php"> 
     
    <input type="hidden" name="action" value="save_ch2te_options" /> 
    <input type="hidden" name="option_id" 
            value="<?php echo intval( $option_id ); ?>" /> 
    <?php wp_nonce_field( 'ch2te' ); ?> 
     
    <table> 
        <tr> 
            <td>Setting name</td> 
            <td><input type="text" name="setting_name" value="<?php echo esc_html( $options['setting_name'] ); ?>"/></td> 
        </tr> 
        <tr> 
            <td>Feed width</td> 
            <td><input type="text" name="width" value="<?php echo intval( $options['width'] ); ?>"/></td> 
        </tr> 
        <tr> 
            <td>Number of Tweets to display</td> 
            <td><input type="text" name="number_of_tweets" value="<?php echo intval( $options['number_of_tweets'] ); ?>"/></td> 
        </tr> 

    </table><br /> 
    <input type="submit" value="Submit" class="button-primary" /> 
    </form> 
    </div> 
<?php }

// Code from Creating a new WP REST endpoint recipe

add_action( 'rest_api_init', 'ch2te_rest_api_init' );

function ch2te_rest_api_init() {
	register_rest_route( 'twitter-embed/v1', '/optionlist', array(
		'methods' => 'GET',
		'callback' => 'ch2te_rest_option_list',
		'permission_callback' => '__return_true'
	) );
}

function ch2te_rest_option_list( WP_REST_Request $request ) {
	$option_array = array();
	
	for ( $counter = 1; $counter <= 5; $counter++ ) {
		$temp_options = ch2te_get_options( $counter );
		$option_array[$counter] = 
		$temp_options['setting_name'];
	}        
	
	$response = new WP_REST_Response( $option_array );
	return $response;
}
	
// Code from Creating a server-side rendering block that leverages an existing shortcode recipe

add_action( 'init', 'ch2te_register_block' );

function ch2te_register_block() {
	if ( !function_exists( 'register_block_type' ) ) {
		return;
	}

	$asset_file = include( plugin_dir_path( __FILE__ ) .  'build/index.asset.php');

	wp_register_script(
		'ch2twe-twitter-embed',
		plugins_url( 'build/index.js', __FILE__ ),
		$asset_file['dependencies'], $asset_file['version']
	);

	wp_register_style(
		'ch2twe-editor-style',
		plugins_url( 'css/editor.css', __FILE__ ),
		array( ),
		filemtime( plugin_dir_path( __FILE__ ) . '/css/editor.css' )
	);

	register_block_type( 'ch2twe-twitter-embed/twitter-embed', 
		array(
			'editor_script' => 'ch2twe-twitter-embed',
			'editor_style' => 'ch2twe-editor-style',
			'render_callback' => 'ch2te_twitter_embed_shortcode',
			'attributes' => array(
				'user_name'    => array(
					'type'  => 'string',
					'default'   => 'WordPress',
				),
				'option_id'    => array(
					'type'  => 'string',
					'default'   => '1',
				),
			)
	) );
} 

