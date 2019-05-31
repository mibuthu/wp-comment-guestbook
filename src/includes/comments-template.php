<?php
/**
 * The custom template for displaying Comments for comment-guestbook plugin.
 *
 * @package comment-guestbook
 */

declare( strict_types=1 );
if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once CGB_PATH . 'includes/options.php';
require_once CGB_PATH . 'includes/comments-functions.php';

$cgb_options = CGB_Options::get_instance();
$cgb_func    = CGB_Comments_Functions::get_instance();

global $wp_query;
$cgb_in_page = ! isset( $wp_query->comments );

// Prepare $wp_query when template is displayed in post/page content.
if ( $cgb_in_page ) {
	// Avoid phpcs warning for WordPress hook name.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	$wp_query->comments      = apply_filters( 'comments_array', $cgb_func->get_comments( $wp_query->post->ID ) );
	$wp_query->comment_count = count( $wp_query->comments );
}

// Show comment incl comment forms.
if ( ( '' === $cgb_options->get( 'cgb_clist_in_page_content' ) && ! $cgb_in_page ) ||
		( '' !== $cgb_options->get( 'cgb_clist_in_page_content' ) && $cgb_in_page ) ) {
	echo '
			<div id="comments">';

	// Comment form above comments.
	$cgb_func->show_comment_form_html( 'above_comments' );

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
	if ( have_comments() ) {
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
		$cgb_func->show_nav_html( 'above_comments' );
		echo '<ol class="commentlist">';
		$cgb_func->list_comments();
		echo '</ol>';
		$cgb_func->show_nav_html( 'below_comments' );
	}

	// Comment form below comments.
	$cgb_func->show_comment_form_html( 'below_comments' );
	echo '
			</div><!-- #comments -->';
}
