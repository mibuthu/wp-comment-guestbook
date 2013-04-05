<?php
/**
 * Comment Guestbook Widget
*/
class comment_guestbook_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
				'comment_guestbook_widget', // Base ID
				'Comment Guestbook', // Name
				array( 'description' => __( 'This widget displays a list of recent comments. If you want to enable a link to the guestbook page you have to insert a link address to the comment-guestbook page.', 'text_domain' ), ) // Args
		);
		add_action( 'comment_post', array($this, 'flush_widget_cache') );
		add_action( 'transition_comment_status', array($this, 'flush_widget_cache') );
	}

	public function flush_widget_cache() {
		wp_cache_delete('widget_comment_guestbook', 'widget');
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $comments, $comment;

		$cache = wp_cache_get('widget_comment_guestbook', 'widget');
		if( ! is_array( $cache ) ) {
			$cache = array();
		}
		if( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
		if( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}
		extract($args, EXTR_SKIP);
		$out = '';
		$title = apply_filters( 'widget_title', $instance['title'] );
		if( empty( $instance['num_comments'] ) || ! $num_comments = absint( $instance['num_comments'] ) ) {
			$num_comments = 5;
		}
		$comment_args = array( 'number' => $num_comments, 'status' => 'approve', 'post_status' => 'publish' );
		if( 'true' === $instance['gb_comments_only'] ) {
			$comment_args['post_id'] = url_to_postid( $instance['url_to_page'] );
		}
		$comments = get_comments( apply_filters( 'widget_comments_args', $comment_args ) );
		$out .= $before_widget;
		if( $title ) {
			$out .= $before_title . $title . $after_title;
		}
		$out .= '<ul class="cgb-widget">';
		if( $comments ) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );
			foreach( (array) $comments as $comment) {
				$out .= '<li class="cgb-widget-item">';
				if( 'true' === $instance['link_to_comment'] ) {
					$out .= '<a href="'.esc_url( get_comment_link( $comment->comment_ID ) ).'">';
				}
				if( 'true' === $instance['show_date'] ) {
					$out .= '<span class="cgb-date" title="'.__( 'Date of comment:' ).' '.get_comment_date().'">'.get_comment_date( $instance['date_format'] ).' </span>';
				}
				if( 'true' === $instance['show_author'] ) {
					$out .= '<span class="cgb-author" title="'.__( 'Comment author:' ).' '.get_comment_author().'">'.get_comment_author().'</span>';
				}
				if( 'true' === $instance['show_page_title'] ) {
					if( 'false' === $instance['hide_gb_page_title'] || url_to_postid( $instance['url_to_page'] ) != $comment->comment_post_ID ) {
						$out .= '<span class="cgb-widget-title" title="'.__( 'Page of Comment:' ).' '.get_the_title( $comment->comment_post_ID ).'">';
						if( 'true' === $instance['show_author'] ) {
							$out .= ' '.__( 'in' ).' ';
						}
						$out .= $this->truncate( $instance['page_title_length'], get_the_title( $comment->comment_post_ID ) ).'</span>';
					}
				}
				if( 'true' === $instance['link_to_comment'] ) {
					$out .= '</a>';
				}
				if( 'true' === $instance['show_comment_text'] ) {
					$out .= '<div class="cgb-widget-text" title="'.strip_tags( get_comment_text() ).'">'.$this->truncate( $instance['comment_text_length'], get_comment_text() ).'</div>';
				}
				$out .= '</li>';
			}
		}
		$out .= '</ul>';
		if( 'true' === $instance['link_to_page'] ) {
			$out .= '<div class="cgb-widget-pagelink" style="clear:both"><a title="'.$instance['link_to_page_caption'].'" href="'.$instance[ 'url_to_page'].'">'.$instance['link_to_page_caption'].'</a></div>';
		}
		$out .= $after_widget;
		echo $out;
		$cache[$args['widget_id']] = $out;
		wp_cache_set('widget_recent_comments', $cache, 'widget');
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['num_comments'] = strip_tags( $new_instance['num_comments'] );
		$instance['link_to_comment'] = ( isset( $new_instance['link_to_comment'] ) && 1==$new_instance['link_to_comment'] ) ? 'true' : 'false';
		$instance['show_date'] = ( isset( $new_instance['show_date'] ) && 1==$new_instance['show_date'] ) ? 'true' : 'false';
		$instance['date_format'] = strip_tags( $new_instance['date_format'] );
		$instance['show_author'] = ( isset( $new_instance['show_author'] ) && 1==$new_instance['show_author'] ) ? 'true' : 'false';
		$instance['show_page_title'] = ( isset( $new_instance['show_page_title'] ) && 1==$new_instance['show_page_title'] ) ? 'true' : 'false';
		$instance['page_title_length'] = strip_tags( $new_instance['page_title_length'] );
		$instance['show_comment_text'] = ( isset( $new_instance['show_comment_text'] ) && 1==$new_instance['show_comment_text'] ) ? 'true' : 'false';
		$instance['comment_text_length'] = strip_tags( $new_instance['comment_text_length'] );
		$instance['url_to_page'] = strip_tags( $new_instance['url_to_page'] );
		$instance['gb_comments_only'] = ( isset( $new_instance['gb_comments_only'] ) && 1==$new_instance['gb_comments_only'] ) ? 'true' : 'false';
		$instance['link_to_page'] = ( isset( $new_instance['link_to_page'] ) && 1==$new_instance['link_to_page'] ) ? 'true' : 'false';
		$instance['link_to_page_caption'] = strip_tags( $new_instance['link_to_page_caption'] );
		$instance['hide_gb_page_title'] = ( isset( $new_instance['hide_gb_page_title'] ) && 1==$new_instance['hide_gb_page_title'] ) ? 'true' : 'false';

		$this->flush_widget_cache();
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_comments']) ) {
			delete_option('widget_recent_comments');
		}

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title =                isset( $instance['title'] )                ? $instance['title']                : __( 'Recent guestbook entries', 'text_domain' );
		$num_comments =         isset( $instance['num_comments'] )         ? $instance['num_comments']         : '5';
		$link_to_comment =      isset( $instance['link_to_comment'] )      ? $instance['link_to_comment']      : 'false';
		$show_date =            isset( $instance['show_date'] )            ? $instance['show_date']            : 'false';
		$date_format =          isset( $instance['date_format'] )          ? $instance['date_format']          : get_option( 'date_format' );
		$show_author =          isset( $instance['show_author'] )          ? $instance['show_author']          : 'true';
		$show_page_title =      isset( $instance['show_page_title'] )      ? $instance['show_page_title']      : 'false';
		$page_title_length =    isset( $instance['page_title_length'] )    ? $instance['page_title_length']    : '18';
		$show_comment_text =    isset( $instance['show_comment_text'] )    ? $instance['show_comment_text']    : 'true';
		$comment_text_length =  isset( $instance['comment_text_length'] )  ? $instance['comment_text_length']  : '25';
		$url_to_page =          isset( $instance['url_to_page'] )          ? $instance['url_to_page']          : '';
		$gb_comments_only =     isset( $instance['gb_comments_only'] )     ? $instance['gb_comments_only']     : 'false';
		$link_to_page =         isset( $instance['link_to_page'] )         ? $instance['link_to_page']         : 'false';
		$link_to_page_caption = isset( $instance['link_to_page_caption'] ) ? $instance['link_to_page_caption'] : __( 'goto guestbook page', 'text_domain' );
		$hide_gb_page_title =   isset( $instance['hide_gb_page_title'] )   ? $instance['hide_gb_page_title']   : 'false';

		$out = '';
		$out .= $this->form_item( 'title', 'text', $title, __( 'Title:' ) );
		$out .= $this->form_item( 'num_comments', 'text', $num_comments, __( 'Number of comments:' ), null, null, 30 );
		$out .= $this->form_item( 'link_to_comment', 'checkbox', $link_to_comment, __( 'Add a link to each comment' ) );
		$out .= $this->form_item( 'show_date', 'checkbox', $show_date, __( 'Show comment date' ), null, 'margin:0 0 0.2em 0' );
		$out .= $this->form_item( 'date_format', 'text', $date_format, __( 'Date format:' ), null, 'margin:0 0 0.6em 0.9em' );
		$out .= $this->form_item( 'show_author', 'checkbox', $show_author, __( 'Show comment author' ) );
		$out .= $this->form_item( 'show_page_title', 'checkbox', $show_page_title, __( 'Show title of comment page' ), null, 'margin:0 0 0.2em 0' );
		$out .= $this->form_item( 'page_title_length', 'text', $page_title_length, __( 'Truncate title to' ), __( 'characters' ), 'margin:0 0 0.6em 0.9em', 30 );
		$out .= $this->form_item( 'show_comment_text', 'checkbox', $show_comment_text, __( 'Show comment text' ), null, 'margin:0 0 0.2em 0' );
		$out .= $this->form_item( 'comment_text_length', 'text', $comment_text_length, __( 'Truncate text to ' ), __( 'characters' ), 'margin:0 0 0.6em 0.9em', 30 );
		$out .= $this->form_item( 'url_to_page', 'text', $url_to_page, __( 'URL to the linked guestbook page:' ), null, 'margin:1em 0 0.6em 0' );
		$out .= $this->form_item( 'gb_comments_only', 'checkbox', $gb_comments_only, __( 'Show GB-comments only' ), null, 'margin:0 0 0.6em 0.9em' );
		$out .= $this->form_item( 'link_to_page', 'checkbox', $link_to_page, __( 'Add a link to guestbook page' ), null, 'margin:0 0 0.2em 0.9em' );
		$out .= $this->form_item( 'link_to_page_caption', 'text', $link_to_page_caption, __( 'Caption for the link:' ), null, 'margin:0 0 0.8em 1.8em' );
		$out .= $this->form_item( 'hide_gb_page_title', 'checkbox', $hide_gb_page_title, __( 'Hide guestbook page title' ), null, 'margin:0 0 1em 0.9em' );
		echo $out;
	}

	private function form_item( $item, $type, $value, $caption, $caption_after=null, $style=null, $width=null ) {
		$style_text = ( null===$style ) ? '' : ' style="'.$style.'"';
		if( 'checkbox' === $type ) {
			$checked_text = ( 'true'===$value || 1==$value ) ? 'checked = "checked" ' : '';
			return '
				<p'.$style_text.'>
					<label><input class="widefat" id="'.$this->get_field_id( $item ).'" name="'.$this->get_field_name( $item ).'" type="checkbox" '.$checked_text.'value="1" /> '.$caption.'</label>
				</p>';
		}
		else {
			$width_text = ( null === $width ) ? '' : 'style="width:'.$width.'px" ';
			$caption_after_text = ( null === $caption_after ) ? '' : '<label>'.$caption_after.'</label>';
			return '
				<p'.$style_text.'>
					<label for="'.$this->get_field_id( $item ).'">'.$caption.' </label>
					<input '.$width_text.'class="widefat" id="'.$this->get_field_id( $item ).'" name="'.$this->get_field_name( $item ).'" type="text" value="'.esc_attr( $value ).'" />'.$caption_after_text.'
				</p>';
		}
	}

	/** ************************************************************************
	 * Function to truncate and shorten text
	 *
	 * @param int $max_length The length to which the text should be shortened
	 * @param string $html The html code which should be shortened
	 ***************************************************************************/
	private function truncate( $max_length, $html ) {
		if( $max_length > 0 && strlen( $html ) > $max_length ) {
			$printedLength = 0;
			$position = 0;
			$tags = array();
			$out = '';
			while ($printedLength < $max_length && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position)) {
				list($tag, $tagPosition) = $match[0];
				// Print text leading up to the tag
				$str = substr($html, $position, $tagPosition - $position);
				if ($printedLength + strlen($str) > $max_length) {
					$out .= substr($str, 0, $max_length - $printedLength);
					$printedLength = $max_length;
					break;
				}
				$out .= $str;
				$printedLength += strlen($str);
				if ($tag[0] == '&') {
					// Handle the entity
					$out .= $tag;
					$printedLength++;
				}
				else {
					// Handle the tag
					$tagName = $match[1][0];
					if ($tag[1] == '/')
					{
						// This is a closing tag
						$openingTag = array_pop($tags);
						assert($openingTag == $tagName); // check that tags are properly nested
						$out .= $tag;
					}
					else if ($tag[strlen($tag) - 2] == '/') {
						// Self-closing tag
						$out .= $tag;
					}
					else {
						// Opening tag
						$out .= $tag;
						$tags[] = $tagName;
					}
				}
				// Continue after the tag
				$position = $tagPosition + strlen($tag);
			}
			// Print any remaining text
			if ($printedLength < $max_length && $position < strlen($html)) {
				$out .= substr($html, $position, $max_length - $printedLength);
			}
			// Print "..." if the html is not complete
			if( strlen( $html) != $position ) {
				$out .= ' ...';
			}
			// Close any open tags.
			while (!empty($tags)) {
				$out .= '</'.array_pop($tags).'>';
			}
			return $out;
		}
		else {
			return $html;
		}
	}
}
?>