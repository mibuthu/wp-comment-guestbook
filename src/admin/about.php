<?php
/**
 * CommentGuestbooks About Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook\Admin;

use const WordPress\Plugins\mibuthu\CommentGuestbook\PLUGIN_URL;

if ( ! defined( 'WP_ADMIN' ) ) {
	exit();
}

/**
 * CommentGuestbooks About Class
 *
 * This class handles the display of the admin about page
 */
class About {


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct() {
		// Nothing to do.
	}


	/**
	 * Show the admin about page
	 *
	 * @return void
	 */
	public function show_page() {
		// Check required privilegs.
		if ( ! current_user_can( 'edit_posts' ) ) {
			// phpcs:ignore WordPress.WP.I18n.MissingArgDomainDefault
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		echo wp_kses_post(
			'
			<div class="wrap nosubsub" style="padding-bottom:15px">
				<div id="icon-edit-comments" class="icon32"><br /></div><h2>' . sprintf( __( 'About %1$s', 'comment-guestbook' ), 'Comment Guestbook' ) . '</h2>'
		);
		$this->show_help();
		$this->show_author();
		$this->show_translation_info();
		echo '
			</div>';
	}


	/**
	 * Show help HTML
	 *
	 * @return void
	 */
	private function show_help() {
		echo wp_kses_post(
			'
			<h3>' . __( 'Help and Instructions', 'comment-guestbook' ) . '</h3>
			<h4>' . __( 'Create a guestbook page', 'comment-guestbook' ) . '</h4>
			<div class="help-content">
				<p>' . sprintf( __( '%1$s is included in a page by using a "shortcode".', 'comment-guestbook' ), 'Comment Guestbook' ) . '</p>
				<p>' .
				sprintf(
					__( 'Goto %1$s to create a new page for the guestbook.', 'comment-guestbook' ),
					'<a href="' .
					admin_url( 'post-new.php?post_type=page' ) . '">' .
					// phpcs:ignore WordPress.WP.I18n.MissingArgDomainDefault
					__( 'Pages' ) .
					' &rarr; ' . __( 'Add New', 'comment-guestbook' ) . '</a>'
				) . '<br />
				' .
				sprintf(
					__( 'Choose a page title e.g. "Guestbook" and paste the shortcode %1$s in the page content text field.', 'comment-guestbook' ),
					'<code>[comment-guestbook]</code>'
				) . '<br />
				' . __( 'If required, additional text and html code can be added there.', 'comment-guestbook' ) . '<br />
				' . __( 'That´s all you have to do. Save and publish the page to finish the guestbook creation.', 'comment-guestbook' ) . '</p>
			</div>
			<h4>' . __( 'Modify the guestbook page', 'comment-guestbook' ) . '</h4>
			<div class="help-content">
				<p>' .
				sprintf(
					__( 'In the %1$s settings page, available under %2$s, you can find a huge amount of options to modify the guestbook page.', 'comment-guestbook' ),
					'Comment Guestbook',
					'<a href="' . admin_url( 'options-general.php?page=cgb_admin_settings' ) . '">' .
					// phpcs:ignore WordPress.WP.I18n.MissingArgDomainDefault
					__( 'Settings' ) .
					' &rarr; ' . __( 'Guestbook', 'comment-guestbook' ) . '</a>'
				) . '<br />
				' . __( 'There are also some options available to change the comments of all posts and pages.', 'comment-guestbook' ) . '</p>
			</div>
			<h4>' . sprintf( __( '%1$s widget', 'comment-guestbook' ), 'Comment Guestbook' ) . '</h4>
			<div class="help-content">
				<p>' .
				sprintf(
					__( 'There is also a %1$s called %2$s available.', 'comment-guestbook' ),
					'<a href="' . admin_url( 'widgets.php' ) . '">' .
					// phpcs:ignore WordPress.WP.I18n.MissingArgDomainDefault
					__( 'Widget' ) .
					'</a>',
					'"Comment Guestbook"'
				) . '<br />
				' . __( 'With this widget you can can add a list of the latest comments in your sidebar.', 'comment-guestbook' ) . '<br />
				' . __( 'There are also many options to modify the output.', 'comment-guestbook' ) . '</p>
			</div>'
		);
	}


	/**
	 * Show author HTML
	 *
	 * @return void
	 */
	private function show_author() {
		echo wp_kses_post(
			'
			<h3>' . __( 'About the plugin author', 'comment-guestbook' ) . '</h3>
			<div class="help-content">
				<p>' . sprintf( __( 'This plugin is developed by %1$s, you can find more information about the plugin on the %2$s.', 'comment-guestbook' ), 'mibuthu', '<a href="https://wordpress.org/plugins/comment-guestbook/" target="_blank" rel="noopener">' . __( 'WordPress plugin site', 'comment-guestbook' ) . '</a>' ) . '</p>
				<p>' . sprintf( __( 'If you like the plugin please rate it on the %1$s.', 'comment-guestbook' ), '<a href="https://wordpress.org/support/plugin/comment-guestbook/reviews/" target="_blank" rel="noopener">' . __( 'WordPress plugin review site', 'comment-guestbook' ) . '</a>' ) . '<br />
				<p>' . __( 'If you want to support the plugin I would be happy to get a small donation', 'comment-guestbook' ) . ':<br />
				<a class="donate" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W54LNZMWF9KW2" target="_blank" rel="noopener"><img src="' . PLUGIN_URL . 'admin/images/paypal_btn_donate.gif" alt="PayPal Donation" title="' . sprintf( __( 'Donate with %1$s', 'comment-guestbook' ), 'PayPal' ) . '" border="0"></a>
				<a class="donate" href="https://liberapay.com/mibuthu/donate" target="_blank" rel="noopener"><img src="' . PLUGIN_URL . 'admin/images/liberapay-donate.svg" alt="Liberapay Donation" title="' . sprintf( __( 'Donate with %1$s', 'comment-guestbook' ), 'Liberapay' ) . '" border="0"></a>
				<a class="donate" href="https://flattr.com/submit/auto?user_id=mibuthu&url=https%3A%2F%2Fwordpress.org%2Fplugins%2Fcomment-guestbook" target="_blank" rel="noopener"><img src="' . PLUGIN_URL . 'admin/images/flattr-badge-large.png" alt="Flattr this" title="' . sprintf( __( 'Donate with %1$s', 'comment-guestbook' ), 'Flattr' ) . '" border="0"></a></p>
			</div>'
		);
	}


	/**
	 * Show translation info HTML
	 *
	 * @return void
	 */
	private function show_translation_info() {
		echo wp_kses_post(
			'
			<h3>' . __( 'Translations', 'comment-guestbook' ) . '</h3>
			<div class="help-content">
				<p>' . __( 'Please help translating this plugin into your language.', 'comment-guestbook' ) . '</p>
				<p>' . sprintf( __( 'You can submit your translations at %1$s.', 'comment-guestbook' ), '<a href="https://www.transifex.com/projects/p/wp-comment-guestbook">Transifex</a>' ) . '<br />
				' . __( 'There the source strings will be kept in sync with the actual development version. And in each plugin release the available translation files will be updated.', 'comment-guestbook' ) . '</p>'
		);
	}

}
