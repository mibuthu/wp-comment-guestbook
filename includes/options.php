<?php
if(!defined('WPINC')) {
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
			self::$instance = new self();
		}
		// Return class instance
		return self::$instance;
	}

	private function __construct() {
		add_action('init', array(&$this, 'init_options'), 1);
		add_action('admin_init', array(&$this, 'register_options'));
	}

	public function init_options() {
		$this->options = array(
			'cgb_ignore_comments_open'        => array('section' => 'general',       'std_val' => '1'),
			'cgb_ignore_comment_registration' => array('section' => 'general',       'std_val' => '1'),
			'cgb_ignore_comment_moderation'   => array('section' => 'general',       'std_val' => ''),
			'cgb_threaded_gb_comments'        => array('section' => 'general',       'std_val' => 'default'),
			'cgb_adjust_output'               => array('section' => 'general',       'std_val' => ''),
			'cgb_l10n_domain'                 => array('section' => 'general',       'std_val' => 'default'),

			'cgb_form_below_comments'         => array('section' => 'comment_form',  'std_val' => ''),
			'cgb_form_above_comments'         => array('section' => 'comment_form',  'std_val' => ''),
			'cgb_form_in_page'                => array('section' => 'comment_form',  'std_val' => '1'),
			'cgb_form_expand_type'            => array('section' => 'comment_form',  'std_val' => 'false'),
			'cgb_form_expand_link_text'       => array('section' => 'comment_form',  'std_val' => __('Add a new guestbook entry','comment-guestbook')),
			'cgb_add_cmessage'                => array('section' => 'comment_form',  'std_val' => ''),
			'cgb_form_require_no_name_mail'   => array('section' => 'comment_form',  'std_val' => ''),
			'cgb_form_remove_mail'            => array('section' => 'comment_form',  'std_val' => ''),
			'cgb_form_remove_website'         => array('section' => 'comment_form',  'std_val' => ''),
			'cgb_form_comment_label'          => array('section' => 'comment_form',  'std_val' => 'default'),
			'cgb_form_title_reply'            => array('section' => 'comment_form',  'std_val' => 'default'),
			'cgb_form_title_reply_to'         => array('section' => 'comment_form',  'std_val' => 'default'),
			'cgb_form_notes_before'           => array('section' => 'comment_form',  'std_val' => 'default'),
			'cgb_form_notes_after'            => array('section' => 'comment_form',  'std_val' => 'default'),
			'cgb_form_label_submit'           => array('section' => 'comment_form',  'std_val' => 'default'),
			'cgb_form_cancel_reply'           => array('section' => 'comment_form',  'std_val' => 'default'),
			'cgb_form_must_login_message'     => array('section' => 'comment_form',  'std_val' => 'default'),
			'cgb_form_styles'                 => array('section' => 'comment_form',  'std_val' => ''),
			'cgb_form_args'                   => array('section' => 'comment_form',  'std_val' => ''),

			'cgb_clist_order'                 => array('section' => 'comment_list',  'std_val' => 'default'),
			'cgb_clist_child_order'           => array('section' => 'comment_list',  'std_val' => 'default'),
			'cgb_clist_default_page'          => array('section' => 'comment_list',  'std_val' => 'default'),
			'cgb_clist_pagination'            => array('section' => 'comment_list',  'std_val' => 'default'),
			'cgb_clist_per_page'              => array('section' => 'comment_list',  'std_val' => '0'),
			'cgb_clist_show_all'              => array('section' => 'comment_list',  'std_val' => ''),
			'cgb_clist_num_pagination'        => array('section' => 'comment_list',  'std_val' => ''),
			'cgb_clist_title'                 => array('section' => 'comment_list',  'std_val' => ''),
			'cgb_clist_in_page_content'       => array('section' => 'comment_list',  'std_val' => ''),
			'cgb_comment_callback'            => array('section' => 'comment_list',  'std_val' => '--func--comment_callback'),
			'cgb_clist_styles'                => array('section' => 'comment_list',  'std_val' => ''),
			'cgb_clist_args'                  => array('section' => 'comment_list',  'std_val' => ''),

			'cgb_comment_adjust'              => array('section' => 'comment_html',  'std_val' => ''),
			'cgb_comment_html'                => array('section' => 'comment_html',  'std_val' => '--func--comment_html'),

			'cgb_cmessage_text'               => array('section' => 'cmessage',      'std_val' => __('Thanks for your comment','comment-guestbook')),
			'cgb_cmessage_type'               => array('section' => 'cmessage',      'std_val' => 'inline'),
			'cgb_cmessage_duration'           => array('section' => 'cmessage',      'std_val' => '3000'),
			'cgb_cmessage_styles'             => array('section' => 'cmessage',      'std_val' => 'background-color:rgb(255, 255, 224);&#10;border-color:rgb(230, 219, 85);&#10;color:rgb(51, 51, 51);&#10;padding:6px 20px;&#10;text-align:center;&#10;border-radius:5px;&#10;border-width:1px;&#10;border-style:solid'),

			'cgb_page_add_cmessage'           => array('section' => 'page_comments', 'std_val' => ''),
			'cgb_page_remove_mail'            => array('section' => 'page_comments', 'std_val' => ''),
			'cgb_page_remove_website'         => array('section' => 'page_comments', 'std_val' => ''),
		);
	}

	public function load_options_helptexts() {
		require_once(CGB_PATH.'includes/options_helptexts.php');
		foreach($options_helptexts as $name => $values) {
			$this->options[$name] += $values;
		}
		unset($options_helptexts);

		$this->sections = $sections_helptexts;
		unset($sections_helptexts);
	}

	public function register_options() {
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
