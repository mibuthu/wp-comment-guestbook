<?php
/**
 * Widget config class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook\Widget;

use const WordPress\Plugins\mibuthu\CommentGuestbook\PLUGIN_PATH;
use WordPress\Plugins\mibuthu\CommentGuestbook\Option;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/option.php';


/**
 * CommentGuestbook Widget arguments config class
 *
 * @property-read Option $title The widget title
 * @property-read Option $num_comments The number of comments
 * @property-read Option $link_to_comment Add a link to the comment
 * @property-read Option $show_data Show the comment date
 * @property-read Option $date_format The date format
 * @property-read Option $show_author Show the comment author
 * @property-read Option $author_length Max display length of the comment author
 * @property-read Option $show_page_title Show the page title
 * @property-read Option $page_title_length The page title length
 * @property-read Option $show_comment_text Show the comment text
 * @property-read Option $comment_text_length The comment text length
 * @property-read Option $url_to_page The URL of the guestbook page
 * @property-read Option $gb_comments_only Show guestbook page comments only
 * @property-read Option $hide_gb_page_title Hide the guestbook page title
 * @property-read Option $link_to_page Add a link with the url to the guestbook page
 * @property-read Option $link_to_page_caption The caption of the guestbook page link
 */
class Config {

	/**
	 * Widget Config Options
	 *
	 * @var array<string,Option>
	 */
	private $options;

	/**
	 * Widget Config Admin Data
	 *
	 * @var ConfigAdminData
	 */
	public $admin_data = null;


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct() {
		// Define all available options
		$this->options = [
			'title'                => Option::new( __( 'Recent guestbook entries', 'comment-guestbook' ) ),
			'num_comments'         => Option::new( '5' ),
			'link_to_comment'      => Option::new_false(),
			'show_date'            => Option::new_false(),
			'date_format'          => Option::new( get_option( 'date_format' ) ),
			'show_author'          => Option::new_true(),
			'author_length'        => Option::new( '18' ),
			'show_page_title'      => Option::new_false(),
			'page_title_length'    => Option::new( '18' ),
			'show_comment_text'    => Option::new_true(),
			'comment_text_length'  => Option::new( '25' ),
			'url_to_page'          => Option::new( '' ),
			'gb_comments_only'     => Option::new_false(),
			'hide_gb_page_title'   => Option::new_false(),
			'link_to_page'         => Option::new_false(),
			'link_to_page_caption' => Option::new( __( 'goto guestbook page', 'comment-guestbook' ) ),
		];
	}


	/**
	 * Get the widget option
	 *
	 * @param string $name Option name
	 * @return Option
	 */
	public function __get( $name ) {
		if ( isset( $this->options[ $name ] ) ) {
			return $this->options[ $name ];
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Widget argument "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
		return Option::new( '' );
	}


	/**
	 * Get all widget options
	 *
	 * @return array<string,Option>
	 */
	public function get_all() {
		return $this->options;
	}


	/**
	 * Set the values of a provided $instance array
	 *
	 * @param array<string,string> $instance The array including the values to set.
	 * @return void Nothing to return.
	 */
	public function set_from_instance( $instance ) {
		foreach ( $instance as $name => $value ) {
			if ( isset( $this->options[ $name ] ) ) {
				$this->options[ $name ]->value = $value;
			}
		}
	}


	/**
	 * Load helptexts of widget args
	 *
	 * @return void
	 */
	public function load_args_admin_data() {
		require_once PLUGIN_PATH . 'widget/config-admin-data.php';
		$this->admin_data = new ConfigAdminData();
	}

}
