<?php
/**
 * CommentGuestbook Frontend Filter Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/config.php';

/**
 * CommentGuestbook Frontend Filter Class
 *
 * This class handles all filters to overwrite the defaults according to the CommentGuestbook settings.
 */
class Filters {

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
	}


	/**
	 * Prepare the required filters
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'comments_open', [ &$this, 'filter_comments_open' ], 50, 2 );
		add_filter( 'option_comment_registration', [ &$this, 'filter_ignore_comment_registration' ] );
		add_filter( 'option_comment_moderation', [ &$this, 'filter_ignore_comment_moderation' ] );
		add_filter( 'option_require_name_email', [ &$this, 'filter_require_name_email' ] );
	}


	/**
	 * Filter to override comments_open status.
	 *
	 * @param bool $open    Whether the current post is open for comments.
	 * @param int  $post_id The post ID (not used).
	 * @return bool
	 *
	 * @suppress PhanUnusedPublicNoOverrideMethodParameter
	 */
	public function filter_comments_open( $open, $post_id ) {
		if ( ! $open && $this->config->ignore_comments_open->to_bool() ) {
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
		if ( $this->config->ignore_comment_registration->to_bool() ) {
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
		if ( $this->config->ignore_comment_moderation->to_bool() ) {
			return false;
		}
		return $option_value;
	}


	/**
	 * Filter to override email requirement for a new comment if the email field is removed.
	 *
	 * @param string $option_value The actual value of the option "require_name_email".
	 * @return string
	 */
	public function filter_require_name_email( $option_value ) {
		// Check if the given wp-option is enabled.
		// Use the given default value.
		if ( empty( $option_value ) ) {
			return $option_value;
		}
		// Check if the "require name, email" option is disabled for comment-guestbook comments.
		if ( $this->config->form_require_no_name_mail->to_bool() ) {
			return '';
		}
		// Check if the plugin options require an override.
		if ( $this->config->form_remove_mail->to_bool() || $this->config->page_remove_mail->to_bool() ) {
			$user = wp_get_current_user();
			// Check if the user is logged in and if a valid author name is given.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$author = isset( $_POST['author'] ) ? sanitize_title( stripslashes_deep( $_POST['author'] ) ) : '';
			if ( ! $user->exists() && ! empty( $author ) ) {
				// Override value.
				return '';
			}
		}
		return $option_value;
	}

}
