<?php
if(!defined('WPINC')) {
	exit;
}

require_once(CGB_PATH.'includes/options.php');

// This class handles the shortcode [comment-guestbook]
class SC_Comment_Guestbook {
	private static $instance;
	private $options;

	public static function &get_instance() {
		// Create class instance if required
		if(!isset(self::$instance)) {
			self::$instance = new SC_Comment_Guestbook();
		}
		// Return class instance
		return self::$instance;
	}

	private function __construct() {
		// get options instance
		$this->options = &CGB_Options::get_instance();
	}

	// main function to show the rendered HTML output
	public function show_html($atts) {
		$this->init_sc();
		// Show comment list in page content
		if('' !== $this->options->get('cgb_clist_in_page_content') && '' !== $this->options->get('cgb_adjust_output')) {
			ob_start();
				include(CGB_PATH.'includes/comments-template.php');
				$out = ob_get_contents();
			ob_end_clean();
			return $out;
		}
		// Show comment form in page content (Only show one form above the comment list. The form_in_page will not be displayed if form_above_comments and adjust_output is enabled.)
		// (The form will also be hidden if the comment list is displayed in page content.)
		elseif('' !== $this->options->get('cgb_form_in_page') && ('' === $this->options->get('cgb_form_above_comments') || '' === $this->options->get('cgb_adjust_output'))) {
			require_once(CGB_PATH.'includes/comments-functions.php');
			ob_start();
				CGB_Comments_Functions::get_instance()->show_comment_form_html('in_page');
				$out = ob_get_contents();
			ob_end_clean();
			return $out;
		}
		// Show nothing
		else {
			return '';
		}
	}

	private function init_sc() {
		global $cgb;
		// Add comment reply script in footer(required if comment status is overwritten)
		if('' !== $this->options->get('cgb_ignore_comments_open')) {
			add_action('wp_footer', array(&$this, 'enqueue_sc_scripts'));
		}

		// Filter to override comments_open status
		if('' !== $this->options->get('cgb_ignore_comments_open')) {
			add_filter('comments_open', array(&$cgb, 'filter_ignore_comments_open'), 50);
		}
		// Filter to override registration requirements for comments on guestbook page
		if(get_option('comment_registration') && $this->options->get('cgb_ignore_comment_registration')) {
			add_filter('option_comment_registration', array(&$cgb, 'filter_ignore_comment_registration'));
		}
		// Filter to override threaded comments on guestbook page
		if('enabled' == $this->options->get('cgb_threaded_gb_comments') || 'disabled' ==  $this->options->get('cgb_threaded_gb_comments')) {
			add_filter('option_thread_comments', array(&$this, 'filter_threaded_comments'));
		}
		// Filter to override name and email requirement on guestbook page
		if('' !== $this->options->get('cgb_form_require_no_name_mail')) {
			add_filter('option_require_name_email', array(&$this, 'filter_require_no_name_mail'));
		}
		// Filter to show the adjusted comment style
		if('' !== $this->options->get('cgb_adjust_output')) {
			add_filter('comments_template', array(&$this, 'filter_comments_template'));
			if('desc' === $this->options->get('cgb_clist_order') || '' !== $this->options->get('cgb_clist_show_all')) {
				add_filter('comments_array', array(&$this, 'filter_comments_array'));
			}
			if('default' !== $this->options->get('cgb_clist_default_page')) {
				add_filter('option_default_comments_page', array(&$this, 'filter_comments_default_page'));
			}
			if('default' !== $this->options->get('cgb_clist_pagination')) {
				add_filter('option_page_comments', array(&$this, 'filter_comments_pagination'));
			}
			if('default' !== $this->options->get('cgb_clist_per_page')) {
				add_filter('option_comments_per_page', array(&$this, 'filter_comments_per_page'));
			}
		}
		// Filter to add comment id fields to identify required filters
		add_filter('comment_id_fields', array(&$this, 'filter_comment_id_fields'));
	}

	public function enqueue_sc_scripts() {
		wp_enqueue_script('comment-reply', false, array(), false, true);
	}

	public function filter_threaded_comments($option_value) {
		if('enabled' == $this->options->get('cgb_threaded_gb_comments')) {
			return 1;
		}
		return 0;
	}

	public function filter_require_no_name_mail($option_value) {
		return false;
	}

	public function filter_comments_template($file) {
		// Set customized comments-template fie if a commentlist output modification is required
		return CGB_PATH.'includes/comments-template.php';
	}

	public function filter_comments_array($comments) {
		// Set correct comments list if the comments of all posts/pages should be displayed
		if('' !== $this->options->get('cgb_clist_show_all')) {
			require_once(CGB_PATH.'includes/comments-functions.php');
			$cgb_func = CGB_Comments_Functions::get_instance();
			$comments = $cgb_func->get_comments(null);
		}
		// Invert array if clist order desc is required
		if('desc' === $this->options->get('cgb_clist_order')) {
			$comments = array_reverse($comments);
		}
		return $comments;
	}

	public function filter_comments_default_page($page) {
		// Override comments default page
		if('first' === $this->options->get('cgb_clist_default_page')) {
			$page = 'oldest';
		}
		elseif('last' === $this->options->get('cgb_clist_default_page')) {
			$page = 'newest';
		}
		return $page;
	}

	public function filter_comments_pagination($value) {
		if('false' == $this->options->get('cgb_clist_pagination')) {
			$value = '';
		}
		elseif('true' == $this->options->get('cgb_clist_pagination')) {
			$value = '1';
		}
		return $value;
	}

	public function filter_comments_per_page($value) {
		if(0 != intval($this->options->get('cgb_clist_per_page'))) {
			$value = intval($this->options->get('cgb_clist_per_page'));
		}
		return $value;
	}

	public function filter_comment_id_fields($html) {
		// Add field to verify the comment was made in guestbook page
		// use the post-id as value (this allows a compare between 'comment_post_ID' and 'is_cgb_comment' values
		$html .= '<input type="hidden" name="is_cgb_comment" id="is_cgb_comment" value="'.get_the_ID().'" />';
		// Add fields comment form to identify a guestbook comment when override of comment status is required
		if('' !== $this->options->get('cgb_ignore_comments_open')) {
			$html .= '<input type="hidden" name="cgb_comments_status" id="cgb_comments_status" value="open" />';
		}
		return $html;
	}
}
?>
