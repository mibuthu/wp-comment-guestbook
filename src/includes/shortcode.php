<?php
/**
 * CommentGuestbook Shortcode Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!
if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once CGB_PATH . 'includes/options.php';

/**
 * CommentGuestbook Shortcode Class
 *
 * This class handles the shortcode [comment-guestbook].
 */
class CGB_Shortcode {

	/**
	 * Class singleton instance reference
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Options class instance reference
	 *
	 * @var CGB_Options
	 */
	private $options;


	/**
	 * Singleton provider and setup
	 *
	 * @return self
	 */
	public static function &get_instance() {
		// There seems to be an issue with the self variable in phan.
		// @phan-suppress-next-line PhanPluginUndeclaredVariableIsset.
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Class constructor which initializes required variables
	 *
	 * @return void
	 */
	private function __construct() {
		$this->options = &CGB_Options::get_instance();
		require_once CGB_PATH . '/includes/filters.php';
		new CGB_Filters();
	}


	/**
	 * Main function to show the rendered HTML output
	 *
	 * @param array<string,string> $atts Shortcode attributes (not used).
	 * @param string               $content Shortcode content (not used).
	 * @return string HTML to render.
	 */
	public function show_html( $atts, $content ) {
		$this->init_sc();
		if ( '' !== $this->options->get( 'cgb_clist_in_page_content' ) && '' !== $this->options->get( 'cgb_adjust_output' ) ) {
			/**
			 * Show comment list in page content
			 */
			ob_start();
				include CGB_PATH . 'includes/comments-template.php';
				$out = strval( ob_get_contents() );
			ob_end_clean();
			return $out;
		} elseif ( '' !== $this->options->get( 'cgb_form_in_page' ) && ( '' === $this->options->get( 'cgb_form_above_comments' ) || '' === $this->options->get( 'cgb_adjust_output' ) ) ) {
			/**
			 * Show comment form in page content (Only show one form above the comment list. The form_in_page will not be displayed if form_above_comments and adjust_output is enabled.)
			 * (The form will also be hidden if the comment list is displayed in page content.)
			 */
			require_once CGB_PATH . 'includes/comments-functions.php';
			ob_start();
				CGB_Comments_Functions::get_instance()->show_comment_form_html( 'in_page' );
				$out = strval( ob_get_contents() );
			ob_end_clean();
			return $out;
		} else {
			/**
			 * Show nothing
			 */
			return '';
		}
	}


	/**
	 * Initialize the shortcode
	 *
	 * Set all required actions and filters.
	 *
	 * @return void
	 */
	private function init_sc() {
		// Filter to override threaded comments on guestbook page.
		if ( 'enabled' === $this->options->get( 'cgb_clist_threaded' ) || 'disabled' === $this->options->get( 'cgb_clist_threaded' ) ) {
			add_filter( 'option_thread_comments', array( &$this, 'filter_threaded_comments' ) );
		}
		// Filter to override name and email requirement on guestbook page.
		if ( '' !== $this->options->get( 'cgb_form_require_no_name_mail' ) ) {
			add_filter( 'option_require_name_email', '__return_false' );
		}
		// Filter to show the adjusted comment style.
		if ( '' !== $this->options->get( 'cgb_adjust_output' ) ) {
			add_filter( 'comments_template', array( &$this, 'filter_comments_template' ) );
			if ( 'desc' === $this->options->get( 'cgb_clist_order' ) || '' !== $this->options->get( 'cgb_clist_show_all' ) ) {
				add_filter( 'comments_array', array( &$this, 'filter_comments_array' ) );
			}
			if ( 'default' !== $this->options->get( 'cgb_clist_default_page' ) ) {
				add_filter( 'option_default_comments_page', array( &$this, 'filter_comments_default_page' ) );
			}
			if ( 'default' !== $this->options->get( 'cgb_clist_pagination' ) ) {
				add_filter( 'option_page_comments', array( &$this, 'filter_comments_pagination' ) );
			}
			if ( 'default' !== $this->options->get( 'cgb_clist_per_page' ) ) {
				add_filter( 'option_comments_per_page', array( &$this, 'filter_comments_per_page' ) );
			}
		}
		// Filter to add comment id fields to identify required filters.
		add_filter( 'comment_id_fields', array( &$this, 'filter_comment_id_fields' ) );
	}


	/**
	 * Filter to adjust threaded_comments option
	 *
	 * @param string $option_value The actual value of the option "thread_comments" (not used).
	 * @return string
	 */
	public function filter_threaded_comments( $option_value ) {
		if ( 'enabled' === $this->options->get( 'cgb_clist_threaded' ) ) {
			return '1';
		}
		return '';
	}


	/**
	 * Filter to adjust the comments template
	 *
	 * @param string $file The actual file of the template (not used).
	 * @return string
	 */
	public function filter_comments_template( $file ) {
		return CGB_PATH . 'includes/comments-template.php';
	}


	/**
	 * Filter to adjust the comments array
	 *
	 * @param WP_Comment[] $comments The actual comments array (not used).
	 * @return WP_Comment[]
	 */
	public function filter_comments_array( $comments ) {
		// Set correct comments list if the comments of all posts/pages should be displayed.
		if ( '' !== $this->options->get( 'cgb_clist_show_all' ) ) {
			require_once CGB_PATH . 'includes/comments-functions.php';
			$cgb_func = CGB_Comments_Functions::get_instance();
			$comments = $cgb_func->get_comments();
		}
		// Reverse array if clist order desc is required.
		if ( 'desc' === $this->options->get( 'cgb_clist_order' ) ) {
			$comments = array_reverse( $comments );
		}
		return $comments;
	}


	/**
	 * Filter to adjust default_comments_page option
	 *
	 * @param string $option_value The actual value of the option "default_comments_page" (not used).
	 * @return string
	 */
	public function filter_comments_default_page( $option_value ) {
		if ( 'first' === $this->options->get( 'cgb_clist_default_page' ) ) {
			return 'oldest';
		} elseif ( 'last' === $this->options->get( 'cgb_clist_default_page' ) ) {
			return 'newest';
		}
	}


	/**
	 * Filter to adjust page_comments option
	 *
	 * @param string $option_value The actual value of the option "page_comments" (not used).
	 * @return string
	 */
	public function filter_comments_pagination( $option_value ) {
		if ( 'false' === $this->options->get( 'cgb_clist_pagination' ) ) {
			return '';
		} elseif ( 'true' === $this->options->get( 'cgb_clist_pagination' ) ) {
			return '1';
		}
	}


	/**
	 * Filter to adjust comments_per_page option
	 *
	 * @param string $option_value The actual value of the option "comments_per_page".
	 * @return string
	 */
	public function filter_comments_per_page( $option_value ) {
		if ( 0 !== intval( $this->options->get( 'cgb_clist_per_page' ) ) ) {
			return $this->options->get( 'cgb_clist_per_page' );
		}
		return $option_value;
	}


	/**
	 * Filter to adjust comments_id_fields
	 *
	 * @param string $html The actual html of the comments_id_fields.
	 * @return string
	 */
	public function filter_comment_id_fields( $html ) {
		/**
		 * Add field to verify the comment was made in guestbook page.
		 * Use the post-id as value (this allows a compare between 'comment_post_ID' and 'is_cgb_comment' values.
		 */
		$html .= '<input type="hidden" name="is_cgb_comment" id="is_cgb_comment" value="' . get_the_ID() . '" />';
		// Add fields comment form to identify a guestbook comment when override of comment status is required.
		if ( '' !== $this->options->get( 'cgb_ignore_comments_open' ) ) {
			$html .= '<input type="hidden" name="cgb_comments_status" id="cgb_comments_status" value="open" />';
		}
		return $html;
	}

}

