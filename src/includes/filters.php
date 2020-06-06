<?php
/**
 * CommentGuestbook Frontend Filter Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!
if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once CGB_PATH . 'includes/options.php';

/**
 * CommentGuestbook Frontend Filter Class
 *
 * This class handles all filters to overwrite the defaults according to the CommentGuestbook settings.
 */
class CGB_Filters {

	/**
	 * Options class instance reference
	 *
	 * @var CGB_Options
	 */
	private $options;

	/**
	 * Holds the variable where the filter setting was called from
	 *
	 * @var string
	 */
	private $called_from;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @param string $called_from  If the function was called from 'shortcode' or 'after_new_comment'.
	 * @return void
	 */
	public function __construct( $called_from = 'shortcode' ) {
		$this->options     = &CGB_Options::get_instance();
		$this->called_from = $called_from;
		$this->prepare_filters();
	}


	/**
	 * Prepare the required filters
	 *
	 * @return void
	 */
	public function prepare_filters() {
		add_filter( 'comments_open', array( &$this, 'filter_comments_open' ), 50, 2 );
		add_filter( 'option_comment_registration', array( &$this, 'filter_ignore_comment_registration' ) );
		add_filter( 'option_comment_moderation', array( &$this, 'filter_ignore_comment_moderation' ) );
	}


	/**
	 * Filter to override comments_open status.
	 *
	 * @param bool $open    Whether the current post is open for comments.
	 * @param int  $post_id The post ID.
	 * @return bool
	 */
	public function filter_comments_open( $open, $post_id ) {
		if ( ! $open && (bool) $this->options->get( 'cgb_ignore_comments_open' ) ) {
			return true;
		}
		return $open;
	}


	/**
	 * Filter to override registration requirements for comments
	 *
	 * @param bool $option_value The actual value of the option "comment_registration".
	 * @return bool
	 */
	public function filter_ignore_comment_registration( $option_value ) {
		if ( $this->options->get( 'cgb_ignore_comment_registration' ) ) {
			return false;
		}
		return $option_value;
	}


	/**
	 * Filter to override moderation requirements for comments on guestbook page
	 *
	 * @param bool $option_value The actual value of the option "comment_moderation".
	 * @return bool
	 */
	public function filter_ignore_comment_moderation( $option_value ) {
		if ( $this->options->get( 'cgb_ignore_comment_moderation' ) ) {
			return false;
		}
		return $option_value;
	}

}
