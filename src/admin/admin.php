<?php
/**
 * CommentGuestbooks Main Admin Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook\Admin;

use WordPress\Plugins\mibuthu\CommentGuestbook\Config;
use const WordPress\Plugins\mibuthu\CommentGuestbook\PLUGIN_PATH;
use const WordPress\Plugins\mibuthu\CommentGuestbook\PLUGIN_URL;

if ( ! defined( 'WP_ADMIN' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/config.php';

/**
 * The Comment Guestbook Main Admin Class
 *
 * This class handles all CommentGuestbook admin pages.
 */
class Admin {

	/**
	 * Config class instance reference
	 *
	 * @var Config
	 */
	private $config;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @param Config $config_instance The config instance as a reference.
	 */
	public function __construct( &$config_instance ) {
		$this->config = $config_instance;
	}


	/**
	 * Initialize the admin page (register required admin actions)
	 *
	 * @return void
	 */
	public function init_admin_page() {
		add_action( 'admin_menu', [ &$this, 'register_pages' ] );
		add_action( 'plugins_loaded', [ &$this->config, 'version_upgrade' ] );
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
			[ &$this, 'show_about_page' ]
		);
		add_action( 'admin_print_scripts-' . $page, [ &$this, 'embed_about_styles' ] );
		$page = add_submenu_page(
			'options-general.php',
			__( 'Comment Guestbook Settings', 'comment-guestbook' ),
			__( 'Guestbook', 'comment-guestbook' ),
			'manage_options',
			'cgb_admin_settings',
			[ &$this, 'show_settings_page' ]
		);
		add_action( 'admin_print_scripts-' . $page, [ &$this, 'embed_settings_styles' ] );
	}


	/**
	 * Show the plugins about page
	 *
	 * @return void
	 */
	public function show_about_page() {
		require_once PLUGIN_PATH . 'admin/about.php';
		$about = new About();
		$about->show_page();
	}


	/**
	 * Show the plugins settings page
	 *
	 * @return void
	 */
	public function show_settings_page() {
		require_once PLUGIN_PATH . 'admin/settings.php';
		$settings = new Settings( $this->config );
		$settings->show_page();
	}


	/**
	 * Embed the plugins about page styles
	 * TODO: move to admin about class
	 *
	 * @return void
	 */
	public function embed_about_styles() {
		wp_enqueue_style( 'cgb_admin_about', PLUGIN_URL . 'admin/css/about.css', [], '1.0' );
	}


	/**
	 * Embed the plugins settings page styles
	 * TODO: move to admin settings class
	 *
	 * @return void
	 */
	public function embed_settings_styles() {
		wp_enqueue_style( 'cgb_admin_settings', PLUGIN_URL . 'admin/css/settings.css', [], '1.0' );
	}

}

