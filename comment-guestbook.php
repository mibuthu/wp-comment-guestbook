<?php
/*
Plugin Name: Comment Guestbook
Plugin URI: http://wordpress.org/extend/plugins/comment-guestbook/
Description: Add a guestbook page which uses the wordpress integrated comments.
Version: 0.2.0
Author: Michael Burtscher
Author URI: http://wordpress.org/extend/plugins/comment-guestbook/

A plugin for the blogging MySQL/PHP-based WordPress.
Copyright 2012 Michael Burtscher

This program is free software; you can redistribute it and/or
modify it under the terms of the GNUs General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You can view a copy of the HTML version of the GNU General Public
License at http://www.gnu.org/copyleft/gpl.html
*/

// GENERAL DEFINITIONS
define( 'CGB_PATH', plugin_dir_path( __FILE__ ) );


// MAIN PLUGIN CLASS
class comment_guestbook {

	/**
	 * Constructor:
	 * Initializes the plugin.
	 */
	public function __construct() {

		// ALWAYS:
		// Register shortcodes
		add_shortcode( 'comment-guestbook', array( &$this, 'shortcode_comment_guestbook' ) );

		// ADMIN PAGE:
		if ( is_admin() ) {
			// Include required php-files and initialize required objects
			require_once( 'php/admin.php' );
			$admin = new cgb_admin();
			// Register actions
			add_action( 'plugins_loaded', array( &$admin->options, 'version_upgrade' ) );
			add_action( 'admin_menu', array( &$admin, 'register_pages' ) );
		}
	} // end constructor

	public function shortcode_comment_guestbook( $atts ) {
		require_once( 'php/sc_comment-guestbook.php' );
		$shortcode = new sc_comment_guestbook();
		return $shortcode->show_html( $atts );
	}
} // end class

// create a class instance
$cgb = new comment_guestbook();
?>
