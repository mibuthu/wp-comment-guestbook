<?php
if(!defined('ABSPATH')) {
	exit;
}

require_once(CGB_PATH.'includes/options.php');

// This class handles all available admin pages
class CGB_Admin {
	private static $instance;
	private $options;

	private function __construct() {
		$this->options = &CGB_Options::get_instance();
	}

	public static function &get_instance() {
		// Create class instance if required
		if(!isset(self::$instance)) {
			self::$instance = new CGB_Admin();
		}
		// Return class instance
		return self::$instance;
	}

	public function init_admin_page() {
		// Register actions
		add_action('admin_menu', array(&$this, 'register_pages'));
		add_action('plugins_loaded', array(&$this->options, 'version_upgrade'));
	}

	/**
	 * Add and register all admin pages in the admin menu
	 */
	public function register_pages() {
		add_submenu_page('edit-comments.php', 'About Comment Guestbook', 'About Guestbook', 'edit_posts', 'cgb_admin_main', array(&$this, 'show_about_page'));
		add_submenu_page('options-general.php', 'Comment Guestbook Settings', 'Guestbook', 'manage_options', 'cgb_admin_options', array(&$this, 'show_settings_page'));
	}

	public function show_about_page() {
		require_once(CGB_PATH.'admin/includes/admin-about.php');
		CGB_Admin_About::get_instance()->show_about();
	}
/*
	public function embed_about_scripts() {
		require_once(CGB_PATH.'admin/includes/admin-about.php');
		CGB_Admin_About::get_instance()->embed_about_scripts();
	}
*/
	public function show_settings_page() {
		require_once(CGB_PATH.'admin/includes/admin-settings.php');
		CGB_Admin_Settings::get_instance()->show_settings();
	}
}
?>
