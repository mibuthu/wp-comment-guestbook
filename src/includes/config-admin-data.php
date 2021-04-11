<?php
/**
 * Additional data for the options required for the settings help page.
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

/**
 * ConfigAdminData class
 *
 * This class provides all additional data for the Config class which is only required in the admin page.
 *
 * @property-read string $cgb_ignore_comments_open Guestbook comment status.
 * @property-read string $cgb_ignore_comment_registration Guestbook comment registration.
 * @property-read string $cgb_ignore_comment_moderation Guestbook comment moderation.
 * @property-read string $cgb_adjust_output Comments ajdustment.
 * @property-read string $cgb_l10n_domain Domain for translation.
 * @property-read string $cgb_form_below_comments Form below comments.
 * @property-read string $cgb_form_above_comments Form above comments.
 * @property-read string $cgb_form_in_page Form in page/post.
 * @property-read string $cgb_form_expand_type Collapsed comment form.
 * @property-read string $cgb_form_expand_link_text Link text for form expansion.
 * @property-read string $cgb_form_require_no_name_mail Comment author name/email.
 * @property-read string $cgb_form_remove_mail Remove Email field.
 * @property-read string $cgb_form_remove_website Remove website field.
 * @property-read string $cgb_form_comment_label Label for comment field.
 * @property-read string $cgb_form_title Comment form title.
 * @property-read string $cgb_form_title_reply_to Reply comment form title.
 * @property-read string $cgb_form_notes_before Notes before form fields.
 * @property-read string $cgb_form_notes_after Notes after form fields.
 * @property-read string $cgb_form_label_submit Label of submit button.
 * @property-read string $cgb_form_cancel_reply Label for cancel reply link.
 * @property-read string $cgb_form_must_login_message Must login message.
 * @property-read string $cgb_form_styles Comment form styles.
 * @property-read string $cgb_form_args Comment form args.
 * @property-read string $cgb_clist_threaded Threaded guestbook comments.
 * @property-read string $cgb_clist_order Comment list order.
 * @property-read string $cgb_clist_child_order_desc Comment list child order.
 * @property-read string $cgb_clist_default_page Comment list default page.
 * @property-read string $cgb_clist_pagination Bread comments into pages.
 * @property-read string $cgb_clist_per_page Comments per page.
 * @property-read string $cgb_clist_num_pagination Numbered pagination.
 * @property-read string $cgb_clist_show_all Show all comments.
 * @property-read string $cgb_clist_title Title for the comment list.
 * @property-read string $cgb_clist_in_page_content Comment list in page content.
 * @property-read string $cgb_clist_styles Comment list styles.
 * @property-read string $cgb_clist_args Comment list args.
 * @property-read string $cgb_comment_adjust Comment adjustment.
 * @property-read string $cgb_comment_html Comment html code.
 * @property-read string $cgb_comment_callback Comment callback function.
 * @property-read string $cgb_cmessage_enabled Enable message.
 * @property-read string $cgb_cmessage_text New comment message text.
 * @property-read string $cgb_cmessage_type New comment message type.
 * @property-read string $cgb_cmessage_duration New comment message duration.
 * @property-read string $cgb_cmessage_styles New comment message styles.
 * @property-read string $cgb_page_cmessage_enabled Message after new comments in other pages/posts.
 * @property-read string $cgb_page_remove_mail Remove Email field in other pages/posts.
 * @property-read string $cgb_page_remove_website Remove Website field in other pages/posts.
 */
final class ConfigAdminData {

	/**
	 * Additional data for the config options
	 *
	 * @var array<string,array<string,string|array<string,string>>>
	 */
	private $config_data;

	/**
	 * Additional data for the config sections
	 *
	 * @var array<string,array<string,string>>
	 */
	public $section_data;


	/**
	 * Constructor: Initialize the data
	 */
	public function __construct() {
		$this->config_data = [
			// General.
			'cgb_ignore_comments_open'        => [
				'type'        => 'checkbox',
				'label'       => __( 'Guestbook comment status', 'comment-guestbook' ),
				'caption'     => __( 'Always allow comments on the guestbook page', 'comment-guestbook' ),
				'description' => __( 'If this option is enabled comments on the guestbook page will always be allowed. Hence the corresponding WordPress and page standard settings will be overruled in the guestbook page.', 'comment-guestbook' ),
			],

			'cgb_ignore_comment_registration' => [
				'type'        => 'checkbox',
				'label'       => __( 'Guestbook comment registration', 'comment-guestbook' ),
				'caption'     => __( 'Allow comments on the guestbook page without registration', 'comment-guestbook' ),
				'description' => __( 'If this option is enabled comments will be allowed without registration. Hence the corresponding WordPress standard setting will be overruled in the guestbook page.', 'comment-guestbook' ),
			],

			'cgb_ignore_comment_moderation'   => [
				'type'        => 'checkbox',
				'label'       => __( 'Guestbook comment moderation', 'comment-guestbook' ),
				'caption'     => __( 'Disable moderation of new comments on the guestbook page', 'comment-guestbook' ),
				'description' => __( 'If this option is enabled comments will be displayed without manual moderation. Hence the corresponding WordPress standard setting will be overruled in the guestbook page.', 'comment-guestbook' ),
			],

			'cgb_adjust_output'               => [
				'type'        => 'checkbox',
				'label'       => __( 'Comments adjustment', 'comment-guestbook' ),
				'caption'     => __( 'Adjust the guestbook comments output', 'comment-guestbook' ),
				'description' =>
					sprintf(
						__( 'This option specifies if the standard WordPress function %1$s shall be overwritten.', 'comment-guestbook' ),
						'<a href="https://developer.wordpress.org/reference/functions/wp_list_comments/">wp_list_comments</a>'
					) . '<br />' .
					__( 'Activating this option is required for many settings in the other sections (see options and sections descriptions).', 'comment-guestbook' ),

			],

			'cgb_l10n_domain'                 => [
				'type'        => 'text',
				'label'       => __( 'Domain for translation', 'comment-guestbook' ),
				'description' =>
					__( 'This option defines the domain for translation for the adjusted guestbook comments output.', 'comment-guestbook' ) . '<br />' .
					sprintf(
						__( 'For example if you want to use the translations of the twentyeleven theme the correct value is %1$s.', 'comment-guestbook' ),
						'"twentyeleven"'
					) . '<br />' .
					sprintf(
						__( 'Have a look at the corresponding description in the %1$s for more details.', 'comment-guestbook' ),
						'<a href="https://developer.wordpress.org/reference/functions/__/" target="_blank" rel="noopener">WordPress Code Reference</a>'
					),
			],

			// Comment-form.
			'cgb_form_below_comments'         => [
				'type'        => 'checkbox',
				'label'       => __( 'Form below comments', 'comment-guestbook' ),
				'caption'     => __( 'Show a comment form in the comment area below the comments', 'comment-guestbook' ),
				'description' => __( 'With this option you can add a comment form in the comment section below the comment list.', 'comment-guestbook' ),
			],

			'cgb_form_above_comments'         => [
				'type'        => 'checkbox',
				'label'       => __( 'Form above comments', 'comment-guestbook' ),
				'caption'     => __( 'Show a comment form in the comment area above the comments', 'comment-guestbook' ),
				'description' => __( 'With this option you can add a comment form in the comment section above the comment list.', 'comment-guestbook' ),
			],

			'cgb_form_in_page'                => [
				'type'        => 'checkbox',
				'label'       => __( 'Form in page/post', 'comment-guestbook' ),
				'caption'     => __( 'Show a comment form in the page/post area', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can add a comment form in the page or post area. The form will be displayed at the position of the shortcode.', 'comment-guestbook' ) . '<br />' .
					sprintf(
						__( 'If the option %1$s is enabled, this form will not be displayed to avoid showing 2 forms in succession.', 'comment-guestbook' ),
						'"' . __( 'Form above comments', 'comment-guestbook' ) . '"'
					),
			],

			'cgb_form_expand_type'            => [
				'type'        => 'radio',
				'label'       => __( 'Collapsed comment form', 'comment-guestbook' ),
				'captions'    => [
					'false'    => __( 'form is not collapsed (default)', 'comment-guestbook' ),
					'static'   => __( 'collapsed form with static expansion', 'comment-guestbook' ),
					'animated' => __( 'collapsed form with animated expansion', 'comment-guestbook' ),
				],
				'description' =>
					__( 'With this option you can collapse (hide) the comment forms by default and add a link which expands the form.', 'comment-guestbook' ) . '<br />' .
					__( 'There are 2 options for expansion available: static and animated. Animated shows a small animation during the form expansion.', 'comment-guestbook' ),
			],

			'cgb_form_expand_link_text'       => [
				'type'        => 'text',
				'label'       => __( 'Link text for form expansion', 'comment-guestbook' ),
				'description' => sprintf(
					__( 'With this option you can set the link text to expand the comment form if %1$s is enabled.', 'comment-guestbook' ),
					'"' . __( 'Collapsed comment form', 'comment-guestbook' ) . '"'
				),
			],

			'cgb_form_require_no_name_mail'   => [
				'type'        => 'checkbox',
				'label'       => __( 'Comment author name/email', 'comment-guestbook' ),
				'caption'     => __( 'Override Name and Email field requirement', 'comment-guestbook' ),
				'description' => __( 'If this option is enabled the name and email field is not a required field in the comment guestbook form (independent of the WordPress standard discussion setting).', 'comment-guestbook' ),
			],

			'cgb_form_remove_mail'            => [
				'type'        => 'checkbox',
				'label'       => __( 'Remove Email field', 'comment-guestbook' ),
				'caption'     => __( 'Remove the Email field in comment guestbook form', 'comment-guestbook' ),
				'description' => __( 'If this option is enabled the email field will be removed in the comment guestbook form.', 'comment-guestbook' ),
			],

			'cgb_form_remove_website'         => [
				'type'        => 'checkbox',
				'label'       => __( 'Remove Website field', 'comment-guestbook' ),
				'caption'     => __( 'Remove the Website url field in comment guestbook form', 'comment-guestbook' ),
				'description' => __( 'If this option is enabled the website url field will be removed in the comment guestbook form.', 'comment-guestbook' ),
			],

			'cgb_form_comment_label'          => [
				'type'        => 'text',
				'label'       => __( 'Label for comment field', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can specify a custom label for the comment field.', 'comment-guestbook' ) . '<br />' .
					sprintf( __( 'Set the value to %1$s for the wordpress default label. Enter an empty string to hide the label.', 'comment-guestbook' ), '"default"' ),
			],

			'cgb_form_title'                  => [
				'type'        => 'text',
				'label'       => __( 'Comment form title', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can specify a custom title for the comment form (when not replying to a comment).', 'comment-guestbook' ) . '<br />' .
					sprintf( __( 'Set the value to %1$s for the wordpress default title. Enter an empty string to hide the title.', 'comment-guestbook' ), '"default"' ),
			],

			'cgb_form_title_reply_to'         => [
				'type'        => 'text',
				'label'       => __( 'Reply comment form title', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can specify a custom title for the comment form (when replying to a comment).', 'comment-guestbook' ) . '<br />' .
					sprintf( __( 'Set the value to %1$s for the wordpress default title. Enter an empty string to hide the title.', 'comment-guestbook' ), '"default"' ),
			],

			'cgb_form_notes_before'           => [
				'type'        => 'text',
				'label'       => __( 'Notes before form fields', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can override the text for the notes before the comment form fields.', 'comment-guestbook' ) . '<br />' .
					sprintf( __( 'Set the value to %1$s for the wordpress default notes. Enter an empty string to hide the notes.', 'comment-guestbook' ), '"default"' ),
			],

			'cgb_form_notes_after'            => [
				'type'        => 'text',
				'label'       => __( 'Notes after form fields', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can override the text for the notes after the comment form fields (and before the submit button).', 'comment-guestbook' ) . '<br />' .
					sprintf( __( 'Set the value to %1$s for the wordpress default notes. Enter an empty string to hide the notes.', 'comment-guestbook' ), '"default"' ),
			],

			'cgb_form_label_submit'           => [
				'type'        => 'text',
				'label'       => __( 'Label of submit button', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can override the label of the comment form submit button.', 'comment-guestbook' ) . '<br />' .
					sprintf( __( 'Set the value to %1$s or to an empty string for the wordpress default label.', 'comment-guestbook' ), '"default"' ),
			],

			'cgb_form_cancel_reply'           => [
				'type'        => 'text',
				'label'       => __( 'Label for cancel reply link', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can override the label for the comment form cancel reply link.', 'comment-guestbook' ) . '<br />' .
					sprintf( __( 'Set the value to %1$s or to an empty string for the wordpress default label.', 'comment-guestbook' ), '"default"' ),
			],

			'cgb_form_must_login_message'     => [
				'type'        => 'text',
				'label'       => __( '"Must login" message', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can override the message which will be displayed when the user must login to add a new comment.', 'comment-guestbook' ) . '<br />' .
					sprintf( __( 'The term %1$s will be replaced by the url to login. You can specify it in your text if you want to include a link to the login page.', 'comment-guestbook' ), '"%s"' ) . '<br />' .
					__( 'Example (standard text)', 'comment-guestbook' ) . ': <code>You must be &lt;a href="%s"&gt;logged in&lt;/a&gt; to post a comment.</code><br />' .
					sprintf( __( 'Set the value to %1$s or to an empty string for the wordpress default message.', 'comment-guestbook' ), '"default"' ),
			],

			'cgb_form_styles'                 => [
				'type'        => 'textarea',
				'rows'        => '7',
				'label'       => __( 'Comment form styles', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can specify custom css styles for the guestbook comment form.', 'comment-guestbook' ) . '<br />' .
					__( 'Example', 'comment-guestbook' ) . ':<br />' .
					'<code>.form-submit { text-align:center; }<br />&nbsp;#submit { font-weight:bold; }</code>',
			],

			'cgb_form_args'                   => [
				'type'        => 'textarea',
				'rows'        => '10',
				'label'       => __( 'Comment form args', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can specify args for the comment form.', 'comment-guestbook' ) . '<br />' .
					__( 'This can be required because some themes change the comment form styling direcly with args.', 'comment-guestbook' ) . '<br />' .
					__( 'With this option you can insert these specific args for the guestbook form.', 'comment-guestbook' ) . '<br />' .
					sprintf(
						__( 'A list of all available args and there discription can be found in the %1$s.', 'comment-guestbook' ),
						'<a href="https://developer.wordpress.org/reference/functions/comment_form/#parameters">WordPress Code Reference</a>'
					) . '<br />' .
					__( 'The given text must be valid php array, e.g.:', 'comment-guestbook' ) . '<br />' .
					'<code>array(<br />' .
					'&nbsp;&nbsp;\'comment_notes_after\' =&gt; \'&lt;p&gt;\'.sprintf(__(\'You may use these &lt;abbr&gt;HTML&lt;/abbr&gt;<br />' .
					'&nbsp;&nbsp;&nbsp;&nbsp;tags and attributes: %s\'), allowed_tags()).\'&lt;/p&gt;\',<br />' .
					'&nbsp;&nbsp;\'fields\' =&gt; array(<br />' .
					'&nbsp;&nbsp;&nbsp;&nbsp;\'author\' =&gt; \'&lt;input type="text" name="author" /&gt;\',<br />' .
					'&nbsp;&nbsp;&nbsp;&nbsp;\'location\' =&gt; \'&lt;input type="text" name="location" /&gt;\')<br />' .
					')</code><br />' .
					__( 'The given args will be overwritten with all the specific comment form options listed above.', 'comment-guestbook' ),
			],

			// Comment-list section.
			'cgb_clist_threaded'              => [
				'type'        => 'radio',
				'label'       => __( 'Threaded guestbook comments', 'comment-guestbook' ),
				'captions'    => [
					'default'  => __( 'Standard WP-discussion setting', 'comment-guestbook' ),
					'enabled'  => __( 'Enabled', 'comment-guestbook' ),
					'disabled' => __( 'Disabled', 'comment-guestbook' ),
				],
				'description' =>
					__( 'With this option the WordPress standard setting for threaded comments can be overruled on guestbook pages. If enabled a reply to a available comment is allowed, when disabled it isn´t.', 'comment-guestbook' ) . '<br />' .
					__( 'You can define the allowed depth of threaded comments in the WordPress discussion settings. There also the corresponding WordPress standard value for all comments can be changed.', 'comment-guestbook' ),
			],

			'cgb_clist_order'                 => [
				'type'        => 'radio',
				'label'       => __( 'Comment list order', 'comment-guestbook' ),
				'captions'    => [
					'default' => 'Standard WP-discussion setting',
					'asc'     => 'Oldest comments first',
					'desc'    => 'Newest comments first',
				],
				'description' => __( 'This option allows you to override the standard order for top level comments on guestbook pages.', 'comment-guestbook' ),
			],

			'cgb_clist_child_order_desc'      => [
				'type'        => 'checkbox',
				'label'       => __( 'Comment list child order', 'comment-guestbook' ),
				'caption'     => 'Newest child comments first',
				'description' => __( 'This option allows you to override the standard order (oldest first) for all child comments on guestbook pages.', 'comment-guestbook' ),
			],

			'cgb_clist_default_page'          => [
				'type'        => 'radio',
				'label'       => __( 'Comment list default page', 'comment-guestbook' ),
				'captions'    => [
					'default' => 'Standard WP-discussion setting',
					'first'   => 'First page',
					'last'    => 'Last page',
				],
				'description' => __( 'This option allows you to override the standard default page on guestbook pages.', 'comment-guestbook' ),
			],

			'cgb_clist_pagination'            => [
				'type'        => 'radio',
				'label'       => __( 'Break comments into pages', 'comment-guestbook' ),
				'captions'    => [
					'default' => __( 'Standard WP-discussion setting', 'comment-guestbook' ),
					'false'   => __( 'Disable pagination', 'comment-guestbook' ),
					'true'    => __( 'Enable pagination', 'comment-guestbook' ),
				],
				'description' => __( 'With this option you to override the WordPress default setting on guestbook pages if the comments shall be broken into pages.', 'comment-guestbook' ),
			],

			'cgb_clist_per_page'              => [
				'type'        => 'number',
				'label'       => __( 'Comments per page', 'comment-guestbook' ),
				'range'       => [ 'min_value' => '0' ],
				'description' =>
					__( 'This option allows you to override the standard number of comments listed per page on guestbook pages, if pagination is enabled.', 'comment-guestbook' ) . '<br />' .
					sprintf(
						__( 'The default value is %1$s to use the WordPress default setting (see %2$s).', 'comment-guestbook' ),
						'"0"',
						'<a href="' . admin_url( 'options-discussion.php' ) . '">' . __( 'WordPress Discussion Settings', 'comment-guestbook' ) . '</a>'
					),
			],

			'cgb_clist_num_pagination'        => [
				'type'        => 'checkbox',
				'label'       => __( 'Numbered pagination', 'comment-guestbook' ),
				'caption'     => __( 'Show a numbered pagination navigation', 'comment-guestbook' ),
				'description' =>
					__( 'Normally only a next and a previous link is available.', 'comment-guestbook' ) . '<br />' .
					__( 'When this option is enabled additionally a numbered list with all comment pages is displayed.', 'comment-guestbook' ),
			],

			'cgb_clist_show_all'              => [
				'type'        => 'checkbox',
				'label'       => __( 'Show all comments', 'comment-guestbook' ),
				'caption'     => __( 'Show comments of all posts and pages', 'comment-guestbook' ),
				'description' =>
					__( 'Normally only the comments of the actual guestbook site are shown.', 'comment-guestbook' ) . '<br />' .
					__( 'But with this option you can enable to show the comments of all posts and pages on guestbook pages.', 'comment-guestbook' ) . '<br />' .
					__( 'It is recommended to enable "Comment Adjustment" in Section "Comment html code" if you enable this option.', 'comment-guestbook' ) . '<br />' .
					__( 'There you have the possibility to include a reference to the original page/post in the comment html code.', 'comment-guestbook' ),
			],

			'cgb_clist_title'                 => [
				'type'        => 'text',
				'label'       => __( 'Title for the comment list', 'comment-guestbook' ),
				'description' => __( 'With this option you can specify an additional title which will be displayed in front of the comment list.', 'comment-guestbook' ),
			],

			'cgb_clist_in_page_content'       => [
				'type'        => 'checkbox',
				'label'       => __( 'Comment list in page content', 'comment-guestbook' ),
				'caption'     => __( 'Show the comment list in the page content', 'comment-guestbook' ),
				'description' =>
					__( 'When this option is enabled the comment list is displayed directly in the post/page content and will be removed from the comment area.', 'comment-guestbook' ) . '<br />' .
					__( 'This can help to display the comment list in some situaltions, e.g. if your theme does not have a comment area at all.', 'comment-guestbook' ) . '<br />' .
					__( 'The comment list will be displayed instead of the shortcode, the comment form in the comment sections will be displayed before and/or after the comment list, as specified in the comment form options.', 'comment-guestbook' ),
			],

			'cgb_clist_styles'                => [
				'type'        => 'textarea',
				'rows'        => '7',
				'label'       => __( 'Comment list styles', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can specify custom css styles for the guestbook comment list.', 'comment-guestbook' ) . '<br />' .
					__( 'Enter all required styles like you would do it in a css file, e.g.', 'comment-guestbook' ) . ':<br />' .
					'<code>ol.commentlist { list-style:none; }<br />&nbsp;ul.children { list-style-type:circle; }</code>',
			],

			'cgb_clist_args'                  => [
				'type'        => 'textarea',
				'rows'        => '7',
				'label'       => __( 'Comment list args', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can manually specify args for the comment list.', 'comment-guestbook' ) . '<br />' .
					sprintf(
						__( 'A list of all available arguments and there description can be found in the %1$s.', 'comment-guestbook' ),
						'<a href="https://developer.wordpress.org/reference/functions/wp_list_comments/#parameters">' . __( 'WordPress Code Reference', 'comment-guestbook' ) . '</a>'
					) . '<br />' .
					__( 'The given text must be valid php array, e.g.', 'comment-guestbook' ) . ':<br />' .
					'<code>array(<br />' .
					'&nbsp;&nbsp;\'style\' =&gt; \'div\',<br />' .
					'&nbsp;&nbsp;\'avatar_size\' =&gt; 45<br />' .
					')</code><br />' .
					__( 'This setting will be overwritten with all the specific comment list options listed above.', 'comment-guestbook' ),
			],

			// Comment html code.
			'cgb_comment_adjust'              => [
				'type'        => 'checkbox',
				'label'       => __( 'Comment adjustment', 'comment-guestbook' ),
				'caption'     => __( 'Adjust the html-output of each comment', 'comment-guestbook' ),
				'description' =>
					sprintf(
						__( 'This option specifies if the comment html code should be replaced with the %1$s below on the guestbook page.', 'comment-guestbook' ),
						'"' . __( 'Comment html code', 'comment-guestbook' ) . '"'
					) . '<br />' .
					sprintf(
						__( 'If %1$s in %2$s is disabled this option has no effect.', 'comment-guestbook' ),
						'"' . __( 'Guestbook comments adjustment', 'comment-guestbook' ) . '"',
						'"' . __( 'General settings', 'comment-guestbook' ) . '"'
					),
			],

			'cgb_comment_html'                => [
				'type'        => 'textarea',
				'rows'        => '18',
				'label'       => __( 'Comment html code', 'comment-guestbook' ),
				'description' =>
					__( 'This option specifies the html code for each comment, if "Comment adjustment" is enabled.', 'comment-guestbook' ) . '<br />' .
					__( 'You can use php-code to get the required comment data. The following variables and objects are availabe', 'comment-guestbook' ) . ':<br />' .
					'- <code>$l10n_domain</code> ... ' .
					__( 'Use this php variable to get the "Domain for translation" value.', 'comment-guestbook' ) . '<br />' .
					'- <code>$comment</code> ... ' .
					sprintf(
						__( 'This objects includes all available data of the comment. You can use all available fields of %1$s return object listed in the %2$s.', 'comment-guestbook' ),
						'"' . __( 'get_comment', 'comment-guestbook' ) . '"',
						'<a href="https://developer.wordpress.org/reference/functions/get_comment/" target="_blank" rel="noopener">Wordpress Code Reference</a>'
					) . '<br />' .
					'- <code>$is_comment_from_other_page</code> ... ' .
					__( 'This boolean variable gives you information if the comment was created in another page or post', 'comment-guestbook' ) . '<br />' .
					'- <code>$other_page_title</code> ... ' .
					__( 'With this variable you have access to the Page name of a commente created in another page or post.', 'comment-guestbook' ) . '<br />' .
					'- <code>$other_page_link</code> ... ' .
					__( 'With this variable you can include a link to the original page of a comment created in another page or post.', 'comment-guestbook' ) . '<br />' .
					sprintf(
						__( 'Wordpress provides some additional functions to access the comment data (see %1$s for details).', 'comment-guestbook' ),
						'<a href="https://codex.wordpress.org/Function_Reference#Comment.2C_Ping.2C_and_Trackback_Functions" target="_blank" rel="noopener">WordPress Codex</a>'
					) . '<br />' .
					__( 'The code given as an example is a slightly modified version of the twentyeleven theme.', 'comment-guestbook' ) . '<br />' .
					sprintf(
						__( 'If you want to adapt the code for your theme you can normally find the theme template in the file %1$s in your theme directory.', 'comment-guestbook' ),
						'"functions.php"'
					) . '<br />' .
					sprintf(
						__( 'E.g. for twentyeleven the function is called %1$s.', 'comment-guestbook' ),
						'"twentyeleven_comment"'
					) . '<br />' .
					sprintf(
						__( 'If you have enabled the option %1$s it is recommended to enable %2$s and add a link to the original page of the comment.', 'comment-guestbook' ),
						'"' . __( 'Show all comments', 'comment-guestbook' ) . '"',
						'"' . __( 'Comment adjustment', 'comment-guestbook' ) . '"'
					) . '<br />' .
					__( 'Example', 'comment-guestbook' ) . ':<br />' .
					'<code>if($is_comment_from_other_page && "0" == $comment->comment_parent) {<br />' .
					'&nbsp;&nbsp;&nbsp;&nbsp;echo \' \'.__(\'Link to page:\', $l10n_domain).\' \'.$other_page_link;<br />' .
					'}</code>',
			],

			'cgb_comment_callback'            => [
				'type'        => 'text',
				'label'       => __( 'Comment callback function', 'comment-guestbook' ),
				'description' =>
					__( 'This option sets the name of comment callback function which outputs the html-code to view each comment.', 'comment-guestbook' ) . '<br />' .
					__( 'You only require this function if "Guestbook comments adjustment" is enabled and "Comment adjustment" is disabled.', 'comment-guestbook' ) . '<br />' .
					__( 'Normally this function is set through the selected theme. Comment Guestbook searches for the theme-function and uses this as default.', 'comment-guestbook' ) . '<br />' .
					__( 'If the theme-function wasn´t found this field will be empty, then the WordPress internal functionality will be used.', 'comment-guestbook' ) . '<br />' .
					__( 'If you want to insert the function of your theme manually, you can find the name normally in file "functions.php" of your theme.', 'comment-guestbook' ) . '<br />' .
					__( 'Often it is called "themename_comment", e.g. "twentyeleven_comment" for twentyeleven theme.', 'comment-guestbook' ),
			],

			// Message after new comment.
			'cgb_cmessage_enabled'            => [
				'type'        => 'checkbox',
				'label'       => __( 'Enable message', 'comment-guestbook' ),
				'caption'     => __( 'Add a message after a new guestbook comment', 'comment-guestbook' ),
				'description' => __( 'If this option is enabled a message will be shown after a new comment was made on a guestbook page.', 'comment-guestbook' ) . '<br />',
			],

			'cgb_cmessage_text'               => [
				'type'        => 'text',
				'label'       => __( 'Message text', 'comment-guestbook' ),
				'description' => __( 'This option allows you to change the text for the message after a new comment.', 'comment-guestbook' ),
			],

			'cgb_cmessage_type'               => [
				'type'        => 'radio',
				'label'       => __( 'Message type', 'comment-guestbook' ),
				'captions'    => [
					'inline'  => 'Show the message inline',
					'overlay' => 'Show the message in overlay',
				],
				'description' =>
					__( 'This option allows to change the format of the message after a new comment.', 'comment-guestbook' ) . '<br />' .
					sprintf(
						__( 'With %1$s the message is shown directly below the comment in a div added via javascript.', 'comment-guestbook' ),
						'"inline"'
					) . '<br />' .
					sprintf(
						__( 'With %1$s the message is shown in an overlay div.', 'comment-guestbook' ),
						'overlay'
					) . '<br />' .
					__( 'The message will be slided in with an animation and after a short time the message will be slided out.', 'comment-guestbook' ),
			],

			'cgb_cmessage_duration'           => [
				'type'        => 'text',
				'label'       => __( 'Message duration', 'comment-guestbook' ),
				'description' =>
					__( 'How long should the message after a new comment should be displayed?', 'comment-guestbook' ) . '<br />' .
					__( 'Normally the message after a new comment will be removed after a certain time.', 'comment-guestbook' ) . '<br />' .
					__( 'You can define this duration with in milliseconds.', 'comment-guestbook' ) . '<br />' .
					sprintf(
						__( 'Set the value to %1$s if you do not want to hide the message.', 'comment-guestbook' ),
						'"0"'
					),
			],

			'cgb_cmessage_styles'             => [
				'type'        => 'textarea',
				'rows'        => '7',
				'label'       => __( 'Message styles', 'comment-guestbook' ),
				'description' =>
					__( 'With this option you can define the css styles for the message after a new comment.', 'comment-guestbook' ) . '<br />' .
					__( 'The given code will be used for the style attribute of the message surrounding div tag.', 'comment-guestbook' ),
			],

			// Comments in other pages/posts.
			'cgb_page_cmessage_enabled'       => [
				'type'        => 'checkbox',
				'label'       => __( 'Message after new comments', 'comment-guestbook' ),
				'caption'     => __( 'Show a "Thank you" message after a new comment', 'comment-guestbook' ),
				'description' =>
					__( 'If this option is enabled a message will be shown after a new comment was made.', 'comment-guestbook' ) . '<br />' .
					sprintf(
						__( 'There are many options to change the message text and format in the %1$s section.', 'comment-guestbook' ),
						'"' . __( 'Message after new comment', 'comment-guestbook' ) . '"'
					),
			],

			'cgb_page_remove_mail'            => [
				'type'        => 'checkbox',
				'label'       => __( 'Remove Email field', 'comment-guestbook' ),
				'caption'     => __( 'Remove the Email field in comment forms', 'comment-guestbook' ),
				'description' => __( 'If this option is enabled the email field will be removed in comment forms.', 'comment-guestbook' ),
			],

			'cgb_page_remove_website'         => [
				'type'        => 'checkbox',
				'label'       => __( 'Remove Website field', 'comment-guestbook' ),
				'caption'     => __( 'Remove the Website url field in comment forms', 'comment-guestbook' ),
				'description' => __( 'If this option is enabled the website url field will be removed in comment forms.', 'comment-guestbook' ),
			],
		];

		$this->section_data = [
			'general'       => [
				'caption'     => __( 'General settings', 'comment-guestbook' ),
				'description' => __( 'Some general settings for this plugin.', 'comment-guestbook' ),
			],
			'comment_form'  => [
				'caption'     => __( 'Comment-form settings', 'comment-guestbook' ),
				'description' =>
					__( 'In this section you can find settings to modify the comment form.', 'comment-guestbook' ) . '<br />' .
					'<strong>' . __( 'Attention', 'comment-guestbook' ) . ':</strong><br />' .
					sprintf(
						__( 'If you want to change any option in this section you have to enable the option %1$s in %2$s first.', 'comment-guestbook' ),
						'"' . $this->config_data['cgb_adjust_output']['label'] . '"',
						'"' . __( 'General settings', 'comment-guestbook' ) . '"'
					) . '<br />' .
					sprintf(
						__( 'Only the options %1$s, %2$s and all comment form modification options are working without it.', 'comment-guestbook' ),
						'"' . $this->config_data['cgb_form_in_page']['label'] . '"',
						'"' . $this->config_data['cgb_cmessage_enabled']['label'] . '"'
					),
			],
			'comment_list'  => [
				'caption'     => __( 'Comment-list settings', 'comment-guestbook' ),
				'description' =>
					__( 'In this section you can find settings to modify the comments list.', 'comment-guestbook' ) . '<br />' .
					'<strong>' . __( 'Attention', 'comment-guestbook' ) . ':</strong><br />' .
					sprintf(
						__( 'If you want to change any option in this section you have to enable the option %1$s in %2$s first.', 'comment-guestbook' ),
						'"' . $this->config_data['cgb_adjust_output']['label'] . '"',
						'"' . __( 'General settings', 'comment-guestbook' ) . '"'
					),
			],
			'comment_html'  => [
				'caption'     => __( 'Comment html code', 'comment-guestbook' ),
				'description' => __( 'In this section you can change the html code for the comment output in guestbook pages.', 'comment-guestbook' ),
			],
			'cmessage'      => [
				'caption'     => __( 'Message after new comments', 'comment-guestbook' ),
				'description' => __( 'In this section you can enable and modify the message after a new comment.', 'comment-guestbook' ) . '<br />' .
					sprintf(
						__( 'All modification options are also valid for other posts and pages if the option %1$s in the section %2$s is enabled.', 'comment-guestbook' ),
						'"' . $this->config_data['cgb_page_cmessage_enabled']['label'] . '"',
						'"' . __( 'Comments in other posts/pages', 'comment-guestbook' ) . '"'
					),
			],
			'page_comments' => [
				'caption'     => __( 'Comments in other posts/pages', 'comment-guestbook' ),
				'description' =>
					__( 'In this sections you can change the behavior of comments lists and forms in all other posts and pages of your website (exept the guestbook pages).', 'comment-guestbook' ) . '<br />' .
					__( 'If you want to change these settings also for guestbook comments please specify the same setting values in the other option tabs.', 'comment-guestbook' ),
			],
		];
	}


	/**
	 * Get the data for a given option.
	 *
	 * @param string $option_name The name of the option.
	 * @return array<string,string|array<string,string>>
	 */
	public function __get( $option_name ) {
		if ( isset( $this->config_data[ $option_name ] ) ) {
			return $this->config_data[ $option_name ];
		}
	}

}
