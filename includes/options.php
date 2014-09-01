<?php
if(!defined('ABSPATH')) {
	exit;
}

// This class handles all available options
class CGB_Options {

	private static $instance;
	public $sections;
	public $options;

	public static function &get_instance() {
		// Create class instance if required
		if(!isset(self::$instance)) {
			self::$instance = new CGB_Options();
			self::$instance->init();
		}
		// Return class instance
		return self::$instance;
	}

	private function __construct() {
		$this->sections = array(
			'general'        => array('caption' => __('General settings'),
			                          'desc'    => __('Some general settings for this plugin.')),
			'comment_form'   => array('caption' => __('Comment-form settings'),
			                          'desc'    => __('In this section you can find settings to modify the comment form.<br />
			                                           <strong>Attention:</strong><br />If you want to change any option in this section you have to enable the option "Guestbook comment adjustment" in the "General settings" first.<br />
			                                           Only the options "Show form in page/post" and "Message after comment" are working without it. Also all form modification options are working in the page/post form without "Guestbook comment adjustment" enabled.')),
			'comment_list'   => array('caption' => __('Comment-list settings'),
			                          'desc'    => __('In this section you can find settings to modify the comments list.<br />
			                                           <strong>Attention:</strong> If you want to change any option in this section you have to enable the option "Guestbook comment adjustment" in the "General settings" first.')),
			'comment_html'   => array('caption' => __('Comment html code'),
			                          'desc'    => __('In this section you can change the html code for the comment output in guestbook pages.')),
			'cmessage'       => array('caption' => __('Message after new comment'),
			                          'desc'    => __('In this section you can find settings to modify the message after a new comment.<br />
			                                           You can enable the message in the "Comment-form settings" for the guestbook page.<br />
			                                           This options are also valid for all other posts and pages if you enable the message in the "Comments in all posts/page" section.')),
			'page_comments'  => array('caption' => __('Comments in other posts/pages'),
			                          'desc'    => __('In this sections you can change the behavior of comments lists and forms in all other posts and pages of your website (exept the guestbook pages).'))
		);

		$this->options = array(
			// General section
			'cgb_ignore_comments_open'        => array('section' => 'general',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '1',
			                                           'label'   => __('Guestbook comment status'),
			                                           'caption' => __('Always allow comments on the guestbook page'),
			                                           'desc'    => __('If this option is enabled the wordpress and actual page setting will be overwritten and comments will be always allowed on the guestbook page.')),

			'cgb_ignore_comment_registration' => array('section' => 'general',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '1',
			                                           'label'   => __('Guestbook comment registration'),
			                                           'caption' => __('Allow comments on the guestbook page without registration'),
			                                           'desc'    => __('If this option is enabled the wordpress setting will be overwritten and comments will be always allowed without registration on the guestbook page.')),

			'cgb_threaded_gb_comments'        => array('section' => 'general',
			                                           'type'    => 'radio',
			                                           'std_val' => 'default',
			                                           'label'   => __('Enable threaded guestbook comments'),
			                                           'caption' => array('default' => 'Standard WP-discussion setting', 'enabled' => 'Enabled', 'disabled' => 'Disabled'),
			                                           'desc'    => __('This option allows you to overwrite the threaded comments option for guestbook pages.<br />
			                                                            If this option is enabled a reply to a given comment is allowed, when disabled it isn´t.<br />
			                                                            You can define the allowed depth of threaded comments in the WordPress discussion settings. There also the standard value for all comments can be changed.')),

			'cgb_adjust_output'               => array('section' => 'general',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '',
			                                           'label'   => __('Guestbook comments adjustment'),
			                                           'caption' => __('Adjust the guestbook comments output'),
			                                           'desc'    => __('This option specifies if the "list_comments" wordpress function shall be overwritten.<br />
			                                                        Switching on this option is required to make most of the other adjustments working (see options and sections descriptions).')),

			'cgb_l10n_domain'                 => array('section' => 'general',
			                                           'type'    => 'text',
			                                           'std_val' => 'default',
			                                           'label'   => __('Domain for translation'),
			                                           'desc'    => __('Sets the domain for translation for the modified code which is set in Comment Guestbook.<br />
			                                                            Standard value is "default". For example if you want to use the function of the twentyeleven theme the value would be "twentyeleven".<br />
			                                                            See the <a href="http://codex.wordpress.org/Function_Reference/_2" target="_blank">description in Wordpress codex</a> for more details.')),
			// Comment-form section
			'cgb_form_below_comments'         => array('section' => 'comment_form',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '',
			                                           'label'   => __('Show form below comments'),
			                                           'caption' => __('Add a comment form in the comment area below the comments'),
			                                           'desc'    => __('With this option you can add a comment form in the comment section below the comment list.')),

			'cgb_form_above_comments'         => array('section' => 'comment_form',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '',
			                                           'label'   => __('Show form above comments'),
			                                           'caption' => __('Add a comment form in the comment area above the comments'),
			                                           'desc'    => __('With this option you can add a comment form in the comment section above the comment list.')),

			'cgb_form_in_page'                => array('section' => 'comment_form',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '1',
			                                           'label'   => __('Show form in page/post'),
			                                           'caption' => __('Add a comment form in the page/post area'),
			                                           'desc'    => __('With this option you can add a comment form in the page or post area. The form will be displayed at the position of the shortcode.<br />
			                                                            If the option "Show form above comments" is enabled, this form will not be displayed to avoid showing 2 forms in succession.')),

			'cgb_add_cmessage'                => array('section' => 'comment_form',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '',
			                                           'label'   => __('Message after new comment'),
			                                           'caption' => __('Show a "Thank you" message after a new guestbook comment'),
			                                           'desc'    => __('If this option is enabled a message will be shown after a new comment was made.<br />
			                                                            There are many additional options availabe to change the message text and format in the "Message after new comment" section.')),

			'cgb_form_remove_mail'            => array('section' => 'comment_form',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '',
			                                           'label'   => __('Remove Email field'),
			                                           'caption' => __('Remove the Email field in comment guestbook form'),
			                                           'desc'    => __('If this option is enabled the email field will be removed in the comment guestbook form.')),

			'cgb_form_remove_website'         => array('section' => 'comment_form',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '',
			                                           'label'   => __('Remove Website field'),
			                                           'caption' => __('Remove the Website url field in comment guestbook form'),
			                                           'desc'    => __('If this option is enabled the website url field will be removed in the comment guestbook form.')),

			'cgb_form_comment_label'          => array('section' => 'comment_form',
			                                           'type'    => 'text',
			                                           'std_val' => 'default',
			                                           'label'   => __('Label for comment field'),
			                                           'desc'    => __('With this option you can specify a specific label for the comment field.<br />
			                                                            The standard is "default" to use the wordpress default label. Enter an empty string to hide the label.')),

			'cgb_form_title_reply'            => array('section' => 'comment_form',
			                                           'type'    => 'text',
			                                           'std_val' => 'default',
			                                           'label'   => __('Comment form title'),
			                                           'desc'    => __('With this option you can specify a specific title for the comment form (when not replying to a comment).<br />
			                                                            The standard is "default" to use the wordpress default title. Enter an empty string to hide the title.')),

			'cgb_form_title_reply_to'         => array('section' => 'comment_form',
			                                           'type'    => 'text',
			                                           'std_val' => 'default',
			                                           'label'   => __('Reply comment form title'),
			                                           'desc'    => __('With this option you can specify a specific title for the comment form (when replying to a comment).<br />
			                                                            The standard is "default" to use the wordpress default title. Enter an empty string to hide the title.')),

			'cgb_form_notes_before'           => array('section' => 'comment_form',
			                                           'type'    => 'text',
			                                           'std_val' => 'default',
			                                           'label'   => __('Notes before form fields'),
			                                           'desc'    => __('With this option you can overwrite the text for the notes before the comment form fields.<br />
			                                                            The standard is "default" to use the wordpress default notes. Enter an empty string to hide the notes.')),

			'cgb_form_notes_after'            => array('section' => 'comment_form',
			                                           'type'    => 'text',
			                                           'std_val' => 'default',
			                                           'label'   => __('Notes after form fields'),
			                                           'desc'    => __('With this option you can overwrite the text for the notes after the comment form fields (and before the submit button).<br />
			                                                            The standard is "default" to use the wordpress default notes. Enter an empty string to hide the notes.')),

			'cgb_form_label_submit'           => array('section' => 'comment_form',
			                                           'type'    => 'text',
			                                           'std_val' => 'default',
			                                           'label'   => __('Label of submit button'),
			                                           'desc'    => __('With this option you can overwrite the label of the comment form submit button.<br />
			                                                            The standard is "default" or an empty string to use the wordpress default label.')),

			'cgb_form_cancel_reply'           => array('section' => 'comment_form',
			                                           'type'    => 'text',
			                                           'std_val' => 'default',
			                                           'label'   => __('Label for cancel reply link'),
			                                           'desc'    => __('With this option you can overwrite the label for the comment form cancel reply link.<br />
			                                                            The standard is "default" or an empty string to use the wordpress default label.')),

			'cgb_form_styles'                 => array('section' => 'comment_form',
			                                           'type'    => 'textarea',
			                                           'rows'    => 6,
			                                           'std_val' => '',
			                                           'label'   => __('Comment form styles'),
			                                           'desc'    => __('With this option you can specify custom css styles for the guestbook comment form.<br />
			                                                            Enter all required styles like you would do it in a css file, e.g.:<br />
			                                                            <code>.form-submit { text-align:center; }<br />&nbsp;#submit { font-weight:bold; }</code>')),

			'cgb_form_args'                   => array('section' => 'comment_form',
			                                           'type'    => 'textarea',
			                                           'rows'    => 10,
			                                           'std_val' => '',
			                                           'label'   => __('Comment form args'),
			                                           'desc'    => __('With this option you can specify args for the comment form.<br />
			                                                            This can be required because some themes change the comment form styling direcly with args.<br />
			                                                            With this option you can insert these specific args in your guestbook form.<br />
			                                                            A list of all available args and there discription can be found <a href="https://codex.wordpress.org/Function_Reference/comment_form#.24args">here</a>.<br />
			                                                            The given text must be valid php array, e.g.<br />
			                                                            <code>array(<br />
			                                                            &nbsp;&nbsp;\'comment_notes_after\' =&gt; \'&lt;p&gt;\'.sprintf(__(\'You may use these &lt;abbr&gt;HTML&lt;/abbr&gt;<br />
			                                                            &nbsp;&nbsp;&nbsp;&nbsp;tags and attributes: %s\'), allowed_tags()).\'&lt;/p&gt;\',<br />
			                                                            &nbsp;&nbsp;\'fields\' =&gt; array(<br />
			                                                            &nbsp;&nbsp;&nbsp;&nbsp;\'author\' =&gt; \'&lt;input type="text" name="author" /&gt;\',<br />
			                                                            &nbsp;&nbsp;&nbsp;&nbsp;\'location\' =&gt; \'&lt;input type="text" name="location" /&gt;\')<br />
			                                                            )</code><br />
			                                                            This setting will be overwritten with all the specific comment form options listed above.')),
			// Comment-list section
			'cgb_clist_order'                 => array('section' => 'comment_list',
			                                           'type'    => 'radio',
			                                           'std_val' => 'default',
			                                           'label'   => __('Comment list order'),
			                                           'caption' => array('default' => 'Standard WP-discussion setting', 'asc' => 'Oldest comments first', 'desc' => 'Newest comments first'),
			                                           'desc'    => __('This option allows you to overwrite the standard order for top level comments for the guestbook pages.')),

			'cgb_clist_child_order'           => array('section' => 'comment_list',
			                                           'type'    => 'radio',
			                                           'std_val' => 'default',
			                                           'label'   => __('Comment list child order'),
			                                           'caption' => array('default' => 'Standard WP-discussion setting', 'asc' => 'Oldest child comments first', 'desc' => 'Newest child comments first'),
			                                           'desc'    => __('This option allows you to overwrite the standard order for all child comments for the guestbook pages.')),

			'cgb_clist_default_page'          => array('section' => 'comment_list',
			                                           'type'    => 'radio',
			                                           'std_val' => 'default',
			                                           'label'   => __('Comment list default page'),
			                                           'caption' => array('default' => 'Standard WP-discussion setting', 'first' => 'First page', 'last' => 'Last page'),
			                                           'desc'    => __('This option allows you to overwrite the standard default page for the guestbook pages.')),

			'cgb_clist_show_all'              => array('section' => 'comment_list',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '',
			                                           'label'   => __('Show all comments'),
			                                           'caption' => __('Show comments of all posts and pages'),
			                                           'desc'    => __('Normally only the comments of the actual guestbook site are shown.<br />
			                                                            But with this option you can enable to show the comments of all posts and pages on the guestbook pages.<br />
			                                                            It is recommended to enable "Comment Adjustment" in Section "Comment html code" if you enable this option.<br />
			                                                            There you have the possibility to include a reference to the original page/post in the comment html code.')),

			'cgb_clist_num_pagination'        => array('section' => 'comment_list',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '',
			                                           'label'   => __('Numbered pagination links'),
			                                           'caption' => __('Create a numbered pagination navigation'),
			                                           'desc'    => __('Normally only a next and previous links are shown. But if this option is enabled a numbered list of all the comment pages is displayed.')),

			'cgb_clist_title'                 => array('section' => 'comment_list',
			                                           'type'    => 'text',
			                                           'std_val' => '',
			                                           'label'   => __('Title for the comment list'),
			                                           'desc'    => __('With this option you can specify an additional title which will be displayed in front of the comment list.')),

			'cgb_clist_in_page_content'       => array('section' => 'comment_list',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '',
			                                           'label'   => __('Comment list in page content'),
			                                           'caption' => __('Show the comment list in the page content'),
			                                           'desc'    => __('If this option is enabled the comment list is displayed directly in the post/page content and will be removed from the comment area.<br />
			                                                            This can help you in some cases to display the comment list, for example if your theme does not have a comment area at all.<br />
			                                                            The comment list will be displayed instead of the shortcode, the comment form in the page section will be hidden and the comment form in the comment sections will be displayed before and/or after the comment list like specified in the comment form options.')),

			'cgb_comment_callback'            => array('section' => 'comment_list',
			                                           'type'    => 'text',
			                                           'std_val' => '--func--comment_callback',
			                                           'label'   => __('Comment callback function'),
			                                           'desc'    => __('This option sets the name of comment callback function which outputs the html-code to view each comment.<br />
			                                                            You only require this function if "Guestbook comments adjustment" is enabled and "Comment adjustment" is disabled.<br />
			                                                            Normally this function is set through the selected theme. Comment Guestbook searches for the theme-function and uses this as default.<br />
			                                                            If the theme-function wasn´t found this field will be empty, then the WordPress internal functionality will be used.<br />
			                                                            If you want to insert the function of your theme manually, you can find the name normally in file "functions.php" of your theme.<br />
			                                                            Often it is called "themename_comment", e.g. "twentyeleven_comment" for twentyeleven theme.')),

			'cgb_clist_styles'                => array('section' => 'comment_list',
			                                           'type'    => 'textarea',
			                                           'rows'    => 6,
			                                           'std_val' => '',
			                                           'label'   => __('Comment list styles'),
			                                           'desc'    => __('With this option you can specify custom css styles for the guestbook comment list.<br />
			                                                            Enter all required styles like you would do it in a css file, e.g.:<br />
			                                                            <code>ol.commentlist { list-style:none; }<br />&nbsp;ul.children { list-style-type:circle; }</code>')),
			// Comment html code
			'cgb_comment_adjust'              => array('section' => 'comment_html',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '',
			                                           'label'   => __('Comment adjustment'),
			                                           'caption' => __('Adjust the html-output of each comment'),
			                                           'desc'    => __('This option specifies if the comment html code should be replaced with the "Comment html code" below on the guestbook page.<br />
	 		                                                            If "Guestbook comments adjustment" in "General settings" is disabled this option has no effect.')),

			'cgb_comment_html'                => array('section' => 'comment_html',
			                                           'type'    => 'textarea',
			                                           'rows'    => '15',
			                                           'std_val' => '--func--comment_html',
			                                           'label'   => __('Comment html code'),
			                                           'desc'    => __('This option specifies the html code for each comment, if "Comment adjustment" is enabled.<br />
			                                                            You can use php-code to get the required comment data. The following variables and objects are availabe:<br />
			                                                            - <code>$l10n_domain</code> ... Use this php variable to get the "Domain for translation" value.<br />
			                                                            - <code>$comment</code> ... This objects includes all available data of the comment. You can use all available fields of "get_comment" return object listed in <a href="http://codex.wordpress.org/Function_Reference/get_comment" target="_blank">relevant wordpress codex site</a>.<br />
			                                                            - <code>$is_comment_from_other_page</code> ... This boolean variable gives you information if the comment was created in another page or post.<br />
			                                                            - <code>$other_page_title</code> ... With this variable you have access to the Page name of a commente created in another page or post.<br />
			                                                            - <code>$other_page_link</code> ... With this variable you can include a link to the original page of a comment created in another page or post.<br />
			                                                            Wordpress provides some additional functions to access the comment data (see <a href="http://codex.wordpress.org/Function_Reference#Comment.2C_Ping.2C_and_Trackback_Functions" target="_blank">wordpress codex</a> for datails).<br />
			                                                            The code given as an example is a slightly modified version of the twentyeleven theme.<br />
			                                                            If you want to adapt the code to your theme you can normally find the theme template in the file "functions.php" in your theme directory.<br />
			                                                            E.g. for twentyeleven the function is called "twentyeleven_comment".<br />
			                                                            If you have enabled the option "Show all comments" it is recommended to enable "Comment adjustment" and add a link to the original page of the comment.<br />
			                                                            Example: <code>if($is_comment_from_other_page && "0" == $comment->comment_parent) { echo \' \'.__(\'Link to page:\', $l10n_domain).\' \'.$other_page_link; }</code>')),
			// Message after new comment
			'cgb_cmessage_text'               => array('section' => 'cmessage',
			                                           'type'    => 'text',
			                                           'std_val' => __('Thanks for your comment'),
			                                           'label'   => __('Message text'),
			                                           'desc'    => __('This option allows you to change the text for the message after a new comment.')),

			'cgb_cmessage_type'               => array('section' => 'cmessage',
			                                           'type'    => 'radio',
			                                           'std_val' => 'inline',
			                                           'label'   => __('Message type'),
			                                           'caption' => array('inline' => 'Show the message inline', 'overlay' => 'Show the message in overlay'),
			                                           'desc'    => __('This option allows to change the format of the message after a new comment.<br />
			                                                            With "inline" the message is shown directly below the comment in a div added via javascript.<br />
			                                                            With "overlay" the message is shown in an overlay div.<br />
			                                                            The message will be slided in with an animation and after a short time the message will be slided out.')),

			'cgb_cmessage_duration'           => array('section' => 'cmessage',
			                                           'type'    => 'text',
			                                           'std_val' => '3000',
			                                           'label'   => __('Message duration'),
			                                           'desc'    => __('How long should the message after a new comment should be displayed?<br />
			                                                            Normally the message after a new comment will be removed after a certain time.<br />
			                                                            You can define this duration with in milliseconds.<br />
			                                                            Set the value to 0 if you do not want to hide the message.')),

			'cgb_cmessage_styles'             => array('section' => 'cmessage',
			                                           'type'    => 'textarea',
			                                           'std_val' => 'background-color:rgb(255, 255, 224);&#10;border-color:rgb(230, 219, 85);&#10;color:rgb(51, 51, 51);&#10;padding:6px 20px;&#10;text-align:center;&#10;border-radius:5px;&#10;border-width:1px;&#10;border-style:solid',
			                                           'label'   => __('Message styles'),
			                                           'desc'    => __('With this option you can define the css styles for the message after a new comment.<br />
			                                                            The given code will be used for the style attribute of the message surrounding div tag.')),
			// Comments in other pages/posts
			'cgb_page_add_cmessage'           => array('section' => 'page_comments',
			                                           'type'    => 'checkbox',
			                                           'std_val' => '',
			                                           'label'   => __('Message after comment'),
			                                           'caption' => __('Show a "Thank you" message after a new comment'),
			                                           'desc'    => __('If this option is enabled a message will be shown after a new comment was made.<br />
			                                                            There are many additional options availabe to change the message text and format in the "Message after new comment" section.')),
		);
	}

	public function init() {
		add_action('admin_init', array(&$this, 'register'));
	}

	public function register() {
		foreach($this->options as $oname => $o) {
			register_setting('cgb_'.$o['section'], $oname);
		}
	}

/*
	public function set($name, $value) {
		if(isset($this->options[$name])) {
			return update_option($name, $value);
		}
		else {
			return false;
		}
	}
*/
	public function get($name) {
		if(isset($this->options[$name])) {
			// set std_val, if a function is used to set the value
			if(substr($this->options[$name]['std_val'], 0, 8) == '--func--') {
				$this->options[$name]['std_val'] = call_user_func(array('cgb_options', substr($this->options[$name]['std_val'], 8)));
			}
			return get_option($name, $this->options[$name]['std_val']);
		}
		else {
			return null;
		}
	}

	/**
	 * Upgrades renamed or modified options to the actual version
	 *
	 * Version 0.5.1 to 0.6.0:
	 *   cgb_clist_adjust -> cgb_adjust_output
	 *   cgb_cmessage -> splitted up in cgb_add_cmessage and cgb_page_add_cmessage
	 */
	public function version_upgrade() {
		$value = get_option('cgb_clist_adjust', null);
		if(null != $value) {
			add_option('cgb_adjust_output', $value);
			delete_option('cgb_clist_adjust');
		}
		$value = get_option('cgb_cmessage', null);
		if(null != $value) {
			if('default' != $value) {
				add_option('cgb_add_cmessage', '1');
			}
			if('always' == $value) {
				add_option('cgb_page_add_cmessage', '1');
			}
			delete_option('cgb_cmessage');
		}
	}

	private function comment_callback() {
		$func = get_stylesheet().'_comment';
		if(function_exists($func)) {
			return $func;
		}
		else {
			return '';
		}
	}

	private function comment_html() {
		// use 2 spaces instead of 1 tab to have a better view in the options dialog
		$out = '<footer class="comment-meta">
<div class="comment-author vcard">
<?php
	$avatar_size = 68;
	if ("0" != $comment->comment_parent)
		$avatar_size = 39;
	echo get_avatar($comment, $avatar_size);
	printf(\'<a href="%1$s"><time datetime="%2$s">%3$s</time></a>\',
		esc_url(get_comment_link($comment->comment_ID)),
		get_comment_time("c"),
		sprintf(__(\'%1$s at %2$s<br />\', $l10n_domain), get_comment_date(), get_comment_time()));
	printf(\'<span class="fn">%s</span>\', get_comment_author_link());
	if($is_comment_from_other_page && "0" == $comment->comment_parent)
		echo \' \'.__(\'in\', $l10n_domain).\' \'.$other_page_link;
	edit_comment_link(__("Edit", $l10n_domain), \'<span class="edit-link">\', "</span>"); ?>
</div><!-- .comment-author .vcard -->
<?php if ($comment->comment_approved == "0") : ?>
	<em class="comment-awaiting-moderation"><?php _e("Your comment is awaiting moderation.", $l10n_domain); ?></em>
	<br />
<?php endif; ?>
</footer>
<div class="comment-content"><?php comment_text(); ?></div>
<div class="reply">
	<?php comment_reply_link(array_merge($args, array("reply_text" => __("Reply <span>&darr;</span>", $l10n_domain), "depth" => $depth, "max_depth" => $args["max_depth"]))); ?>
</div><!-- .reply -->';
		return $out;
	}
}
