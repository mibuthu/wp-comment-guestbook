<?php

// This class handles all available options
class cgb_options {

	public static $group = 'comment-guestbook';

	public static $options = array(
		'cgb_clist_adjust'           => array( 'section' => 'comment_list',
		                                       'type'    => 'checkbox',
		                                       'std_val' => '',
		                                       'label'   => 'Comment list adjustment',
		                                       'caption' => 'Adjust the comment list output',
		                                       'desc'    => 'This option specifies if the comment list in the guestbook page should be adjusted or if the standard list specified in the theme should be used.' ),

		'cgb_clist_comment_callback' => array( 'section' => 'comment_list',
		                                       'type'    => 'text',
		                                       'std_val' => '--func--comment_callback',
		                                       'label'   => 'Comment callback function',
		                                       'desc'    => 'This option sets the name of comment callback function which outputs the html-code to view each comment.<br />
		                                                     You only require this function if "Commentlist adjustment" was enabled, but no comment adjustment will be used.<br />
		                                                     Normally this function is set through the selected theme. Comment Guestbook searches for the theme-function and uses this as default, if it was found. <br />
		                                                     If the theme-function wasn´t found this field will be empty, then the WordPress internal functionality will be used.<br />
		                                                     If you want to insert the function of your theme manually, you can find the name in "functions.php" in your theme directory.<br />
		                                                     Normally it is called "themename_comment", e.g. for twentyeleven theme: "twentyeleven_comment".' ),

		'cgb_comment_adjust'         => array( 'section' => 'comment_html',
		                                       'type'    => 'checkbox',
		                                       'std_val' => '',
		                                       'label'   => 'Comment adjustment',
		                                       'caption' => 'Adjust the html-output of each comment',
		                                       'desc'    => 'This option specifies if the comment html code should be replaced with the html code given in "Comment html code" on the guestbook page.<br />
		                                                     If "Comment list adjustment" is disabled this option has no effect.' ),

		'cgb_comment_html'           => array( 'section' => 'comment_html',
		                                       'type'    => 'textarea',
		                                       'std_val' => '--func--comment_html',
		                                       'label'   => 'Comment html code',
		                                       'desc'    => 'This option specifies the html code for each comment, if "Comment adjustment" is enabled.<br />
		                                                     You can use php-code to get the required comment data. Use the php variable $l10n_domain to get the "Domain for translation" value.<br />
		                                                     The code given as an example is a slightly modified version of the code given in the twentyeleven theme.' ),

		'cgb_l10n_domain'            => array( 'section' => 'general',
		                                       'type'    => 'text',
		                                       'std_val' => 'default',
		                                       'label'   => 'Domain for translation',
		                                       'desc'    => 'Sets the domain for translation for the modified code which is set in Comment Guestbook.<br />
		                                                     Standard value is "default". For example if you want to use the function of the twentyeleven theme the value would be "twentyeleven".<br />
		                                                     See the <a href="http://codex.wordpress.org/Function_Reference/_2" target="_blank">description in Wordpress codex</a> for more details.<br />' )
		);

	public static function register() {
		foreach( cgb_options::$options as $oname => $o ) {
			register_setting( 'cgb_'.$o['section'], $oname );
		}
	}
/*
	public static function set( $name, $value ) {
		if( isset( cgb_options::$options[$name] ) ) {
			return update_option( $name, $value );
		}
		else {
			return false;
		}
	}
*/
	public static function get( $name ) {
		if( isset( cgb_options::$options[$name] ) ) {
			// set std_val, if a function is used to set the value
			if( substr( cgb_options::$options[$name]['std_val'], 0, 8 ) == '--func--' ) {
				cgb_options::$options[$name]['std_val'] = call_user_func( array('cgb_options', substr( cgb_options::$options[$name]['std_val'], 8 ) ) );
			}
			return get_option( $name, cgb_options::$options[$name]['std_val'] );
		}
		else {
			return null;
		}
	}

	private static function comment_callback() {
		$func = get_stylesheet().'_comment';
		if( function_exists( $func ) ) {
			return $func;
		}
		else {
			return '';
		}
	}

	private static function comment_html() {
		// use 2 spaces instead of 1 tab to have a better view in the options dialog
		$out = '<footer class="comment-meta">
<div class="comment-author vcard">
<?php
	$avatar_size = 68;
	if ( "0" != $comment->comment_parent )
		$avatar_size = 39;
	echo get_avatar( $comment, $avatar_size );
	printf( \'<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>\',
		esc_url( get_comment_link( $comment->comment_ID ) ),
		get_comment_time( "c" ),
		sprintf( __( \'%1$s at %2$s<br />\', $l10n_domain ), get_comment_date(), get_comment_time() ) );
	printf( \'<span class="fn">%s</span>\', get_comment_author_link() );
	edit_comment_link( __( "Edit", $l10n_domain ), \'<span class="edit-link">\', "</span>" ); ?>
</div><!-- .comment-author .vcard -->
<?php if ( $comment->comment_approved == "0" ) : ?>
	<em class="comment-awaiting-moderation"><?php _e( "Your comment is awaiting moderation.", $l10n_domain ); ?></em>
	<br />
<?php endif; ?>
</footer>
<div class="comment-content"><?php comment_text(); ?></div>
<div class="reply">
	<?php comment_reply_link( array_merge( $args, array( "reply_text" => __( "Reply <span>&darr;</span>", $l10n_domain ), "depth" => $depth, "max_depth" => $args["max_depth"] ) ) ); ?>
</div><!-- .reply -->';
		return $out;
	}
}
?>
