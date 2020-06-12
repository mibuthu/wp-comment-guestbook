<?php
/**
 * CommentGuestbook Functions Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!
if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once CGB_PATH . 'includes/options.php';

/**
 * CommentGuestbook Functions Class
 *
 * This class handles some general function required for CommentGuestbook.
 */
class CGB_Comments_Functions {

	/**
	 * Class singleton instance reference
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * The used textdomain for the translations
	 *
	 * @var string
	 */
	public $l10n_domain;

	/**
	 * Options class instance reference
	 *
	 * @var CGB_Options
	 */
	private $options;

	/**
	 * Navigation preview button label text
	 *
	 * @var string
	 */
	private $nav_label_prev;

	/**
	 * Navigation next button label text
	 *
	 * @var string
	 */
	private $nav_label_next;

	/**
	 * Number of forms
	 *
	 * @var int
	 */
	private $num_forms;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @return void
	 */
	private function __construct() {
		$this->options     = &CGB_Options::get_instance();
		$this->l10n_domain = $this->options->get( 'cgb_l10n_domain' );
		// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		$this->nav_label_prev = __( '&larr; Older Comments', $this->l10n_domain );
		// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		$this->nav_label_next = __( 'Newer Comments &rarr;', $this->l10n_domain );
		if ( 'desc' === $this->options->get( 'cgb_clist_order' ) ) {
			// Switch labels and correct arrow.
			$tmp_label            = $this->nav_label_prev;
			$this->nav_label_prev = '&larr; ' . substr( $this->nav_label_next, 0, - 6 );
			$this->nav_label_next = substr( $tmp_label, 6 ) . ' &rarr;';
		}
		$this->num_forms = 0;
	}


	/**
	 * Singleton provider and setup
	 *
	 * @return self
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
	 * Show the comments list with the required adaptations
	 *
	 * @return void
	 */
	public function list_comments() {
		$args = array();
		// Comment list args.
		if ( '' !== $this->options->get( 'cgb_clist_args' ) ) {
			$args_array = null;
			// phpcs:ignore Squiz.PHP.Eval.Discouraged
			eval( '$args_array = ' . $this->options->get( 'cgb_clist_args' ) . ';' );
			if ( is_array( $args_array ) ) {
				$args += $args_array;
			}
		}
		// Comment callback function.
		if ( '' === $this->options->get( 'cgb_comment_adjust' ) && is_callable( $this->options->get( 'cgb_comment_callback' ) ) ) {
			$args['callback'] = $this->options->get( 'cgb_comment_callback' );
		} else {
			$args['callback'] = array( &$this, 'show_comment_html' );
		}
		// Fix order of top level comments.
		if ( 'default' !== $this->options->get( 'cgb_clist_order' ) ) {
			$args['reverse_top_level'] = false;
		}
		// Fix order of child comments.
		if ( 'desc' === $this->options->get( 'cgb_clist_child_order' ) ) {
			$args['reverse_children'] = true;
		} elseif ( 'asc' === $this->options->get( 'cgb_clist_child_order' ) ) {
			$args['reverse_children'] = false;
		}
		// Change child order if top level order is desc due to array_reverse.
		if ( 'desc' === $this->options->get( 'cgb_clist_order' ) ) {
			$args['reverse_children'] = isset( $args['reverse_children'] ) ? ! $args['reverse_children'] : true;
		}
		// Print comments.
		wp_list_comments( $args );
	}


	/**
	 * Show comment
	 *
	 * @param WP_Comment           $comment The comment to display.
	 * @param array<string,string> $args The comment args (not used).
	 * @param int                  $depth The depth of the comment (not used).
	 *
	 * @return void
	 */
	public function show_comment_html( $comment, $args, $depth ) {
		// Define all variables which can be used in show_comments_html text option.
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['comment']         = $comment;
		$l10n_domain                = $this->options->get( 'cgb_l10n_domain' );
		$is_comment_from_other_page = ( get_the_ID() !== $comment->comment_post_ID );
		$other_page_title           = $is_comment_from_other_page ? get_the_title( $comment->comment_post_ID ) : '';
		$other_page_link            = $is_comment_from_other_page ? '<a href="' . get_page_link( $comment->comment_post_ID ) . '">' . $other_page_title . '</a>' : '';
		switch ( $comment->comment_type ) {
			case 'pingback':
			case 'trackback':
				echo '
					<li class="post pingback">
					<p>' .
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
					esc_html__( 'Pingback:', $l10n_domain ) .
					get_comment_author_link() .
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
					'<span class="edit-link"><a href="' . esc_html( strval( get_edit_comment_link() ) ) . '">' . esc_html__( 'Edit', $l10n_domain ) . '</a></span>' .
					'</p>';
				break;
			default:
				echo '
					<li ' .
						// @phan-suppress-next-line PhanTypeMismatchArgument -- null is o.k. for comment_class arguments, it's the default value.
						comment_class( '', null, null, false ) . ' id="li-comment-' . esc_attr( strval( get_comment_ID() ) ) . '">
						<article id="comment-' . esc_attr( strval( get_comment_ID() ) ) . '" class="comment">';
				// phpcs:ignore Squiz.PHP.Eval.Discouraged
				eval( '?>' . $this->options->get( 'cgb_comment_html' ) );
				echo '
						</article><!-- #comment-## -->';
				break;
		}
	}


	/**
	 * Show the navigation bar
	 *
	 * @param string $location The position where the navigation bar should be displayed.
	 *
	 * @return void
	 */
	public function show_nav_html( $location ) {
		if ( get_comment_pages_count() > 1 && (bool) get_option( 'page_comments' ) ) {
			$nav_id = 'comment-nav-' . ( 'above_comments' === $location ? 'above' : 'below' );
			echo '<nav id="' . esc_attr( $nav_id ) . '">';

			if ( '' !== $this->options->get( 'cgb_clist_num_pagination' ) ) {
				// Numbered Pagination.
				echo '<div class="pagination" style="text-align:center;">';
				paginate_comments_links(
					array(
						'prev_text' => $this->nav_label_prev,
						'next_text' => $this->nav_label_next,
						'mid_size'  => 3,
					)
				);
				echo '</div>';
			} else {
				// Only previous and next links.
				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- $this->get_comment_nav_label is already escaped.
				// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain -- using a variable is required here.
				echo '<h1 class="assistive-text">' . esc_html__( 'Comment navigation', $this->l10n_domain ) . '</h1>
					<div class="nav-previous">' . $this->get_comment_nav_label( true ) . '</div>
					<div class="nav-next">' . $this->get_comment_nav_label() . '</div>';
				// phpcs:enable
			}

			echo '</nav>';
		}
	}


	/**
	 * Get the navigation button label
	 *
	 * @param bool $previous If the label for the previous button shall be displayed instead of the next label.
	 *
	 * @return string
	 */
	private function get_comment_nav_label( $previous = false ) {
		ob_start();
		if ( $previous ) {
			previous_comments_link( $this->nav_label_prev );
		} else {
			next_comments_link( $this->nav_label_next );
		}
		$out = strval( ob_get_contents() );
		ob_end_clean();

		return $out;
	}


	/**
	 * Show the comment form with thre required plugin adaptations
	 *
	 * @param string $location The location where the comment form shall be displayed.
	 *
	 * @return void
	 */
	public function show_comment_form_html( $location ) {
		// Print custom form styles.
		if ( ! (bool) $this->num_forms ) {
			$styles = $this->options->get( 'cgb_form_styles' );
			// Add styles for foldable forms.
			if ( 'static' === $this->options->get( 'cgb_form_expand_type' ) ) {
				$styles .= '
						div.form-wrapper { display:none; }
						a.form-link:target { display:none; }
						a.form-link:target + div.form-wrapper { display:block; }';
			} elseif ( 'animated' === $this->options->get( 'cgb_form_expand_type' ) ) {
				$styles .= '
						div.form-wrapper { position:absolute; transform:scaleY(0); transform-origin:top; transition:transform 0.3s; }
						a.form-link:target { display:none; }
						a.form-link:target + div.form-wrapper { position:relative; transform:scaleY(1); }';
			}
			// Print styles.
			if ( '' !== $styles ) {
				echo '
					<style>
						' . esc_html( $styles ) . '
					</style>';
			}
		}
		$this->num_forms ++;
		// Show form.
		if ( ( 'above_comments' === $location && '' !== $this->options->get( 'cgb_form_above_comments' ) )
			|| ( 'below_comments' === $location && '' !== $this->options->get( 'cgb_form_below_comments' ) )
			|| ( 'in_page' === $location )
		) { // The check if the in_page form shall be displayed must be done before this function is called.
			// Add required parts for foldable comment form.
			if ( 'false' !== $this->options->get( 'cgb_form_expand_type' ) ) {
				echo '
					<a class="form-link" id="show-form-' . esc_attr( strval( $this->num_forms ) ) . '" href="#show-form-' . esc_attr( strval( $this->num_forms ) ) . '">' .
						esc_html( $this->options->get( 'cgb_form_expand_link_text' ) ) . '</a>
					<div class="form-wrapper">';
			}
			// Print form.
			comment_form( $this->get_guestbook_comment_form_args() );
			if ( 'false' !== $this->options->get( 'cgb_form_expand_type' ) ) {
				echo '</div>';
			}
		}
	}


	/**
	 * Get all args for the guestbook comment form
	 *
	 * @return array<string,string>
	 */
	public function get_guestbook_comment_form_args() {
		$args = array();
		// Form args.
		if ( '' !== $this->options->get( 'cgb_form_args' ) ) {
			$args_array = null;
			// phpcs:ignore Squiz.PHP.Eval.Discouraged
			eval( '$args_array = ' . $this->options->get( 'cgb_form_args' ) . ';' );
			if ( is_array( $args_array ) ) {
				$args += $args_array;
			}
		}
		// Remove mail field.
		if ( '' !== $this->options->get( 'cgb_form_remove_mail' ) ) {
			add_filter( 'comment_form_field_email', '__return_empty_string', 20 );
		}
		// Remove website url field.
		if ( '' !== $this->options->get( 'cgb_form_remove_website' ) ) {
			add_filter( 'comment_form_field_url', '__return_empty_string', 20 );
		}
		// Change comment field label.
		if ( 'default' !== $this->options->get( 'cgb_form_comment_label' ) ) {
			add_filter( 'comment_form_field_comment', array( &$this, 'comment_field_label_filter' ), 20 );
		}
		// title.
		if ( 'default' !== $this->options->get( 'cgb_form_title' ) ) {
			$args['title_reply'] = $this->options->get( 'cgb_form_title' );
		}
		// title_reply_to.
		if ( 'default' !== $this->options->get( 'cgb_form_title_reply_to' ) ) {
			$args['title_reply_to'] = $this->options->get( 'cgb_form_title_reply_to' );
		}
		// comment_notes_before.
		if ( 'default' !== $this->options->get( 'cgb_form_notes_before' ) ) {
			$args['comment_notes_before'] = $this->options->get( 'cgb_form_notes_before' );
		}
		// comment_notes_after.
		if ( 'default' !== $this->options->get( 'cgb_form_notes_after' ) ) {
			$args['comment_notes_after'] = $this->options->get( 'cgb_form_notes_after' );
		}
		// label_submit.
		$option = $this->options->get( 'cgb_form_label_submit' );
		if ( 'default' !== $option && '' !== $option ) {
			$args['label_submit'] = $option;
		}
		// cancel_reply_link.
		$option = $this->options->get( 'cgb_form_cancel_reply' );
		if ( 'default' !== $option && '' !== $option ) {
			$args['cancel_reply_link'] = $option;
		}

		// must_login message.
		$option = $this->options->get( 'cgb_form_must_login_message' );
		if ( 'default' !== $option && '' !== $option ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			$args['must_log_in'] = sprintf( $option, wp_login_url( apply_filters( 'the_permalink', get_permalink() ) ) );
		}

		return $args;
	}


	/**
	 * Get the actual comment page of a specific comment
	 *
	 * @param int         $comment_id The comment from which the page shall be returned.
	 * @param null|string $comment_author The comment author.
	 *
	 * @return int
	 */
	public function get_page_of_comment( $comment_id, $comment_author = null ) {
		global $wpdb;
		$comment = get_comment( $comment_id );
		if ( ! $comment instanceof WP_Comment ) {
			return 1;
		}
		// Set initial comment author (required for threaded comments).
		if ( null === $comment_author ) {
			$comment_author = $comment->comment_author;
		}
		// Set max. depth option.
		if ( (bool) get_option( 'thread_comments' ) ) {
			$max_depth = intval( get_option( 'thread_comments_depth' ) );
		} else {
			$max_depth = - 1;
		}
		// Find this comment's top level parent if threading is enabled.
		if ( $max_depth > 1 && 0 !== intval( $comment->comment_parent ) ) {
			return $this->get_page_of_comment( intval( $comment->comment_parent ), $comment_author );
		}
		// Set per_page option.
		$per_page = intval( get_option( 'comments_per_page' ) );
		if ( $per_page < 1 ) {
			return 1;
		}
		// Set sort_direction option.
		$sort_direction = $this->options->get( 'cgb_clist_order' );
		// Set show_all_comments option.
		$show_all_comments = ( '' !== $this->options->get( 'cgb_adjust_output' ) && '' !== $this->options->get( 'cgb_clist_show_all' ) );
		// Prepare sql string.
		$time_compare_operator = ( 'desc' === $sort_direction ) ? '>' : '<';
		$sql                   =
			'SELECT COUNT(comment_ID) FROM ' . $wpdb->comments .
			' WHERE comment_parent = 0 AND (comment_approved = "1" OR (comment_approved = "0" AND comment_author = "%s"))' .
			' AND comment_date_gmt ' . $time_compare_operator . ' "%s"';
		// Count comments older/newer than the actual one.
		if ( $show_all_comments ) {
			// TODO: Use caching for db queries.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$result = $wpdb->get_var(
				$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					$sql,
					$comment_author,
					$comment->comment_date_gmt
				)
			);
		} else {
			// TODO: Use caching for db queries.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$result = $wpdb->get_var(
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- number is correct due to $sql variable
				$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					$sql . ' AND comment_post_ID = %d',
					$comment_author,
					$comment->comment_date_gmt,
					$comment->comment_post_ID
				)
			);
		}

		// Divide result by comments per page to get this comment's page number.
		return intval( ceil( ( intval( $result ) + 1 ) / $per_page ) );
	}


	/**
	 * Get all comments of all posts/pages or a specific post/page id
	 *
	 * @param null|int $post_id The optional post id where the comments shall be displayed from. Use null for all posts/pages.
	 *
	 * @return WP_Comment[]
	 */
	public function get_comments( $post_id = null ) {
		// TODO: Use API instead of SELECTs. (see same todo in wp-includes/comment-template.php line 881 (tag 3.6).
		global $wpdb;
		$commenter            = wp_get_current_commenter();
		$comment_author       = $commenter['comment_author'];
		$comment_author_email = $commenter['comment_author_email'];
		if ( null === $post_id ) {
			// Comment from all pages/posts.
			if ( (bool) get_current_user_id() ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$comments = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT * FROM ' . $wpdb->comments . '
							WHERE comment_approved = "1" OR (user_id = %d AND comment_approved = "0")
							ORDER BY comment_date_gmt',
						get_current_user_id()
					)
				);
			} elseif ( empty( $comment_author ) ) {
				$comments = get_comments(
					array(
						'status' => 'approve',
						'order'  => 'ASC',
					)
				);
			} else {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$comments = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT * FROM ' . $wpdb->comments . '
							WHERE comment_approved = "1" OR (comment_author = %s AND comment_author_email = %s AND comment_approved = "0")
							ORDER BY comment_date_gmt',
						wp_specialchars_decode( $comment_author, ENT_QUOTES ),
						$comment_author_email
					)
				);
			}
		} else {
			// Only comments of given page/post.
			if ( (bool) get_current_user_id() ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$comments = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT * FROM ' . $wpdb->comments . '
							WHERE comment_post_ID = %d AND (comment_approved = "1" OR (user_id = %d AND comment_approved = "0"))
							ORDER BY comment_date_gmt',
						$post_id,
						get_current_user_id()
					)
				);
			} elseif ( empty( $comment_author ) ) {
				$comments = get_comments(
					array(
						'post_id' => $post_id,
						'status'  => 'approve',
						'order'   => 'ASC',
					)
				);
			} else {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$comments = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT * FROM ' . $wpdb->comments . '
							WHERE comment_post_ID = %d AND (comment_approved = "1" OR (comment_author = %s AND comment_author_email = %s AND comment_approved = "0"))
							ORDER BY comment_date_gmt',
						$post_id,
						wp_specialchars_decode( $comment_author, ENT_QUOTES ),
						$comment_author_email
					)
				);
			}
		}
		return is_array( $comments ) ? $comments : array();
	}


	/**
	 * Filter the comment field label according to the setting "cgb_form_comment_label"
	 *
	 * @param string $comment_html The HTML code to filter.
	 *
	 * @return string
	 */
	public function comment_field_label_filter( $comment_html ) {
		return preg_replace( '/(<label.*>)(.*)(<\/label>)/i', '${1}' . $this->options->get( 'cgb_form_comment_label' ) . '${3}', $comment_html, 1 );
	}

}

