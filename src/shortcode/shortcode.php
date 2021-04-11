<?php
/**
 * CommentGuestbook Shortcode Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook\Shortcode;

use const WordPress\Plugins\mibuthu\CommentGuestbook\PLUGIN_PATH;
use WordPress\Plugins\mibuthu\CommentGuestbook\Config;
use WordPress\Plugins\mibuthu\CommentGuestbook\Filters;
use WordPress\Plugins\mibuthu\CommentGuestbook\Comments_Functions;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/config.php';

/**
 * CommentGuestbook Shortcode Class
 *
 * This class handles the shortcode [comment-guestbook].
 */
class Shortcode {

	/**
	 * Config class instance reference
	 *
	 * @var Config
	 */
	private $config;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @param Config $config_instance The Config instance as a reference.
	 * @return void
	 */
	public function __construct( &$config_instance ) {
		$this->config = $config_instance;
		require_once PLUGIN_PATH . '/includes/filters.php';
		$filters = new Filters( $this->config );
		$filters->init();
	}


	/**
	 * Main function to show the rendered HTML output
	 *
	 * @param array<string,string> $atts Shortcode attributes (not used).
	 * @param string               $content Shortcode content (not used).
	 * @return string HTML to render.
	 *
	 * @suppress PhanUnusedPublicNoOverrideMethodParameter
	 */
	public function show_html( $atts, $content ) {
		$this->init_sc();
		if ( $this->config->clist_in_page_content->to_bool() && $this->config->adjust_output->to_bool() ) {
			/**
			 * Show comment list in page content
			 */
			ob_start();
				$GLOBALS['cgb_comment_template_in_page'] = true;
				comments_template();
				$out = strval( ob_get_contents() );
			ob_end_clean();
			unset( $GLOBALS['cgb_comment_template_in_page'] );
			return $out;
		} elseif ( $this->config->form_in_page->to_bool() && ( ! $this->config->form_above_comments->to_bool() || ! $this->config->adjust_output->to_bool() ) ) {
			/**
			 * Show comment form in page content (Only show one form above the comment list. The form_in_page will not be displayed if form_above_comments and adjust_output is enabled.)
			 * (The form will also be hidden if the comment list is displayed in page content.)
			 */
			require_once PLUGIN_PATH . 'includes/comments-functions.php';
			$comment_functions = new Comments_Functions( $this->config );
			return $comment_functions->show_comment_form_html( 'in_page' );
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
		if ( 'enabled' === $this->config->clist_threaded->to_str() || 'disabled' === $this->config->clist_threaded->to_str() ) {
			add_filter( 'option_thread_comments', [ &$this, 'filter_threaded_comments' ] );
		}
		// Filter to override name and email requirement on guestbook page.
		if ( $this->config->form_require_no_name_mail->to_bool() ) {
			add_filter( 'option_require_name_email', '__return_false' );
		}
		// Filter to show the adjusted comment style.
		if ( $this->config->adjust_output->to_bool() ) {
			add_filter( 'comments_template', [ &$this, 'filter_comments_template' ] );
			if ( 'desc' === $this->config->clist_order->to_str() || $this->config->clist_show_all->to_bool() ) {
				add_filter( 'comments_template_query_args', [ &$this, 'filter_comments_template_query_args' ] );
			}
			if ( 'default' !== $this->config->clist_default_page->to_str() ) {
				add_filter( 'option_default_comments_page', [ &$this, 'filter_comments_default_page' ] );
			}
			if ( 'default' !== $this->config->clist_pagination->to_str() ) {
				add_filter( 'option_page_comments', [ &$this, 'filter_comments_pagination' ] );
			}
		}
		// Filter to add comment id fields to identify required filters.
		add_filter( 'comment_id_fields', [ &$this, 'filter_comment_id_fields' ] );
	}


	/**
	 * Filter to adjust threaded_comments option
	 *
	 * @param string $option_value The actual value of the option "thread_comments" (not used).
	 * @return string
	 *
	 * @suppress PhanUnusedPublicNoOverrideMethodParameter
	 */
	public function filter_threaded_comments( $option_value ) {
		if ( 'enabled' === $this->config->clist_threaded->to_str() ) {
			return '1';
		}
		return '';
	}


	/**
	 * Filter to adjust the comments template
	 *
	 * @param string $file The actual file of the template (not used).
	 * @return string
	 *
	 * @suppress PhanUnusedPublicNoOverrideMethodParameter
	 */
	public function filter_comments_template( $file ) {
		// Set required global variables which are required in the template.
		require_once PLUGIN_PATH . 'includes/comments-functions.php';
		$GLOBALS['cgb_func']   = new Comments_Functions( $this->config );
		$GLOBALS['cgb_config'] = &$this->config;
		return PLUGIN_PATH . 'includes/comments-template.php';
	}


	/**
	 * Filter to adjust the comments query args
	 *
	 * @param array<string,string> $query_args The actual comments array.
	 * @return array<string,string>
	 */
	public function filter_comments_template_query_args( $query_args ) {
		// Unset post_id to include the comments of all pages/posts if clist show all option is set.
		if ( $this->config->clist_show_all->to_bool() ) {
			unset( $query_args['post_id'] );
		}
		// Reverse array if clist order desc is required.
		if ( 'desc' === $this->config->clist_order->to_str() ) {
			$query_args['order'] = 'DESC';
		}
		return $query_args;
	}


	/**
	 * Filter to adjust default_comments_page option
	 *
	 * @param string $option_value The actual value of the option "default_comments_page" (not used).
	 * @return string
	 *
	 * @suppress PhanUnusedPublicNoOverrideMethodParameter
	 */
	public function filter_comments_default_page( $option_value ) {
		if ( 'first' === $this->config->clist_default_page->to_str() ) {
			return 'oldest';
		} elseif ( 'last' === $this->config->clist_default_page->to_str() ) {
			return 'newest';
		}
	}


	/**
	 * Filter to adjust page_comments option
	 *
	 * @param string $option_value The actual value of the option "page_comments" (not used).
	 * @return string
	 *
	 * @suppress PhanUnusedPublicNoOverrideMethodParameter
	 */
	public function filter_comments_pagination( $option_value ) {
		if ( 'false' === $this->config->clist_pagination->to_str() ) {
			return '';
		} elseif ( 'true' === $this->config->clist_pagination->to_str() ) {
			return '1';
		}
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
		if ( $this->config->ignore_comments_open->to_bool() ) {
			$html .= '<input type="hidden" name="cgb_comments_status" id="cgb_comments_status" value="open" />';
		}
		return $html;
	}

}

