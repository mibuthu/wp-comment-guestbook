<?php
/**
 * The custom template for displaying Comments for comment-guestbook plugin.
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!
if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once CGB_PATH . 'includes/options.php';
require_once CGB_PATH . 'includes/comments-functions.php';

$cgb_options = CGB_Options::get_instance();
$cgb_func    = CGB_Comments_Functions::get_instance();

global $wp_query;

// Show comment including the comment forms (in page content or in comment area).
if ( ( '' === $cgb_options->get( 'cgb_clist_in_page_content' ) && ! isset( $GLOBALS['cgb_comment_template_in_page'] ) ) ||
		( '' !== $cgb_options->get( 'cgb_clist_in_page_content' ) && isset( $GLOBALS['cgb_comment_template_in_page'] ) ) ) {
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
		$cgb_styles = $cgb_options->get( 'cgb_clist_styles' );
		if ( '' !== $cgb_styles ) {
			echo '
				<style>
					' . wp_kses_post( $cgb_styles ) . '
				</style>';
		}
		// Print custom title.
		$cgb_title = $cgb_options->get( 'cgb_clist_title' );
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

