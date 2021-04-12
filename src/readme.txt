=== Comment Guestbook ===
Contributors: mibuthu
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W54LNZMWF9KW2
Tags: comment, guestbook, site, comments, integrated, shortcode, modify, list, form
Requires at least: 4.9
Tested up to: 5.7
Requires PHP: 5.6
Stable tag: 0.8.0
Plugin URI: https://wordpress.org/plugins/comment-guestbook/
Licence: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a guestbook page which uses the wordpress integrated comments.


== Description ==

The purpose of this plugin is to add a guestbook site which uses the wordpress integrated comments.
Using the wordpress integrated comments system has some important advantages:

* Consistent styling of guestbook form and comment list for every theme you are using.
* All wordpress comment features are available for the guestbook comments also (e.g. E-Mail Notification, listing in "All Comments" on admin page,...)
* All plugins which are modifying the comment list or comment form will also work with Comment Guestbook automatically (e.g. a captcha plugin for antispam).

= Other features and options: =
* Setup comment form position (in page, above comment list, below comment list). You can also use more than one.
* Adjust the comment form (change texts, hide elements)
* Ajdust the comment order (newest first, oldest first)
* Option to show numbered pagination links for the comment list
* Ajdust the comment html-code for the guestbook page
* Option to include all comments of your site in the comment list on the guestbook page
* Sidebar widget to show recent comments with a lot of options
* Show a message after a new comment was made. This option you can also enable for all post/pages in your blog

= Usage: =
Simply insert the shortcode `[comment-guestbook]` into a page to enable this plugin.

On the admin page (goto Comments -> About Guestbook) you can find a detailed description. In the admin area you can find a settings page with a lot of options to modify the behavior and output.

= Development: =
If you want to follow the development status have a look at the [git-repository on github](https://github.com/mibuthu/wp-comment-guestbook "wp-comment-guestbook at github").
Feel free to add your merge requests there, if you want to help to improve the plugin.

= Translations: =
Please help translating this plugin into multiple languages.
You can submit your translations at [transifex.com](https://www.transifex.com/projects/p/wp-comment-guestbook "wp-comment-guestbook at transifex").
There the source strings will always be in sync with the actual development version.


== Installation ==

The easiest way to install is using the wordpress plugin installation mechanism. On the admin page you can install new plugins in "Plugins" -> "Add new". Search for "Comment Guestbook" and press "Install now".

If you want to install the plugin manually download the zip-file and extract the files in your wp-content/plugins folder.


== Frequently Asked Questions ==

= Where can I find the settings to manipulate the output (e.g. of the comment list)? =
You can find a lot of options on the admin page in the submenu "Comments" -> "Guestbook". There you have the possibility to change the output of e.g the comment list only for the guestbook page, independent from the general Wordpress settings.

= I have included the shortcode in my guestbook page but the comment form and/or the comment list are not appearing. =
Please check if comments are enabled for your guestbook page. There are several places to change these setting:

* General wordpress discussion setting (Settings -> Discussion -> "Allow people to post comments on new articles"): This setting changes the behavior for all pages and post, but can be overwritten by the settings below.
* Post/page discussion setting (Discussion box -> "Allow comments." in post/page edit screen): This setting overrides the general wordpress setting for each page or post. If you cannot see the Discussion box you have to enable it in the Screen Option.
* Theme settings: A lot of themes have their own options for displaying the comment list. They often will override the wordpress settings.
* Guestbook settings (Comments -> Guestbook -> General Settings -> "Guestbook comment status": This option will override the wordpress settings for the guestbook page. But the theme settings can still causes problems.

If you still have problems after checking all these possibilities there is one more option available in the Guestbook settings (Comments -> Guestbook -> Comment-list settings -> "Show the comment list in the page content".
If you enable this option the comment list will be displayed in the page content instead of the seperate comment section. After that the comment list should be displayed also with themes which specifies no comment section for pages.
Have a look at the option description on the settings page for detailed information.

= Can I call the shortcode directly via php e.g. for my own template, theme or plugin? =
Yes, you can create an instance of the "CGB_Shortcode" class which located in "includes/shortcode.php" in the plugin folder and call the function show_html($atts).With $atts you can specify all the shortcode attributes you require. Another possibility would be to call the wordpress function "do_shortcode()".


== Screenshots ==

1.  Admin about page
2.  Admin settings page (General settings tab)
3.  Admin settings page (Comment-form settings tab)
4.  Admin settings page (Comment-list settings tab)
5.  Admin settings page (Comment html code tab)
6.  Admin settings page (Message after new comment tab)
7.  Admin settings page (Comments in other posts/pages tab)
8.  Widget options on admin page
9.  Example guestbook site
10. Example guestbook widget

== Changelog ==

= 0.8.0 (2021-04-12) =
* raised minimum required PHP version to 5.6
* some internal code refactoring (namespaces, file structure, ...)
* added a wrapper div with a specific class for the notes-before and notes-after the comment form

= 0.7.5 (2020-10-26) =
* fixed plugin error with some themes and in the customizer
* fixed show comment list in page content option

= 0.7.4 (2020-09-15) =
* complete code rewrite:
  * switched to wordpress coding standard
  * added comments for all files, classes and functions
  * code check with phpcs and phan
  * changed folder structure
* added codeception acceptance tests for most plugin options (will be completed in a future version)
* improved some help texts
* added dutch translations
* updated translations

= 0.7.3 (2017-02-11) =
* added option to ignore comment moderation in the guestbook page
* added option to ignore name/email requirement in the guestbook page
* fixed incorrect html code in the widget when text truncate is enabled
* fixed displayed page number after a new comment with under some conditions
* prepared more strings for translation
* updated german translation
* moved screenshots to assets folder to reduce download size

= 0.7.2 (2017-01-21) =
* added options to set collapsed comment forms and to set a link text for the button to expand the form
* added options to override pagination and per page settings for the guestbook site
* fixed option "Guestbook comment registration"
* fixed option "Remove email field"
* only print form styles once
* security improvements for external links
* added greek translation (thanks to Spirossmil, translation not fully completed yet)

= 0.7.1 (2015-07-25) =
* added option to add manual args for wp_comment_list function
* fixes / improvements in truncate function
* updated some helptexts
* added some additional german translations

= 0.7.0 (2015-05-25) =
* added multi language support
* added german translation (not fully complete yet)
* added option to change "You must login" message

= 0.6.9 (2015-03-28) =
* added additional options for other pages/posts to remove mail or website field in comment form

= 0.6.8 (2014-12-13) =
* fixed problems with manual truncate function and unicode characters

= 0.6.7 (2014-11-16) =
* added automatic truncate support of texts via css
* added unicode support for manual truncate function
* fixed problem with special characters in textarea fields of settings page

= 0.6.6 (2014-09-01) =
* added option to override registration requirement for comments on guestbook pages (this option is enabled by default)
* added option to enable/disable threaded comments on guestbook pages independent from wordpress settings
* fixed problem that e-mail was still mandatory if e-mail field is hidden

= 0.6.5 (2014-06-08) =
* fixed an issue in comment list custom styles code which breaks the comment list

= 0.6.4 (2014-06-08) =
* added options for custom css styles in comment form and comment list
* added option to show an additional title in front of the comment list

= 0.6.3 (2014-04-13) =
* added option to specify comment form args directly
* show more number in pagination
* small fix in truncate function
* small css changes on admin page
* added check if comment_callback_function exists

= 0.6.2 (2014-03-09) =
* added option to hide email and website form field
* added option to change comment label in comment form
* apply comment form customization also in page/post form

= 0.6.1 (2014-03-01) =
* added option to change label of submit button
* added option to change label of reply link
* fixed option default value in descriptions
* fixed readme

= 0.6.0 (2014-02-22) =
* Splitted admin page in about and settings page
* Moved settings page to "Settings"
* Rearranged settings sections
* Added some options to change the comment form

= 0.5.1 (2014-01-06) =
* Fix to show the comment list in page content at the correct position
* Fix an issue with comments_open filter in combination with some other plugins
* Added escaping of html title attribute in widget

= 0.5.0 (2013-08-24) =
* Added option to show comment list in page content (to support users with theme issues)
* A lot of code cleanup
* Fixed showing wrong page after entering a new comment
* Avoid displaying 2 comment forms in succession

= 0.4.1 (2013-08-11) =
* Added tooltip help texts for the widget options
* Added additional options for message after a new comment (duration, style)
* Removed cmessage attribute from all link URLs after an new comment was made

= 0.4.0 (2013-05-12) =
* Added the possibility to show a message after a new comment was made
* Minor security improvements

= 0.3.1 (2013-04-07) =
* Fixed link to comment in widget for some special settings
* Added widget option to truncate author
* Added widget option to truncate page title
* Added widget option to change the date format
* A lot of code cleanup in widget class

= 0.3.0 (2013-03-30) =
* Fixed required user capability to change guestbook options
* Added option to show comments of all pages/posts on guestbook page
* Added widget to show recent comments with a lot of options
* Improved some help texts on admin page

= 0.2.2 (2012-12-27) =
* Added section for comment form settings on admin page
* Added options to define comment form position(s)
* Fixed html-code on front page
* Small html-code fixes on admin page

= 0.2.1 (2012-11-11) =
* Fixed overwriting of Allow comments status
* Fixed redirected page after creating a new comment when Comment list order is Newest comments first
* Added a new option to create a numbered pagination navigation

= 0.2.0 (2012-11-03) =
* Internal code changes
* Added several new options to modify the output (find details on the admin page)

= 0.1.2 (2012-09-01) =
* Fixed all php warnings
* Added screenshots

= 0.1.1 (2012-06-24) =
* Rearrangement of settings on admin page via different tabs

= 0.1.0 (2012-04-30) =
* Allow adjustment of comment html code
* Added the options "Comment adjustment", "Comment html code" and "Domain for translation"

= 0.0.2 (2012-04-08) =
* Allow adjustment of comment list output
* Added the options "adjust comment list output" and "comment callback function" on the admin page

= 0.0.1 (2012-04-03) =
* Initial release

== Upgrade Notice ==

The easiest way to upgrade is the use the wordpress integrated update mechanism. No additional steps are required.
