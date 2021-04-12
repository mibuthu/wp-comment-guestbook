<?php
/**
 * Plugin Name: Comment Guestbook
 * Plugin URI: https://wordpress.org/plugins/comment-guestbook/
 * Description: Add a guestbook page which uses the WordPress integrated comments.
 * Version: 0.8.0
 * Author: mibuthu
 * Author URI: https://wordpress.org/plugins/comment-guestbook/
 * Text Domain: comment-guestbook
 * License: GPLv2
 *
 * A plugin for the blogging MySQL/PHP-based WordPress.
 * Copyright 2012-2021 mibuthu
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

namespace WordPress\Plugins\mibuthu\CommentGuestbook;

use WordPress\Plugins\mibuthu\CommentGuestbook\Shortcode\Shortcode;
use WordPress\Plugins\mibuthu\CommentGuestbook\Admin\Admin;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

// General definitions.
define( __NAMESPACE__ . '\PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( __NAMESPACE__ . '\PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

require_once PLUGIN_PATH . 'includes/config.php';

/**
 * Main plugin class
 *
 * This is the initial class for loading the plugin.
 */
class CommentGuestbook {

	/**
	 * Config instance used for the whole plugin
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Holds the info if the actual post is a guestbook (has the guestbook shortcode)
	 *
	 * @var bool
	 */
	private $is_guestbook_post = false;


	/**
	 * Class Constructor
	 * Initializes the plugin.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->config = new Config();

		// Always!
		add_action( 'plugins_loaded', [ &$this, 'load_textdomain' ], 10 );
		add_shortcode( 'comment-guestbook', [ &$this, 'shortcode_comment_guestbook' ] );
		add_action( 'widgets_init', [ &$this, 'widget_init' ] );

		// Depending on Page Type!
		if ( is_admin() ) { // Admin page.
			require_once PLUGIN_PATH . 'admin/admin.php';
			$admin = new Admin( $this->config );
			$admin->init_admin_page();
		} else { // Front page.
			add_filter( 'option_comments_per_page', [ &$this, 'filter_comments_per_page' ] );
			add_action( 'pre_get_posts', [ &$this, 'detect_shortcode' ] );
			// Enable filters after a new comment (required to overwrite settings during comment creation).
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$comment_post_id = isset( $_POST['comment_post_ID'] ) ? intval( $_POST['comment_post_ID'] ) : false;
			if ( ! empty( $comment_post_id ) ) {
				$this->detect_shortcode( $comment_post_id );
				// Filters required after new guestbook comment.
				if ( $this->is_guestbook_post ) {
					require_once PLUGIN_PATH . 'includes/filters.php';
					$filters = new Filters( $this->config );
					$filters->init();
					add_filter( 'comment_post_redirect', [ &$this, 'filter_comment_post_redirect' ] );
				}
			}
		}
		// Filters for comments on other pages/posts.
		add_action( 'comment_form_before_fields', [ &$this, 'page_comment_filters' ] );
		// Add message after comment.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$cmessage = isset( $_GET['cmessage'] ) ? intval( $_GET['cmessage'] ) : 0;
		if ( 1 === $cmessage ) {
			require_once PLUGIN_PATH . 'includes/cmessage.php';
			$cmessage = new CMessage( $this->config );
			$cmessage->init();
		}
	}


	/**
	 * Checks if the actual post is a guestbook post (has the comment-guestbook shortcode).
	 * Add the required filter which has to be provided before the shortcode is loaded.
	 *
	 * @param int $post_id The post id of the post to check (optional).
	 * @return void
	 */
	public function detect_shortcode( $post_id = null ) {
		$post = get_post( $post_id );
		if ( $post instanceof \WP_Post ) {
			$this->is_guestbook_post = has_shortcode( $post->post_content, 'comment-guestbook' );
			if ( $this->is_guestbook_post ) {
				add_filter( 'option_comments_per_page', [ &$this, 'filter_comments_per_page' ] );
			}
		}
	}


	/**
	 * Load comment-guestbook textdomain for translations
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'comment-guestbook', false, basename( PLUGIN_PATH ) . '/languages' );
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
		if ( ! $shortcodes instanceof Shortcode ) {
			require_once PLUGIN_PATH . 'shortcode/shortcode.php';
			$shortcodes = new Shortcode( $this->config );
		}
		return $shortcodes->show_html( $atts, $content );
	}


	/**
	 * Initialize comment-guestbook widget
	 *
	 * @return void
	 */
	public function widget_init() {
		require_once PLUGIN_PATH . 'widget/widget.php';
		$widget = new Widget\Widget( $this->config );
		register_widget( $widget );
	}


	/**
	 * Filter to adjust comments_per_page option
	 *
	 * @param string $option_value The actual value of the option "comments_per_page".
	 * @return string
	 */
	public function filter_comments_per_page( $option_value ) {
		if ( 0 < $this->config->clist_per_page->to_int() ) {
			return $this->config->clist_per_page->to_str();
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
			require_once PLUGIN_PATH . 'includes/comments-functions.php';
			$cgb_func = new Comments_Functions( $this->config );
			$page     = $cgb_func->get_page_of_comment( $comment_id );
			$location = get_comment_link( $comment_id, [ 'page' => $page ] );
		}

		// Add the query value for message after comment.
		require_once PLUGIN_PATH . 'includes/cmessage.php';
		$cmessage = new CMessage( $this->config );
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
		if ( ! ( is_object( $post ) && has_shortcode( $post->post_content, 'comment - guestbook' ) ) ) {
			// Remove mail field.
			if ( $this->config->page_remove_mail->to_bool() ) {
				add_filter( 'comment_form_field_email', '__return_empty_string', 20 );
			}
			// Remove website url field.
			if ( $this->config->page_remove_website->to_bool() ) {
				add_filter( 'comment_form_field_url', '__return_empty_string', 20 );
			}
		}
		// Add message after comment.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$cmessage = isset( $_GET['cmessage'] ) ? intval( $_GET['cmessage'] ) : 0;
		if ( 1 === $cmessage ) {
			require_once PLUGIN_PATH . 'includes/cmessage.php';
			$cmessage = new CMessage( $this->config );
			$cmessage->init();
		}
	}

}

/**
 * CommentGuestbook Class instance
 *
 * @var CommentGuestbook
 */
$comment_guestbook = new CommentGuestbook();

