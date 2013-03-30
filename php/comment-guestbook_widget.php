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
		$comments = get_comments( apply_filters( 'widget_comments_args', array( 'number' => $num_comments, 'status' => 'approve', 'post_status' => 'publish' ) ) );
		$out .= $before_widget;
		if( $title ) {
			$out .= $before_title . $title . $after_title;
		}
		$out .= '<ul id="recentcomments">';
		if( $comments ) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );
			foreach( (array) $comments as $comment) {
				$out .= '<li class="recentcomments">';
				if( 'true' === $instance['link_to_comment'] ) {
					$out .= '<a href="'.esc_url( get_comment_link( $comment->comment_ID ) ).'">';
				}
				$out .= get_comment_date().': ';
				$out .= get_comment_author();
				$out .= ' '.__( 'in' ).' '.get_the_title( $comment->comment_post_ID );
				if( 'true' === $instance['link_to_comment'] ) {
					$out .= '</a>';
				}
				$out .= '<br />'.get_comment_text();
				$out .= '</li>';
			}
		}
		$out .= '</ul>';
		if( 'true' === $instance['link_to_page'] ) {
			$out .= '<div style="clear:both"><a title="'.$instance['link_to_page_caption'].'" href="'.$instance[ 'url_to_page'].'">'.$instance['link_to_page_caption'].'</a></div>';
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
		$instance['link_to_comment'] = (isset( $new_instance['link_to_comment'] ) && 1==$new_instance['link_to_comment'] ) ? 'true' : 'false';
		$instance['link_to_page'] = (isset( $new_instance['link_to_page'] ) && 1==$new_instance['link_to_page'] ) ? 'true' : 'false';
		$instance['link_to_page_caption'] = strip_tags( $new_instance['link_to_page_caption'] );
		$instance['url_to_page'] = strip_tags( $new_instance['url_to_page'] );

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
		$link_to_comment =      isset( $instance['link_to_comment'] )        ? $instance['link_to_comment']    : 'false';
		$link_to_page =         isset( $instance['link_to_page'] )         ? $instance['link_to_page']         : 'false';
		$link_to_page_caption = isset( $instance['link_to_page_caption'] ) ? $instance['link_to_page_caption'] : __( 'goto guestbook page', 'text_domain' );
		$url_to_page =          isset( $instance['url_to_page'] )          ? $instance['url_to_page']          : '';
		$link_to_comment_checked = 'true'===$link_to_comment || 1==$link_to_comment ? 'checked = "checked" ' : '';
		$link_to_page_checked =  'true'===$link_to_page  || 1==$link_to_page ? 'checked = "checked" ' : '';

		$out = '';
		// $title
		$out .= '
		<p>
			<label for="'.$this->get_field_id( 'title' ).'">'.__( 'Title:' ).'</label>
			<input class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" type="text" value="'.esc_attr( $title ).'" />
		</p>';
		// $num_comments
		$out .= '
		<p>
			<label for="'.$this->get_field_id( 'num_comments' ).'">'.__( 'Number of displayed comments:' ).'</label>
			<input style="width:30px" class="widefat" id="'.$this->get_field_id( 'num_comments' ).'" name="'.$this->get_field_name( 'num_comments' ).'" type="text" value="'.esc_attr( $num_comments ).'" />
		</p>';
		// $link_to_comment
		$out .= '
		<p>
			<label><input class="widefat" id="'.$this->get_field_id( 'link_to_comment' ).'" name="'.$this->get_field_name( 'link_to_comment' ).'" type="checkbox" '.$link_to_comment_checked.'value="1" /> '.__( 'Add a link to each comment' ).'</label>
		</p>';
		// $link_to_page
		$out .= '
		<p style="margin:0 0 0.4em 0em">
			<label><input class="widefat" id="'.$this->get_field_id( 'link_to_page' ).'" name="'.$this->get_field_name( 'link_to_page' ).'" type="checkbox" '.$link_to_page_checked.'value="1" /> '.__( 'Add a link to guestbook page' ).'</label>
		</p>';
		// $link_to_page_caption
		$out .= '
		<p style="margin:0 0 0.6em 1.2em">
			<label for="'.$this->get_field_id( 'link_to_page_caption' ).'">'.__( 'Caption for the link:' ).'</label>
			<input class="widefat" id="'.$this->get_field_id( 'link_to_page_caption' ).'" name="'.$this->get_field_name( 'link_to_page_caption' ).'" type="text" value="'.esc_attr( $link_to_page_caption ).'" />
		</p>';
		// $url_to_page
		$out .= '
		<p style="margin:0 0 1em 1.2em">
			<label for="'.$this->get_field_id( 'url_to_page' ).'">'.__( 'URL to the linked eventlist page:' ).'</label>
			<input class="widefat" id="'.$this->get_field_id( 'url_to_page' ).'" name="'.$this->get_field_name( 'url_to_page' ).'" type="text" value="'.esc_attr( $url_to_page ).'" />
		</p>';
		echo $out;
	}
}
?>