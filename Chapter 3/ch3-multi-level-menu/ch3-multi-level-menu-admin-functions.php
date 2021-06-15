<?php

if ( !defined( 'ch3mlm' ) ) {
    exit;
}

add_action( 'admin_menu', 'ch3mlm_admin_menu' );

function ch3mlm_admin_menu() {
    // Create top-level menu item
    add_menu_page( 'My Complex Plugin Configuration Page',
        'My Complex Plugin', 'manage_options',
        'ch3mlm-main-menu', 'ch3mlm_my_complex_main',
        'dashicons-menu-alt3' );
    
    // Create a sub-menu under the top-level menu
    add_submenu_page( 'ch3mlm-main-menu',
     'My Complex Menu Sub-Config Page',
     'Sub-Config Page',
     'manage_options', 'ch3mlm-sub-menu',
     'ch3mlm_my_complex_submenu' );
}