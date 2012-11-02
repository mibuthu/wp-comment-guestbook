<?php
require_once( CGB_PATH.'php/options.php' );

// This class handles all required function to display the comment list
class cgb_comments_functions {
	public $l10n_domain;

	public function __construct() {
		global $cgb;
		$this->l10n_domain = $cgb->options->get( 'cgb_l10n_domain' );
	}

	public function list_comments() {
		global $cgb;
		if( $cgb->options->get( 'cgb_comment_adjust' ) == '' ) {
			wp_list_comments( array( 'callback' => $cgb->options->get( 'cgb_clist_comment_callback' ) ) );
		}
		else {
			wp_list_comments( array( 'callback' => array( &$this, 'show_comment_html' ) ) );
		}
	}
	public function show_comment_html( $comment, $args, $depth ) {
		global $cgb;
		$GLOBALS['comment'] = $comment;
		$l10n_domain = $cgb->options->get( 'cgb_l10n_domain' );
		switch ( $comment->comment_type ) {
			case 'pingback' :
			case 'trackback' :
				echo '
					<li class="post pingback">
					<p>'.__( 'Pingback:', $l10n_domain ).get_comment_author_link().get_edit_comment_link( __( 'Edit', $l10n_domain ), '<span class="edit-link">', '</span>' ).'</p>';
				break;
			default :
				echo '
					<li '.comment_class( '', null, null, false ).' id="li-comment-'.get_comment_ID().'">
						<article id="comment-'.get_comment_ID().'" class="comment">';
				eval( '?>'.$cgb->options->get( 'cgb_comment_html' ) );
				echo '
						</article><!-- #comment-## -->';
				break;
		}
	}
}
?>
