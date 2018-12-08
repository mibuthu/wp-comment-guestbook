<?php
/**
 * Options Class
 *
 * @package comment-guestbook
 */

declare(strict_types=1);
if ( ! defined( 'WPINC' ) ) {
	die;
}

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
	 * @var array
	 */
	public $sections;

	/**
	 * Options array
	 *
	 * @var array
	 */
	public $options;


	/**
	 * Singleton provider and setup
	 *
	 * @return object
	 */
	public static function &get_instance() {
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
			'cgb_ignore_comments_open'        => array( 'std_val' => '1' ),
			'cgb_ignore_comment_registration' => array( 'std_val' => '1' ),
			'cgb_ignore_comment_moderation'   => array( 'std_val' => '' ),
			'cgb_threaded_gb_comments'        => array( 'std_val' => 'default' ),
			'cgb_adjust_output'               => array( 'std_val' => '' ),
			'cgb_l10n_domain'                 => array( 'std_val' => 'default' ),
			// Comment form.
			'cgb_form_below_comments'         => array( 'std_val' => '' ),
			'cgb_form_above_comments'         => array( 'std_val' => '' ),
			'cgb_form_in_page'                => array( 'std_val' => '1' ),
			'cgb_form_expand_type'            => array( 'std_val' => 'false' ),
			'cgb_form_expand_link_text'       => array( 'std_val' => __( 'Add a new guestbook entry', 'comment-guestbook' ) ),
			'cgb_add_cmessage'                => array( 'std_val' => '' ),
			'cgb_form_require_no_name_mail'   => array( 'std_val' => '' ),
			'cgb_form_remove_mail'            => array( 'std_val' => '' ),
			'cgb_form_remove_website'         => array( 'std_val' => '' ),
			'cgb_form_comment_label'          => array( 'std_val' => 'default' ),
			'cgb_form_title_reply'            => array( 'std_val' => 'default' ),
			'cgb_form_title_reply_to'         => array( 'std_val' => 'default' ),
			'cgb_form_notes_before'           => array( 'std_val' => 'default' ),
			'cgb_form_notes_after'            => array( 'std_val' => 'default' ),
			'cgb_form_label_submit'           => array( 'std_val' => 'default' ),
			'cgb_form_cancel_reply'           => array( 'std_val' => 'default' ),
			'cgb_form_must_login_message'     => array( 'std_val' => 'default' ),
			'cgb_form_styles'                 => array( 'std_val' => '' ),
			'cgb_form_args'                   => array( 'std_val' => '' ),
			// Comment list.
			'cgb_clist_order'                 => array( 'std_val' => 'default' ),
			'cgb_clist_child_order'           => array( 'std_val' => 'default' ),
			'cgb_clist_default_page'          => array( 'std_val' => 'default' ),
			'cgb_clist_pagination'            => array( 'std_val' => 'default' ),
			'cgb_clist_per_page'              => array( 'std_val' => '0' ),
			'cgb_clist_show_all'              => array( 'std_val' => '' ),
			'cgb_clist_num_pagination'        => array( 'std_val' => '' ),
			'cgb_clist_title'                 => array( 'std_val' => '' ),
			'cgb_clist_in_page_content'       => array( 'std_val' => '' ),
			'cgb_comment_callback'            => array( 'std_val' => '--func--comment_callback' ),
			'cgb_clist_styles'                => array( 'std_val' => '' ),
			'cgb_clist_args'                  => array( 'std_val' => '' ),
			// Comment html code.
			'cgb_comment_adjust'              => array( 'std_val' => '' ),
			'cgb_comment_html'                => array( 'std_val' => '--func--comment_html' ),
			// Message after new comment.
			'cgb_cmessage_text'               => array( 'std_val' => __( 'Thanks for your comment', 'comment-guestbook' ) ),
			'cgb_cmessage_type'               => array( 'std_val' => 'inline' ),
			'cgb_cmessage_duration'           => array( 'std_val' => '3000' ),
			'cgb_cmessage_styles'             => array( 'std_val' => 'background-color:rgb(255, 255, 224);&#10;border-color:rgb(230, 219, 85);&#10;color:rgb(51, 51, 51);&#10;padding:6px 20px;&#10;text-align:center;&#10;border-radius:5px;&#10;border-width:1px;&#10;border-style:solid' ),
			'cgb_page_add_cmessage'           => array( 'std_val' => '' ),
			'cgb_page_remove_mail'            => array( 'std_val' => '' ),
			'cgb_page_remove_website'         => array( 'std_val' => '' ),
		);
	}


	/**
	 * Register the options
	 *
	 * @return void
	 */
	public function register() {
		foreach ( $this->options as $oname => $o ) {
			register_setting( 'cgb_options', $oname );
		}
	}


	/**
	 * Load options helptexts from additional file
	 *
	 * @return void
	 */
	public function load_options_helptexts() {
		require_once CGB_PATH . 'includes/options-helptexts.php';
		foreach ( $cgb_options_helptexts as $name => $values ) {
			$this->options[ $name ] += $values;
		}
		unset( $cgb_options_helptexts );

		$this->sections = $cgb_sections_helptexts;
		unset( $cgb_sections_helptexts );
	}


	/**
	 * Get the value of the specified option
	 *
	 * @param string $name Option name.
	 * @return string Option value.
	 */
	public function get( $name ) {
		if ( isset( $this->options[ $name ] ) ) {
			// Set std_val, if a function is used to set the value.
			if ( '--func--' === substr( $this->options[ $name ]['std_val'], 0, 8 ) ) {
				$this->options[ $name ]['std_val'] = call_user_func( array( 'cgb_options', substr( $this->options[ $name ]['std_val'], 8 ) ) );
			}
			return get_option( $name, $this->options[ $name ]['std_val'] );
		} else {
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
		$value = get_option( 'cgb_clist_adjust', null );
		if ( null !== $value ) {
			add_option( 'cgb_adjust_output', $value );
			delete_option( 'cgb_clist_adjust' );
		}
		$value = get_option( 'cgb_cmessage', null );
		if ( null !== $value ) {
			if ( 'default' !== $value ) {
				add_option( 'cgb_add_cmessage', '1' );
			}
			if ( 'always' === $value ) {
				add_option( 'cgb_page_add_cmessage', '1' );
			}
			delete_option( 'cgb_cmessage' );
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
