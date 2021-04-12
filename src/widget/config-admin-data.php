<?php
/**
 * Additional data for the widget arguments required for the widget admin page.
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook\Widget;

use const WordPress\Plugins\mibuthu\CommentGuestbook\PLUGIN_PATH;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/option.php';


/**
 * CommentGuestbook Widget args config admin data class
 *
 * This class provides all additional data for the arguments which is only required in the admin page.
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
class ConfigAdminData {

	/**
	 * Additional data for the arguments
	 *
	 * @var array<string,array<string,string|int|null>>
	 */
	private $args_data;


	/**
	 * Constructor: Initialize the data
	 */
	public function __construct() {
		$this->args_data = [
			'title'                => [
				'type'          => 'text',
				'caption'       => __( 'Title', 'comment-guestbook' ) . ':',
				'caption_after' => null,
				'tooltip'       => __( 'This option defines the displayed title for the widget.', 'comment-guestbook' ),
				'form_style'    => null,
				'form_width'    => null,
			],

			'num_comments'         => [
				'type'          => 'text',
				'caption'       => __( 'Number of comments', 'comment-guestbook' ) . ':',
				'caption_after' => null,
				'tooltip'       => __( 'The number of comments to display', 'comment-guestbook' ),
				'form_style'    => null,
				'form_width'    => 30,
			],

			'link_to_comment'      => [
				'type'          => 'checkbox',
				'caption'       => __( 'Add a link to each comment', 'comment-guestbook' ),
				'caption_after' => null,
				'tooltip'       => __( 'With this option a link to every displayed comment can be added.', 'comment-guestbook' ),
				'form_style'    => null,
				'form_width'    => null,
			],

			'show_date'            => [
				'type'          => 'checkbox',
				'caption'       => __( 'Show comment date', 'comment-guestbook' ),
				'caption_after' => null,
				'tooltip'       => __( 'This option defines if the comment date will be displayed.', 'comment-guestbook' ),
				'form_style'    => 'margin:0 0 0.2em 0',
				'form_width'    => null,
			],

			'date_format'          => [
				'type'          => 'text',
				'caption'       => __( 'Date format', 'comment-guestbook' ) . ':',
				'caption_after' => null,
				'tooltip'       =>
					__( 'This option defines the date format of the displayed comment.', 'comment-guestbook' ) .
					__( 'You can use all date formats available in PHP. Search for php date format to get an overview of the available options.', 'comment-guestbook' ),
				'form_style'    => 'margin:0 0 0.6em 0.9em',
				'form_width'    => 100,
			],

			'show_author'          => [
				'type'          => 'checkbox',
				'caption'       => __( 'Show comment author', 'comment-guestbook' ),
				'caption_after' => null,
				'tooltip'       => __( 'This option defines if the comment author will be displayed.', 'comment-guestbook' ),
				'form_style'    => 'margin:0 0 0.2em 0',
				'form_width'    => null,
			],

			'author_length'        => [
				'type'          => 'text',
				'caption'       => __( 'Truncate author to', 'comment-guestbook' ),
				'caption_after' => __( 'characters', 'comment-guestbook' ),
				'tooltip'       =>
					__( 'If the comment author is displayed this option limits the number of displayed characters.', 'comment-guestbook' ) .
					sprintf(
						__( 'Set this value to %1$s to view the full author or set it to %2$s to automatically truncate the text via css.', 'comment-guestbook' ),
						'[0]',
						'[auto]'
					),
				'form_style'    => 'margin:0 0 0.6em 0.9em',
				'form_width'    => 42,
			],

			'show_page_title'      => [
				'type'          => 'checkbox',
				'caption'       => __( 'Show the title of the comment page', 'comment-guestbook' ),
				'caption_after' => null,
				'tooltip'       => __( 'This options specifies if the page title of the comment page will be displayed.', 'comment-guestbook' ),
				'form_style'    => 'margin:0 0 0.2em 0',
				'form_width'    => null,
			],

			'page_title_length'    => [
				'type'          => 'text',
				'caption'       => __( 'Truncate title to', 'comment-guestbook' ),
				'caption_after' => __( 'characters', 'comment-guestbook' ),
				'tooltip'       =>
					__( 'If the comment page title is displayed this option limits the number of displayed characters.', 'comment-guestbook' ) .
					sprintf(
						__( 'Set this value to %1$s to view the full title or set it to %2$s to automatically truncate the text via css.', 'comment-guestbook' ),
						'[0]',
						'[auto]'
					),
				'form_style'    => 'margin:0 0 0.6em 0.9em',
				'form_width'    => 42,
			],

			'show_comment_text'    => [
				'type'          => 'checkbox',
				'caption'       => __( 'Show comment text', 'comment-guestbook' ),
				'caption_after' => null,
				'tooltip'       => __( 'The options specifies if the comment text will be displayed in the widget.', 'comment-guestbook' ),
				'form_style'    => 'margin:0 0 0.2em 0',
				'form_width'    => null,
			],

			'comment_text_length'  => [
				'type'          => 'text',
				'caption'       => __( 'Truncate text to', 'comment-guestbook' ),
				'caption_after' => __( 'characters', 'comment-guestbook' ),
				'tooltip'       =>
					__( 'If the comment text is displayed this option limits the number of displayed characters.', 'comment-guestbook' ) .
					sprintf(
						__( 'Set this value to %1$s to view the full text or set it to %2$s to automatically truncate the text via css.', 'comment-guestbook' ),
						'[0]',
						'[auto]'
					),
				'form_style'    => 'margin:0 0 0.6em 0.9em',
				'form_width'    => 42,
			],

			'url_to_page'          => [
				'type'          => 'text',
				'caption'       => __( 'URL to the linked guestbook page', 'comment-guestbook' ) . ':',
				'caption_after' => null,
				'tooltip'       => __( 'This options specifies the url to the guestbook page. This url is must be set if one of the options below is required.', 'comment-guestbook' ),
				'form_style'    => 'margin:1em 0 0.6em 0',
				'form_width'    => null,
			],

			'gb_comments_only'     => [
				'type'          => 'checkbox',
				'caption'       => __( 'Show guestbook page comments only', 'comment-guestbook' ),
				'caption_after' => null,
				'tooltip'       =>
					__( 'Only show comments from the guestbook page specified above.', 'comment-guestbook' ) . ' ' .
					__( 'The url to the guestbook page is required if you want to enable this option.', 'comment-guestbook' ),
				'form_style'    => 'margin:0 0 0.6em 0.9em',
				'form_width'    => null,
			],

			'hide_gb_page_title'   => [
				'type'          => 'checkbox',
				'caption'       => __( 'Hide guestbook page title', 'comment-guestbook' ),
				'caption_after' => null,
				'tooltip'       =>
					sprintf(
						__( 'With this option you can hide the page title for guestbook comments if you have enabled the option %1$s.', 'comment-guestbook' ),
						'[' . __( 'Show the title of the comment page', 'comment-guestbook' ) . ']'
					) . ' ' .
					__( 'The url to the guestbook page is required if you want to enable this option.', 'comment-guestbook' ),
				'form_style'    => 'margin:0 0 0.6em 0.9em',
				'form_width'    => null,
			],

			'link_to_page'         => [
				'type'          => 'checkbox',
				'caption'       => __( 'Add a link to the guestbook page', 'comment-guestbook' ),
				'caption_after' => null,
				'tooltip'       =>
					__( 'The option adds a general link to the guestbook page below the comment list.', 'comment-guestbook' ) . ' ' .
					__( 'The url to the guestbook page is required if you want to enable this option.', 'comment-guestbook' ),
				'form_style'    => 'margin:0 0 0.2em 0.9em',
				'form_width'    => null,
			],

			'link_to_page_caption' => [
				'type'          => 'text',
				'caption'       => __( 'Caption for the link', 'comment-guestbook' ) . ':',
				'caption_after' => null,
				'tooltip'       => __( 'Set the caption for the link to the guestbook page.', 'comment-guestbook' ),
				'form_style'    => 'margin:0 0 0.8em 1.8em',
				'form_width'    => null,
			],
		];
	}


	/**
	 * Get the data for a given argument
	 *
	 * @param string $arg_name The name of the attribute.
	 * @return array<string,string|int|null>
	 */
	public function __get( $arg_name ) {
		if ( isset( $this->args_data[ $arg_name ] ) ) {
			return $this->args_data[ $arg_name ];
		}
	}

}
