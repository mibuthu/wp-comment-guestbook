<?php
/**
 * CommentGuestbooks Main Admin Class
 *
 * @package comment-guestbook
 */

declare(strict_types=1);
if ( ! defined( 'WP_ADMIN' ) ) {
	exit;
}

require_once CGB_PATH . 'includes/options.php';

/**
 * CommentGuestbooms Main Admin Class
 *
 * This class handles all CommentGuestbook admin pages.
 */
class CGB_Admin {

	/**
	 * Class singleton instance reference
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Options class instance reference
	 *
	 * @var CGB_Options
	 */
	private $options;


	/**
	 * Singleton provider and setup
	 *
	 * @return self
	 */
	public static function &get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Class constructor which initializes required variables
	 */
	private function __construct() {
		$this->options = &CGB_Options::get_instance();
	}


	/**
	 * Initialize the admin page (register required admin actions)
	 *
	 * @return void
	 */
	public function init_admin_page() {
		add_action( 'admin_menu', array( &$this, 'register_pages' ) );
		add_action( 'plugins_loaded', array( &$this->options, 'version_upgrade' ) );
	}


	/**
	 * Add and register all pages in the admin menu
	 *
	 * @return void
	 */
	public function register_pages() {
		$page = add_submenu_page(
			'edit-comments.php',
			sprintf( __( 'About %1$s', 'comment-guestbook' ), 'Comment Guestbook' ),
			__( 'About Guestbook', 'comment-guestbook' ),
			'edit_posts',
			'cgb_admin_about',
			array( &$this, 'show_about_page' )
		);
		add_action( 'admin_print_scripts-' . $page, array( &$this, 'embed_about_styles' ) );
		$page = add_submenu_page(
			'options-general.php',
			__( 'Comment Guestbook Settings', 'comment-guestbook' ),
			__( 'Guestbook', 'comment-guestbook' ),
			'manage_options',
			'cgb_admin_options',
			array( &$this, 'show_settings_page' )
		);
		add_action( 'admin_print_scripts-' . $page, array( &$this, 'embed_settings_styles' ) );
	}


	/**
	 * Show the plugins about page
	 *
	 * @return void
	 */
	public function show_about_page() {
		require_once CGB_PATH . 'admin/includes/admin-about.php';
		CGB_Admin_About::get_instance()->show_about();
	}


	/**
	 * Show the plugins settings page
	 *
	 * @return void
	 */
	public function show_settings_page() {
		require_once CGB_PATH . 'admin/includes/admin-settings.php';
		CGB_Admin_Settings::get_instance()->show_settings();
	}


	/**
	 * Embed the plugins about page styles
	 * TODO: move to admin about class
	 *
	 * @return void
	 */
	public function embed_about_styles() {
		wp_enqueue_style( 'cgb_admin_about', CGB_URL . 'admin/css/admin_about.css', array(), '1.0' );
	}


	/**
	 * Embed the plugins settings page styles
	 * TODO: move to admin settings class
	 *
	 * @return void
	 */
	public function embed_settings_styles() {
		wp_enqueue_style( 'cgb_admin_settings', CGB_URL . 'admin/css/admin_settings.css', array(), '1.0' );
	}

}

