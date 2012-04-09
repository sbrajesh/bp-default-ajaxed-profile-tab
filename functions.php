<?php

//include javascript
add_action( 'wp_enqueue_scripts', 'bp_ajaxed_profile_enqueue_scripts' );
function bp_ajaxed_profile_enqueue_scripts(){
    wp_enqueue_script( 'ajaxed-tab-profile-js', get_stylesheet_directory_uri() . '/_inc/theme.js', array( 'jquery','dtheme-ajax-js' ));
}
/**
 * Load content via ajax
 */

add_action( 'wp_ajax_xprofile_filter',   'bpcustom_profile_sub_template_loader' );
add_action( 'wp_ajax_messages_filter',  'bpcustom_profile_sub_template_loader' );
add_action( 'wp_ajax_activity_filter',  'bpcustom_profile_activity_template_loader' );
add_action( 'wp_ajax_settings_filter',  'bpcustom_profile_settings_template_loader' );
//for pages having name-loop.php
function bpcustom_profile_sub_template_loader() {
    
    
	$object = esc_attr( $_POST['object'] );
    
        if($object=='xprofile')
            $object='profile';
	locate_template( array( "members/single/$object/$object-loop.php" ), true );
}
//for activity
function bpcustom_profile_activity_template_loader() {
	
	locate_template( array( "members/single/activity.php" ), true );
}

function bpcustom_profile_settings_template_loader() {
    
    locate_template( array( "members/single/settings/settings-general.php" ), true );
}


?>