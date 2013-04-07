=== Comment Guestbook ===
Contributors: mibuthu
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W54LNZMWF9KW2
Tags: comment, guestbook, site, comments, integrated, shortcode, modify, list, form
Requires at least: 3.2
Tested up to: 3.5.1
Stable tag: 0.3.1
Plugin URI: http://wordpress.org/extend/plugins/comment-guestbook/
Licence: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a guestbook site which uses the wordpress integrated comments.


== Description ==

The purpose of this plugin is to add a guestbook site which uses the wordpress integrated comments.
Using the wordpress integrated comments system has some important advantages:

* Consistent styling of guestbook form and comment list for every theme you are using.
* All wordpress comment features are available for the guestbook comments also (e.g. E-Mail Notification, listing in "All Comments" on admin page,...)
* All plugins which are modifying the comment list or comment form will also work with Comment Guestbook automatically (e.g. a captcha plugin for antispam).

Other features and options:

* Setup comment form position (in page, above comment list, below comment list). You can also use more than one of them.
* Ajdust the comment order to your requirements.
* Option to show numbered pagination links for the comment list.
* Ajdust the comment html-code for the guestbook page.
* Option to include all comments of your site in the comment list on the guestbook page.
* Sidebar widget to show recent comments with a lot of options

Simply insert the shortcode [comment-guestbook] into a page to enable this plugin.

On the admin page (goto Comments -> Guestbook) you can find a detailed description and a lot options to modify the output.


If you want to follow the development status have a look at the [git-repository on github](https://github.com/mibuthu/wp-comment-guestbook "wp-comment-guestbook git-repository").


== Installation ==

The easiest version of installing is to go to the admin page. There you can install new plugins in the menu Plugins -> Add new. Search for "Comment Guestbook" and press "Install now".

If you want to install the plugin manually download the zip-file and extract the files in your wp-content/plugins folder.


== Frequently Asked Questions ==

= Where can I find the settings to manipulate the output (e.g. of the comment list)? =
You can find a lot of options on the admin page in the submenu "Comments" -> "Guestbook". There you have the possibility to change the output of e.g the comment list only for the guestbook page, independent from the general Wordpress settings.

= I have included the shortcode in my guestbook page but the comment form and/or the comment list are not appearing. =
Please check if comments are enabled for your guestbook page. There are several places to change these setting:

* General wordpress discussion setting (Settings -> Discussion -> "Allow people to post comments on new articles"): This setting changes the behavior for all pages and post, but can be overwritten from the settings below.
* Post/page discussion setting (Discussion box -> "Allow comments." in post/page edit screen): This setting overwrites the general wordpress setting for each page or post. If you cannot see the Discussion box you have to enable it in the Screen Option.
* Theme settings: A lot of themes have their own options for displaying the comment list. They often will overwrite the wordpress settings.
* Guestbook settings (Comments -> Guestbook -> General Settings -> "Guestbook comment status": This option will overwrite the wordpress settings for the guestbook page. But the theme settings can still causes problems.

= Can I call the shortcode directly via php e.g. for my own template, theme or plugin? =
Yes, you can create an instance of the "sc_comment_guestbook" class which located in "php/sc_comment-guestbook.php" in the plugin folder and call the function show_html($atts).With $atts you can specify all the shortcode attributes you require. Another possibility would be to call the wordpress function "do_shortcode()".


== Screenshots ==

1. Admin page (comment-list settings)
2. Admin page (comment-form settings)
3. Widget options on admin page
4. Example guestbook site
5. Example guestbook widget

== Changelog ==

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
