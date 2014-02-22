<?php
if(!defined('ABSPATH')) {
	exit;
}

require_once(CGB_PATH.'includes/options.php');

// This class handles all data for the admin about page
class CGB_Admin_About {
	private static $instance;
	private $options;

	private function __construct() {
		$this->options = &CGB_Options::get_instance();
	}

	public static function &get_instance() {
		// Create class instance if required
		if(!isset(self::$instance)) {
			self::$instance = new CGB_Admin_About();
		}
		// Return class instance
		return self::$instance;
	}

	// show the admin about page as a submenu of "Comments"
	public function show_about() {
		if(!current_user_can('edit_posts')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}

		$out = '
			<div class="wrap nosubsub" style="padding-bottom:15px">
			<div id="icon-edit-comments" class="icon32"><br /></div><h2>About Comment Guestbook</h2>
			</div>
			<h3>Create a guestbook page</h3>
			<div style="padding:0 15px">
				<p>"Comment guestbook" works by using a "shortcode" in a page.</p>
				<p>To create a guestbook goto "Pages" &rarr; "Add new" in the admin menu and create a new page. Choose your page title e.g. "Guestbook" and add the shortcode <code>[comment-guestbook]</code> in the text field.<br />
				You can add additional text and html code if you want to display something else on that page. That´s all you have to do. Save and publish the page to finish the guestbook creation.</p>
			</div>';
		echo $out;
	}
}
?>
