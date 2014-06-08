<?php
/**
 * The custom template for displaying Comments for comment-guestbook plugin.
 */
if(!defined('ABSPATH')) {
	exit;
}

require_once(CGB_PATH.'includes/options.php');
require_once(CGB_PATH.'includes/comments-functions.php');

$cgb_options = CGB_Options::get_instance();
$cgb_func = CGB_Comments_Functions::get_instance();

global $wp_query;
$in_page = !isset($wp_query->comments);

// Prepare $wp_query when template is displayed in post/page content
if($in_page) {
	$wp_query->comments = apply_filters('comments_array', $cgb_func->get_comments($wp_query->post->ID));
	$wp_query->comment_count = count($wp_query->comments);
}

// Show comment incl comment forms
if(('' === $cgb_options->get('cgb_clist_in_page_content') && !$in_page) ||
		('' !== $cgb_options->get('cgb_clist_in_page_content') && $in_page)) {
	echo '
			<div id="comments">';

	// comment form above comments
	$cgb_func->show_comment_form_html('above_comments');

	// is password required?
	if(post_password_required()) {
		echo '
				<p class="nopassword">'.__('This post is password protected. Enter the password to view any comments.', $cgb_func->l10n_domain).'</p>
			</div><!-- #comments -->';
		return;
	}

	// are comments available?
	if(have_comments()) {
		// print custom list styles
		$styles = $cgb_options->get('cgb_clist_styles');
		if('' != $styles) {
			echo '
				<style>
					'.$styles.'
				</style>';
		}
		// print custom title
		$title = $cgb_options->get('cgb_clist_title');
		if('' != $title) {
			echo '<h2 id="comments-title">'.$title.'</h2>';
		}
		// show comment list
		$cgb_func->show_nav_html('above_comments');
		echo '<ol class="commentlist">';
		$cgb_func->list_comments();
		echo '</ol>';
		$cgb_func->show_nav_html('below_comments');
	}

	// comment form below comments
	$cgb_func->show_comment_form_html('below_comments');
	echo '
			</div><!-- #comments -->';
}
?>
