<?php
/**
 * CommentGuestbook Widget Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!
if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once CGB_PATH . 'includes/options.php';
require_once CGB_PATH . 'includes/attribute.php';

/**
 * Comment Guestbook Widget
 */
class CGB_Widget extends WP_Widget {

	/**
	 * Options class instance reference
	 *
	 * @var CGB_Options
	 */
	private $options;

	/**
	 * Widget Items
	 *
	 * @var array<string,CGB_Attribute>
	 */
	private $items;


	/**
	 * Register widget with WordPress.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
			'comment_guestbook_widget', // Base ID.
			'Comment Guestbook', // Name.
			array( 'description' => __( 'This widget displays a list of recent comments.', 'comment-guestbook' ) ) // Args.
		);
		add_action( 'comment_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'transition_comment_status', array( $this, 'flush_widget_cache' ) );
		add_filter( 'safe_style_css', array( $this, 'safe_style_css_filter' ) );
		$this->options = &CGB_Options::get_instance();

		// Define all available items.
		$this->items = array(
			'title'                => new CGB_Attribute( __( 'Recent guestbook entries', 'comment-guestbook' ) ),
			'num_comments'         => new CGB_Attribute( '5' ),
			'link_to_comment'      => new CGB_Attribute( 'false' ),
			'show_date'            => new CGB_Attribute( 'false' ),
			'date_format'          => new CGB_Attribute( get_option( 'date_format' ) ),
			'show_author'          => new CGB_Attribute( 'true' ),
			'author_length'        => new CGB_Attribute( '18' ),
			'show_page_title'      => new CGB_Attribute( 'false' ),
			'page_title_length'    => new CGB_Attribute( '18' ),
			'show_comment_text'    => new CGB_Attribute( 'true' ),
			'comment_text_length'  => new CGB_Attribute( '25' ),
			'url_to_page'          => new CGB_Attribute( '' ),
			'gb_comments_only'     => new CGB_Attribute( 'false' ),
			'hide_gb_page_title'   => new CGB_Attribute( 'false' ),
			'link_to_page'         => new CGB_Attribute( 'false' ),
			'link_to_page_caption' => new CGB_Attribute( __( 'goto guestbook page', 'comment-guestbook' ) ),
		);
	}


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array<string,string> $args     Widget arguments.
	 * @param array<string,string> $instance Saved values from database.
	 * @return string
	 */
	public function widget( $args, $instance ) {
		// Use html from cache if available.
		$cache = wp_cache_get( 'widget_comment_guestbook', 'widget' );
		if ( ! is_array( $cache ) ) {
			$cache = array();
		}
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo wp_kses_post( $cache[ $args['widget_id'] ] );
			return '';
		}

		// Prepare html.
		foreach ( $this->items as $itemname => $item ) {
			if ( ! isset( $instance[ $itemname ] ) ) {
				$instance[ $itemname ] = $item->value;
			}
		}
		$out               = '';
		$instance['title'] = apply_filters( 'widget_title', $instance['title'] );
		$comment_args      = array(
			'number'      => absint( $instance['num_comments'] ),
			'status'      => 'approve',
			'post_status' => 'publish',
		);
		if ( 'true' === $instance['gb_comments_only'] ) {
			$comment_args['post_id'] = url_to_postid( $instance['url_to_page'] );
		}
		$out .= $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			$out .= $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		// Create comment list and keep widget content out of google indexing.
		$out .= '
					<!--googleoff: all-->
					<ul class="cgb-widget">';
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$comments = get_comments( apply_filters( 'widget_comments_args', $comment_args ) );
		if ( is_array( $comments ) && ! empty( $comments ) ) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.).
			$post_ids          = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			$update_term_cache = strpos( get_option( 'permalink_structure' ), '%category%' ) !== false;
			_prime_post_caches( $post_ids, $update_term_cache, false );
			foreach ( $comments as $comment ) {
				if ( ! $comment instanceof WP_Comment ) {
					continue;
				}
				$out .= '
					<li class="cgb-widget-item">';
				if ( 'true' === $instance['link_to_comment'] ) {
					$out .= '<a href="' . $this->get_comment_link( $comment ) . '">';
				}
				if ( 'true' === $instance['show_date'] ) {
					$out .= '<span class="cgb-date" title="' . __( 'Date of comment', 'comment-guestbook' ) . ': ' . esc_attr( get_comment_date( '', $comment ) ) . '">' . get_comment_date( $instance['date_format'], $comment ) . ' </span>';
				}
				if ( 'true' === $instance['show_author'] ) {
					$out .= $this->truncate(
						$instance['author_length'],
						get_comment_author( $comment ),
						'span',
						array(
							'class' => 'cgb-author',
							'title' => __(
								'Comment author',
								'comment-guestbook'
							) . ': ' . esc_attr( get_comment_author( $comment ) ),
						)
					);
				}
				if ( 'true' === $instance['show_page_title'] ) {
					if ( 'true' !== $instance['hide_gb_page_title'] || url_to_postid( $instance['url_to_page'] ) !== intval( $comment->comment_post_ID ) ) {
						$out .= '<span class="cgb-widget-title" title="' . __( 'Page of comment', 'comment-guestbook' ) . ': ' . esc_attr( get_the_title( $comment->comment_post_ID ) ) . '">';
						if ( 'true' === $instance['show_author'] ) {
							$out .= ' ' . __( 'in', 'comment-guestbook' ) . ' ';
						}
						$out .= $this->truncate( $instance['page_title_length'], get_the_title( $comment->comment_post_ID ) ) . '</span>';
					}
				}
				if ( 'true' === $instance['link_to_comment'] ) {
					$out .= '</a>';
				}
				if ( 'true' === $instance['show_comment_text'] ) {
					$out .= $this->truncate(
						$instance['comment_text_length'],
						get_comment_text( $comment ),
						'div',
						array(
							'class' => 'cgb-widget-text',
							'title' => esc_attr( get_comment_text( $comment ) ),
						)
					);
				}
				$out .= '</li>';
			}
		}
		$out .= '
				</ul>
				<!--googleon: all>
				';
		if ( 'true' === $instance['link_to_page'] ) {
			$out .= '
				<div class="cgb-widget-pagelink" style="clear:both"><a title="' . esc_attr( $instance['link_to_page_caption'] ) . '" href="' . $instance['url_to_page'] . '">' .
					$instance['link_to_page_caption'] . '</a></div>
				';
		}
		$out .= $args['after_widget'];
		echo wp_kses_post( $out );
		$cache[ $args['widget_id'] ] = $out;
		wp_cache_set( 'widget_recent_comments', $cache, 'widget' );
	}


	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array<string,string> $new_instance Values just sent to be saved.
	 * @param array<string,string> $old_instance Previously saved values from database.
	 * @return array<string,string> Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$this->load_helptexts();
		$instance = array();
		foreach ( $this->items as $itemname => $item ) {
			if ( 'checkbox' === $item->type ) {
				$instance[ $itemname ] = ( isset( $new_instance[ $itemname ] ) && 1 === intval( $new_instance[ $itemname ] ) ) ? 'true' : 'false';
			} else { // 'text'
				$instance[ $itemname ] = wp_strip_all_tags( $new_instance[ $itemname ] );
			}
		}
		$this->flush_widget_cache();
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_recent_comments'] ) ) {
			delete_option( 'widget_recent_comments' );
		}
		return $instance;
	}


	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array<string,string> $instance Previously saved values from database.
	 * @return string
	 */
	public function form( $instance ) {
		$this->load_helptexts();
		// Display general information at the top.
		echo '<p>' . esc_html__( 'For all options tooltips are available which provide additional help and information. They appear if the mouse is hovered over the options text field or checkbox.', 'comment-guestbook' ) . '</p>';
		// Display the options.
		foreach ( $this->items as $itemname => $item ) {
			if ( ! isset( $instance[ $itemname ] ) ) {
				$instance[ $itemname ] = $item->value;
			}
			$style_text = ( null === $item->form_style ) ? '' : ' style="' . $item->form_style . '"';
			if ( 'checkbox' === $item->type ) {
				$checked_text = ( 'true' === $instance[ $itemname ] || 1 === $instance[ $itemname ] ) ? 'checked = "checked" ' : '';
				echo '
					<p' . wp_kses_post( $style_text ) . ' title="' . esc_attr( $item->tooltip ) . '">
						<label><input class="widefat" id="' . esc_attr( $this->get_field_id( $itemname ) ) . '" name="' . esc_attr( $this->get_field_name( $itemname ) ) .
							'" type="checkbox" ' . esc_attr( $checked_text ) . 'value="1" /> ' . wp_kses_post( $item->caption ) . '</label>
					</p>';
			} else { // 'text'
				$width_text         = ( null === $item->form_width ) ? '' : 'style="width:' . $item->form_width . 'px" ';
				$caption_after_text = ( null === $item->caption_after ) ? '' : '<label> ' . $item->caption_after . '</label>';
				echo '
					<p' . wp_kses_post( $style_text ) . ' title="' . esc_attr( $item->tooltip ) . '">
						<label for="' . esc_attr( $this->get_field_id( $itemname ) ) . '">' . wp_kses_post( $item->caption ) . ' </label>
						<input ' . wp_kses_post( $width_text ) . 'class="widefat" id="' . esc_attr( $this->get_field_id( $itemname ) ) .
							'" name="' . esc_attr( $this->get_field_name( $itemname ) ) . '" type="text" value="' . esc_attr( $instance[ $itemname ] ) . '" />' .
							wp_kses_post( $caption_after_text ) . '
					</p>';
			}
		}
		return '';
	}


	/**
	 * Provides the guestbook specific comment link for pages/posts
	 * where the 'comment-guestbook' shortcode is used.
	 *
	 * @param WP_Comment $comment The WordPress comment object of the comment to retrieve.
	 * @return string
	 */
	private function get_comment_link( $comment ) {
		$link_args = array();
		if ( '' !== $this->options->get( 'cgb_adjust_output' ) ) {
			if ( 0 !== get_option( 'page_comments' ) && 0 < get_option( 'comments_per_page' ) ) {
				if ( 'desc' === $this->options->get( 'cgb_clist_order' )
					|| 'asc' === $this->options->get( 'cgb_clist_order' )
					|| '' !== $this->options->get( 'cgb_clist_show_all' ) ) {
					$pattern = get_shortcode_regex();
					if ( 0 < preg_match_all( '/' . $pattern . '/s', get_post( $comment->comment_post_ID )->post_content, $matches )
							&& array_key_exists( 2, $matches )
							&& in_array( 'comment-guestbook', $matches[2], true ) ) {
						// Shortcode is being used in that page or post.
						$args = array(
							'status' => 'approve',
							'order'  => $this->options->get( 'cgb_clist_order' ),
						);
						if ( '' === $this->options->get( 'cgb_clist_show_all' ) ) {
							$args['post_id'] = $comment->comment_post_ID;
						}
						$comments          = get_comments( $args );
						$toplevel_comments = array();
						foreach ( $comments as $_comment ) {
							if ( 0 === $_comment->comment_parent ) {
								$toplevel_comments[] = $_comment->comment_ID;
							}
						}
						// Switch actual comment to top-level comment.
						$toplevel_comment = $comment;
						while ( 0 !== intval( $toplevel_comment->comment_parent ) ) {
							$toplevel_comment = get_comment( $toplevel_comment->comment_parent );
						}
						$oldercoms         = array_search( $toplevel_comment->comment_ID, $toplevel_comments, true );
						$link_args['page'] = ceil( ( $oldercoms + 1 ) / get_option( 'comments_per_page' ) );
					}
				}
			}
		}
		return esc_url( get_comment_link( $comment->comment_ID, $link_args ) );
	}


	/**
	 * Truncate HTML, close opened tags
	 *
	 * @param int|string           $max_length         The length (number of characters) to which the text will be shortened.
	 *                                                 With "0" the full text will be returned. With "auto" also the complete text will be used,
	 *                                                 but a wrapper div will be added which shortens the text to 1 full line via css.
	 * @param string               $html               The html code which should be shortened.
	 * @param string               $wrapper_type       Defines which kind of wrapper shall be added around the html code if a manual length shall be used.
	 *                                                 The possible values are "none", "div" and "span". With "none" no wrapper will be added, "div" and
	 *                                                 "span" are the 2 available wrapper types.
	 *                                                 If max_length is set to auto a div is mandatory and will be added always, independent of the given value.
	 * @param array<string,string> $wrapper_attributes Additional attributes for the wrapper element. The array key defines the attribute name.
	 * @return string
	 */
	private function truncate( $max_length, $html, $wrapper_type = 'none', $wrapper_attributes = array() ) {
		// Apply wrapper and add required css for autolength (if required).
		$autolength = 'auto' === $max_length ? true : false;
		if ( $autolength ) {
			$wrapper_type = 'div';
		} elseif ( 'div' !== $wrapper_type && 'span' !== $wrapper_type ) {
			$wrapper_type = 'none';
		}
		if ( 'none' !== $wrapper_type ) {
			$wrapper_text = '<' . $wrapper_type;
			foreach ( $wrapper_attributes as $name => $value ) {
				$wrapper_text .= ' ' . $name . '="' . $value . '"';
			}
			if ( $autolength ) {
				$wrapper_text .= ' style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis"';
			}
			$html = $wrapper_text . '>' . $html . '</' . $wrapper_type . '>';
		}

		// Apply full length, when no truncate is required.
		mb_internal_encoding( 'UTF-8' );

		if ( ! is_numeric( $max_length ) || 0 >= intval( $max_length ) || mb_strlen( $html ) <= intval( $max_length ) ) {
			return $html;
		}
		// Apply truncated length.
		$max_length     = intval( $max_length );
		$truncated      = false;
		$printed_length = 0;
		$position       = 0;
		$tags           = array();
		$match          = array();
		$ret            = '';

		while ( $printed_length < $max_length && $this->mb_preg_match( '{</?([a-z]+\d?)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position ) ) {
			$tag          = strval( $match[0][0] );
			$tag_position = intval( $match[0][1] );
			// Print text leading up to the tag.
			$str = mb_substr( $html, $position, $tag_position - $position );
			if ( ! empty( $str ) ) {
				if ( 0 < ( $printed_length + mb_strlen( $str ) > $max_length ) ) {
					$ret           .= mb_substr( $str, 0, $max_length - $printed_length );
					$printed_length = $max_length;
					$truncated      = true;
					break;
				}
				$ret            .= $str;
				$printed_length += mb_strlen( $str );
			}
			if ( '&' === $tag[0] ) {
				// Handle the entity.
				$ret .= $tag;
				$printed_length++;
			} else {
				// Handle the tag.
				$tag_name = strval( $match[1][0] );
				if ( $this->mb_preg_match( '{^</}', $tag ) ) {
					// This is a closing tag.
					$opening_tag = strval( array_pop( $tags ) );
					if ( $opening_tag !== $tag_name ) {
						// Not properly nested tag found: trigger a warning and add the not matching opening tag again.
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
						trigger_error( 'Not properly nested tag found (last opening tag: ' . esc_attr( $opening_tag ) . ', closing tag: ' . esc_attr( $tag_name ) . ')', E_USER_WARNING );
						$tags[] = $opening_tag;
					} else {
						$ret .= $tag;
					}
				} elseif ( $this->mb_preg_match( '{/\s*>$}', $tag ) ) {
					// Self-closing tag.
					$ret .= $tag;
				} else {
					// Opening tag.
					$ret   .= $tag;
					$tags[] = $tag_name;
				}
			}
			// Continue after the tag.
			$position = $tag_position + mb_strlen( $tag );
		}
			// Print any remaining text.
		if ( $printed_length < $max_length && $position < mb_strlen( $html ) ) {
			$ret .= mb_substr( $html, intval( $position ), intval( $max_length - $printed_length ) );
		}
			// Print ellipsis ("...") if the html was truncated.
		if ( $truncated ) {
			$ret .= ' &hellip;';
		}
			// Close any open tags.
		while ( ! empty( $tags ) ) {
			$ret .= '</' . array_pop( $tags ) . '>';
		}
		return $ret;
	}


	/**
	 * Flush the widget cache for the comment-guestbook widgets
	 *
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( 'widget_comment_guestbook', 'widget' );
	}


	/**
	 * Add required css attributes to allowed kses css atttributes
	 *
	 * @param string[] $styles The default allowed styles.
	 * @return string[]
	 */
	public function safe_style_css_filter( $styles ) {
		$styles[] = 'white-space';
		$styles[] = 'text-overflow';
		return $styles;
	}


	/**
	 * Load the widget items helptexts
	 *
	 * @return void
	 */
	public function load_helptexts() {
		global $cgb_widget_items_helptexts;
		require_once CGB_PATH . 'includes/widget-helptexts.php';
		foreach ( $cgb_widget_items_helptexts as $name => $values ) {
			$this->items[ $name ]->update( $values );
		}
		unset( $cgb_widget_items_helptexts );
	}


	/**
	 * A helper function for multibyte preg_match
	 * WARNING: Please notice the limitations regarding supported flags (see $flags description)
	 *
	 * @param string   $pattern  The patern to search for.
	 * @param string   $subject  The input string.
	 * @param string[] $matches  If matches is provided, then it is filled with the results of search.
	 *                           $matches[0] will contain the text that matched the full patter, $matches[1] will
	 *                           have the text that matched the first captured parenthesized subpattern, an so on.
	 * @param int      $flags    Similar to preg_match flags but only 'PREG_OFFSET_CAPTURE' is supported.
	 * @param int      $offset   Normally, the search starts from the beginning of the subject string. The optional
	 *                           parameter offset can be used to specify the alternate place from which to start the
	 *                           search (in characters).
	 * @return bool
	 */
	private function mb_preg_match( $pattern, $subject, &$matches = null, $flags = 0, $offset = 0 ) {
		$offset = strlen( strval( mb_substr( $subject, 0, $offset ) ) );
		$ret    = (bool) preg_match( $pattern, $subject, $matches, $flags, $offset );
		if ( $ret && (bool) ( $flags & PREG_OFFSET_CAPTURE ) ) {
			foreach ( $matches as &$match ) {
				$match[1] = mb_strlen( substr( $subject, 0, $match[1] ) );
			}
		}
		return $ret;
	}

}

