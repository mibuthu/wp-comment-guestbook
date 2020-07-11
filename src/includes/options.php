<?php
/**
 * Options Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!
if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once CGB_PATH . 'includes/attribute.php';

/**
 * Options class
 *
 * This class handles all available options with their information
 */
class CGB_Options {

	/**
	 * Class singleton instance reference
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Option sections
	 *
	 * @var array<string,array<string,string>>
	 */
	public $sections;

	/**
	 * Options
	 *
	 * @var array<string,CGB_Attribute>
	 */
	public $options;


	/**
	 * Singleton provider and setup
	 *
	 * @return object
	 */
	public static function &get_instance() {
		// There seems to be an issue with the self variable in phan.
		// @phan-suppress-next-line PhanPluginUndeclaredVariableIsset.
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Class constructor which initializes required variables
	 *
	 * @return void
	 */
	private function __construct() {
		add_action( 'init', array( &$this, 'init' ), 1 );
		add_action( 'admin_init', array( &$this, 'register' ) );
	}


	/**
	 * Initialize the options
	 *
	 * @return void
	 */
	public function init() {
		$this->options = array(
			// General.
			'cgb_ignore_comments_open'        => new CGB_Attribute( '1', null, 'general' ),
			'cgb_ignore_comment_registration' => new CGB_Attribute( '1', null, 'general' ),
			'cgb_ignore_comment_moderation'   => new CGB_Attribute( '', null, 'general' ),
			'cgb_adjust_output'               => new CGB_Attribute( '', null, 'general' ),
			'cgb_l10n_domain'                 => new CGB_Attribute( 'default', null, 'general' ),
			// Comment form.
			'cgb_form_below_comments'         => new CGB_Attribute( '', null, 'comment_form' ),
			'cgb_form_above_comments'         => new CGB_Attribute( '', null, 'comment_form' ),
			'cgb_form_in_page'                => new CGB_Attribute( '1', null, 'comment_form' ),
			'cgb_form_expand_type'            => new CGB_Attribute( 'false', null, 'comment_form' ),
			'cgb_form_expand_link_text'       => new CGB_Attribute( __( 'Add a new guestbook entry', 'comment-guestbook' ), null, 'comment_form' ),
			'cgb_form_require_no_name_mail'   => new CGB_Attribute( '', null, 'comment_form' ),
			'cgb_form_remove_mail'            => new CGB_Attribute( '', null, 'comment_form' ),
			'cgb_form_remove_website'         => new CGB_Attribute( '', null, 'comment_form' ),
			'cgb_form_comment_label'          => new CGB_Attribute( 'default', null, 'comment_form' ),
			'cgb_form_title'                  => new CGB_Attribute( 'default', null, 'comment_form' ),
			'cgb_form_title_reply_to'         => new CGB_Attribute( 'default', null, 'comment_form' ),
			'cgb_form_notes_before'           => new CGB_Attribute( 'default', null, 'comment_form' ),
			'cgb_form_notes_after'            => new CGB_Attribute( 'default', null, 'comment_form' ),
			'cgb_form_label_submit'           => new CGB_Attribute( 'default', null, 'comment_form' ),
			'cgb_form_cancel_reply'           => new CGB_Attribute( 'default', null, 'comment_form' ),
			'cgb_form_must_login_message'     => new CGB_Attribute( 'default', null, 'comment_form' ),
			'cgb_form_styles'                 => new CGB_Attribute( '', null, 'comment_form' ),
			'cgb_form_args'                   => new CGB_Attribute( '', null, 'comment_form' ),
			// Comment list.
			'cgb_clist_threaded'              => new CGB_Attribute( 'default', null, 'comment_list' ),
			'cgb_clist_order'                 => new CGB_Attribute( 'default', null, 'comment_list' ),
			'cgb_clist_child_order_desc'      => new CGB_Attribute( '', null, 'comment_list' ),
			'cgb_clist_default_page'          => new CGB_Attribute( 'default', null, 'comment_list' ),
			'cgb_clist_pagination'            => new CGB_Attribute( 'default', null, 'comment_list' ),
			'cgb_clist_per_page'              => new CGB_Attribute( '0', null, 'comment_list' ),
			'cgb_clist_show_all'              => new CGB_Attribute( '', null, 'comment_list' ),
			'cgb_clist_num_pagination'        => new CGB_Attribute( '', null, 'comment_list' ),
			'cgb_clist_title'                 => new CGB_Attribute( '', null, 'comment_list' ),
			'cgb_clist_in_page_content'       => new CGB_Attribute( '', null, 'comment_list' ),
			'cgb_comment_callback'            => new CGB_Attribute( '--func--comment_callback', null, 'comment_list' ),
			'cgb_clist_styles'                => new CGB_Attribute( '', null, 'comment_list' ),
			'cgb_clist_args'                  => new CGB_Attribute( '', null, 'comment_list' ),
			// Comment html code.
			'cgb_comment_adjust'              => new CGB_Attribute( '', null, 'comment_html' ),
			'cgb_comment_html'                => new CGB_Attribute( '--func--comment_html', null, 'comment_html' ),
			// Message after new comment.
			'cgb_cmessage_enabled'            => new CGB_Attribute( '', null, 'cmessage' ),
			'cgb_cmessage_text'               => new CGB_Attribute( __( 'Thanks for your comment', 'comment-guestbook' ), null, 'cmessage' ),
			'cgb_cmessage_type'               => new CGB_Attribute( 'inline', null, 'cmessage' ),
			'cgb_cmessage_duration'           => new CGB_Attribute( '3000', null, 'cmessage' ),
			'cgb_cmessage_styles'             => new CGB_Attribute(
				'background-color:rgb(255, 255, 224);' .
				'&#10;border-color:rgb(230, 219, 85);' .
				'&#10;color:rgb(51, 51, 51);' .
				'&#10;padding:6px 20px;' .
				'&#10;text-align:center;' .
				'&#10;border-radius:5px;' .
				'&#10;border-width:1px;' .
				'&#10;border-style:solid',
				null,
				'cmessage'
			),
			// Comments in other pages/posts.
			'cgb_page_cmessage_enabled'       => new CGB_Attribute( '', null, 'page_comments' ),
			'cgb_page_remove_mail'            => new CGB_Attribute( '', null, 'page_comments' ),
			'cgb_page_remove_website'         => new CGB_Attribute( '', null, 'page_comments' ),
		);
	}


		/**
		 * Register the options
		 *
		 * @return void
		 */
	public function register() {
		foreach ( $this->options as $oname => $o ) {
			register_setting( 'cgb_' . $o->section, $oname );
		}
	}


	/**
	 * Load options helptexts from additional file
	 *
	 * @return void
	 */
	public function load_options_helptexts() {
		$cgb_options_helptexts = array();
		$cgb_sections          = array();
		require_once CGB_PATH . 'includes/options-helptexts.php';
		foreach ( $cgb_options_helptexts as $name => $values ) {
			$this->options[ $name ]->update( $values );
		}
		unset( $cgb_options_helptexts );

		$this->sections = $cgb_sections;
		unset( $cgb_sections );
	}


	/**
	 * Get the value of the specified option
	 *
	 * @param string $name Option name.
	 * @return string Option value.
	 * @throws Exception Option not available.
	 */
	public function get( $name ) {
		if ( ! isset( $this->options[ $name ] ) ) {
			throw new Exception( 'Requested option "' . $name . '" not available!' );
		}
		// Execute callback, if a function is used to set the value.
		if ( '--func--' === substr( $this->options[ $name ]->value, 0, 8 ) ) {
			$this->options[ $name ]->value = call_user_func( array( 'cgb_options', substr( $this->options[ $name ]->value, 8 ) ) );
		}
		return get_option( $name, $this->options[ $name ]->value );
	}


	/**
	 * Upgrades renamed or modified options to the actual version
	 *
	 * Version 0.7.3 to 0.7.4:
	 *   cgb_threaded_gb_comments -> cgb_clist_threaded
	 *   cgb_add_cmessage -> cgb_cmessage_enabled
	 *   cgb_page_add_cmessage -> cgb_page_cmessage_enabled
	 *   cgb_form_title_reply -> cgb_form_title
	 *   cgb_clist_child_order (radio) -> cgb_clist_child_order_desc (checkbox)
	 *
	 * @return void
	 */
	public function version_upgrade() {
		$value = get_option( 'cgb_threaded_gb_comments', null );
		if ( null !== $value ) {
			add_option( 'cgb_clist_threaded', $value );
			delete_option( 'cgb_threaded_gb_comments' );
		}
		$value = get_option( 'cgb_add_cmessage', null );
		if ( null !== $value ) {
			add_option( 'cgb_cmessage_enabled', $value );
			delete_option( 'cgb_add_cmessage' );
		}
		$value = get_option( 'cgb_page_add_cmessage', null );
		if ( null !== $value ) {
			add_option( 'cgb_page_cmessage_enabled', $value );
			delete_option( 'cgb_page_add_cmessage' );
		}
		$value = get_option( 'cgb_form_title_reply', null );
		if ( null !== $value ) {
			add_option( 'cgb_form_title', $value );
			delete_option( 'cgb_form_title_reply' );
		}
		$value = get_option( 'cgb_clist_child_order, null' );
		if ( null !== $value ) {
			if ( 'desc' === $value ) {
				add_option( 'cgb_clist_child_order_desc', 1 );
			}
			delete_option( 'cgb_clist_child_order' );
		}
	}


	/**
	 * Returns the default value for the comment_callback option
	 *
	 * @return string
	 */
	private function comment_callback() {
		$func = get_stylesheet() . '_comment';
		if ( function_exists( $func ) ) {
			return $func;
		} else {
			return '';
		}
	}


	/**
	 * Returns the default value for the comment_html option
	 *
	 * @return string
	 */
	private function comment_html() {
		return '
<footer class="comment-meta">
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
	}

}
