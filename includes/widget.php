<?php
if(!defined('WPINC')) {
	exit;
}

require_once(CGB_PATH.'includes/options.php');

/**
 * Comment Guestbook Widget
*/
class CGB_Widget extends WP_Widget {

	private $options;
	private $items;

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
				'comment_guestbook_widget', // Base ID
				'Comment Guestbook', // Name
				array('description' => __('This widget displays a list of recent comments.','comment-guestbook'),) // Args
		);
		add_action('comment_post', array($this, 'flush_widget_cache'));
		add_action('transition_comment_status', array($this, 'flush_widget_cache'));
		$this->options = &CGB_Options::get_instance();

		// define all available items
		$this->items = array(
			'title' =>                array('std_value'     => __('Recent guestbook entries','comment-guestbook')),
			'num_comments' =>         array('std_value'     => '5'),
			'link_to_comment' =>      array('std_value'     => 'false'),
			'show_date' =>            array('std_value'     => 'false'),
			'date_format' =>          array('std_value'     => get_option('date_format')),
			'show_author' =>          array('std_value'     => 'true'),
			'author_length' =>        array('std_value'     => '18'),
			'show_page_title' =>      array('std_value'     => 'false'),
			'page_title_length' =>    array('std_value'     => '18'),
			'show_comment_text' =>    array('std_value'     => 'true'),
			'comment_text_length' =>  array('std_value'     => '25'),
			'url_to_page' =>          array('std_value'     => ''),
			'gb_comments_only' =>     array('std_value'     => 'false'),
			'hide_gb_page_title' =>   array('std_value'     => 'false'),
			'link_to_page' =>         array('std_value'     => 'false'),
			'link_to_page_caption' => array('std_value'     => __('goto guestbook page','comment-guestbook')),
		);

		add_action('admin_init', array(&$this, 'load_widget_items_helptexts'), 2);
	}

	public function load_widget_items_helptexts() {
		require_once(CGB_PATH.'includes/widget_helptexts.php');
		foreach($widget_items_helptexts as $name => $values) {
			$this->items[$name] += $values;
		}
		unset($widget_items_helptexts);
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
	public function widget($args, $instance) {
		global $comments, $comment;

		$cache = wp_cache_get('widget_comment_guestbook', 'widget');
		if(! is_array($cache)) {
			$cache = array();
		}
		if(! isset($args['widget_id'])) {
			$args['widget_id'] = $this->id;
		}
		if(isset($cache[ $args['widget_id'] ])) {
			echo $cache[ $args['widget_id'] ];
			return;
		}
		extract($args, EXTR_SKIP);
		foreach($this->items as $itemname => $item) {
			if(! isset($instance[$itemname])) {
				$instance[$itemname] = $item['std_value'];
			}
		}
		$out = '';
		$instance['title'] = apply_filters('widget_title', $instance['title']);
		$comment_args = array('number' => absint($instance['num_comments']), 'status' => 'approve', 'post_status' => 'publish');
		if('true' === $instance['gb_comments_only']) {
			$comment_args['post_id'] = url_to_postid($instance['url_to_page']);
		}
		$comments = get_comments(apply_filters('widget_comments_args', $comment_args));
		$out .= $before_widget;
		if($instance['title']) {
			$out .= $before_title . $instance['title'] . $after_title;
		}
		$out .= '
				<ul class="cgb-widget">';
		if($comments) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
			$post_ids = array_unique(wp_list_pluck($comments, 'comment_post_ID'));
			_prime_post_caches($post_ids, strpos(get_option('permalink_structure'), '%category%'), false);
			foreach((array) $comments as $comment) {
				$out .= '
					<li class="cgb-widget-item">';
				if('true' === $instance['link_to_comment']) {
					$out .= '<a href="'.$this->get_comment_link($comment).'">';
				}
				if('true' === $instance['show_date']) {
					$out .= '<span class="cgb-date" title="'.__('Date of comment','comment-guestbook').': '.esc_attr(get_comment_date()).'">'.get_comment_date($instance['date_format']).' </span>';
				}
				if('true' === $instance['show_author']) {
					$out .= $this->truncate($instance['author_length'], get_comment_author(), 'span', array('class' => 'cgb-author', 'title' => __('Comment author','comment-guestbook').': '.esc_attr(get_comment_author())));
				}
				if('true' === $instance['show_page_title']) {
					if('false' === $instance['hide_gb_page_title'] || url_to_postid($instance['url_to_page']) != $comment->comment_post_ID) {
						$out .= '<span class="cgb-widget-title" title="'.__('Page of comment','comment-guestbook').': '.esc_attr(get_the_title($comment->comment_post_ID)).'">';
						if('true' === $instance['show_author']) {
							$out .= ' '.__('in','comment-guestbook').' ';
						}
						$out .= $this->truncate($instance['page_title_length'], get_the_title($comment->comment_post_ID)).'</span>';
					}
				}
				if('true' === $instance['link_to_comment']) {
					$out .= '</a>';
				}
				if('true' === $instance['show_comment_text']) {
					$out .= $this->truncate($instance['comment_text_length'], get_comment_text(), 'div', array('class' => 'cgb-widget-text', 'title' => esc_attr(get_comment_text())));
				}
				$out .= '</li>';
			}
		}
		$out .= '
				</ul>
				';
		if('true' === $instance['link_to_page']) {
			$out .= '
				<div class="cgb-widget-pagelink" style="clear:both"><a title="'.esc_attr($instance['link_to_page_caption']).'" href="'.$instance[ 'url_to_page'].'">'.$instance['link_to_page_caption'].'</a></div>
				';
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
	public function update($new_instance, $old_instance) {
		$instance = array();
		foreach($this->items as $itemname => $item) {
			if('checkbox' === $item['type']) {
				$instance[$itemname] = (isset($new_instance[$itemname]) && 1==$new_instance[$itemname]) ? 'true' : 'false';
			}
			else { // 'text'
				$instance[$itemname] = strip_tags($new_instance[$itemname]);
			}
		}
		$this->flush_widget_cache();
		$alloptions = wp_cache_get('alloptions', 'options');
		if (isset($alloptions['widget_recent_comments'])) {
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
	public function form($instance) {
		// Display general information at the top
		$out = '<p>'.__('For all options tooltips are available which provide additional help and information. They appear if the mouse is hovered over the options text field or checkbox.','comment-guestbook').'</p>';
		// Display the options
		foreach($this->items as $itemname => $item) {
			if(! isset($instance[$itemname])) {
				$instance[$itemname] = $item['std_value'];
			}
			$style_text = (null===$item['form_style']) ? '' : ' style="'.$item['form_style'].'"';
			if('checkbox' === $item['type']) {
				$checked_text = ('true'===$instance[$itemname] || 1==$instance[$itemname]) ? 'checked = "checked" ' : '';
				$out .= '
					<p'.$style_text.' title="'.$item['tooltip'].'">
						<label><input class="widefat" id="'.$this->get_field_id($itemname).'" name="'.$this->get_field_name($itemname).'" type="checkbox" '.$checked_text.'value="1" /> '.$item['caption'].'</label>
					</p>';
			}
			else { // 'text'
				$width_text = (null === $item['form_width']) ? '' : 'style="width:'.$item['form_width'].'px" ';
				$caption_after_text = (null === $item['caption_after']) ? '' : '<label> '.$item['caption_after'].'</label>';
				$out .= '
					<p'.$style_text.' title="'.$item['tooltip'].'">
						<label for="'.$this->get_field_id($itemname).'">'.$item['caption'].' </label>
						<input '.$width_text.'class="widefat" id="'.$this->get_field_id($itemname).'" name="'.$this->get_field_name($itemname).'" type="text" value="'.esc_attr($instance[$itemname]).'" />'.$caption_after_text.'
					</p>';
			}
		}
		echo $out;
	}

	/** **************************************************************************
	 * Function to get guestbook specific comment link for pages/posts
	 * where the 'comment-guestbook' shortcode is being used.
	 *
	 * @param object $comment Wordpress comment object of the comment to retrieve
	 *****************************************************************************/
	private function get_comment_link($comment) {
		$link_args = array();
		if('' !== $this->options->get('cgb_adjust_output')) {
			if(0 != get_option('page_comments') && 0 < get_option('comments_per_page')) {
				if('desc' === $this->options->get('cgb_clist_order') || 'asc' === $this->options->get('cgb_clist_order') || '' !== $this->options->get('cgb_clist_show_all')) {
					$pattern = get_shortcode_regex();
					if(preg_match_all('/'. $pattern .'/s', get_post($comment->comment_post_ID)->post_content, $matches)
							&& array_key_exists(2, $matches)
							&& in_array('comment-guestbook', $matches[2])) {
						// shortcode is being used in that page or post
						$args = array('status' => 'approve', 'order' => $this->options->get('cgb_clist_order'));
						if('' === $this->options->get('cgb_clist_show_all')) {
							$args['post_id'] = $comment->comment_post_ID;
						}
						$comments = get_comments($args);
						$toplevel_comments = array();
						foreach($comments as $_comment) {
							if(0 == $_comment->comment_parent) {
								 $toplevel_comments[] = $_comment->comment_ID;
							}
						}
						// switch actual comment to top-level comment
						$toplevel_comment = $comment;
						while(0 != $toplevel_comment->comment_parent) {
							$toplevel_comment = get_comment($toplevel_comment->comment_parent);
						}
						$oldercoms = array_search($toplevel_comment->comment_ID, $toplevel_comments);
						$link_args['page'] = ceil(($oldercoms + 1) / get_option('comments_per_page'));
					}
				}
			}
		}
		return esc_url(get_comment_link($comment->comment_ID, $link_args));
	}

	/** **************************************************************************************************
	 * Truncate HTML, close opened tags
	 *
	 * @param int $max_length           The length (number of characters) to which the text will be
	 *                                  shortened. With "0" the full text will be returned. With "auto"
	 *                                  also the complete text will be used, but a wrapper div will be
	 *                                  added which shortens the text to 1 full line via css.
	 * @param string $html              The html code which should be shortened.
	 * @param string $wrapper_type      Defines which kind of wrapper shall be added around the html code
	 *                                  if a manual length shall be used. The possible values are "none",
	 *                                  "div" and "span". With "none" no wrapper will be added, "div" and
	 *                                  "span" are the 2 available wrapper types.
	 *                                  If max_length is set to auto a div is mandatory and will be added
	 *                                  always, independent of the given value.
	 * @param array $wrapper_attributes Additional attributes for the wrapper element. The array
	 *                                  key defines the attribute name.
	 *****************************************************************************************************/
	private function truncate($max_length, $html, $wrapper_type='none', $wrapper_attributes=array()) {
		// Apply wrapper and add required css for autolength (if required)
		$autolength = 'auto' == $max_length ? true : false;
		if($autolength) {
			$wrapper_type = 'div';
		}
		elseif('div' != $wrapper_type && 'span' != $wrapper_type) {
			$wrapper_type = 'none';
		}
		if('none' != $wrapper_type) {
			$wrapper_text = '<'.$wrapper_type;
			foreach($wrapper_attributes as $name => $value) {
				$wrapper_text .= ' '.$name.'="'.$value.'"';
			}
			if($autolength) {
				$wrapper_text .= ' style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis"';
			}
			$html = $wrapper_text.'>'.$html.'</'.$wrapper_type.'>';
		}

		// Apply manual length
		mb_internal_encoding("UTF-8");
		if(is_numeric($max_length) && 0 < $max_length && mb_strlen($html) > $max_length) {
			$truncated = false;
			$printedLength = 0;
			$position = 0;
			$tags = array();
			$out = '';
			while($printedLength < $max_length && $this->mb_preg_match('{</?([a-z]+\d?)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position)) {
				list($tag, $tagPosition) = $match[0];
				// Print text leading up to the tag
				$str = mb_substr($html, $position, $tagPosition - $position);
				if($printedLength + mb_strlen($str) > $max_length) {
					$out .= mb_substr($str, 0, $max_length - $printedLength);
					$printedLength = $max_length;
					$truncated = true;
					break;
				}
				$out .= $str;
				$printedLength += mb_strlen($str);
				if('&' == $tag[0]) {
					// Handle the entity
					$out .= $tag;
					$printedLength++;
				}
				else {
					// Handle the tag
					$tagName = $match[1][0];
					if($this->mb_preg_match('{^</}', $tag)) {
						// This is a closing tag
						$openingTag = array_pop($tags);
						if($openingTag != $tagName) {
							// Not properly nested tag found: trigger a warning and add the not matching opening tag again
							trigger_error('Not properly nested tag found (last opening tag: '.$openingTag.', closing tag: '.$tagName.')', E_USER_WARNING);
							$tags[] = $openingTag;
						}
						else {
							$out .= $tag;
						}
					}
					else if($this->mb_preg_match('{/\s*>$}', $tag)) {
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
				$position = $tagPosition + mb_strlen($tag);
			}
			// Print any remaining text
			if($printedLength < $max_length && $position < mb_strlen($html)) {
				$out .= mb_substr($html, $position, $max_length - $printedLength);
			}
			// Print ellipsis ("...") if the html was truncated
			if($truncated) {
				$out .= ' &hellip;';
			}
			// Close any open tags.
			while(!empty($tags)) {
				$out .= '</'.array_pop($tags).'>';
			}
			return $out;
		}
		return $html;
	}

	private function mb_preg_match($ps_pattern, $ps_subject, &$pa_matches=null, $pn_flags=0, $pn_offset=0, $ps_encoding=null) {
		// WARNING! - All this function does is to correct offsets, nothing else:
		//(code is independent of PREG_PATTER_ORDER / PREG_SET_ORDER)
		if(is_null($ps_encoding)) {
			$ps_encoding = mb_internal_encoding();
		}
		$pn_offset = strlen(mb_substr($ps_subject, 0, $pn_offset, $ps_encoding));
		$out = preg_match($ps_pattern, $ps_subject, $pa_matches, $pn_flags, $pn_offset);
		if($out && ($pn_flags & PREG_OFFSET_CAPTURE))
			foreach($pa_matches as &$ha_match) {
				$ha_match[1] = mb_strlen(substr($ps_subject, 0, $ha_match[1]), $ps_encoding);
			}
		return $out;
	}
}
?>
