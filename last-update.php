<?php
/*
Plugin Name: GP Last Update
Plugin URI: http://glot-o-matic.com/gp-last-update
Description: Adds a "Last Update" column to the translation set list in GlotPress.
Version: 1.0
Author: GregRoss
Author URI: http://toolstack.com
Tags: glotpress, glotpress plugin 
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class GP_Last_Update {
	public $id = 'last-update';
	
	private $dateformat;

	public function __construct() {
		// Get the date format we're going to use.
		$this->dateformat = gp_const_get('GP_LAST_UPDATE_FORMAT', 'M j Y @ g:i a' );
		
		// If for some reason the date format is empty, use the default.
		if( $this->dateformat == '' ) { $this->dateformat = 'M j Y @ g:i a'; }
		
		// Get the required permission to see the last update info, make sure it's all lower case.
		$reqperm = strtolower( gp_const_get('GP_LAST_UPDATE_REQUIRED_PERMISSION', false) );
		
		// If it's not recognized as read or approve, force it to admin.
		if( $reqprem != 'read' || $reqperm != 'approve' ) { $reqperm = 'admin'; }

		// Check to see the current user has permissions.
		if( GP::$permission->current_user_can( $reqperm, 'project' ) ) {
			// Add the hook.
			add_action( 'gp_project_template_translation_set_extra', array( $this, 'gp_project_template_translation_set_extra' ), 10, 2 );
		}
	}

	public function gp_project_template_translation_set_extra( $set, $project ) {
		// Get the translation set's last update time, in GMT.
		$dt = gp_gmt_strtotime( GP::$translation->last_modified( $set ) );

		// Check to see if we have a valid time.
		if( $dt > 0 ) {		
			// Output the last update info for the translation set.
			echo 'Last updated on ' . date( $this->dateformat, $dt ) . '<br>';
		}
		else {
			// A 0 value for the time means there has never been an update done.
			echo 'Never updated<br>';
		}
	}

}

// Add an action to WordPress's init hook to setup the plugin.  Don't just setup the plugin here as the GlotPress plugin may not have loaded yet.
add_action( 'gp_init', 'gp_last_update_init' );

// This function creates the plugin.
function gp_last_update_init() {
	GLOBAL $gp_last_update;
	
	$gp_last_update = new GP_Last_Update;
}
