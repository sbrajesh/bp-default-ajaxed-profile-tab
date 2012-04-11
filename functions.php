<?php

//include javascript
add_action( 'wp_enqueue_scripts', 'bp_ajaxed_profile_enqueue_scripts' );
function bp_ajaxed_profile_enqueue_scripts(){
    wp_enqueue_script( 'ajaxed-tab-profile-js', get_stylesheet_directory_uri() . '/_inc/theme.js', array( 'jquery','dtheme-ajax-js' ));
}
/**
 * Get current component
 * @return string 
 */
function bpcustom_get_current_component(){
     $object = esc_attr( $_POST['object'] );
        
        if($object=='xprofile')
            $object='profile';
        else if($object=='members')
            $object='friends';
      return $object;  
}
//get current action
function bpcustom_get_current_action(){
            $action=$_POST['extras'];
            
            if(!empty($action)&&$action=='favs')//a fix for activity
                $action='favorites';
            
        return $action;
}
//init action/component override
function bpcustom_setup_components(){
    global $bp;
       

        //check for action too
        $component=bpcustom_get_current_component();
        $action=bpcustom_get_current_action();
	 if($component=='settings'&&empty($action))
            $action='general';
        global $bp;
        
        $bp->current_component=$component;//hack
        
        if(!empty($action))
            $bp->current_action=$action;
       
        return $component;// just to avoid another call, not a good pattern though
}
/**
 * Load content via ajax
 */

add_action( 'wp_ajax_profile_xprofile_filter',  'bpcustom_profile_sub_template_loader');
add_action( 'wp_ajax_profile_blogs_filter',     'bpcustom_profile_sub_template_loader');
add_action( 'wp_ajax_profile_members_filter',   'bpcustom_profile_sub_template_loader');
add_action( 'wp_ajax_profile_groups_filter',    'bpcustom_profile_sub_template_loader' );
add_action( 'wp_ajax_profile_messages_filter',  'bpcustom_profile_sub_template_loader' );
add_action( 'wp_ajax_profile_activity_filter',  'bpcustom_activity_template_loader');
add_action( 'wp_ajax_profile_settings_filter',  'bpcustom_profile_sub_template_loader');
//for pages having componentName.php
function bpcustom_profile_sub_template_loader() {
    
        $object=bpcustom_setup_components();
	locate_template( array( "members/single/{$object}.php" ), true );
      
}

//lets do some magic for Activity
//a copy of bp_dtheme_activity_template_loader
function bpcustom_activity_template_loader() {
	global $bp;

	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;
            bpcustom_setup_components();
            
            $action=bpcustom_get_current_action();
            
	$scope = $action;
        
        
       

	// We need to calculate and return the feed URL for each scope
	switch ( $scope ) {
		case 'friends':
			$feed_url = $bp->loggedin_user->domain . bp_get_activity_slug() . '/friends/feed/';
			break;
		case 'groups':
			$feed_url = $bp->loggedin_user->domain . bp_get_activity_slug() . '/groups/feed/';
			break;
		case 'favorites':
			$feed_url = $bp->loggedin_user->domain . bp_get_activity_slug() . '/favorites/feed/';
			break;
		case 'mentions':
			$feed_url = $bp->loggedin_user->domain . bp_get_activity_slug() . '/mentions/feed/';
			bp_activity_clear_new_mentions( $bp->loggedin_user->id );
			break;
		default:
			$feed_url = home_url( bp_get_activity_root_slug() . '/feed/' );
			break;
	}

	/* Buffer the loop in the template to a var for JS to spit out. */
	ob_start();
	locate_template( array( 'members/single/activity.php' ), true );
	$result = array();
	$result['contents'] = ob_get_contents();
	$result['feed_url'] = apply_filters( 'bp_dtheme_activity_feed_url', $feed_url, $scope );
	ob_end_clean();

	echo json_encode( $result );
}

?>