<?php

// This class handles all available admin pages
class cgb_admin {

	// show the main admin page as a submenu of "Links"
	public static function show_main() {
		if (!current_user_can('edit_posts'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		//require_once('sc_comment-guestbook.php');

		$out ='
			<div class="wrap nosubsub" style="padding-bottom:15px">
			<div id="icon-edit-comments" class="icon32"><br /></div><h2>Comment Guestbook</h2>
			</div>
			<h3>Create a guestbook page</h3>
			<div style="padding:0 15px">
				<p>"Comment guestbook" works using a "shortcode" in a page. Shortcodes are snippets of pseudo code that are placed in blog posts or pages to easily render HTML output.</p>
				<p>To create a guestbook goto "Pages" -> "Add new" in the admin menu to create a new page. Choose your page title e.g. "Guestbook" and add the shortcode <code>[comment-guestbook]</code> in the text field.<br />
				You can add additional normal text if you want to display something else on the top of this page. That´s all you have to do. Save and publish the page to finish the guestbook creation.</p>
				<p>The shortcode will be replaced by the comment form. The view of the comments will be modified like it is specified in the options below. The modification of the comments is only aplied for pages where the shortcode is used.</p>
				<br />
			</div>';
		$out .= '
			<h3>Comment Guestbook Options</h3>
			<div style="padding:0 10px">
				<h4>Comment form options:</h4>
				<div style="padding:0 10px">
					<p>This is an early version of this plugin. No options are available yet.</p>
				</div>
				<h4>Comments list options:</h4>
				<div style="padding:0 10px">
					<p>This is an early version of this plugin. No options are available yet.</p>
				</div>
			</div>';
		echo $out;
	}
}
?>
