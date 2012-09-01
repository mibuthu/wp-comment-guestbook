<?php
require_once( CGB_PATH.'php/options.php' );

// This class handles all available admin pages
class cgb_comment {

	public static function show_html( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		$l10n_domain = cgb_options::get( 'cgb_l10n_domain' );
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
				eval( '?>'.cgb_options::get( 'cgb_comment_html' ) );
				echo '
						</article><!-- #comment-## -->';
				break;
		}
	}
}
?>
