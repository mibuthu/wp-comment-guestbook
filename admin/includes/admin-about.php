<?php
if(!defined('WPINC')) {
	die;
}

require_once(CGB_PATH.'includes/options.php');

// This class handles all data for the admin about page
class CGB_Admin_About {
	private static $instance;

	private function __construct() {
		// nothing to do
	}

	public static function &get_instance() {
		// singleton setup
		if(!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	// show the admin about page as a submenu of "Comments"
	public function show_about() {
		if(!current_user_can('edit_posts')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}

		echo('
			<div class="wrap nosubsub" style="padding-bottom:15px">
			<div id="icon-edit-comments" class="icon32"><br /></div><h2>About Comment Guestbook</h2>
			</div>
			<h3>Help and Instructions</h3>
			<h4>Create a guestbook page</h4>
			<div class="help-content">
				<p>"Comment guestbook" works by using a "shortcode" in a page.</p>
				<p>To create a guestbook goto <a href="'.admin_url('post-new.php?post_type=page').'">Pages &rarr; Add new</a> and create a new page. <br />
				Choose your page title e.g. "Guestbook" and add the shortcode <code>[comment-guestbook]</code> in the text field.<br />
				If required, you can add additional text and html code on that page.<br />
				That´s all you have to do. Save and publish the page to finish the guestbook creation.</p>
			</div>
			<h4>Modify the guestbook page</h4>
			<div class="help-content">
				<p>In the comment guestbook settings page, available under <a href="'.admin_url('options-general.php?page=cgb_admin_options').'">Settings &rarr; Guestbook</a>, you can find a huge amount of options to modify the guestbook page.<br />
				There are also some options available to change the comments of all posts and pages.</p>
			</div>
			<h4>Comment guestbook widget</h4>
			<div class="help-content">
				<p>There is also a <a href="'.admin_url('widgets.php').'">widget</a> called "Comment Guestbook" available.<br />
				With this widget you can can add a list of the latest comments in your sidebar.<br />
				Also in the widget you have many options to modify the output.</p>
			</div>
			<br />
			<h3>About</h3>
			<div class="help-content">
				<p>This plugin is developed by mibuthu, more information about the plugin you can find on the <a href="http://wordpress.org/plugins/comment-guestbook">wordpress plugin site</a>.</p>
				<p>If you like the plugin please give me a good rating on the <a href="http://wordpress.org/support/view/plugin-reviews/comment-guestbook">wordpress plugin review site</a>.<br />
				<p>I would also be happy to get a small <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W54LNZMWF9KW2" target="_blank">donation</a>.</p>
			</div>');
	}
}
?>
