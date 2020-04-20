<?php
/**
 * Plugin Name: Comment Guestbook
 * Plugin URI: https://wordpress.org/plugins/comment-guestbook/
 * Description: Add a guestbook page which uses the WordPress integrated comments.
 * Version: 0.7.3
 * Author: mibuthu
 * Author URI: https://wordpress.org/plugins/comment-guestbook/
 * Text Domain: comment-guestbook
 * License: GPLv2
 *
 * A plugin for the blogging MySQL/PHP-based WordPress.
 * Copyright 2012-2018 mibuthu
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


/**
 * Main plugin class
 *
 * This is the initial class for loading the plugin.
 */
class CGB_CommentGuestbook {

	/**
	 * Reference to options instance
	 *
	 * @var null|CGB_Options
	 */
	private $options;


	/**
	 * Class Constructor
	 * Initializes the plugin.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->options = null;

		// Always!
		add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ) );
		add_shortcode( 'comment-guestbook', array( &$this, 'shortcode_comment_guestbook' ) );
		add_action( 'widgets_init', array( &$this, 'widget_init' ) );

		// Depending on Page Type!
		if ( is_admin() ) { // Admin page.
			require_once CGB_PATH . 'admin/admin.php';
			CGB_Admin::get_instance()->init_admin_page();
		} else { // Front page.
			require_once 'includes/options.php';
			$this->options = CGB_Options::get_instance();

			// Filters required after a new comment.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$comment_post_id = isset( $_POST['comment_post_ID'] ) ? intval( $_POST['comment_post_ID'] ) : false;
			if ( ! empty( $comment_post_id ) ) {
				add_filter( 'option_require_name_email', array( &$this, 'filter_require_name_email' ) );
				add_filter( 'comment_post_redirect', array( &$this, 'filter_comment_post_redirect' ) );

				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$is_cgb_comment = isset( $_POST['is_cgb_comment'] ) ? (bool) intval( $_POST['is_cgb_comment'] ) : false;
				// Filters required after new guestbook comment.
				if ( $is_cgb_comment && $is_cgb_comment === $comment_post_id ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing
					$cbg_comments_status = isset( $_POST['cgb_comments_status'] ) ? sanitize_key( $_POST['cgb_comments_status'] ) : false;
					if ( ! empty( $cbg_comments_status ) && 'open' === $cbg_comments_status ) {
						// Overwrite comments_open status.
						add_filter( 'comments_open', '__return_true', 50 );
					}
				}
				add_filter( 'option_comment_registration', array( &$this, 'filter_ignore_comment_registration' ) );
				add_filter( 'option_comment_moderation', array( &$this, 'filter_ignore_comment_moderation' ) );
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
	 * Filter to override registration requirements for comments on guestbook page
	 *
	 * @param bool $option_value The actual value of the option "comment_registration".
	 * @return bool
	 */
	public function filter_ignore_comment_registration( $option_value ) {
		return $this->options->get( 'cgb_ignore_comment_registration' ) ? false : $option_value;
	}


	/**
	 * Filter to override moderation requirements for comments on guestbook page
	 *
	 * @param bool $option_value The actual value of the option "comment_moderation".
	 * @return bool
	 */
	public function filter_ignore_comment_moderation( $option_value ) {
		return $this->options->get( 'cgb_ignore_comment_moderation' ) ? false : $option_value;
	}


	/**
	 * Filter to override email requirement for a new comment if the email field is removed.
	 *
	 * @param string $option_value The actual value of the option "require_name_email".
	 * @return string
	 */
	public function filter_require_name_email( $option_value ) {
		// Check if the given wp-option is enabled.
		// Use the given default value.
		if ( empty( $option_value ) ) {
			return $option_value;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$is_cgb_comment = ( isset( $_POST['is_cgb_comment'] ) && isset( $_POST['comment_post_ID'] ) && $_POST['is_cgb_comment'] === $_POST['comment_post_ID'] );
		// Check if the "require name, email" option is disabled for comment-guestbook comments.
		if ( $is_cgb_comment && (bool) $this->options->get( 'cgb_form_require_no_name_mail' ) ) {
			return '';
		}
		// Check if the plugin options require an override.
		if ( ( $is_cgb_comment && (bool) $this->options->get( 'cgb_form_remove_mail' ) ) || (bool) $this->options->get( 'cgb_page_remove_mail' ) ) {
			$user = wp_get_current_user();
			// Check if the user is logged in and if a valid author name is given.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$author = isset( $_POST['author'] ) ? sanitize_title( stripslashes_deep( $_POST['author'] ) ) : '';
			if ( ! $user->exists() && ! empty( $author ) ) {
				// Override value.
				return '';
			}
		}
		return $option_value;
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

