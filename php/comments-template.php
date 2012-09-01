<?php
/**
 * The custom template for displaying Comments for comment-guestbook plugin.
 */

require_once( CGB_PATH.'php/options.php' );
$l10n_domain = cgb_options::get( 'cgb_l10n_domain' );
?>
	<div id="comments">
	<?php if ( post_password_required() ) : ?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', $l10n_domain ); ?></p>
	<?php
			/* Stop the rest of comments.php from being processed,
			 * but don't kill the script entirely -- we still have
			 * to fully load the template.
			 */
			echo '</div><!-- #comments -->';
			return;
		endif;
	?>

	<?php if ( have_comments() ) : ?>
   <?php /*
		<h2 id="comments-title">
			<?php
				printf( get_the_title().' Entries:' );
			?>
		</h2>
		*/
   ?>
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above">
			<h1 class="assistive-text"><?php _e( 'Comment navigation', $l10n_domain ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', $l10n_domain ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', $l10n_domain ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

		<ol class="commentlist">
			<?php
				/* Loop through and list the comments. Tell wp_list_comments()
				 * to use the specified function to format the comments.
				 */
				if( cgb_options::get( 'cgb_comment_adjust' ) == '' ) {
					wp_list_comments( array( 'callback' => cgb_options::get( 'cgb_clist_comment_callback' ) ) );
				}
				else {
					require_once( CGB_PATH.'php/comment.php' );
					wp_list_comments( array( 'callback' => array( 'cgb_comment', 'show_html' ) ) );
				}
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below">
			<h1 class="assistive-text"><?php _e( 'Comment navigation', $l10n_domain ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', $l10n_domain ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;' , $l10n_domain) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

	<?php
		/* If there are no comments and comments are closed, let's leave a little note, shall we?
		 * But we don't want the note on pages or post types that do not support comments.
		 */
		elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="nocomments"><?php _e( 'Comments are closed.', $l10n_domain ); ?></p>
	<?php endif; ?>
	<?php /*comment_form();*/ ?>

</div><!-- #comments -->
