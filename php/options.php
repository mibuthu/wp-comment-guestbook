<?php

// This class handles all available options
class cgb_options {

	private static $instance;
	public $group;
	public $options;

	public static function &get_instance() {
		// Create class instance if required
		if( !isset( self::$instance ) ) {
			self::$instance = new cgb_options();
			self::$instance->init();
		}
		// Return class instance
		return self::$instance;
	}

	public function __construct() {
		$this->group = 'comment-guestbook';

		$this->options = array(

			'cgb_ignore_comments_open'   => array( 'section' => 'general',
			                                       'type'    => 'checkbox',
			                                       'std_val' => '1',
			                                       'label'   => 'Guestbook comment status',
			                                       'caption' => 'Allow comments on the guestbook page',
			                                       'desc'    => 'Always allow comments on the guestbook page. If enabled the comment status of the guestbook page will be ignored.' ),

			'cgb_l10n_domain'            => array( 'section' => 'general',
			                                       'type'    => 'text',
			                                       'std_val' => 'default',
			                                       'label'   => 'Domain for translation',
			                                       'desc'    => 'Sets the domain for translation for the modified code which is set in Comment Guestbook.<br />
			                                                     Standard value is "default". For example if you want to use the function of the twentyeleven theme the value would be "twentyeleven".<br />
			                                                     See the <a href="http://codex.wordpress.org/Function_Reference/_2" target="_blank">description in Wordpress codex</a> for more details.<br />' ),

			'cgb_clist_adjust'           => array( 'section' => 'comment_list',
			                                       'type'    => 'checkbox',
			                                       'std_val' => '',
			                                       'label'   => 'Comment list adjustment',
			                                       'caption' => 'Adjust the comment list output',
			                                       'desc'    => 'This option specifies if the comment list in the guestbook page should be adjusted or if the standard list specified in the theme should be used.' ),

			'cgb_clist_order'            => array( 'section' => 'comment_list',
			                                       'type'    => 'radio',
			                                       'std_val' => 'default',
			                                       'label'   => 'Comment list order',
			                                       'caption' => array( 'default' => 'Standard WP-discussion setting', 'asc' => 'Oldest comments first', 'desc' => 'Newest comments first' ),
			                                       'desc'    => 'This option allows you to overwrite the standard order for top level comments only for the guestbook page.<br />
			                                                     "Comment list adjustment" must be enabled.' ),

			'cgb_clist_child_order'      => array( 'section' => 'comment_list',
			                                       'type'    => 'radio',
			                                       'std_val' => 'default',
			                                       'label'   => 'Comment list child order',
			                                       'caption' => array( 'default' => 'Standard WP-discussion setting', 'asc' => 'Oldest child comments first', 'desc' => 'Newest child comments first' ),
			                                       'desc'    => 'This option allows you to overwrite the standard order for all child comments only for the guestbook page.<br />
			                                                     "Comment list adjustment" must be enabled.' ),

			'cgb_form_below_comments'    => array( 'section' => 'comment_list',
			                                       'type'    => 'checkbox',
			                                       'std_val' => '',
			                                       'label'   => 'Additional comment form',
			                                       'caption' => 'Add an additional comment form below the comments',
			                                       'desc'    => 'With this option you can add an additional comment form below the comment list.<br />
			                                                     This option is only available if "Comment list adjustment" is enabled' ),

			'cgb_comment_callback'       => array( 'section' => 'comment_list',
			                                       'type'    => 'text',
			                                       'std_val' => '--func--comment_callback',
			                                       'label'   => 'Comment callback function',
			                                       'desc'    => 'This option sets the name of comment callback function which outputs the html-code to view each comment.<br />
			                                                     You only require this function if "Comment list adjustment" was enabled, but no comment adjustment will be used.<br />
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
			                                                     The code given as an example is a slightly modified version of the code given in the twentyeleven theme.' )
		);
	}

	public function init() {
		add_action( 'admin_init', array( &$this, 'register' ) );
	}

	public function register() {
		foreach( $this->options as $oname => $o ) {
			register_setting( 'cgb_'.$o['section'], $oname );
		}
	}

/*
	public function set( $name, $value ) {
		if( isset( $this->options[$name] ) ) {
			return update_option( $name, $value );
		}
		else {
			return false;
		}
	}
*/
	public function get( $name ) {
		if( isset( $this->options[$name] ) ) {
			// set std_val, if a function is used to set the value
			if( substr( $this->options[$name]['std_val'], 0, 8 ) == '--func--' ) {
				$this->options[$name]['std_val'] = call_user_func( array('cgb_options', substr( $this->options[$name]['std_val'], 8 ) ) );
			}
			return get_option( $name, $this->options[$name]['std_val'] );
		}
		else {
			return null;
		}
	}

	/**
	 * Upgrades renamed or modified options to the actual version
	 *
	 * Version 0.1.0 to 0.1.1:
	 *   cgb_clist_comment_adjust   -> cgb_comment_adjust
	 *   cgb_clist_comment_html     -> cgb_comment_html
	 *
	 * Version 0.1.2 to 0.2.0:
	 *   cgb_clist_comment_callback -> cgb_comment_callback
	 */
	public function version_upgrade() {
		$value = get_option( 'cgb_clist_comment_adjust', null );
		if( $value != null ) {
			add_option( 'cgb_comment_adjust', $value, '', 'no' );
			delete_option( 'cgb_clist_comment_adjust' );
		}
		$value = get_option( 'cgb_clist_comment_html', null );
		if( $value != null ) {
			add_option( 'cgb_comment_html', $value, '', 'no' );
			delete_option( 'cgb_clist_comment_html' );
		}
		$value = get_option( 'cgb_clist_comment_callback', null );
		if( $value != null ) {
			add_option( 'cgb_comment_callback', $value, '', 'no' );
			delete_option( 'cgb_clist_comment_callback' );
		}
	}

	private function comment_callback() {
		$func = get_stylesheet().'_comment';
		if( function_exists( $func ) ) {
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
