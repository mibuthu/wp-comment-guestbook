<?php
/**
 * Plugin Name: Comment Guestbook
 * Plugin URI: https://wordpress.org/plugins/comment-guestbook/
 * Description: Add a guestbook page which uses the WordPress integrated comments.
 * Version: 0.7.4
 * Author: mibuthu
 * Author URI: https://wordpress.org/plugins/comment-guestbook/
 * Text Domain: comment-guestbook
 * License: GPLv2
 *
 * A plugin for the blogging MySQL/PHP-based WordPress.
 * Copyright 2012-2020 mibuthu
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNUs General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You can view a copy of the HTML version of the GNU General Public
 * License at http://www.gnu.org/copyleft/gpl.html
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!
if ( ! defined( 'WPINC' ) ) {
	exit();
}

// General definitions.
define( 'CGB_URL', plugin_dir_url( __FILE__ ) );
define( 'CGB_PATH', plugin_dir_path( __FILE__ ) );

require_once CGB_PATH . 'includes/options.php';

/**
 * Main plugin class
 *
 * This is the initial class for loading the plugin.
 */
class CGB_CommentGuestbook {

	/**
	 * Reference to options instance
	 *
	 * @var CGB_Options
	 */
	private $options;


	/**
	 * Class Constructor
	 * Initializes the plugin.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->options = CGB_Options::get_instance();

		// Always!
		add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ) );
		add_shortcode( 'comment-guestbook', array( &$this, 'shortcode_comment_guestbook' ) );
		add_action( 'widgets_init', array( &$this, 'widget_init' ) );

		// Depending on Page Type!
		if ( is_admin() ) { // Admin page.
			require_once CGB_PATH . 'admin/admin.php';
			CGB_Admin::get_instance()->init_admin_page();
		} else { // Front page.

			// Enable filters after a new comment (required to overwrite settings during comment creation).
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$comment_post_id = isset( $_POST['comment_post_ID'] ) ? intval( $_POST['comment_post_ID'] ) : false;
			if ( ! empty( $comment_post_id ) ) {
				$post = get_post( $comment_post_id );
				// Filters required after new guestbook comment.
				if ( $post instanceof WP_Post && (bool) strpos( $post->post_content, '[comment-guestbook]' ) ) {
					require_once CGB_PATH . 'includes/filters.php';
					new CGB_Filters( 'after_new_comment' );
					add_filter( 'comment_post_redirect', array( &$this, 'filter_comment_post_redirect' ) );
				}
			}
		}

		// Filters for comments on other pages/posts.
		add_action( 'comment_form_before_fields', array( &$this, 'page_comment_filters' ) );
		// Add message after comment.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$cmessage = isset( $_GET['cmessage'] ) ? intval( $_GET['cmessage'] ) : 0;
		if ( 1 === $cmessage ) {
			require_once CGB_PATH . 'includes/cmessage.php';
			$cmessage = CGB_CMessage::get_instance();
			$cmessage->init();
		}
	}


	/**
	 * Load comment-guestbook textdomain for translations
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'comment-guestbook', false, basename( CGB_PATH ) . '/languages' );
	}


	/**
	 * Initialize comment-guestbook shortcode
	 *
	 * @param array<string,string> $atts Shortcode attributes.
	 * @param string               $content Shortcode content.
	 * @return string HTML to display
	 */
	public function shortcode_comment_guestbook( $atts, $content = '' ) {
		static $shortcodes;
		if ( ! $shortcodes instanceof CGB_Shortcode ) {
			require_once CGB_PATH . 'includes/shortcode.php';
			$shortcodes = CGB_Shortcode::get_instance();
		}
		return $shortcodes->show_html( $atts, $content );
	}


	/**
	 * Initialize comment-guestbook widget
	 *
	 * @return void
	 */
	public function widget_init() {
		require_once CGB_PATH . 'includes/widget.php';
		register_widget( 'CGB_Widget' );
	}


	/**
	 * Filter to fix the link after adding a comment (required if clist_order = desc)
	 * and add the query value for the message after comment
	 *
	 * @param string $location The given link location.
	 * @return string
	 */
	public function filter_comment_post_redirect( $location ) {
		/**
		 * The page must be corrected due to available comment guestbook options.
		 * This is only required for cgb_comments and only if it is not a comment from another page
		 * (checked by comparing 'is_cgb_comment' and 'comment_post_ID' POST values).
		 */
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$comment_post_id = isset( $_POST['comment_post_ID'] ) ? intval( $_POST['comment_post_ID'] ) : false;
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$is_cgb_comment = isset( $_POST['is_cgb_comment'] ) ? intval( $_POST['is_cgb_comment'] ) : false;
		if ( $is_cgb_comment === $comment_post_id ) {
			global $comment_id;
			require_once 'includes/comments-functions.php';
			$cgb_func = CGB_Comments_Functions::get_instance();
			$page     = $cgb_func->get_page_of_comment( $comment_id );
			$location = get_comment_link( $comment_id, array( 'page' => $page ) );
		}

		// Add the query value for message after comment.
		require_once CGB_PATH . 'includes/cmessage.php';
		$cmessage = CGB_CMessage::get_instance();
		$location = $cmessage->add_cmessage_indicator( $location );
		return $location;
	}


	/**
	 * Set filters for other pages / Posts
	 *
	 * @return void
	 */
	public function page_comment_filters() {
		global $post;
		if ( ! ( is_object( $post ) && (bool) strstr( $post->post_content, '[comment-guestbook' ) ) ) {
			// Remove mail field.
			if ( '' !== $this->options->get( 'cgb_page_remove_mail' ) ) {
				add_filter( 'comment_form_field_email', '__return_empty_string', 20 );
			}
			// Remove website url field.
			if ( '' !== $this->options->get( 'cgb_page_remove_website' ) ) {
				add_filter( 'comment_form_field_url', '__return_empty_string', 20 );
			}
		}
		// Add message after comment.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$cmessage = isset( $_GET['cmessage'] ) ? intval( $_GET['cmessage'] ) : 0;
		if ( 1 === $cmessage ) {
			require_once CGB_PATH . 'includes/cmessage.php';
			CGB_CMessage::get_instance()->init();
		}
	}

}

/**
 * CommentGuestbook Class instance
 *
 * @var CGB_CommentGuestbook
 */
$cgb_comment_guestbook = new CGB_CommentGuestbook();

