<?php
require_once( CGB_PATH.'php/options.php' );

// This class handles the shortcode [comment-guestbook]
class sc_comment_guestbook {
	private $options;

	public function __construct() {
		// get options instance
		$this->options = &cgb_options::get_instance();
	}

	// main function to show the rendered HTML output
	public function show_html( $atts ) {
		$this->init_filters();
		if( comments_open() ) {
			ob_start();
				comment_form();
				$out = ob_get_contents();
			ob_end_clean();
		}
		else {
			$out = '<div id="respond" style="text-align:center">Guestbook is closed</div>';
		}
		return $out;
	}

	private function init_filters() {
		// Filter to overwrite comments_opten status
		if( '' !== $this->options->get( 'cgb_ignore_comments_open' ) ) {
			add_filter( 'comments_open', array( &$this, 'filter_comments_open' ) );
		}
		// Filter to show the adjusted comment style
		if( 1 == $this->options->get( 'cgb_clist_adjust' ) ) {
			add_filter( 'comments_template', array( &$this, 'filter_comments_template' ) );
			if( 'desc' === $this->options->get( 'cgb_clist_order' ) ) {
				add_filter( 'comments_array', array( &$this, 'filter_comments_array' ) );
			}
		}
	}

	public function filter_comments_open( $open ) {
		return true;
	}

	public function filter_comments_template( $file ) {
		// Set customized comments-template fie if a commentlist output modification is required
		return CGB_PATH.'php/comments-template.php';
	}

	public function filter_comments_array( $comments ) {
		// Invert array if clist order desc is required
		return array_reverse( $comments );
	}
}
?>
