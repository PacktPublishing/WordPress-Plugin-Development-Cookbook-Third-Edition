<?php 
// Check that code was called from WordPress with 
// uninstallation constant declared 
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit; 
}
 
// Check if options exist and delete them if present 
if ( false != get_option( 'ch2pho_options' ) ) { 
    delete_option( 'ch2pho_options' ); 
} 
