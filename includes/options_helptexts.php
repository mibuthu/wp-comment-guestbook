<?php
if(!defined('WPINC')) {
	exit;
}

$options_helptexts = array(
	// General section
	'cgb_ignore_comments_open'        => array('type'    => 'checkbox',
	                                           'label'   => __('Guestbook comment status','comment-guestbook'),
	                                           'caption' => __('Always allow comments on the guestbook page','comment-guestbook'),
	                                           'desc'    => __('If this option is enabled comments on the guestbook page will always be allowed. Hence the corresponding WordPress and page standard settings will be overruled in the guestbook page.','comment-guestbook')),

	'cgb_ignore_comment_registration' => array('type'    => 'checkbox',
	                                           'label'   => __('Guestbook comment registration','comment-guestbook'),
	                                           'caption' => __('Allow comments on the guestbook page without registration','comment-guestbook'),
	                                           'desc'    => __('If this option is enabled comment will be allowed without registration. Hence the corresponding WordPress standard setting will be overruled in the guestbook page.','comment-guestbook')),

	'cgb_ignore_comment_moderation'   => array('type'    => 'checkbox',
	                                           'label'   => __('Guestbook comment moderation','comment-guestbook'),
	                                           'caption' => __('Disable moderation of new comments on the guestbook page','comment-guestbook'),
	                                           'desc'    => __('If this option is enabled the no approval of new comments is required. Hence the corresponding WordPress standard setting will be overruled in the guestbook page.','comment-guestbook')),

	'cgb_threaded_gb_comments'        => array('type'    => 'radio',
	                                           'label'   => __('Enable threaded guestbook comments','comment-guestbook'),
	                                           'caption' => array('default' => __('Standard WP-discussion setting','comment-guestbook'), 'enabled' => __('Enabled','comment-guestbook'), 'disabled' => __('Disabled','comment-guestbook')),
	                                           'desc'    => __('With this option the WordPress standard setting for threaded comments can be overruled for the guestbook page. If enabled a reply to a available comment is allowed, when disabled it isn´t.<br />
	                                                            You can define the allowed depth of threaded comments in the WordPress discussion settings. There also the corresponding WordPress standard value for all comments can be changed.','comment-guestbook')),

	'cgb_adjust_output'               => array('type'    => 'checkbox',
	                                           'label'   => __('Comments adjustment','comment-guestbook'),
	                                           'caption' => __('Adjust the guestbook comments output','comment-guestbook'),
	                                           'desc'    => sprintf(__('This option specifies if the standard WordPress function %1$s shall be overwritten.<br />
	                                                                    Activating this option is required for many settings in the other sections (see options and sections descriptions).','comment-guestbook'),
	                                                                '<a href="https://codex.wordpress.org/Function_Reference/wp_list_comments">wp_list_comments</a>')),

	'cgb_l10n_domain'                 => array('type'    => 'text',
	                                           'label'   => __('Domain for translation','comment-guestbook'),
	                                           'desc'    => __('This option defines the domain for translation for the adjusted guestbook comments output.','comment-guestbook').'<br />'.
	                                                        sprintf(__('For example if you want to use the translations of the twentyeleven theme the correct value is %1$s.','comment-guestbook'), '"twentyeleven"').'<br />'.
	                                                        sprintf(__('Have a look at %1$sthe corresponding description in the WordPress codex%2$s for more details.','comment-guestbook'), '<a href="http://codex.wordpress.org/Function_Reference/_2" target="_blank" rel="noopener">', '</a>')),
	// Comment-form section
	'cgb_form_below_comments'         => array('type'    => 'checkbox',
	                                           'label'   => __('Form below comments','comment-guestbook'),
	                                           'caption' => __('Show a comment form in the comment area below the comments','comment-guestbook'),
	                                           'desc'    => __('With this option you can add a comment form in the comment section below the comment list.','comment-guestbook')),

	'cgb_form_above_comments'         => array('type'    => 'checkbox',
	                                           'label'   => __('Form above comments','comment-guestbook'),
	                                           'caption' => __('Show a comment form in the comment area above the comments','comment-guestbook'),
	                                           'desc'    => __('With this option you can add a comment form in the comment section above the comment list.','comment-guestbook')),

	'cgb_form_in_page'                => array('type'    => 'checkbox',
	                                           'label'   => __('Form in page/post','comment-guestbook'),
	                                           'caption' => __('Show a comment form in the page/post area','comment-guestbook'),
	                                           'desc'    => __('With this option you can add a comment form in the page or post area. The form will be displayed at the position of the shortcode.','comment-guestbook').'<br />'.
	                                                        sprintf(__('If the option %1$s is enabled, this form will not be displayed to avoid showing 2 forms in succession.','comment-guestbook'), '"'.__('Form above comments','comment-guestbook').'"')),

	'cgb_form_expand_type'            => array('type'    => 'radio',
	                                           'label'   => __('Collapsed comment form','comment-guestbook'),
	                                           'caption' => array('false' => __('form is not collapsed (default)','comment-guestbook'), 'static' => __('collapsed form with static expansion','comment-guestbook'), 'animated' => __('collapsed form with animated expansion','comment-guestbook')),
	                                           'desc'    => __('With this option you can collapse (hide) the comment forms by default and add a link which expands the form.','comment-guestbook').'<br />'.
	                                                        __('There are 2 options for expansion available: static and animated. Animated shows a small animation during the form expansion.','comment-guestbook')),

	'cgb_form_expand_link_text'       => array('type'    => 'text',
	                                           'label'   => __('Link text for form expansion','comment-guestbook'),
	                                           'desc'    => sprintf(__('With this option you can set the link text to expand the comment form if %1$s is enabled.','comment-guestbook'), '"'.__('Collapsed comment form','comment-guestbook').'"')),

	'cgb_add_cmessage'                => array('type'    => 'checkbox',
	                                           'label'   => __('Message after new comments','comment-guestbook'),
	                                           'caption' => __('Show a "Thank you" message after a new guestbook comment','comment-guestbook'),
	                                           'desc'    => __('If this option is enabled a message will be shown after a new comment was made.','comment-guestbook').'<br />'.
	                                                        sprintf(__('There are many additional options availabe to change the message text and format in the %1$s section.','comment-guestbook'), '"'.__('Message after new comments','comment-guestbook').'"')),

	'cgb_form_require_no_name_mail'   => array('type'    => 'checkbox',
	                                           'label'   => __('Comment author name/email','comment-guestbook'),
	                                           'caption' => __('Override Name and Email field requirement','comment-guestbook'),
	                                           'desc'    => __('If this option is enabled the name and email field is not a required field in the comment guestbook form (independent of the WordPress standard discussion setting).','comment-guestbook')),

	'cgb_form_remove_mail'            => array('type'    => 'checkbox',
	                                           'label'   => __('Remove Email field','comment-guestbook'),
	                                           'caption' => __('Remove the Email field in comment guestbook form','comment-guestbook'),
	                                           'desc'    => __('If this option is enabled the email field will be removed in the comment guestbook form.','comment-guestbook')),

	'cgb_form_remove_website'         => array('type'    => 'checkbox',
	                                           'label'   => __('Remove Website field','comment-guestbook'),
	                                           'caption' => __('Remove the Website url field in comment guestbook form','comment-guestbook'),
	                                           'desc'    => __('If this option is enabled the website url field will be removed in the comment guestbook form.','comment-guestbook')),

	'cgb_form_comment_label'          => array('type'    => 'text',
	                                           'label'   => __('Label for comment field','comment-guestbook'),
	                                           'desc'    => __('With this option you can specify a custom label for the comment field.','comment-guestbook').'<br />'.
	                                                        sprintf(__('Set the value to %1$s for the wordpress default label. Enter an empty string to hide the label.','comment-guestbook'),'"default"')),

	'cgb_form_title_reply'            => array('type'    => 'text',
	                                           'label'   => __('Comment form title','comment-guestbook'),
	                                           'desc'    => __('With this option you can specify a custom title for the comment form (when not replying to a comment).','comment-guestbook').'<br />'.
	                                                        sprintf(__('Set the value to %1$s for the wordpress default title. Enter an empty string to hide the title.','comment-guestbook'),'"default"')),

	'cgb_form_title_reply_to'         => array('type'    => 'text',
	                                           'label'   => __('Reply comment form title','comment-guestbook'),
	                                           'desc'    => __('With this option you can specify a custom title for the comment form (when replying to a comment).','comment-guestbook').'<br />'.
	                                                        sprintf(__('Set the value to %1$s for the wordpress default title. Enter an empty string to hide the title.','comment-guestbook'),'"default"')),

	'cgb_form_notes_before'           => array('type'    => 'text',
	                                           'label'   => __('Notes before form fields','comment-guestbook'),
	                                           'desc'    => __('With this option you can override the text for the notes before the comment form fields.','comment-guestbook').'<br />'.
	                                                        sprintf(__('Set the value to %1$s for the wordpress default notes. Enter an empty string to hide the notes.','comment-guestbook'),'"default"')),

	'cgb_form_notes_after'            => array('type'    => 'text',
	                                           'label'   => __('Notes after form fields','comment-guestbook'),
	                                           'desc'    => __('With this option you can override the text for the notes after the comment form fields (and before the submit button).','comment-guestbook').'<br />'.
	                                                        sprintf(__('Set the value to %1$s for the wordpress default notes. Enter an empty string to hide the notes.','comment-guestbook'),'"default"')),

	'cgb_form_label_submit'           => array('type'    => 'text',
	                                           'label'   => __('Label of submit button','comment-guestbook'),
	                                           'desc'    => __('With this option you can override the label of the comment form submit button.','comment-guestbook').'<br />'.
	                                                        sprintf(__('Set the value to %1$s or to an empty string for the wordpress default label.','comment-guestbook'),'"default"')),

	'cgb_form_cancel_reply'           => array('type'    => 'text',
	                                           'label'   => __('Label for cancel reply link','comment-guestbook'),
	                                           'desc'    => __('With this option you can override the label for the comment form cancel reply link.','comment-guestbook').'<br />'.
	                                                        sprintf(__('Set the value to %1$s or to an empty string for the wordpress default label.','comment-guestbook'),'"default"')),

	'cgb_form_must_login_message'     => array('type'    => 'text',
	                                           'label'   => __('"Must login" message','comment-guestbook'),
	                                           'desc'    => __('With this option you can override the message which will be displayed when the user must login to add a new comment.','comment-guestbook').'<br />'.
	                                                        sprintf(__('The term %1$s will be replaced by the url to login. You can specify it in your text if you want to include a link to the login page.','comment-guestbook'),'"%s"').'<br />'.
	                                                        __('Example (standard text)','comment-guestbook').': <code>You must be &lt;a href="%s"&gt;logged in&lt;/a&gt; to post a comment.</code><br />'.
	                                                        sprintf(__('Set the value to %1$s or to an empty string for the wordpress default message.','comment-guestbook'),'"default"')),

	'cgb_form_styles'                 => array('type'    => 'textarea',
	                                           'rows'    => 6,
	                                           'label'   => __('Comment form styles','comment-guestbook'),
	                                           'desc'    => __('With this option you can specify custom css styles for the guestbook comment form.','comment-guestbook').'<br />'.
	                                                        __('Example','comment-guestbook').':<br />
	                                                            <code>.form-submit { text-align:center; }<br />&nbsp;#submit { font-weight:bold; }</code>'),

	'cgb_form_args'                   => array('type'    => 'textarea',
	                                           'rows'    => 10,
	                                           'label'   => __('Comment form args','comment-guestbook'),
	                                           'desc'    => __('With this option you can specify args for the comment form.','comment-guestbook').'<br />'.
	                                                        __('This can be required because some themes change the comment form styling direcly with args.','comment-guestbook').'<br />'.
	                                                        __('With this option you can insert these specific args for the guestbook form.','comment-guestbook').'<br />'.
	                                                        sprintf(__('A list of all available args and there discription can be found %1$s.','comment-guestbook'), '<a href="https://codex.wordpress.org/Function_Reference/comment_form#.24args">'.__('here','comment-guestbook').'</a>').'<br />'.
	                                                        __('The given text must be valid php array, e.g.:','comment-guestbook').'<br />
	                                                            <code>array(<br />
	                                                            &nbsp;&nbsp;\'comment_notes_after\' =&gt; \'&lt;p&gt;\'.sprintf(__(\'You may use these &lt;abbr&gt;HTML&lt;/abbr&gt;<br />
	                                                            &nbsp;&nbsp;&nbsp;&nbsp;tags and attributes: %s\'), allowed_tags()).\'&lt;/p&gt;\',<br />
	                                                            &nbsp;&nbsp;\'fields\' =&gt; array(<br />
	                                                            &nbsp;&nbsp;&nbsp;&nbsp;\'author\' =&gt; \'&lt;input type="text" name="author" /&gt;\',<br />
	                                                            &nbsp;&nbsp;&nbsp;&nbsp;\'location\' =&gt; \'&lt;input type="text" name="location" /&gt;\')<br />
	                                                            )</code><br />'.
	                                                         __('The given args will be overwritten with all the specific comment form options listed above.','comment-guestbook')),
	// Comment-list section
	'cgb_clist_order'                 => array('type'    => 'radio',
	                                           'label'   => __('Comment list order','comment-guestbook'),
	                                           'caption' => array('default' => 'Standard WP-discussion setting', 'asc' => 'Oldest comments first', 'desc' => 'Newest comments first'),
	                                           'desc'    => __('This option allows you to override the standard order for top level comments for the guestbook pages.','comment-guestbook')),

	'cgb_clist_child_order'           => array('type'    => 'radio',
	                                           'label'   => __('Comment list child order','comment-guestbook'),
	                                           'caption' => array('default' => 'Standard WP-discussion setting', 'asc' => 'Oldest child comments first', 'desc' => 'Newest child comments first'),
	                                           'desc'    => __('This option allows you to override the standard order for all child comments for the guestbook pages.','comment-guestbook')),

	'cgb_clist_default_page'          => array('type'    => 'radio',
	                                           'label'   => __('Comment list default page','comment-guestbook'),
	                                           'caption' => array('default' => 'Standard WP-discussion setting', 'first' => 'First page', 'last' => 'Last page'),
	                                           'desc'    => __('This option allows you to override the standard default page for the guestbook pages.','comment-guestbook')),

	'cgb_clist_pagination'            => array('type'    => 'radio',
	                                           'label'   => __('Break comments into pages','comment-guestbook'),
	                                           'caption' => array('default' => __('Standard WP-discussion setting','comment-guestbook'), 'false' => __('Disable pagination','comment-guestbook'), 'true' => __('Enable pagination','comment-guestbook')),
	                                           'desc'    => __('With this option you to override the WordPress default setting for the guestbook page if the comments shall be broken into pages.','comment-guestbook')),

	'cgb_clist_per_page'              => array('type'    => 'number',
	                                           'label'   => __('Comments per page','comment-guestbook'),
	                                           'range'   => array('min_value' => '0'),
	                                           'desc'    => __('This option allows you to override the standard number of comments listed per page for the guestbook pages (if pagination is enabled).<br />
	                                                            The default value is "0" to use the WordPress default setting (see WP Discussion options)','comment-guestbook')),

	'cgb_clist_show_all'              => array('type'    => 'checkbox',
	                                           'label'   => __('Show all comments','comment-guestbook'),
	                                           'caption' => __('Show comments of all posts and pages','comment-guestbook'),
	                                           'desc'    => __('Normally only the comments of the actual guestbook site are shown.<br />
	                                                            But with this option you can enable to show the comments of all posts and pages on the guestbook pages.<br />
	                                                            It is recommended to enable "Comment Adjustment" in Section "Comment html code" if you enable this option.<br />
	                                                            There you have the possibility to include a reference to the original page/post in the comment html code.')),

	'cgb_clist_num_pagination'        => array('type'    => 'checkbox',
	                                           'label'   => __('Numbered pagination links','comment-guestbook'),
	                                           'caption' => __('Create a numbered pagination navigation','comment-guestbook'),
	                                           'desc'    => __('Normally only a next and previous links are shown. But if this option is enabled a numbered list of all the comment pages is displayed.','comment-guestbook')),

	'cgb_clist_title'                 => array('type'    => 'text',
	                                           'label'   => __('Title for the comment list','comment-guestbook'),
	                                           'desc'    => __('With this option you can specify an additional title which will be displayed in front of the comment list.','comment-guestbook')),

	'cgb_clist_in_page_content'       => array('type'    => 'checkbox',
	                                           'label'   => __('Comment list in page content','comment-guestbook'),
	                                           'caption' => __('Show the comment list in the page content','comment-guestbook'),
	                                           'desc'    => __('If this option is enabled the comment list is displayed directly in the post/page content and will be removed from the comment area.<br />
	                                                            This can help you in some cases to display the comment list, for example if your theme does not have a comment area at all.<br />
	                                                            The comment list will be displayed instead of the shortcode, the comment form in the page section will be hidden and the comment form in the comment sections will be displayed before and/or after the comment list like specified in the comment form options.')),

	'cgb_comment_callback'            => array('type'    => 'text',
	                                           'label'   => __('Comment callback function','comment-guestbook'),
	                                           'desc'    => __('This option sets the name of comment callback function which outputs the html-code to view each comment.<br />
	                                                            You only require this function if "Guestbook comments adjustment" is enabled and "Comment adjustment" is disabled.<br />
	                                                            Normally this function is set through the selected theme. Comment Guestbook searches for the theme-function and uses this as default.<br />
	                                                            If the theme-function wasn´t found this field will be empty, then the WordPress internal functionality will be used.<br />
	                                                            If you want to insert the function of your theme manually, you can find the name normally in file "functions.php" of your theme.<br />
	                                                            Often it is called "themename_comment", e.g. "twentyeleven_comment" for twentyeleven theme.')),

	'cgb_clist_styles'                => array('type'    => 'textarea',
	                                           'rows'    => 6,
	                                           'label'   => __('Comment list styles','comment-guestbook'),
	                                           'desc'    => __('With this option you can specify custom css styles for the guestbook comment list.<br />
	                                                            Enter all required styles like you would do it in a css file, e.g.:<br />
	                                                            <code>ol.commentlist { list-style:none; }<br />&nbsp;ul.children { list-style-type:circle; }</code>')),

	'cgb_clist_args'                  => array('type'    => 'textarea',
	                                           'rows'    => 7,
	                                           'label'   => __('Comment list args','comment-guestbook'),
	                                           'desc'    => __('With this option you can manually specify args for the comment list.<br />
	                                                            A list of all available arguments and there discription can be found <a href="http://codex.wordpress.org/Function_Reference/wp_list_comments#Arguments">here</a>.<br />
	                                                            The given text must be valid php array, e.g.<br />
	                                                            <code>array(<br />
	                                                            &nbsp;&nbsp;\'style\' =&gt; \'div\',<br />
	                                                            &nbsp;&nbsp;\'avatar_size\' =&gt; 45<br />
	                                                            )</code><br />
	                                                            This setting will be overwritten with all the specific comment list options listed above.')),
	// Comment html code
	'cgb_comment_adjust'              => array('type'    => 'checkbox',
	                                           'label'   => __('Comment adjustment','comment-guestbook'),
	                                           'caption' => __('Adjust the html-output of each comment','comment-guestbook'),
	                                           'desc'    => __('This option specifies if the comment html code should be replaced with the "Comment html code" below on the guestbook page.<br />
	                                                            If "Guestbook comments adjustment" in "General settings" is disabled this option has no effect.')),

	'cgb_comment_html'                => array('type'    => 'textarea',
	                                           'rows'    => '15',
	                                           'label'   => __('Comment html code','comment-guestbook'),
	                                           'desc'    => __('This option specifies the html code for each comment, if "Comment adjustment" is enabled.<br />
	                                                            You can use php-code to get the required comment data. The following variables and objects are availabe:<br />
	                                                            - <code>$l10n_domain</code> ... Use this php variable to get the "Domain for translation" value.<br />
	                                                            - <code>$comment</code> ... This objects includes all available data of the comment. You can use all available fields of "get_comment" return object listed in <a href="http://codex.wordpress.org/Function_Reference/get_comment" target="_blank" rel="noopener">relevant wordpress codex site</a>.<br />
	                                                            - <code>$is_comment_from_other_page</code> ... This boolean variable gives you information if the comment was created in another page or post.<br />
	                                                            - <code>$other_page_title</code> ... With this variable you have access to the Page name of a commente created in another page or post.<br />
	                                                            - <code>$other_page_link</code> ... With this variable you can include a link to the original page of a comment created in another page or post.<br />
	                                                            Wordpress provides some additional functions to access the comment data (see <a href="http://codex.wordpress.org/Function_Reference#Comment.2C_Ping.2C_and_Trackback_Functions" target="_blank" rel="noopener">wordpress codex</a> for datails).<br />
	                                                            The code given as an example is a slightly modified version of the twentyeleven theme.<br />
	                                                            If you want to adapt the code to your theme you can normally find the theme template in the file "functions.php" in your theme directory.<br />
	                                                            E.g. for twentyeleven the function is called "twentyeleven_comment".<br />
	                                                            If you have enabled the option "Show all comments" it is recommended to enable "Comment adjustment" and add a link to the original page of the comment.<br />
	                                                            Example: <code>if($is_comment_from_other_page && "0" == $comment->comment_parent) { echo \' \'.__(\'Link to page:\', $l10n_domain).\' \'.$other_page_link; }</code>')),
	// Message after new comment
	'cgb_cmessage_text'               => array('type'    => 'text',
	                                           'label'   => __('Message text','comment-guestbook'),
	                                           'desc'    => __('This option allows you to change the text for the message after a new comment.','comment-guestbook')),

	'cgb_cmessage_type'               => array('type'    => 'radio',
	                                           'label'   => __('Message type','comment-guestbook'),
	                                           'caption' => array('inline' => 'Show the message inline', 'overlay' => 'Show the message in overlay'),
	                                           'desc'    => __('This option allows to change the format of the message after a new comment.<br />
	                                                            With "inline" the message is shown directly below the comment in a div added via javascript.<br />
	                                                            With "overlay" the message is shown in an overlay div.<br />
	                                                            The message will be slided in with an animation and after a short time the message will be slided out.')),

	'cgb_cmessage_duration'           => array('type'    => 'text',
	                                           'label'   => __('Message duration','comment-guestbook'),
	                                           'desc'    => __('How long should the message after a new comment should be displayed?<br />
	                                                            Normally the message after a new comment will be removed after a certain time.<br />
	                                                            You can define this duration with in milliseconds.<br />
	                                                            Set the value to 0 if you do not want to hide the message.')),

	'cgb_cmessage_styles'             => array('type'    => 'textarea',
	                                           'label'   => __('Message styles','comment-guestbook'),
	                                           'desc'    => __('With this option you can define the css styles for the message after a new comment.<br />
	                                                            The given code will be used for the style attribute of the message surrounding div tag.')),
	// Comments in other pages/posts
	'cgb_page_add_cmessage'           => array('type'    => 'checkbox',
	                                           'label'   => __('Message after new comments','comment-guestbook'),
	                                           'caption' => __('Show a "Thank you" message after a new comment','comment-guestbook'),
	                                           'desc'    => __('If this option is enabled a message will be shown after a new comment was made.<br />
	                                                            There are many additional options availabe to change the message text and format in the "Message after new comment" section.')),

	'cgb_page_remove_mail'            => array('type'    => 'checkbox',
	                                           'label'   => __('Remove Email field','comment-guestbook'),
	                                           'caption' => __('Remove the Email field in comment forms','comment-guestbook'),
	                                           'desc'    => __('If this option is enabled the email field will be removed in comment forms.','comment-guestbook')),

	'cgb_page_remove_website'         => array('type'    => 'checkbox',
	                                           'label'   => __('Remove Website field','comment-guestbook'),
	                                           'caption' => __('Remove the Website url field in comment forms','comment-guestbook'),
	                                           'desc'    => __('If this option is enabled the website url field will be removed in comment forms.','comment-guestbook')),
);

$sections_helptexts = array(
	'general'        => array('caption' => __('General settings','comment-guestbook'),
	                          'desc'    => __('Some general settings for this plugin.','comment-guestbook')),
	'comment_form'   => array('caption' => __('Comment-form settings','comment-guestbook'),
	                          'desc'    => __('In this section you can find settings to modify the comment form.','comment-guestbook').'<br />
	                                           <strong>'.__('Attention','comment-guestbook').':</strong><br />
	                                           '.sprintf(__('If you want to change any option in this section you have to enable the option %1$s in %2$s first.','comment-guestbook'), '"'.$options_helptexts['cgb_adjust_output']['label'].'"', '"'.__('General settings','comment-guestbook').'"').'<br />
	                                           '.sprintf(__('Only the options %1$s, %2$s and all comment form modification options are working without it.','comment-guestbook'), '"'.$options_helptexts['cgb_form_in_page']['label'].'"', '"'.$options_helptexts['cgb_add_cmessage']['label'].'"')),
	'comment_list'   => array('caption' => __('Comment-list settings','comment-guestbook'),
	                          'desc'    => __('In this section you can find settings to modify the comments list.','comment-guestbook').'<br />
	                                           <strong>'.__('Attention','comment-guestbook').':</strong><br />
	                                           '.sprintf(__('If you want to change any option in this section you have to enable the option %1$s in %2$s first.','comment-guestbook'), '"'.$options_helptexts['cgb_adjust_output']['label'].'"', '"'.__('General settings','comment-guestbook').'"')),
	'comment_html'   => array('caption' => __('Comment html code','comment-guestbook'),
	                          'desc'    => __('In this section you can change the html code for the comment output in guestbook pages.','comment-guestbook')),
	'cmessage'       => array('caption' => __('Message after new comments','comment-guestbook'),
	                          'desc'    => __('In this section you can find settings to modify the message after a new comment.','comment-guestbook').'<br />
	                                           '.sprintf(__('You can enable the message in %1$s for the guestbook page.','comment-guestbook'), '"'.__('Comment-form settings','comment-guestbook').'"').'<br />
	                                           '.sprintf(__('This options are also valid for all other posts and pages if you enable the option %1$s in the section %2$s.','comment-guestbook'), '"'.$options_helptexts['cgb_page_add_cmessage']['label'].'"', '"'.__('Comments in other posts/pages','comment-guestbook').'"')),
	'page_comments'  => array('caption' => __('Comments in other posts/pages','comment-guestbook'),
	                          'desc'    => __('In this sections you can change the behavior of comments lists and forms in all other posts and pages of your website (exept the guestbook pages).','comment-guestbook').'<br />
	                                           '.__('If you want to change these settings also for guestbook comments please specify the same setting values in the other option tabs.','comment-guestbook'))
);
