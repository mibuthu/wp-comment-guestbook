<?php
/*
Plugin Name: Comment Guestbook
Plugin URI: http://wordpress.org/extend/plugins/comment-guestbook/
Description: Add a guestbook page which uses the wordpress integrated comments.
Version: 0.4.1
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

if( !defined( 'ABSPATH' ) ) {
	exit;
}

// GENERAL DEFINITIONS
define( 'CGB_URL', plugin_dir_url( __FILE__ ) );
define( 'CGB_PATH', plugin_dir_path( __FILE__ ) );


// MAIN PLUGIN CLASS
class comment_guestbook {
	private $shortcode;

	/**
	 * Constructor:
	 * Initializes the plugin.
	 */
	public function __construct() {
		$this->shortcode = null;

		// ALWAYS:
		// Register shortcodes
		add_shortcode( 'comment-guestbook', array( &$this, 'shortcode_comment_guestbook' ) );
		// Register widgets
		add_action( 'widgets_init', array( &$this, 'widget_init' ) );

		// ADMIN PAGE:
		if ( is_admin() ) {
			// Include required php-files and initialize required objects
			require_once('admin/admin.php');
			$admin = new CGB_Admin();
			// Register actions
			add_action( 'plugins_loaded', array( &$admin->options, 'version_upgrade' ) );
			add_action( 'admin_menu', array( &$admin, 'register_pages' ) );
		}

		// FRONT PAGE:
		else {
			// Fix link after adding a comment (required if clist_order = desc) and added query for message after comment
			add_filter( 'comment_post_redirect', array( &$this, 'filter_comment_post_redirect' ) );
			// Add message after comment
			if( isset( $_GET['cmessage'] ) && 1 == $_GET['cmessage'] ) {
				require_once( CGB_PATH.'php/cmessage.php' );
				$cmessage = CGB_cmessage::get_instance();
				$cmessage->init();
			}
			// Set filter to overwrite comments_open status
			if( isset( $_POST['cgb_comments_status'] ) && 'open' === $_POST['cgb_comments_status'] ) {
				add_filter( 'comments_open', array( &$this, 'filter_comments_open' ) );
			}
		}
	} // end constructor

	public function shortcode_comment_guestbook( $atts ) {
		if( NULL == $this->shortcode ) {
			require_once( 'includes/sc_comment-guestbook.php' );
			$this->shortcode = SC_Comment_Guestbook::get_instance();
		}
		return $this->shortcode->show_html( $atts );
	}

	public function widget_init() {
		// Widget "comment-guestbook"
		require_once( 'php/comment-guestbook_widget.php' );
		return register_widget( 'comment_guestbook_widget' );
	}

	public function filter_comments_open( $open ) {
		return true;
	}

	public function filter_comment_post_redirect ( $location ) {
		// if cgb_clist_order is 'desc' the page must be changed due to the reversed comment list order:
		if( isset( $_POST['cgb_clist_order'] ) && 'desc' === $_POST['cgb_clist_order'] ) {
			global $comment_id;
			require_once( 'php/comments-functions.php' );
			$cgb_func = new cgb_comments_functions();
			$page = $cgb_func->get_page_of_desc_commentlist( $comment_id );
			$location = get_comment_link( $comment_id, array( 'page' => $page ) );
		}
		// add query for message after comment
		require_once( CGB_PATH.'php/cmessage.php' );
		$cmessage = CGB_cmessage::get_instance();
		$location = $cmessage->add_cmessage_indicator( $location );
		return $location;
	}
} // end class

// create a class instance
$cgb = new comment_guestbook();
?>
