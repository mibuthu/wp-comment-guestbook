<?php
/**
 * Widget config class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook\Widget;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

use const WordPress\Plugins\mibuthu\CommentGuestbook\PLUGIN_PATH;
use WordPress\Plugins\mibuthu\CommentGuestbook\Option;


require_once PLUGIN_PATH . 'includes/option.php';



/**
 * CommentGuestbook Widget arguments config class
 *
 * @property string $title The widget title
 * @property string $num_comments The number of comments
 * @property string $link_to_comment Add a link to the comment
 * @property string $show_data Show the comment date
 * @property string $date_format The date format
 * @property string $show_author Show the comment author
 * @property string $author_length Max display length of the comment author
 * @property string $show_page_title Show the page title
 * @property string $page_title_length The page title length
 * @property string $show_comment_text Show the comment text
 * @property string $comment_text_length The comment text length
 * @property string $url_to_page The URL of the guestbook page
 * @property string $gb_comments_only Show guestbook page comments only
 * @property string $hide_gb_page_title Hide the guestbook page title
 * @property string $link_to_page Add a link with the url to the guestbook page
 * @property string $link_to_page_caption The caption of the guestbook page link
 */
class Config {

	/**
	 * Widget Items
	 *
	 * @var array<string,Option>
	 */
	private $args;


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct() {
		// Define all available args.
		$this->args = [
			'title'                => new Option( __( 'Recent guestbook entries', 'comment-guestbook' ) ),
			'num_comments'         => new Option( '5' ),
			'link_to_comment'      => new Option( Option::FALSE, OPTION::BOOLEAN ),
			'show_date'            => new Option( Option::FALSE, OPTION::BOOLEAN ),
			'date_format'          => new Option( get_option( 'date_format' ) ),
			'show_author'          => new Option( Option::TRUE, OPTION::BOOLEAN ),
			'author_length'        => new Option( '18' ),
			'show_page_title'      => new Option( Option::FALSE, OPTION::BOOLEAN ),
			'page_title_length'    => new Option( '18' ),
			'show_comment_text'    => new Option( Option::TRUE, OPTION::BOOLEAN ),
			'comment_text_length'  => new Option( '25' ),
			'url_to_page'          => new Option( '' ),
			'gb_comments_only'     => new Option( Option::FALSE, OPTION::BOOLEAN ),
			'hide_gb_page_title'   => new Option( Option::FALSE, OPTION::BOOLEAN ),
			'link_to_page'         => new Option( Option::FALSE, OPTION::BOOLEAN ),
			'link_to_page_caption' => new Option( __( 'goto guestbook page', 'comment-guestbook' ) ),
		];
	}


	/**
	 * Get the value of the given arguments
	 *
	 * @param string $name Argument name.
	 * @return string|bool Argument value.
	 */
	public function __get( $name ) {
		if ( isset( $this->args[ $name ] ) ) {
			return $this->args[ $name ]->to_bool();
		}
		// Trigger error is allowed in this case.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		trigger_error( 'Widget argument "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
	}


	/**
	 * Get all specified arguments
	 *
	 * @return array<string,Option>
	 */
	public function get_all() {
		return $this->args;
	}


	/**
	 * Load helptexts of widget args
	 *
	 * @return void
	 */
	public function load_args_admin_data() {
		require_once PLUGIN_PATH . 'widget/config-admin-data.php';
		$args_admin_data = new ConfigAdminData();
		foreach ( array_keys( $this->args ) as $arg_name ) {
			$this->args[ $arg_name ]->modify( $args_admin_data->$arg_name );
		}
	}

}
