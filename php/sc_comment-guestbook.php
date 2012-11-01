<?php
require_once( CGB_PATH.'php/options.php' );

// This class handles the shortcode [comment-guestbook]
class sc_comment_guestbook {

	// main function to show the rendered HTML output
	public function show_html( $atts ) {
		global $cgb;
		ob_start ();
		comment_form ();
		$out = ob_get_contents ();
		ob_end_clean ();
		// add filter to show the adjusted comment style
		if( $cgb->options->get ( 'cgb_clist_adjust' ) == 1 ) {
		   add_filter( 'comments_template', array( &$this, 'filter_comments_template' ), 100 );
		}
		return $out;
	}

	public function filter_comments_template( $file ) {
		$file =  CGB_PATH.'php/comments-template.php';
		return $file;
	}
}
?>
