<?php
/*
  Plugin Name: Chapter 2 - Page Header Output Meta Boxes
  Plugin URI: 
  Description: Companion to recipe 'Formatting admin pages using meta boxes'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */
 
global $options_page;
 
add_action( 'wp_head', 'ch2pho_page_header_output' );

function ch2pho_page_header_output() { 
	$options = ch2pho_get_options();
	?>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;
        i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();
        a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;
        m.parentNode.insertBefore(a,m)})(window,document,'script',
        'https://www.google-analytics.com/analytics.js','ga');
        
		ga('create', '<?php echo $options['ga_account_name']; ?>', 'auto');
        ga('send', 'pageview');
    </script>
<?php }

$options = ch2pho_get_options();

if ( $options['track_outgoing_links'] == true ) { 
    add_filter( 'the_content','ch2lfa_link_filter_analytics' ); 
}

function ch2lfa_link_filter_analytics ( $the_content ) {
	$new_content = str_replace( 'href', 'onClick="recordOutboundLink(this);return false;" href', $the_content );

	return $new_content;
}

if ( $options['track_outgoing_links'] == true ) { 
    add_action( 'wp_footer', 'ch2lfa_footer_analytics_code' ); 
} 

function ch2lfa_footer_analytics_code() { ?>
    
<script type="text/javascript">
  function recordOutboundLink( link ) {
	ga('send', 'event', 'Outbound Links', 'Click',
		link.href, {
			'transport': 'beacon',
			'hitCallback': function() { 
				document.location = link.href; 
			}
		} );
	}
</script>

<?php }

/*****************************************************************
 * Code from recipe 'Storing user settings using arrays'         *
 *****************************************************************/

register_activation_hook( __FILE__, 'ch2pho_set_default_options_array' );

function ch2pho_set_default_options_array() { 
    ch2pho_get_options();
}

function ch2pho_get_options() {
    $options = get_option( 'ch2pho_options', array() );

    $new_options['ga_account_name'] = 'UA-000000-0'; 
    $new_options['track_outgoing_links'] = false;
	
    $merged_options = wp_parse_args( $options, $new_options ); 

    $compare_options = array_diff_key( $new_options, $options );   
    if ( empty( $options ) || !empty( $compare_options ) ) {
        update_option( 'ch2pho_options', $merged_options );
    }
    return $merged_options;
}

/*****************************************************************
 * Code from recipe 'Creating an administration page menu item   *
 * in the settings menu'                                         *
 *****************************************************************/

add_action( 'admin_menu', 'ch2pho_settings_menu' );

function ch2pho_settings_menu() {

	global $options_page;
	
	$options_page = add_options_page( 'My Google Analytics Configuration',
		'My Google Analytics', 'manage_options',
		'ch2pho-my-google-analytics', 'ch2pho_config_page' );
		
	if ( !empty( $options_page ) ) {
		add_action( 'load-' . $options_page, 'ch2pho_help_tabs' );
	}
}

/*****************************************************************
 * Code from recipe 'Rendering admin page contents using HTML'   
 *****************************************************************/

function ch2pho_config_page() {
	// Retrieve plugin configuration options from database
	$options = ch2pho_get_options();
	global $options_page;
	?>

	<div id="ch2pho-general" class="wrap">
	<h2>My Google Analytics</h2><br />
	
	<?php if (isset( $_GET['message'] ) && $_GET['message'] == '1'):?>
	<div id='message' class='updated fade'><p><strong>Settings Saved</strong></p></div>
	<?php endif; ?>

	<form method="post" action="admin-post.php">

	 <input type="hidden" name="action"
		value="save_ch2pho_options" />

	 <!-- Adding security through hidden referrer field -->
	 <?php wp_nonce_field( 'ch2pho' ); ?>
	 
	 <!-- Security fields for meta box save processing --> 
        <?php wp_nonce_field( 'closedpostboxes', 
       'closedpostboxesnonce', false ); ?> 
		<?php wp_nonce_field( 'meta-box-order', 
       'meta-box-order-nonce', false ); ?> 

	<div id="poststuff" class="metabox-holder"> 
      <div id="post-body"> 
        <div id="post-body-content"> 
         <?php do_meta_boxes( $options_page, 'normal', $options) ; 
           ?> 
        <input type="submit" value="Submit" 
          class="button-primary"/>      
        </div> 
      </div> 
      <br class="clear"/> 
    </div> 
	</form>
	</div>
	
	<script type="text/javascript">  
    //<![CDATA[  
    jQuery( document ).ready( function( $ ) { 
      // close postboxes that should be closed 
      $( '.if-js-closed' ) .removeClass( 'if-js-closed' ).
         addClass( 'closed' ); 
 
      // postboxes setup 
      postboxes.add_postbox_toggles
         ( '<?php echo $options_page; ?>' ); 
            }); 
 
        //]]>  
    </script>  
<?php }

/*****************************************************************
 * Code from recipe 'Processing and storing admin page post data'*
 *****************************************************************/

add_action( 'admin_init', 'ch2pho_admin_init' );

function ch2pho_admin_init() {
	add_action( 'admin_post_save_ch2pho_options',
		 'process_ch2pho_options' );
}

function process_ch2pho_options() {
	// Check that user has proper security level

	if ( !current_user_can( 'manage_options' ) )
	wp_die( 'Not allowed' );

	// Check that nonce field created in configuration form
	// is present

	check_admin_referer( 'ch2pho' );

	// Retrieve original plugin options array
	$options = ch2pho_get_options();

	// Cycle through all text form fields and store their values
	// in the options array

	foreach ( array( 'ga_account_name' ) as $option_name ) {
		if ( isset( $_POST[$option_name] ) ) {
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
	}

	// Cycle through all check box form fields and set the options
	// array to true or false values based on presence of
	// variables

	foreach ( array( 'track_outgoing_links' ) as $option_name ) {
		if ( isset( $_POST[$option_name] ) ) {
			$options[$option_name] = true;
		} else {
			$options[$option_name] = false;
		}
	}

	// Store updated options array to database
	update_option( 'ch2pho_options', $options );

	// Redirect the page to the configuration form that was
	// processed

	wp_redirect( add_query_arg( array( 'page' => 'ch2pho-my-google-analytics', 'message' => '1' ), admin_url( 'options-general.php' ) ) );
	exit;
}

/*****************************************************************
 * Code from recipe 'Adding custom help pages'*
 *****************************************************************/
 
function ch2pho_help_tabs() 
{
	$screen = get_current_screen();
	$screen->add_help_tab( array(
		'id'       => 'ch2pho-plugin-help-instructions',
		'title'    => 'Instructions',
		'callback' => 'ch2pho_plugin_help_instructions',
	) );

	$screen->add_help_tab( array(
		'id'       => 'ch2pho-plugin-help-faq',
		'title'    => 'FAQ',
		'callback' => 'ch2pho_plugin_help_faq',
	) );

	$screen->set_help_sidebar( '<p>This is the sidebar content</p>' );
	
	global $options_page; 
 
	add_meta_box('ch2pho_general_meta_box', 
		'General Settings', 'ch2pho_plugin_meta_box', 
		$options_page, 'normal', 'core'); 
	 
	add_meta_box('ch2pho_second_meta_box', 
		'Second Settings Section', 'ch2pho_second_meta_box', 
		$options_page, 'normal', 'core');
}

function ch2pho_plugin_help_instructions() { ?>
	<p>These are instructions explaining how to use this plugin.</p>
<?php }

function ch2pho_plugin_help_faq() { ?>
	<p>These are the most frequently asked questions on the use of this plugin.</p>
<?php }

add_action( 'admin_enqueue_scripts', 'ch2pho_load_admin_scripts' );

function ch2pho_load_admin_scripts() {
    global $current_screen;
    global $options_page;

    if ( $current_screen->id == $options_page ) {
        wp_enqueue_script( 'common' );
        wp_enqueue_script( 'wp-lists' );
        wp_enqueue_script( 'postbox' );
    }
}

function ch2pho_plugin_meta_box( $options ) { ?> 
    Account Name: <input type="text" name="ga_account_name" 
         value="<?php echo esc_html( $options['ga_account_name'] ); 
     ?>"/><br /> 
 
    Track Outgoing Links <input type="checkbox" 
         name="track_outgoing_links" <?php 
		 checked( $options['track_outgoing_links'] );
     ?>/><br /> 
 
<?php }

function ch2pho_second_meta_box( $options ) { ?> 
  <p>This is the content of the second metabox.</p>     
<?php }