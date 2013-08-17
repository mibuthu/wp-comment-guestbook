<?php
/**
 * The custom template for displaying Comments for comment-guestbook plugin.
 */
if(!defined('ABSPATH')) {
	exit;
}

require_once(CGB_PATH.'php/options.php');
require_once(CGB_PATH.'php/comments-functions.php');

$cgb_func = new cgb_comments_functions();
$options = cgb_options::get_instance();

global $wp_query;
$in_page = !isset($wp_query->comments);

// Prepare $wp_query when template is displayed in post/page content
if($in_page) {
	$wp_query->comments = get_comments(array('post_id' => $wp_query->post->ID, 'status' => 'approve', 'order' => 'ASC'));
	$wp_query->comment_count = count($wp_query->comments);
}

// Show comment incl comment forms
if(('' === $options->get('cgb_clist_in_page_content') && !$in_page) ||
		('' !== $options->get('cgb_clist_in_page_content') && $in_page)) {
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
		/* TODO: Insert an option to add a title before the comment list
		<h2 id="comments-title">
			<?php
				printf( get_the_title().' Entries:' );
			?>
		</h2>
		*/
		// comment navigation above list
		if(get_comment_pages_count() > 1 && get_option('page_comments')) {
			echo '
				<nav id="comment-nav-above">';
			$cgb_func->show_nav_html();
			echo '
				</nav>';
		}

		// comment list
		echo '
				<ol class="commentlist">';
		$cgb_func->list_comments();
		echo '
				</ol>';

		// comment navigation below list
		if(get_comment_pages_count() > 1 && get_option('page_comments')) {
			echo '
				<nav id="comment-nav-below">';
			$cgb_func->show_nav_html();
			echo '
				</nav>';
		}
	}

	// comment form below comments
	$cgb_func->show_comment_form_html('below_comments');
	echo '
			</div><!-- #comments -->';
}
?>
