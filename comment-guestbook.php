<?php
/*
Plugin Name: Comment Guestbook
Plugin URI: http://wordpress.org/extend/plugins/comment-guestbook/
Description: Add a guestbook page which uses the wordpress integrated comments.
Version: 0.1.1
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

// general definitions
define( 'CGB_PATH', plugin_dir_path( __FILE__ ) );


// ADMIN PAGE:
if ( is_admin() ) {
   add_action( 'admin_menu', 'on_cgb_admin'); // add admin pages in admin menu
   add_action( 'admin_init', 'on_cgb_upgrade_options'); // upgrade the renamed options (this can be removed in a later version)
   add_action( 'admin_init', 'on_cgb_register_settings' ); // register settings
}
// FRONT PAGE:
else {
   add_shortcode('comment-guestbook', 'on_cgb_sc_comment_guestbook'); // add shortcode [comment-guestbook]
}

function on_cgb_admin() {
	require_once( 'php/admin.php' );
	add_submenu_page( 'edit-comments.php', 'Comment Guestbook', 'Guestbook', 'edit_posts', 'cgb_admin_main', array( cgb_admin, 'show_main' ) );
}

function on_cgb_register_settings() {
	require_once( 'php/options.php' );
	cgb_options::register();
}

function on_cgb_sc_comment_guestbook( $atts ) {
	require_once( 'php/sc_comment-guestbook.php' );
	return sc_comment_guestbook::show_html( $atts );
}

// This function upgrades the renamed options
// Version 0.1.0 to 0.1.1:
//    cgb_clist_comment_adjust -> cgb_comment_adjust
//    cgb_clist_comment_html   -> cgb_comment_html
function on_cgb_upgrade_options() {
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
?>
