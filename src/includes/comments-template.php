<?php
/**
 * The custom template for displaying Comments for comment-guestbook plugin.
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/config.php';
require_once PLUGIN_PATH . 'includes/comments-functions.php';

// cgb_config and cgb_func must be provided.
global $wp_query, $cgb_config, $cgb_func;
if ( ! isset( $cgb_config ) || ! $cgb_config instanceof Config ) {
	error_log( 'Required Config instance in the variable "cgb_config" is missing!' );
}
if ( ! isset( $cgb_func ) || ! $cgb_func instanceof Comments_Functions ) {
	error_log( 'Required Config instance in the variable "cgb_config" is missing!' );
}
// Show comment including the comment forms (in page content or in comment area).
if ( ( '' === $cgb_config->get( 'cgb_clist_in_page_content' ) && ! isset( $GLOBALS['cgb_comment_template_in_page'] ) ) ||
		( '' !== $cgb_config->get( 'cgb_clist_in_page_content' ) && isset( $GLOBALS['cgb_comment_template_in_page'] ) ) ) {
	echo '
			<div id="comments">';
	// Comment form above comments.
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- no escaping required here
	echo $cgb_func->show_comment_form_html( 'above_comments' );

	// Is a password required?
	if ( post_password_required() ) {
		echo '
				<p class="nopassword">' .
				esc_html__(
					'This post is password protected. Enter the password to view any comments.',
					// A function for the translation domain is required here.
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
					$cgb_func->l10n_domain
				) . '</p>
			</div><!-- #comments -->';
		return;
	}

	// Are comments available?
	if ( count( $wp_query->comments ) ) {
		// Print custom list styles.
		$cgb_styles = $cgb_config->get( 'cgb_clist_styles' );
		if ( '' !== $cgb_styles ) {
			echo '
				<style>
					' . wp_kses_post( $cgb_styles ) . '
				</style>';
		}
		// Print custom title.
		$cgb_title = $cgb_config->get( 'cgb_clist_title' );
		if ( '' !== $cgb_title ) {
			echo '<h2 id="comments-title">' . esc_html( $cgb_title ) . '</h2>';
		}
		// Show comment list.
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- no escaping required here
		echo $cgb_func->show_nav_html( 'above_comments' );
		echo '<ol class="commentlist cgb-commentlist">';
		echo $cgb_func->list_comments();
		echo '</ol>';
		echo $cgb_func->show_nav_html( 'below_comments' );
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	// Comment form below comments.
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- no escaping required here
	echo $cgb_func->show_comment_form_html( 'below_comments' );
	echo '
			</div><!-- #comments -->';
}

