<?php
/**
 * CommentGuestbook Widget Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook\Widget;

use const WordPress\Plugins\mibuthu\CommentGuestbook\PLUGIN_PATH;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/config.php';
require_once PLUGIN_PATH . 'widget/config.php';

/**
 * Comment Guestbook Widget
 */
class Widget extends \WP_Widget {

	/**
	 * Config class instance reference
	 *
	 * @var \WordPress\Plugins\mibuthu\CommentGuestbook\Config
	 */
	private $config;

	/**
	 * Widget Arguments
	 *
	 * @var Config
	 */
	private $args;


	/**
	 * Register widget with WordPress.
	 *
	 * @param \WordPress\Plugins\mibuthu\CommentGuestbook\Config $config_instance The Config instance as a reference.
	 * @return void
	 */
	public function __construct( &$config_instance ) {
		$this->config = $config_instance;
		$this->args   = new Config();
		parent::__construct(
			'comment_guestbook_widget', // Base ID.
			'Comment Guestbook', // Name.
			[ 'description' => __( 'This widget displays a list of recent comments.', 'comment-guestbook' ) ] // Args.
		);
		add_action( 'comment_post', [ $this, 'flush_widget_cache' ] );
		add_action( 'transition_comment_status', [ $this, 'flush_widget_cache' ] );
		add_filter( 'safe_style_css', [ $this, 'safe_style_css_filter' ] );
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
			$cache = [];
		}
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo wp_kses_post( $cache[ $args['widget_id'] ] );
			return '';
		}

		// Prepare args config
		$this->args->set_from_instance( $instance );
		unset( $instance );
		$this->args->title->value = apply_filters( 'widget_title', $this->args->title->value );

		// Get the comments
		$comments = $this->get_comments();

		// Prepare the output html
		$out = $args['before_widget'];
		if ( ! empty( $this->args->title ) ) {
			$out .= $args['before_title'] . $this->args->title->as_str() . $args['after_title'];
		}
		// Create comment list and keep widget content out of google indexing.
		$out .= '
		<!--googleoff: all-->
		<ul class="cgb-widget">';
		if ( is_array( $comments ) && ! empty( $comments ) ) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.).
			$post_ids          = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			$update_term_cache = strpos( get_option( 'permalink_structure' ), '%category%' ) !== false;
			_prime_post_caches( $post_ids, $update_term_cache, false );
			foreach ( $comments as $comment ) {
				$out .= $this->comment_html( $comment );
			}
		}
		$out .= '
				</ul>
				<!--googleon: all>
				';
		if ( $this->args->link_to_page->is_true() ) {
			$out .= '
				<div class="cgb-widget-pagelink" style="clear:both"><a title="' . esc_attr( $this->args->link_to_page_caption->as_str() ) . '" href="' . $this->args->url_to_page->as_str() . '">' .
					$this->args->link_to_page_caption->as_str() . '</a></div>
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
	 * @see \WP_Widget::update()
	 *
	 * @param array<string,string> $new_instance Values just sent to be saved.
	 * @param array<string,string> $old_instance Previously saved values from database (not used).
	 * @return array<string,string> Updated safe values to be saved.
	 *
	 * @suppress PhanUnusedPublicMethodParameter
	 */
	public function update( $new_instance, $old_instance ) {
		$this->args->load_args_admin_data();
		$instance = [];
		foreach ( array_keys( $this->args->get_all() ) as $argname ) {
			if ( 'checkbox' === $this->args->admin_data->$argname->display_type ) {
				$instance[ $argname ] = ( isset( $new_instance[ $argname ] ) && 1 === intval( $new_instance[ $argname ] ) ) ? 'true' : 'false';
			} else { // 'text'
				$instance[ $argname ] = wp_strip_all_tags( $new_instance[ $argname ] );
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
	 * @see \WP_Widget::form()
	 *
	 * @param array<string,string> $instance Previously saved values from database.
	 * @return string
	 */
	public function form( $instance ) {
		$this->args->load_args_admin_data();
		// Display general information at the top.
		echo '<p>' . esc_html__( 'For all options tooltips are available which provide additional help and information. They appear if the mouse is hovered over the options text field or checkbox.', 'comment-guestbook' ) . '</p>';
		// Display the options.
		foreach ( $this->args->get_all() as $argname => $arg ) {
			$arg_admin_data = $this->args->admin_data->$argname;
			if ( ! isset( $instance[ $argname ] ) ) {
				$instance[ $argname ] = $arg->value;
			}
			$style_text = ( null === $arg_admin_data->form_style ) ? '' : ' style="' . $arg_admin_data->form_style . '"';
			if ( 'checkbox' === $arg_admin_data->display_type ) {
				$checked_text = ( 'true' === $instance[ $argname ] || 1 === $instance[ $argname ] ) ? 'checked = "checked" ' : '';
				echo '
					<p' . wp_kses_post( $style_text ) . ' title="' . esc_attr( $arg_admin_data->tooltip ) . '">
						<label><input class="widefat" id="' . esc_attr( $this->get_field_id( $argname ) ) . '" name="' . esc_attr( $this->get_field_name( $argname ) ) .
							'" type="checkbox" ' . esc_attr( $checked_text ) . 'value="1" /> ' . wp_kses_post( $arg_admin_data->caption ) . '</label>
					</p>';
			} else { // 'text'
				$width_text         = ( null === $arg_admin_data->form_width ) ? '' : 'style="width:' . $arg_admin_data->form_width . 'px" ';
				$caption_after_text = ( null === $arg_admin_data->caption_after ) ? '' : '<label> ' . $arg_admin_data->caption_after . '</label>';
				echo '
					<p' . wp_kses_post( $style_text ) . ' title="' . esc_attr( $arg_admin_data->tooltip ) . '">
						<label for="' . esc_attr( $this->get_field_id( $argname ) ) . '">' . wp_kses_post( $arg_admin_data->caption ) . ' </label>
						<input ' . wp_kses_post( $width_text ) . 'class="widefat" id="' . esc_attr( $this->get_field_id( $argname ) ) .
							'" name="' . esc_attr( $this->get_field_name( $argname ) ) . '" type="text" value="' . esc_attr( $instance[ $argname ] ) . '" />' .
							wp_kses_post( $caption_after_text ) . '
					</p>';
			}
		}
		return '';
	}


	/**
	 * Get the comments
	 *
	 * @return \WP_Comment[]
	 */
	private function get_comments() {
		$comment_args = [
			'number'      => $this->args->num_comments->as_int(),
			'status'      => 'approve',
			'post_status' => 'publish',
		];
		if ( $this->args->gb_comments_only->is_true() ) {
			$comment_args['post_id'] = url_to_postid( $this->args->url_to_page->as_str() );
		}

		$comments = get_comments( apply_filters( 'widget_comments_args', $comment_args ) );
		if ( is_array( $comments ) ) {
			return $comments;
		} else {
			return [];
		}
	}


	/**
	 * Get the comment html
	 *
	 * @param \WP_Comment $comment The comment object
	 * @return string
	 */
	private function comment_html( $comment ) {
		$out = '
			<li class="cgb-widget-item">';
		if ( $this->args->link_to_comment->is_true() ) {
			$out .= '<a href="' . $this->get_comment_link( $comment ) . '">';
		}
		if ( $this->args->show_date->is_true() ) {
			$out .= '<span class="cgb-date" title="' . __( 'Date of comment', 'comment-guestbook' ) . ': ' .
				esc_attr( get_comment_date( '', $comment ) ) . '">' . get_comment_date( $this->args->date_format->as_str(), $comment ) . ' </span>';
		}
		if ( $this->args->show_author->is_true() ) {
			$out .= $this->truncate(
				$this->args->author_length->as_str(),
				get_comment_author( $comment ),
				'span',
				[
					'class' => 'cgb-author',
					'title' => __(
						'Comment author',
						'comment-guestbook'
					) . ': ' . esc_attr( get_comment_author( $comment ) ),
				]
			);
		}
		if ( $this->args->show_page_title->is_true() ) {
			if ( $this->args->hide_gb_page_title->is_false() || url_to_postid( $this->args->url_to_page->as_str() ) !== intval( $comment->comment_post_ID ) ) {
				$out .= '<span class="cgb-widget-title" title="' . __( 'Page of comment', 'comment-guestbook' ) . ': ' . esc_attr( get_the_title( intval( $comment->comment_post_ID ) ) ) . '">';
				if ( $this->args->show_author->is_true() ) {
					$out .= ' ' . __( 'in', 'comment-guestbook' ) . ' ';
				}
				$out .= $this->truncate( $this->args->page_title_length->as_int(), get_the_title( intval( $comment->comment_post_ID ) ) ) . '</span>';
			}
		}
		if ( $this->args->link_to_comment->is_true() ) {
			$out .= '</a>';
		}
		if ( $this->args->show_comment_text->is_true() ) {
			$out .= $this->truncate(
				$this->args->comment_text_length->as_int(),
				get_comment_text( $comment ),
				'div',
				[
					'class' => 'cgb-widget-text',
					'title' => esc_attr( get_comment_text( $comment ) ),
				]
			);
		}
		$out .= '</li>';
		return $out;
	}


	/**
	 * Provides the guestbook specific comment link for pages/posts
	 * where the 'comment-guestbook' shortcode is used.
	 *
	 * @param \WP_Comment $comment The WordPress comment object of the comment to retrieve.
	 * @return string
	 */
	private function get_comment_link( $comment ) {
		$link_args = [];
		if ( $this->config->adjust_output->is_true() ) {
			if ( 0 !== get_option( 'page_comments' ) && 0 < get_option( 'comments_per_page' ) ) {
				if ( 'desc' === $this->config->clist_order->as_str() || 'asc' === $this->config->clist_order->as_str() || $this->config->clist_show_all->is_true() ) {
					$pattern = get_shortcode_regex();
					// @phan-suppress-next-line PhanPossiblyUndeclaredProperty - no problem here.
					if ( 0 < preg_match_all( '/' . $pattern . '/s', get_post( intval( $comment->comment_post_ID ) )->post_content, $matches )
							&& array_key_exists( 2, $matches )
							&& in_array( 'comment-guestbook', $matches[2], true ) ) {
						// Shortcode is being used in that page or post.
						$args = [
							'status' => 'approve',
							'order'  => $this->config->clist_order->as_str(),
						];
						if ( $this->config->clist_show_all->is_false() ) {
							$args['post_id'] = $comment->comment_post_ID;
						}
						$comments          = get_comments( $args );
						$toplevel_comments = [];
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
						// @phan-suppress-next-line PhanPossiblyUndeclaredProperty - no problem here.
						$oldercoms         = array_search( $toplevel_comment->comment_ID, $toplevel_comments, true );
						$link_args['page'] = ceil( ( $oldercoms + 1 ) / get_option( 'comments_per_page' ) );
					}
				}
			}
		}
		return esc_url( get_comment_link( intval( $comment->comment_ID ), $link_args ) );
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
	private function truncate( $max_length, $html, $wrapper_type = 'none', $wrapper_attributes = [] ) {
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
		$tags           = [];
		$match          = [];
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

