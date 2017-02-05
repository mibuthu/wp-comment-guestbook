<?php
if(!defined('ABSPATH')) {
	exit;
}

require_once(CGB_PATH.'includes/options.php');

// This class handles all required function to display the comment list
class CGB_Comments_Functions {
	private static $instance;
	public $l10n_domain;
	private $options;
	private $nav_label_prev;
	private $nav_label_next;
	private $num_forms;

	public static function &get_instance() {
		// Create class instance if required
		if(!isset(self::$instance)) {
			self::$instance = new CGB_Comments_Functions();
		}
		// Return class instance
		return self::$instance;
	}

	private function __construct() {
		// get options instance
		$this->options = &CGB_Options::get_instance();
		// set language domain
		$this->l10n_domain = $this->options->get('cgb_l10n_domain');
		$this->nav_label_prev = __('&larr; Older Comments', $this->l10n_domain);
		$this->nav_label_next = __('Newer Comments &rarr;' , $this->l10n_domain);
		if('desc' === $this->options->get('cgb_clist_order')) {
			//switch labels and correct arrow
			$tmp_label = $this->nav_label_prev;
			$this->nav_label_prev = '&larr; '.substr($this->nav_label_next, 0, -6);
			$this->nav_label_next = substr($tmp_label, 6).' &rarr;';
		}
		$this->num_forms = 0;
	}

	public function list_comments() {
		// Prepare wp_list_comments args
		$args = array();
		// comment list args
		if('' != $this->options->get('cgb_clist_args')) {
			eval('$args_array = '.$this->options->get('cgb_clist_args').';');
			if(is_array($args_array)) {
				$args += $args_array;
			}
		}
		//comment callback function
		if('' === $this->options->get('cgb_comment_adjust') && is_callable($this->options->get('cgb_comment_callback'))) {
			$args['callback'] = $this->options->get('cgb_comment_callback');
		}
		else {
			$args['callback'] = array(&$this, 'show_comment_html');
		}
		//correct order of top level comments
		if('default' !== $this->options->get('cgb_clist_order')) {
			$args['reverse_top_level'] = false;
		}
		//correct order of child comments
		if('desc' === $this->options->get('cgb_clist_child_order')) {
			$args['reverse_children'] = true;
		}
		elseif('asc' === $this->options->get('cgb_clist_child_order')) {
			$args['reverse_children'] = false;
		}
		//change child order if top level order is desc due to array_reverse
		if('desc' === $this->options->get('cgb_clist_order')) {
			$args['reverse_children'] = isset($args['reverse_children']) ? !$args['reverse_children'] : true;
		}
		// Print comments
		wp_list_comments($args);
	}

	public function show_comment_html($comment, $args, $depth) {
		// Define variables which can be used in show_comments_html text option
		$GLOBALS['comment'] = $comment;
		$l10n_domain = $this->options->get('cgb_l10n_domain');
		$is_comment_from_other_page = (get_the_ID() != $comment->comment_post_ID);
		$other_page_title = $is_comment_from_other_page ? get_the_title($comment->comment_post_ID) : '';
		$other_page_link = $is_comment_from_other_page ? '<a href="'.get_page_link($comment->comment_post_ID).'">'.$other_page_title.'</a>' : '';
		switch ($comment->comment_type) {
			case 'pingback' :
			case 'trackback' :
				echo '
					<li class="post pingback">
					<p>'.__('Pingback:', $l10n_domain).get_comment_author_link().get_edit_comment_link(__('Edit', $l10n_domain), '<span class="edit-link">', '</span>').'</p>';
				break;
			default :
				echo '
					<li '.comment_class('', null, null, false).' id="li-comment-'.get_comment_ID().'">
						<article id="comment-'.get_comment_ID().'" class="comment">';
				eval('?>'.$this->options->get('cgb_comment_html'));
				echo '
						</article><!-- #comment-## -->';
				break;
		}
	}

	public function show_nav_html($location) {
		if(get_comment_pages_count() > 1 && get_option('page_comments')) {
			$nav_id = 'comment-nav-'.('above_comments' === $location ? 'above' : 'below');
			echo '<nav id="'.$nav_id.'">';

			// Numbered Pagination
			if('' !== $this->options->get('cgb_clist_num_pagination')) {
				echo '<div class="pagination" style="text-align:center;">';
				paginate_comments_links(array('prev_text' => $this->nav_label_prev, 'next_text' => $this->nav_label_next, 'mid_size' => 3));
				echo '</div>';
			}
			// Only previous and next links
			else {
				echo '<h1 class="assistive-text">'.__('Comment navigation', $this->l10n_domain).'</h1>
					<div class="nav-previous">'.$this->get_comment_nav_label(true).'</div>
					<div class="nav-next">'.$this->get_comment_nav_label().'</div>';
			}

			echo '</nav>';
		}

	}

	public function show_comment_form_html($location) {
		// print custom form styles
		if(!$this->num_forms) {
			$styles = $this->options->get('cgb_form_styles');
			// add styles for foldable forms
			if('static' == $this->options->get('cgb_form_expand_type')) {
				$styles .= '
						div.form-wrapper { display:none; }
						a.form-link:target { display:none; }
						a.form-link:target + div.form-wrapper { display:block; }';
			}
			elseif('animated' == $this->options->get('cgb_form_expand_type')) {
				$styles .= '
						div.form-wrapper { position:absolute; transform:scaleY(0); transform-origin:top; transition:transform 0.3s; }
						a.form-link:target { display:none; }
						a.form-link:target + div.form-wrapper { position:relative; transform:scaleY(1); }';
			}
			// print styles
			if('' != $styles) {
				echo '
					<style>
						'.$styles.'
					</style>';
			}
		}
		$this->num_forms++;
		// show form
		if(('above_comments' === $location && '' !== $this->options->get('cgb_form_above_comments')) ||
		   ('below_comments' === $location && '' !== $this->options->get('cgb_form_below_comments')) ||
		   ('in_page' === $location)) { // the check if the in_page form shall be diesplay must be done before this function is called
			// add required parts for foldable comment form
			if('false' != $this->options->get('cgb_form_expand_type')) {
				echo '
					<a class="form-link" id="show-form-'.$this->num_forms.'" href="#show-form-'.$this->num_forms.'">'.$this->options->get('cgb_form_expand_link_text').'</a>
					<div class="form-wrapper">';
			}
			// print form
			comment_form($this->get_guestbook_comment_form_args());
			if('false' != $this->options->get('cgb_form_expand_type')) {
				echo '</div>';
			}
		}
	}

	public function get_page_of_comment($comment_id, $comment_author=null) {
		global $wpdb;
		if(!$comment = get_comment($comment_id)) {
			return;
		}
		// Set initial comment author (required for threaded comments)
		if(null === $comment_author) {
			$comment_author = $comment->comment_author;
		}
		// Set max. depth option
		if(get_option('thread_comments')) {
			$max_depth = get_option('thread_comments_depth');
		}
		else {
			$max_depth = -1;
		}
		// Find this comment's top level parent if threading is enabled
		if($max_depth > 1 && 0 != $comment->comment_parent) {
			return $this->get_page_of_comment($comment->comment_parent, $comment_author);
		}
		// Set per_page option
		$per_page = get_option('comments_per_page');
		if($per_page < 1) {
			return 1;
		}
		// Set sort_direction option
		$sort_direction = $this->options->get('cgb_clist_order');
		// Set show_all_comments option
		$show_all_comments = ('' !== $this->options->get('cgb_adjust_output') && '' !== $this->options->get('cgb_clist_show_all'));
		// Prepare sql string
		$time_compare_operator = ('desc' === $sort_direction) ? '>' : '<';
		$sql = 'SELECT COUNT(comment_ID) FROM '.$wpdb->comments.
					' WHERE comment_parent = 0 AND (comment_approved = "1" OR (comment_approved = "0" AND comment_author = "%s"))'.
					' AND comment_date_gmt '.$time_compare_operator.' "%s"';
		// Count comments older/newer than the actual one
		if($show_all_comments) {
			$result = $wpdb->get_var($wpdb->prepare(
					$sql,
					$comment_author, $comment->comment_date_gmt));
		}
		else {
			$result = $wpdb->get_var($wpdb->prepare(
					$sql.' AND comment_post_ID = %d',
					$comment_author, $comment->comment_date_gmt, $comment->comment_post_ID));
		}
		// Divide result by comments per page to get this comment's page number
		return ceil(($result+1)/$per_page);
	}

	public function get_comments($post_id=null) {
		// TODO: Use API instead of SELECTs. (see same todo in wp-includes/comment-template.php line 881 (tag 3.6)
		global $wpdb;
		$commenter = wp_get_current_commenter();
		$comment_author = $commenter['comment_author'];
		$comment_author_email = $commenter['comment_author_email'];
		if(null === $post_id) {
			// comment from all pages/posts
			if(get_current_user_id()) {
				$comments = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->comments.' WHERE comment_approved = "1" OR (user_id = %d AND comment_approved = "0") ORDER BY comment_date_gmt', get_current_user_id()));
			}
			else if(empty($comment_author)) {
				$comments = get_comments(array('status' => 'approve', 'order' => 'ASC'));
			}
			else {
				$comments = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->comments.' WHERE comment_approved = "1" OR (comment_author = %s AND comment_author_email = %s AND comment_approved = "0") ORDER BY comment_date_gmt', wp_specialchars_decode($comment_author,ENT_QUOTES), $comment_author_email));
			}
		}
		else {
			// only comments of given page/post
			if(get_current_user_id()) {
				$comments = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->comments.' WHERE comment_post_ID = %d AND (comment_approved = "1" OR (user_id = %d AND comment_approved = "0")) ORDER BY comment_date_gmt', $post_id, get_current_user_id()));
			}
			else if(empty($comment_author)) {
				$comments = get_comments(array('post_id' => $post_id, 'status' => 'approve', 'order' => 'ASC'));
			}
			else {
				$comments = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->comments.' WHERE comment_post_ID = %d AND (comment_approved = "1" OR (comment_author = %s AND comment_author_email = %s AND comment_approved = "0")) ORDER BY comment_date_gmt', $post_id, wp_specialchars_decode($comment_author,ENT_QUOTES), $comment_author_email));
			}
		}
		return $comments;
	}

	private function get_comment_nav_label($previous=false) {
		ob_start();
		if($previous) {
			previous_comments_link($this->nav_label_prev);
		}
		else {
			next_comments_link($this->nav_label_next);
		}
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	public function get_guestbook_comment_form_args() {
		$args = array();
		// form args
		if('' != $this->options->get('cgb_form_args')) {
			eval('$args_array = '.$this->options->get('cgb_form_args').';');
			if(is_array($args_array)) {
				$args += $args_array;
			}
		}
		// remove mail field
		if('' != $this->options->get('cgb_form_remove_mail')) {
			add_filter('comment_form_field_email', array(&$this, 'form_field_remove_filter'), 20);
		}
		// remove website url field
		if('' != $this->options->get('cgb_form_remove_website')) {
			add_filter('comment_form_field_url', array(&$this, 'form_field_remove_filter'), 20);
		}
		// change comment field label
		if('default' != $this->options->get('cgb_form_comment_label')) {
			add_filter('comment_form_field_comment', array(&$this, 'comment_field_label_filter'), 20);
		}
		// title_reply
		if('default' != $this->options->get('cgb_form_title_reply')) {
			$args['title_reply'] = $this->options->get('cgb_form_title_reply');
		}
		// title_reply_to
		if('default' != $this->options->get('cgb_form_title_reply_to')) {
			$args['title_reply_to'] = $this->options->get('cgb_form_title_reply_to');
		}
		// comment_notes_before
		if('default' != $this->options->get('cgb_form_notes_before')) {
			$args['comment_notes_before'] = $this->options->get('cgb_form_notes_before');
		}
		// comment_notes_after
		if('default' != $this->options->get('cgb_form_notes_after')) {
			$args['comment_notes_after'] = $this->options->get('cgb_form_notes_after');
		}
		// label_submit
		$option = $this->options->get('cgb_form_label_submit');
		if('default' != $option && '' != $option) {
			$args['label_submit'] = $option;
		}
		// cancel_reply_link
		$option = $this->options->get('cgb_form_cancel_reply');
		if('default' != $option && '' != $option) {
			$args['cancel_reply_link'] = $option;
		}

		// must_login message
		$option = $this->options->get('cgb_form_must_login_message');
		if('default' != $option && '' != $option) {
			$args['must_log_in'] = sprintf($option, wp_login_url(apply_filters('the_permalink', get_permalink())));
		}
		return $args;
	}

	public function form_field_remove_filter() {
		return '';
	}

	public function comment_field_label_filter($comment_html) {
		return preg_replace('/(<label.*>)(.*)(<\/label>)/i', '${1}'.$this->options->get('cgb_form_comment_label').'${3}', $comment_html, 1);
	}
}
?>
