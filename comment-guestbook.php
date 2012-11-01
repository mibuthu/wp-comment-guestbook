<?php
/*
Plugin Name: Comment Guestbook
Plugin URI: http://wordpress.org/extend/plugins/comment-guestbook/
Description: Add a guestbook page which uses the wordpress integrated comments.
Version: 0.1.2
Author: Michael Burtscher
Author URI: http://wordpress.org/extend/plugins/comment-guestbook/

A plugin for the blogging MySQL/PHP-based WordPress.
Copyright 2012 Michael Burtscher

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

// GENERAL DEFINITIONS
define( 'CGB_PATH', plugin_dir_path( __FILE__ ) );


// MAIN PLUGIN CLASS
class comment_guestbook {

	/**
	 * Constructor:
	 * Initializes the plugin.
	 */
	function __construct() {

		// TODO: replace "plugin-name-locale" with a unique value for your plugin
		//load_plugin_textdomain( 'plugin-name-locale', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

		// Register admin styles and scripts
		//add_action( 'admin_print_styles', array( &$this, 'register_admin_styles' ) );
		//add_action( 'admin_enqueue_scripts', array( &$this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		//add_action( 'wp_enqueue_scripts', array( &$this, 'register_plugin_styles' ) );
		//add_action( 'wp_enqueue_scripts', array( &$this, 'register_plugin_scripts' ) );

		//register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		//register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );

		// Include required php-files
		require_once( 'php/admin.php' );
		require_once( 'php/options.php' );
		require_once( 'php/sc_comment-guestbook.php' );

		// Register all actions and shortcodes
		// for admin page:
		add_action( 'admin_init', array( 'cgb_options', 'register' ) );
		add_action( 'admin_init', array( &$this, 'upgrade_options' ) );
		add_action( 'admin_menu', array( &$this, 'action_admin' ) );
		// for front page:
		add_shortcode( 'comment-guestbook', array( 'sc_comment_guestbook', 'show_html' ) ); // add shortcode [comment-guestbook]
	} // end constructor

	/**
	 * Fired when the plugin is activated.
	 *
	 * @params	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	//public function activate( $network_wide ) {
		// TODO define activation functionality here
	//} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @params	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	//public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here
	//} // end deactivate

	/**
	 * Registers and enqueues admin-specific styles.
	 */
/*	public function register_admin_styles() {

		// TODO change 'plugin-name' to the name of your plugin
		wp_register_style( 'plugin-name-admin-styles', plugins_url( 'plugin-name/css/admin.css' ) );
		wp_enqueue_style( 'plugin-name-admin-styles' );
	} // end register_admin_styles
*/
	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
/*	public function register_admin_scripts() {

		// TODO change 'plugin-name' to the name of your plugin
		wp_register_script( 'plugin-name-admin-script', plugins_url( 'plugin-name/js/admin.js' ) );
		wp_enqueue_script( 'plugin-name-admin-script' );
	} // end register_admin_scripts
*/
	/**
	 * Registers and enqueues plugin-specific styles.
	 */
/*	public function register_plugin_styles() {

		// TODO change 'plugin-name' to the name of your plugin
		wp_register_style( 'plugin-name-plugin-styles', plugins_url( 'plugin-name/css/display.css' ) );
		wp_enqueue_style( 'plugin-name-plugin-styles' );
	} // end register_plugin_styles
*/
	/**
	 * Registers and enqueues plugin-specific scripts.
	 */
/*	public function register_plugin_scripts() {

		// TODO change 'plugin-name' to the name of your plugin
		wp_register_script( 'plugin-name-plugin-script', plugins_url( 'plugin-name/js/display.js' ) );
		wp_enqueue_script( 'plugin-name-plugin-script' );
	} // end register_plugin_scripts
*/

	// This function upgrades the renamed options
	// Version 0.1.0 to 0.1.1:
	//    cgb_clist_comment_adjust -> cgb_comment_adjust
	//    cgb_clist_comment_html   -> cgb_comment_html
	public function upgrade_options() {
		$value = get_option( 'cgb_clist_comment_adjust', null );
		if( $value != null ) {
			add_option( 'cgb_comment_adjust', $value, '', 'no' );
			delete_option( 'cgb_clist_comment_adjust' );
		}
		$value = get_option( 'cgb_clist_comment_html', null );
		if( $value != null ) {
			add_option( 'cgb_comment_html', $value, '', 'no' );
			delete_option( 'cgb_clist_comment_html' );
		}
	}

	/**
	 * Admin Action:
	 *
	 * Add and register all admin pages in the admin menu
	 */
	public function action_admin() {
		add_submenu_page( 'edit-comments.php', 'Comment Guestbook', 'Guestbook', 'edit_posts', 'cgb_admin_main', array( 'cgb_admin', 'show_main' ) );
	}
} // end class

// create a class instance
new comment_guestbook();
?>
