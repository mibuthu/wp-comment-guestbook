<?php
/**
 * CommentGuestbook Functions Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/config.php';

/**
 * CommentGuestbook Functions Class
 *
 * This class handles some general function required for CommentGuestbook.
 */
class Comments_Functions {

	/**
	 * The used textdomain for the translations
	 *
	 * @var string
	 */
	public $l10n_domain;

	/**
	 * Config class instance reference
	 *
	 * @var Config
	 */
	private $config;

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
	 * @param Config $config_instance The Config instance as a reference.
	 * @return void
	 */
	public function __construct( &$config_instance ) {
		$this->config      = $config_instance;
		$this->l10n_domain = $this->config->l10n_domain->to_str();
		// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		$this->nav_label_prev = __( '&larr; Older Comments', $this->l10n_domain );
		// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		$this->nav_label_next = __( 'Newer Comments &rarr;', $this->l10n_domain );
		if ( 'desc' === $this->config->clist_order->to_str() ) {
			// Switch labels and correct arrow.
			$tmp_label            = $this->nav_label_prev;
			$this->nav_label_prev = '&larr; ' . substr( $this->nav_label_next, 0, - 6 );
			$this->nav_label_next = substr( $tmp_label, 6 ) . ' &rarr;';
		}
		$this->num_forms = 0;
	}


	/**
	 * Get the comments list html with the required plugin modifications
	 *
	 * For the modifications the available arguments for the wp_list_comments function are used.
	 *
	 * @return string|void
	 */
	public function list_comments() {
		$args = [ 'echo' => false ];
		// Comment list args.
		if ( $this->config->clist_args->to_bool() ) {
			$args_array = null;
			// phpcs:ignore Squiz.PHP.Eval.Discouraged
			eval( '$args_array = ' . $this->config->clist_args->to_str() . ';' );
			// @phan-suppress-next-line PhanImpossibleCondition - evaluated through eval
			if ( is_array( $args_array ) ) {
				$args += $args_array;
			}
		}
		// Comment callback function.
		if ( $this->config->comment_adjust->to_bool() && is_callable( $this->config->comment_callback->to_str() ) ) {
			$args['callback'] = $this->config->comment_callback->to_str();
		} else {
			$args['callback'] = [ &$this, 'show_comment_html' ];
		}
		// Fix order of top level comments.
		if ( 'default' !== $this->config->clist_order->to_str() ) {
			$args['reverse_top_level'] = false;
		}
		// Fix order of child comments.
		if ( $this->config->clist_child_order_desc->to_bool() ) {
			$args['reverse_children'] = true;
		}
		// Change child order if top level order is desc due to array_reverse.
		if ( 'desc' === $this->config->clist_order->to_str() ) {
			$args['reverse_children'] = isset( $args['reverse_children'] ) ? ! $args['reverse_children'] : true;
		}
		// Print comments.
		return wp_list_comments( $args );
	}


	/**
	 * Show comment
	 *
	 * @param \WP_Comment          $comment The comment to display.
	 * @param array<string,string> $args The comment args (not used).
	 * @param int                  $depth The depth of the comment (not used).
	 *
	 * @return void
	 *
	 * @suppress PhanUnusedPublicNoOverrideMethodParameter
	 */
	public function show_comment_html( $comment, $args, $depth ) {
		// Define all variables which can be used in show_comments_html text option.
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['comment']         = $comment;
		$l10n_domain                = $this->config->l10n_domain->to_str();
		$is_comment_from_other_page = ( get_the_ID() !== $comment->comment_post_ID );
		$other_page_title           = $is_comment_from_other_page ? get_the_title( intval( $comment->comment_post_ID ) ) : '';
		// @phan-suppress-next-line PhanUnusedVariable - required in eval
		$other_page_link = $is_comment_from_other_page ? '<a href="' . get_page_link( intval( $comment->comment_post_ID ) ) . '">' . $other_page_title . '</a>' : '';
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
						// @phan-suppress-next-line PhanTypeMismatchArgumentProbablyReal -- null is o.k. for comment_class arguments, it's the default value.
						comment_class( '', null, null, false ) . ' id="li-comment-' . esc_attr( strval( get_comment_ID() ) ) . '">
						<article id="comment-' . esc_attr( strval( get_comment_ID() ) ) . '" class="comment">';
				// phpcs:ignore Squiz.PHP.Eval.Discouraged
				eval( '?>' . $this->config->comment_html->to_str() );
				echo '
						</article><!-- #comment-## -->';
				break;
		}
	}


	/**
	 * Get the navigation bar html
	 *
	 * @param string $location The position where the navigation bar should be displayed.
	 *
	 * @return string
	 */
	public function show_nav_html( $location ) {
		if ( get_comment_pages_count() > 1 && (bool) get_option( 'page_comments' ) ) {
			$nav_id = 'comment-nav-' . ( 'above_comments' === $location ? 'above' : 'below' );
			$out    = '<nav id="' . esc_attr( $nav_id ) . '">';

			if ( $this->config->clist_num_pagination->to_bool() ) {
				// Numbered Pagination.
				$out .= '<div class="pagination" style="text-align:center;">';
				$out .= paginate_comments_links(
					[
						'echo'      => false,
						'prev_text' => $this->nav_label_prev,
						'next_text' => $this->nav_label_next,
						'mid_size'  => 3,
					]
				);
				$out .= '</div>';
			} else {
				// Only previous and next links.
				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- $this->get_comment_nav_label is already escaped.
				// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain -- using a variable is required here.
				$out .= '<h1 class="assistive-text">' . esc_html__( 'Comment navigation', $this->l10n_domain ) . '</h1>
					<div class="nav-previous">' . $this->get_comment_nav_label( true ) . '</div>
					<div class="nav-next">' . $this->get_comment_nav_label() . '</div>';
				// phpcs:enable
			}

			$out .= '</nav>';
			return $out;
		}
		return '';
	}


	/**
	 * Get the navigation button label
	 *
	 * @param bool $previous If the label for the previous button shall be displayed instead of the next label.
	 *
	 * @return string|void
	 */
	private function get_comment_nav_label( $previous = false ) {
		if ( $previous ) {
			return get_previous_comments_link( $this->nav_label_prev );
		} else {
			return get_next_comments_link( $this->nav_label_next );
		}
	}


	/**
	 * Get the comment form html with the required plugin modifications
	 *
	 * @param string $location The location where the comment form shall be displayed.
	 *
	 * @return string
	 */
	public function show_comment_form_html( $location ) {
		$out = '';
		// Custom form styles.
		if ( ! (bool) $this->num_forms ) {
			$styles = $this->config->form_styles->to_str();
			// Add styles for foldable forms.
			if ( 'static' === $this->config->form_expand_type->to_str() ) {
				$styles .= '
						div.form-wrapper { display:none; }
						a.form-link:target { display:none; }
						a.form-link:target + div.form-wrapper { display:block; }';
			} elseif ( 'animated' === $this->config->form_expand_type->to_str() ) {
				$styles .= '
						div.form-wrapper { position:absolute; transform:scaleY(0); transform-origin:top; transition:transform 0.3s; }
						a.form-link:target { display:none; }
						a.form-link:target + div.form-wrapper { position:relative; transform:scaleY(1); }';
			}
			// Print styles.
			if ( '' !== $styles ) {
				$out .= '
					<style>
						' . esc_html( $styles ) . '
					</style>';
			}
		}
		$this->num_forms ++;
		// Comment form.
		if ( ( 'above_comments' === $location && $this->config->form_above_comments->to_str() )
			|| ( 'below_comments' === $location && $this->config->form_below_comments->to_str() )
			|| ( 'in_page' === $location )
		) { // The check if the in_page form shall be displayed must be done before this function is called.
			// Add required parts for foldable comment form.
			if ( 'false' !== $this->config->form_expand_type->to_str() ) {
				$out .= '
					<a class="form-link" id="show-form-' . esc_attr( strval( $this->num_forms ) ) . '" href="#show-form-' . esc_attr( strval( $this->num_forms ) ) . '">' .
						esc_html( $this->config->form_expand_link_text->to_str() ) . '</a>
					<div class="form-wrapper">';
			}
			// Print form.
			ob_start();
				comment_form( $this->get_guestbook_comment_form_args() );
				$out .= ob_get_contents();
			ob_end_clean();
			if ( 'false' !== $this->config->form_expand_type->to_str() ) {
				$out .= '</div>';
			}
		}
		return $out;
	}


	/**
	 * Get all args for the guestbook comment form
	 *
	 * @return array<string,string>
	 */
	public function get_guestbook_comment_form_args() {
		$args = [];
		// Form args.
		if ( $this->config->form_args->to_bool() ) {
			$args_array = null;
			// phpcs:ignore Squiz.PHP.Eval.Discouraged
			eval( '$args_array = ' . $this->config->form_args->to_str() . ';' );
			// @phan-suppress-next-line PhanImpossibleCondition - evaluated through eval
			if ( is_array( $args_array ) ) {
				$args += $args_array;
			}
		}
		// Remove mail field.
		if ( $this->config->form_remove_mail->to_bool() ) {
			add_filter( 'comment_form_field_email', '__return_empty_string', 20 );
		}
		// Remove website url field.
		if ( $this->config->form_remove_website->to_bool() ) {
			add_filter( 'comment_form_field_url', '__return_empty_string', 20 );
		}
		// Change comment field label.
		if ( 'default' !== $this->config->form_comment_label->to_str() ) {
			add_filter( 'comment_form_field_comment', [ &$this, 'comment_field_label_filter' ], 20 );
		}
		// title.
		if ( 'default' !== $this->config->form_title->to_str() ) {
			$args['title_reply'] = $this->config->form_title->to_str();
		}
		// title_reply_to.
		if ( 'default' !== $this->config->form_title_reply_to->to_str() ) {
			$args['title_reply_to'] = $this->config->form_title_reply_to->to_str();
		}
		// comment_notes_before.
		if ( 'default' !== $this->config->form_notes_before->to_str() ) {
			$args['comment_notes_before'] = '<div class="comment-notes-before">' . $this->config->form_notes_before->to_str() . '</div>';
		}
		// comment_notes_after.
		if ( 'default' !== $this->config->form_notes_after->to_str() ) {
			$args['comment_notes_after'] = '<div class="comment-notes-after">' . $this->config->form_notes_after->to_str() . '</div>';
		}
		// label_submit.
		if ( 'default' !== $this->config->form_label_submit->to_str() && $this->config->form_label_submit->to_bool() ) {
			$args['label_submit'] = $this->config->form_label_submit->to_str();
		}
		// cancel_reply_link.
		if ( 'default' !== $this->config->form_cancel_reply->to_str() && $this->config->form_cancel_reply->to_bool() ) {
			$args['cancel_reply_link'] = $this->config->form_cancel_reply->to_str();
		}

		// must_login message.
		if ( 'default' !== $this->config->form_must_login_message->to_str() && $this->config->form_must_login_message->to_bool() ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			$args['must_log_in'] = sprintf( $this->config->form_must_login_message->to_str(), wp_login_url( apply_filters( 'the_permalink', get_permalink() ) ) );
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
		if ( ! $comment instanceof \WP_Comment ) {
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
		// Set show_all_comments option.
		$show_all_comments = ( $this->config->adjust_output->to_bool() && $this->config->clist_show_all->to_bool() );
		// Prepare sql string.
		$time_compare_operator = ( 'desc' === $this->config->clist_order->to_str() ) ? '>' : '<';
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
	 * Filter the comment field label according to the setting "cgb_form_comment_label"
	 *
	 * @param string $comment_html The HTML code to filter.
	 *
	 * @return string
	 */
	public function comment_field_label_filter( $comment_html ) {
		return preg_replace( '/(<label.*>)(.*)(<\/label>)/i', '${1}' . $this->config->form_comment_label->to_str() . '${3}', $comment_html, 1 );
	}

}

