<?php
/**
 * CommentGuestbook Config Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/option.php';

/**
 * Config class
 *
 * This class handles all available options with their information
 *
 * @property-read Option $ignore_comments_open Guestbook comment status.
 * @property-read Option $ignore_comment_registration Guestbook comment registration.
 * @property-read Option $ignore_comment_moderation Guestbook comment moderation.
 * @property-read Option $adjust_output Comments ajdustment.
 * @property-read Option $l10n_domain Domain for translation.
 * @property-read Option $form_below_comments Form below comments.
 * @property-read Option $form_above_comments Form above comments.
 * @property-read Option $form_in_page Form in page/post.
 * @property-read Option $form_expand_type Collapsed comment form.
 * @property-read Option $form_expand_link_text Link text for form expansion.
 * @property-read Option $form_require_no_name_mail Comment author name/email.
 * @property-read Option $form_remove_mail Remove Email field.
 * @property-read Option $form_remove_website Remove website field.
 * @property-read Option $form_comment_label Label for comment field.
 * @property-read Option $form_title Comment form title.
 * @property-read Option $form_title_reply_to Reply comment form title.
 * @property-read Option $form_notes_before Notes before form fields.
 * @property-read Option $form_notes_after Notes after form fields.
 * @property-read Option $form_label_submit Label of submit button.
 * @property-read Option $form_cancel_reply Label for cancel reply link.
 * @property-read Option $form_must_login_message Must login message.
 * @property-read Option $form_styles Comment form styles.
 * @property-read Option $form_args Comment form args.
 * @property-read Option $clist_threaded Threaded guestbook comments.
 * @property-read Option $clist_order Comment list order.
 * @property-read Option $clist_child_order_desc Comment list child order.
 * @property-read Option $clist_default_page Comment list default page.
 * @property-read Option $clist_pagination Bread comments into pages.
 * @property-read Option $clist_per_page Comments per page.
 * @property-read Option $clist_num_pagination Numbered pagination.
 * @property-read Option $clist_show_all Show all comments.
 * @property-read Option $clist_title Title for the comment list.
 * @property-read Option $clist_in_page_content Comment list in page content.
 * @property-read Option $clist_styles Comment list styles.
 * @property-read Option $clist_args Comment list args.
 * @property-read Option $comment_adjust Comment adjustment.
 * @property-read Option $comment_html Comment html code.
 * @property-read Option $comment_callback Comment callback function.
 * @property-read Option $cmessage_enabled Enable message.
 * @property-read Option $cmessage_text New comment message text.
 * @property-read Option $cmessage_type New comment message type.
 * @property-read Option $cmessage_duration New comment message duration.
 * @property-read Option $cmessage_styles New comment message styles.
 * @property-read Option $page_cmessage_enabled Message after new comments in other pages/posts.
 * @property-read Option $page_remove_mail Remove Email field in other pages/posts.
 * @property-read Option $page_remove_website Remove Website field in other pages/posts.
 */
final class Config {

	/**
	 * Options array
	 *
	 * @var array<string,Option>
	 */
	private $options;

	/** Loaded options
	 *
	 *  Keep track of the already loaded options
	 *
	 * @var string[]
	 */
	private $loaded_options = [];

	/**
	 * Config Admin Data
	 *
	 * @var ConfigAdminData
	 */
	public $admin_data = null;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @return void
	 */
	public function __construct() {
		// Inititialize options directly after loading the plugins textdomain (action: plugins_loaded, priority: 10).
		add_action( 'plugins_loaded', [ &$this, 'init' ], 11 );
		add_action( 'admin_init', [ &$this, 'register' ] );
	}


	/**
	 * Initialize the options
	 *
	 * @return void
	 */
	public function init() {
		$this->options = [
			// General.
			'cgb_ignore_comments_open'        => Option::new_true_num( 'general' ),
			'cgb_ignore_comment_registration' => Option::new_true_num( 'general' ),
			'cgb_ignore_comment_moderation'   => Option::new_false_num( 'general' ),
			'cgb_adjust_output'               => Option::new_false_num( 'general' ),
			'cgb_l10n_domain'                 => Option::new( 'default', 'general' ),
			// Comment form.
			'cgb_form_below_comments'         => Option::new_false_num( 'comment_form' ),
			'cgb_form_above_comments'         => Option::new_false_num( 'comment_form' ),
			'cgb_form_in_page'                => Option::new_true_num( 'comment_form' ),
			'cgb_form_expand_type'            => Option::new_false( 'comment_form' ),
			'cgb_form_expand_link_text'       => Option::new( __( 'Add a new guestbook entry', 'comment-guestbook' ), 'comment_form' ),
			'cgb_form_require_no_name_mail'   => Option::new_false_num( 'comment_form' ),
			'cgb_form_remove_mail'            => Option::new_false_num( 'comment_form' ),
			'cgb_form_remove_website'         => Option::new_false_num( 'comment_form' ),
			'cgb_form_comment_label'          => Option::new( 'default', 'comment_form' ),
			'cgb_form_title'                  => Option::new( 'default', 'comment_form' ),
			'cgb_form_title_reply_to'         => Option::new( 'default', 'comment_form' ),
			'cgb_form_notes_before'           => Option::new( 'default', 'comment_form' ),
			'cgb_form_notes_after'            => Option::new( 'default', 'comment_form' ),
			'cgb_form_label_submit'           => Option::new( 'default', 'comment_form' ),
			'cgb_form_cancel_reply'           => Option::new( 'default', 'comment_form' ),
			'cgb_form_must_login_message'     => Option::new( 'default', 'comment_form' ),
			'cgb_form_styles'                 => Option::new( '', 'comment_form' ),
			'cgb_form_args'                   => Option::new( '', 'comment_form' ),
			// Comment list.value
			'cgb_clist_threaded'              => Option::new( 'default', 'comment_list' ),
			'cgb_clist_order'                 => Option::new( 'default', 'comment_list' ),
			'cgb_clist_child_order_desc'      => Option::new( '', 'comment_list' ),
			'cgb_clist_default_page'          => Option::new( 'default', 'comment_list' ),
			'cgb_clist_pagination'            => Option::new( 'default', 'comment_list' ),
			'cgb_clist_per_page'              => Option::new( '0', 'comment_list' ),
			'cgb_clist_num_pagination'        => Option::new_false_num( 'comment_list' ),
			'cgb_clist_show_all'              => Option::new_false_num( 'comment_list' ),
			'cgb_clist_title'                 => Option::new( '', 'comment_list' ),
			'cgb_clist_in_page_content'       => Option::new_false_num( 'comment_list' ),
			'cgb_clist_styles'                => Option::new( '', 'comment_list' ),
			'cgb_clist_args'                  => Option::new( '', 'comment_list' ),
			// Comment html code.
			'cgb_comment_adjust'              => Option::new_false_num( 'comment_html' ),
			'cgb_comment_html'                => Option::new( '--func--comment_html', 'comment_html' ),
			'cgb_comment_callback'            => Option::new( '--func--comment_callback', 'comment_html' ),
			// Message after new comment.
			'cgb_cmessage_enabled'            => Option::new_false_num( 'cmessage' ),
			'cgb_cmessage_text'               => Option::new( __( 'Thanks for your comment', 'comment-guestbook' ), 'cmessage' ),
			'cgb_cmessage_type'               => Option::new( 'inline', 'cmessage' ),
			'cgb_cmessage_duration'           => Option::new( '3000', 'cmessage' ),
			'cgb_cmessage_styles'             => Option::new(
				'background-color:rgb(255, 255, 224);' .
				'&#10;border-color:rgb(230, 219, 85);' .
				'&#10;color:rgb(51, 51, 51);' .
				'&#10;padding:6px 20px;' .
				'&#10;text-align:center;' .
				'&#10;border-radius:5px;' .
				'&#10;border-width:1px;' .
				'&#10;border-style:solid',
				'cmessage'
			),
			// Comments in other pages/posts.
			'cgb_page_cmessage_enabled'       => Option::new_false_num( 'page_comments' ),
			'cgb_page_remove_mail'            => Option::new_false_num( 'page_comments' ),
			'cgb_page_remove_website'         => Option::new_false_num( 'page_comments' ),
		];
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
	 * Get the option
	 *
	 * The "cgb_" prefix in the option name is optional.
	 * If the option is of type boolean, a boolean value will be returned.
	 *
	 * @param string $name Option name.
	 * @return Option Option value.
	 */
	public function __get( $name ) {
		// Set the prefix if required
		if ( 'cgb_' !== substr( $name, 0, 4 ) ) {
			$name = 'cgb_' . $name;
		}
		// Check if the option exists
		if ( ! isset( $this->options[ $name ] ) ) {
			// Trigger error is allowed in this case.
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'The requested option "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
			return Option::new( '' );
		}
		// Load the option if not already loaded
		if ( ! in_array( $name, $this->loaded_options, true ) ) {
			// Execute callback, if a function is used to set the value.
			if ( '--func--' === substr( $this->options[ $name ]->default_value, 0, 8 ) ) {
				$this->options[ $name ]->value = call_user_func( [ __CLASS__, substr( $this->options[ $name ]->default_value, 8 ) ] );
			} else {
				$this->options[ $name ]->value = get_option( $name, $this->options[ $name ]->default_value );
			}
			// Set option as loaded
			$this->loaded_options[] = $name;
		}
		// Return the option
		return $this->options[ $name ];
	}


	/**
	 * Get all specified options
	 *
	 * @return array<string,Option>
	 */
	public function get_all() {
		return $this->options;
	}


	/**
	 * Returns the default value for the comment_callback option
	 *
	 * @return string
	 */
	private function comment_callback() {
		$func = get_stylesheet() . '_comment';
		if ( function_exists( $func ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $func . ' does exist!' );
			return $func;
		} else {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $func . ' does not exist!' );
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


	/**
	 * Load the additional option data
	 *
	 * @return void
	 */
	public function load_admin_data() {
		require_once PLUGIN_PATH . 'includes/config-admin-data.php';
		$this->admin_data = new ConfigAdminData();
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
		$this->rename_option( 'cgb_threaded_gb_comments', 'cgb_clist_threaded' );
		$this->rename_option( 'cgb_add_cmessage', 'cgb_cmessage_enabled' );
		$this->rename_option( 'cgb_page_add_cmessage', 'cgb_page_cmessage_enabled' );
		$this->rename_option( 'cgb_form_title_reply', 'cgb_form_title' );
		$this->rename_option( 'cgb_clist_child_order', 'cgb_clist_child_order_desc' );
	}


	/**
	 * Rename an existing option
	 *
	 * @param string $old_name The old option name.
	 * @param string $new_name The new option name.
	 * @return void
	 */
	private function rename_option( $old_name, $new_name ) {
		$value = get_option( $old_name, null );
		if ( null !== $value ) {
			add_option( $new_name, $value );
			delete_option( $old_name );
		}
	}

}
