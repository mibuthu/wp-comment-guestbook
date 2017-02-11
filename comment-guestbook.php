<?php
/*
Plugin Name: Comment Guestbook
Plugin URI: http://wordpress.org/extend/plugins/comment-guestbook/
Description: Add a guestbook page which uses the wordpress integrated comments.
Version: 0.7.3
Author: mibuthu
Author URI: http://wordpress.org/extend/plugins/comment-guestbook/
Text Domain: comment-guestbook
License: GPLv2

A plugin for the blogging MySQL/PHP-based WordPress.
Copyright 2012-2017 mibuthu

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

if(!defined('WPINC')) {
	exit;
}

// GENERAL DEFINITIONS
define('CGB_URL', plugin_dir_url(__FILE__));
define('CGB_PATH', plugin_dir_path(__FILE__));


// MAIN PLUGIN CLASS
class Comment_Guestbook {
	private $shortcode;
	private $options;

	/**
	 * Constructor:
	 * Initializes the plugin.
	 */
	public function __construct() {
		$this->shortcode = null;
		$this->options = null;

		// ALWAYS:
		// Register translation
		add_action('plugins_loaded', array(&$this, 'load_textdomain'));
		// Register shortcodes
		add_shortcode('comment-guestbook', array(&$this, 'shortcode_comment_guestbook'));
		// Register widgets
		add_action('widgets_init', array(&$this, 'widget_init'));

		// ADMIN PAGE:
		if (is_admin()) {
			// Include required php-files and initialize required objects
			require_once(CGB_PATH.'admin/admin.php');
			CGB_Admin::get_instance()->init_admin_page();
		}

		// FRONT PAGE:
		else {
			require_once('includes/options.php');
			$this->options = CGB_Options::get_instance();

			// Filters required after a new comment
			if(isset($_POST['comment_post_ID'])) {
				// Set filter to override email requirement for a new comment if the email field is removed
				add_filter('option_require_name_email', array(&$this, 'filter_require_name_email'));
				// Fix link after adding a comment (required if clist_order = desc) and added query for message after comment
				add_filter('comment_post_redirect', array(&$this, 'filter_comment_post_redirect'));

				// Filters required after new guestbook comment
				if(isset($_POST['is_cgb_comment']) && $_POST['is_cgb_comment'] == $_POST['comment_post_ID']) {
					// Set filter to override comments_open status
					if(isset($_POST['cgb_comments_status']) && 'open' === $_POST['cgb_comments_status']) {
						add_filter('comments_open', array(&$this, 'filter_ignore_comments_open'), 50);
					}
					// Set filter to override registration requirements for comments on guestbook page
					add_filter('option_comment_registration', array(&$this, 'filter_ignore_comment_registration'));
					// Set filter to override moderation requirements for comments on guestbook page
					add_filter('option_comment_moderation', array(&$this, 'filter_ignore_comment_moderation'));
				}
			}

			// Filters for comments on other pages/posts
			add_action('comment_form_before_fields', array(&$this, 'page_comment_filters'));
			// Add message after comment
			if(isset($_GET['cmessage']) && 1 == $_GET['cmessage']) {
				require_once(CGB_PATH.'includes/cmessage.php');
				$cmessage = CGB_CMessage::get_instance();
				$cmessage->init();
			}
		}
	} // end constructor

	public function load_textdomain() {
		load_plugin_textdomain('comment-guestbook', false, basename(CGB_PATH).'/languages');
	}

	public function shortcode_comment_guestbook($atts) {
		if(NULL == $this->shortcode) {
			require_once('includes/sc_comment-guestbook.php');
			$this->shortcode = SC_Comment_Guestbook::get_instance();
		}
		return $this->shortcode->show_html($atts);
	}

	public function widget_init() {
		// Widget "comment-guestbook"
		require_once('includes/widget.php');
		return register_widget('CGB_Widget');
	}

	public function filter_ignore_comments_open($option_value) {
		return true;
	}

	public function filter_ignore_comment_registration($option_value) {
		return $this->options->get('cgb_ignore_comment_registration') ? false : $option_value;
	}

	public function filter_ignore_comment_moderation($option_value) {
		return $this->options->get('cgb_ignore_comment_moderation') ? false : $option_value;
	}

	public function filter_require_name_email($option_value) {
		// Check if the wp-option is enabled
		if($option_value) {
			$is_cgb_comment = (isset($_POST['is_cgb_comment']) && $_POST['is_cgb_comment'] == $_POST['comment_post_ID']);
			// check if the require name, email requirement is disabled for cgb-comments
			if($is_cgb_comment && $this->options->get('cgb_form_require_no_name_mail')) {
				return false;
			}
			// check if the plugin options require an override
			if( ($is_cgb_comment && $this->options->get('cgb_form_remove_mail')) || $this->options->get('cgb_page_remove_mail') ) {
				$user = wp_get_current_user();
				// Check if the user is logged in and if a valid author name is given
				if(!$user->exists() && isset($_POST['author']) && '' != trim(strip_tags($_POST['author']))) {
					// override value
					return false;
				}
			}
		}
		// use standard value
		return $option_value;
	}

	public function filter_comment_post_redirect ($location) {
		// page must be corrected due to available comment guestbook options
		// this is only required for cgb_comments and only if it is not a comment from another page (check by comparing 'is_cgb_comment' and 'comment_post_ID' POST values)
		if(isset($_POST['is_cgb_comment']) && $_POST['is_cgb_comment'] == $_POST['comment_post_ID']) {
			global $comment_id;
			require_once('includes/comments-functions.php');
			$cgb_func = CGB_Comments_Functions::get_instance();
			$page = $cgb_func->get_page_of_comment($comment_id);
			$location = get_comment_link($comment_id, array('page' => $page));
		}

		// add query for message after comment
		require_once(CGB_PATH.'includes/cmessage.php');
		$cmessage = CGB_CMessage::get_instance();
		$location = $cmessage->add_cmessage_indicator($location);
		return $location;
	}

	public function page_comment_filters() {
		global $post;
		if(!(is_object($post) && strstr($post->post_content, '[comment-guestbook'))) {
			// remove mail field
			if('' != $this->options->get('cgb_page_remove_mail')) {
				add_filter('comment_form_field_email', array(&$this, 'form_field_remove_filter'), 20);
			}
			// remove website url field
			if('' != $this->options->get('cgb_page_remove_website')) {
				add_filter('comment_form_field_url', array(&$this, 'form_field_remove_filter'), 20);
			}
		}
		// Add message after comment
		if(isset($_GET['cmessage']) && 1 == $_GET['cmessage']) {
			require_once(CGB_PATH.'includes/cmessage.php');
			$cmessage = CGB_CMessage::get_instance();
			$cmessage->init();
		}
	}

	public function form_field_remove_filter() {
		return '';
	}
} // end class

// create a class instance
$cgb = new Comment_Guestbook();
?>
