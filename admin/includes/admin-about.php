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
			<div id="icon-edit-comments" class="icon32"><br /></div><h2>'.__('About Comment Guestbook','comment-guestbook').'</h2>
			</div>
			<h3>'.__('Help and Instructions','comment-guestbook').'</h3>
			<h4>'.__('Create a guestbook page','comment-guestbook').'</h4>
			<div class="help-content">
				<p>'.__('Comment Guestbook is included in a page by using a "shortcode".','comment-guestbook').'</p>
				<p>'.sprintf(__('Goto %1$s to create a new page for the guestbook.','comment-guestbook'), '<a href="'.admin_url('post-new.php?post_type=page').'">'.__('Pages').' &rarr; '.__('Add New','comment-guestbook').'</a>').'<br />
				'.sprintf(__('Choose a page title e.g. "Guestbook" and paste the shortcode %1$s in the page content text field.<br />
				If required, additional text and html code can be added there.','comment-guestbook'), '<code>[comment-guestbook]</code>').'<br />
				'.__('That´s all you have to do. Save and publish the page to finish the guestbook creation.','comment-guestbook').'</p>
			</div>
			<h4>'.__('Modify the guestbook page','comment-guestbook').'</h4>
			<div class="help-content">
				<p>'.sprintf(__('In the comment guestbook settings page, available under %1$s, you can find a huge amount of options to modify the guestbook page.','comment-guestbook'), '<a href="'.admin_url('options-general.php?page=cgb_admin_options').'">'.__('Settings').' &rarr; '.__('Guestbook','comment-guestbook').'</a>').'<br />
				'.__('There are also some options available to change the comments of all posts and pages.','comment-guestbook').'</p>
			</div>
			<h4>'.__('Comment guestbook widget','comment-guestbook').'</h4>
			<div class="help-content">
				<p>'.sprintf(__('There is also a %1$s called "Comment Guestbook" available.','comment-guestbook'), '<a href="'.admin_url('widgets.php').'">'.__('Widget').'</a>').'<br />
				'.__('With this widget you can can add a list of the latest comments in your sidebar.<br />
				There are also many options to modify the output.','comment-guestbook').'</p>
			</div>
			<br />
			<h3>'.__('About Comment Guestbook','comment-guestbook').'</h3>
			<div class="help-content">
				<p>'.sprintf(__('This plugin is developed by mibuthu, more information about the plugin you can find on the %1$swordpress plugin site%2$s.','comment-guestbook'), '<a href="http://wordpress.org/plugins/comment-guestbook">', '</a>').'</p>
				<p>'.sprintf(__('If you like the plugin please give me a good rating on the %1$swordpress plugin review site%2$s.','comment-guestbook'), '<a href="http://wordpress.org/support/view/plugin-reviews/comment-guestbook">', '</a>').'<br />
				<p>'.sprintf(__('I would also be happy to get a small %1$sdonation%2$s.','comment-guestbook'), '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W54LNZMWF9KW2" target="_blank">', '</a>').'</p>
			</div>');
	}
}
?>
