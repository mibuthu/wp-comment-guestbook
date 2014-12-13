<?php
/*
Plugin Name: Comment Guestbook
Plugin URI: http://wordpress.org/extend/plugins/comment-guestbook/
Description: Add a guestbook page which uses the wordpress integrated comments.
Version: 0.6.8
Author: Michael Burtscher
Author URI: http://wordpress.org/extend/plugins/comment-guestbook/
License: GPLv2

A plugin for the blogging MySQL/PHP-based WordPress.
Copyright 2012-2014 Michael Burtscher

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

if(!defined('ABSPATH')) {
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
			// Filters required after new guestbook comment
			if(isset($_POST['is_cgb_comment']) && $_POST['is_cgb_comment'] == $_POST['comment_post_ID']) {
				require_once('includes/options.php');
				$this->options = CGB_Options::get_instance();
				// Set filter to overwrite comments_open status
				if(isset($_POST['cgb_comments_status']) && 'open' === $_POST['cgb_comments_status']) {
					add_filter('comments_open', array(&$this, 'filter_ignore_comments_open'), 50);
				}
				// Set filter to overwrite registration requirements for comments on guestbook page
				if(get_option('comment_registration') && $this->options->get('cgb_ignore_comment_registration')) {
					add_filter('option_comment_registration', array(&$this, 'filter_ignore_comment_registration'));
				}
				// Set filter to overwrite name and email requirement (actual requirement is set via guestbook options)
				add_filter('option_require_name_email', array(&$this, 'filter_require_name_email'));
			}
			// Fix link after adding a comment (required if clist_order = desc) and added query for message after comment
			add_filter('comment_post_redirect', array(&$this, 'filter_comment_post_redirect'));
			// Add message after comment
			if(isset($_GET['cmessage']) && 1 == $_GET['cmessage']) {
				require_once(CGB_PATH.'includes/cmessage.php');
				$cmessage = CGB_CMessage::get_instance();
				$cmessage->init();
			}
		}
	} // end constructor

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
		return false;
	}

	public function filter_require_name_email($option_value) {
		global $comment_author;
		$error_message = false;
		if($option_value && isset($comment_author)) {
			// when E-Mail field is removed
			if($this->options->get('cgb_form_remove_mail')) {
				$option_value = false;
				if('' == $comment_author) {
					wp_die(__('<strong>ERROR</strong>: please fill the required fields (name).'));
				}
			}
		}
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
} // end class

// create a class instance
$cgb = new Comment_Guestbook();
?>
