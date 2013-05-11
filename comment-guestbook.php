<?php
/*
Plugin Name: Comment Guestbook
Plugin URI: http://wordpress.org/extend/plugins/comment-guestbook/
Description: Add a guestbook page which uses the wordpress integrated comments.
Version: 0.3.1
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
define( 'CGB_URL', plugin_dir_url( __FILE__ ) );
define( 'CGB_PATH', plugin_dir_path( __FILE__ ) );


// MAIN PLUGIN CLASS
class comment_guestbook {
	private $options;
	private $shortcode;

	/**
	 * Constructor:
	 * Initializes the plugin.
	 */
	public function __construct() {
		$this->options = null;
		$this->shortcode = null;

		// ALWAYS:
		// Register shortcodes
		add_shortcode( 'comment-guestbook', array( &$this, 'shortcode_comment_guestbook' ) );
		// Register widgets
		add_action( 'widgets_init', array( &$this, 'widget_init' ) );

		// ADMIN PAGE:
		if ( is_admin() ) {
			// Include required php-files and initialize required objects
			require_once( 'php/admin.php' );
			$admin = new cgb_admin();
			// Register actions
			add_action( 'plugins_loaded', array( &$admin->options, 'version_upgrade' ) );
			add_action( 'admin_menu', array( &$admin, 'register_pages' ) );
		}

		// FRONT PAGE:
		else {
			require_once( CGB_PATH.'php/options.php' );
			$this->options = cgb_options::get_instance();
			// Fix link after adding a comment (required if clist_order = desc) and added query for message after comment
			add_filter( 'comment_post_redirect', array( &$this, 'filter_comment_post_redirect' ) );
			// Add js to show message after comment
			if( isset( $_GET['cmessage'] ) && 1 == $_GET['cmessage'] ) {
				add_action( 'init', array( &$this, 'frontpage_init' ) );
				add_action( 'wp_footer', array( &$this, 'frontpage_footer' ) );
			}
			// Set filter to overwrite comments_open status
			if( isset( $_POST['cgb_comments_status'] ) && 'open' === $_POST['cgb_comments_status'] ) {
				add_filter( 'comments_open', array( &$this, 'filter_comments_open' ) );
			}
		}
	} // end constructor

	public function shortcode_comment_guestbook( $atts ) {
		if( NULL == $this->shortcode ) {
			require_once( 'php/sc_comment-guestbook.php' );
			$this->shortcode = sc_comment_guestbook::get_instance();
		}
		return $this->shortcode->show_html( $atts );
	}

	public function widget_init() {
		// Widget "comment-guestbook"
		require_once( 'php/comment-guestbook_widget.php' );
		return register_widget( 'comment_guestbook_widget' );
	}

	public function frontpage_init() {
		wp_register_script( 'block_ui', 'http://malsup.github.com/jquery.blockUI.js', array( 'jquery' ), true );
		wp_register_script( 'cgb_comment_guestbook', CGB_URL.'js/comment-guestbook.js', array( 'block_ui' ), true );
	}

	public function frontpage_footer() {
		$this->print_script_variables();
		wp_print_scripts( 'cgb_comment_guestbook' );
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
		if( 'always' === $this->options->get( 'cgb_message_after_comment' ) ||
				( 'guestbook_only' === $this->option->get( 'cgb_message_after_comment' ) && isset( $_POST['is_cgb_comment'] ) ) ) {
			$url_array = explode( '#', $location );
			$query_delimiter = ( false !== strpos( $url_array[0], '?' ) ) ? '&' : '?';
			$location = $url_array[0].$query_delimiter.'cmessage=1#'.$url_array[1];
		}
		return $location;
	}

	public function print_script_variables() {
		$out = '
			<script type="text/javascript">
				var cmessage_text = "'.$this->options->get( 'cgb_message_after_comment_text' ).'";
				var cmessage_type = 1;
			</script>';
		echo $out;
	}
} // end class

// create a class instance
$cgb = new comment_guestbook();
?>
