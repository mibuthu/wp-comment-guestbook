<?php
if(!defined('ABSPATH')) {
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
		$this->init_filters();
		$out = '';
		if(comments_open()) {
			if('' !== $this->options->get('cgb_form_in_page')) {
				ob_start();
					comment_form();
					$out .= ob_get_contents();
				ob_end_clean();
			}
		}
		else {
			$out .= '<div id="respond" style="text-align:center">Guestbook is closed</div>';
		}
		if('' !== $this->options->get('cgb_clist_in_page_content')) {
			include(CGB_PATH.'includes/comments-template.php');
		}
		return $out;
	}

	private function init_filters() {
		global $cgb;
		// Filter to overwrite comments_open status
		if('' !== $this->options->get('cgb_ignore_comments_open')) {
			add_filter('comments_open', array(&$cgb, 'filter_comments_open'));
		}
		// Filter to show the adjusted comment style
		if(1 == $this->options->get('cgb_clist_adjust')) {
			add_filter('comments_template', array(&$this, 'filter_comments_template'));
			if('desc' === $this->options->get('cgb_clist_order') || '' !== $this->options->get('cgb_clist_show_all')) {
				add_filter('comments_array', array(&$this, 'filter_comments_array'));
			}
			if('default' !== $this->options->get('cgb_clist_default_page')) {
				add_filter('option_default_comments_page', array(&$this, 'filter_comments_default_page'));
			}
		}
		// Filter to add comment id fields to identify required filters
		add_filter('comment_id_fields', array(&$this, 'filter_comment_id_fields'));
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
		// Overwrite comments default page
		if('first' === $this->options->get('cgb_clist_default_page')) {
			$page = 'oldest';
		}
		elseif('last' === $this->options->get('cgb_clist_default_page')) {
			$page = 'newest';
		}
		return $page;
	}

	public function filter_comment_id_fields($html) {
		$html .= '<input type="hidden" name="is_cgb_comment" id="is_cgb_comment" value="1" />';
		// Add fields comment form to identify a guestbook comment when overwrite of comment status is required
		if('' !== $this->options->get('cgb_ignore_comments_open')) {
			$html .= '<input type="hidden" name="cgb_comments_status" id="cgb_comments_status" value="open" />';
		}
		if('desc' === $this->options->get('cgb_clist_order') && 1 == $this->options->get('cgb_clist_adjust')) {
			$html .= '<input type="hidden" name="cgb_clist_order" id="cgb_clist_order" value="desc" />';
		}
		return $html;
	}
}
?>
