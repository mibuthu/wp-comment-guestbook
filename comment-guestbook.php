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
	public function __construct() {

		// ALWAYS:
		// TODO: replace "plugin-name-locale" with a unique value for your plugin
		//load_plugin_textdomain( 'plugin-name-locale', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
		// Register shortcodes
		add_shortcode( 'comment-guestbook', array( &$this, 'shortcode_comment_guestbook' ) );

		// ADMIN PAGE:
		if ( is_admin() ) {
			// Include required php-files and initialize required objects
			require_once( 'php/admin.php' );
			$admin = new cgb_admin();
			// Register actions
			//add_action( 'admin_print_styles', array( &$this, 'register_admin_styles' ) );
			//add_action( 'admin_enqueue_scripts', array( &$this, 'register_admin_scripts' ) );
			add_action( 'plugins_loaded', array( &$admin->options, 'version_upgrade' ) );
			add_action( 'admin_menu', array( &$admin, 'register_pages' ) );
		}
		// FRONT PAGE:
		//else {
			// Register actions
			//add_action( 'wp_enqueue_scripts', array( &$this, 'register_plugin_styles' ) );
			//add_action( 'wp_enqueue_scripts', array( &$this, 'register_plugin_scripts' ) );
		//}
	} // end constructor

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

	public function shortcode_comment_guestbook( $atts ) {
		require_once( 'php/sc_comment-guestbook.php' );
		$shortcode = new sc_comment_guestbook();
		return $shortcode->show_html( $atts );
	}
} // end class

// create a class instance
$cgb = new comment_guestbook();
?>
