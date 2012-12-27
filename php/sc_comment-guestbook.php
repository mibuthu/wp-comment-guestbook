<?php
require_once( CGB_PATH.'php/options.php' );

// This class handles the shortcode [comment-guestbook]
class sc_comment_guestbook {
	private static $instance;
	private $options;

	public static function &get_instance() {
		// Create class instance if required
		if( !isset( self::$instance ) ) {
			self::$instance = new sc_comment_guestbook();
		}
		// Return class instance
		return self::$instance;
	}

	private function __construct() {
		// get options instance
		$this->options = &cgb_options::get_instance();
	}

	// main function to show the rendered HTML output
	public function show_html( $atts ) {
		$this->init_filters();
		$out = '';
		if( comments_open() ) {
			if( '' !== $this->options->get( 'cgb_form_in_page' ) ) {
				ob_start();
					comment_form();
					$out .= ob_get_contents();
				ob_end_clean();
			}
		}
		else {
			$out .= '<div id="respond" style="text-align:center">Guestbook is closed</div>';
		}
		return $out;
	}

	private function init_filters() {
		global $cgb;
		// Filter to overwrite comments_open status
		if( '' !== $this->options->get( 'cgb_ignore_comments_open' ) ) {
			add_filter( 'comments_open', array( &$cgb, 'filter_comments_open' ) );
		}
		// Filter to show the adjusted comment style
		if( 1 == $this->options->get( 'cgb_clist_adjust' ) ) {
			add_filter( 'comments_template', array( &$this, 'filter_comments_template' ) );
			if( 'desc' === $this->options->get( 'cgb_clist_order' ) ) {
				add_filter( 'comments_array', array( &$this, 'filter_comments_array' ) );
			}
			if( 'default' !== $this->options->get( 'cgb_clist_default_page' ) ) {
				add_filter( 'option_default_comments_page', array( &$this, 'filter_comments_default_page') );
			}
		}
		// Filter to add comment id fields to identify required filters
		add_filter( 'comment_id_fields', array( &$this, 'filter_comment_id_fields' ) );
	}

	public function filter_comments_template( $file ) {
		// Set customized comments-template fie if a commentlist output modification is required
		return CGB_PATH.'php/comments-template.php';
	}

	public function filter_comments_array( $comments ) {
		// Invert array if clist order desc is required
		return array_reverse( $comments );
	}

	public function filter_comments_default_page( $page ) {
		// Overwrite comments default page
		if( 'first' === $this->options->get( 'cgb_clist_default_page' ) ) {
			$page = 'oldest';
		}
		elseif( 'last' === $this->options->get( 'cgb_clist_default_page' ) ) {
			$page = 'newest';
		}
		return $page;
	}

	public function filter_comment_id_fields( $html ) {
		// Add fields comment form to identify a guestbook comment when overwrite of comment status is required
		if( '' !== $this->options->get( 'cgb_ignore_comments_open' ) ) {
			$html .= '<input type="hidden" name="cgb_comments_status" id="cgb_comments_status" value="open" />';
		}
		if( 'desc' === $this->options->get( 'cgb_clist_order' ) && 1 == $this->options->get( 'cgb_clist_adjust' ) ) {
			$html .= '<input type="hidden" name="cgb_clist_order" id="cgb_clist_order" value="desc" />';
		}
		return $html;
	}
}
?>
