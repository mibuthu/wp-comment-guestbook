<?php
/**
 * CommentGuestbook Shortcode Class
 *
 * @package comment-guestbook
 */

declare(strict_types=1);
if ( ! defined( 'WPINC' ) ) {
	die;
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
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Class constructor which initializes required variables
	 */
	private function __construct() {
		$this->options = &CGB_Options::get_instance();
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
				$out = ob_get_contents();
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
				$out = ob_get_contents();
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
		global $cgb_comment_guestbook;
		// Add comment reply script in footer(required if comment status is overwritten).
		if ( '' !== $this->options->get( 'cgb_ignore_comments_open' ) ) {
			add_action( 'wp_footer', array( &$this, 'enqueue_sc_scripts' ) );
		}
		// Filter to override comments_open status.
		if ( '' !== $this->options->get( 'cgb_ignore_comments_open' ) ) {
			add_filter( 'comments_open', '__return_true', 50 );
		}
		// Filter to override registration requirements for comments on guestbook page.
		if ( get_option( 'comment_registration' ) && $this->options->get( 'cgb_ignore_comment_registration' ) ) {
			add_filter( 'option_comment_registration', array( &$cgb_comment_guestbook, 'filter_ignore_comment_registration' ) );
		}
		// Filter to override threaded comments on guestbook page.
		if ( 'enabled' === $this->options->get( 'cgb_threaded_gb_comments' ) || 'disabled' === $this->options->get( 'cgb_threaded_gb_comments' ) ) {
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
	 * Embed the shortcode scripts
	 *
	 * @return void
	 */
	public function enqueue_sc_scripts() {
		wp_enqueue_script( 'comment-reply', false, array(), '1.0', true );
	}


	/**
	 * Filter to adjust threaded_comments option
	 *
	 * @param bool $option_value The actual value of the option "thread_comments".
	 * @return bool
	 */
	public function filter_threaded_comments( $option_value ) {
		if ( 'enabled' === $this->options->get( 'cgb_threaded_gb_comments' ) ) {
			return 1;
		}
		return 0;
	}


	/**
	 * Filter to adjust the comments template
	 *
	 * @param bool $file The actual file of the template (not used).
	 * @return bool
	 */
	public function filter_comments_template( $file ) {
		return CGB_PATH . 'includes/comments-template.php';
	}


	/**
	 * Filter to adjust the comments array
	 *
	 * @param bool $comments The actual comments array.
	 * @return bool
	 */
	public function filter_comments_array( $comments ) {
		// Set correct comments list if the comments of all posts/pages should be displayed.
		if ( '' !== $this->options->get( 'cgb_clist_show_all' ) ) {
			require_once CGB_PATH . 'includes/comments-functions.php';
			$cgb_func = CGB_Comments_Functions::get_instance();
			$comments = $cgb_func->get_comments( null );
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
	 * @param bool $option_value The actual value of the option "default_comments_page".
	 * @return bool
	 */
	public function filter_comments_default_page( $option_value ) {
		if ( 'first' === $this->options->get( 'cgb_clist_default_page' ) ) {
			$option_value = 'oldest';
		} elseif ( 'last' === $this->options->get( 'cgb_clist_default_page' ) ) {
			$option_value = 'newest';
		}
		return $option_value;
	}


	/**
	 * Filter to adjust page_comments option
	 *
	 * @param bool $option_value The actual value of the option "page_comments".
	 * @return bool
	 */
	public function filter_comments_pagination( $option_value ) {
		if ( 'false' === $this->options->get( 'cgb_clist_pagination' ) ) {
			$option_value = '';
		} elseif ( 'true' === $this->options->get( 'cgb_clist_pagination' ) ) {
			$option_value = '1';
		}
		return $option_value;
	}


	/**
	 * Filter to adjust comments_per_page option
	 *
	 * @param bool $option_value The actual value of the option "comments_per_page".
	 * @return bool
	 */
	public function filter_comments_per_page( $option_value ) {
		if ( 0 !== intval( $this->options->get( 'cgb_clist_per_page' ) ) ) {
			$option_value = intval( $this->options->get( 'cgb_clist_per_page' ) );
		}
		return $option_value;
	}


	/**
	 * Filter to adjust comments_id_fields
	 *
	 * @param bool $html The actual html of the comments_id_fields.
	 * @return bool
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

