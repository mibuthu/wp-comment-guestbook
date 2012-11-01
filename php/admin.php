<?php

require_once( CGB_PATH.'php/options.php' );

// This class handles all available admin pages
class cgb_admin {

	// show the main admin page as a submenu of "Comments"
	public static function show_main() {
		if (!current_user_can( 'edit_posts' ))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}

		$out ='
			<div class="wrap nosubsub" style="padding-bottom:15px">
			<div id="icon-edit-comments" class="icon32"><br /></div><h2>Comment Guestbook</h2>
			</div>
			<h3>Create a guestbook page</h3>
			<div style="padding:0 15px">
				<p>"Comment guestbook" works by using a "shortcode" in a page.</p>
				<p>To create a guestbook goto "Pages" -> "Add new" in the admin menu and create a new page. Choose your page title e.g. "Guestbook" and add the shortcode <code>[comment-guestbook]</code> in the text field.<br />
				You can add additional normal text if you want to display something else on the top of this page. That´s all you have to do. Save and publish the page to finish the guestbook creation.</p>
				<p>The shortcode will be replaced by the comment form. And the comment list can be adjusted with the options below.</p>
				<br />
			</div>
			<h3>Comment Guestbook Settings</h3>';
		if( !isset( $_GET['tab'] ) ) {
			$_GET['tab'] = 'general';
		}
		$out .= cgb_admin::create_tabs( $_GET['tab'] );
		$out .= '<div id="posttype-page" class="posttypediv">';
		$out .= '
			<form method="post" action="options.php">
			';
		ob_start();
		settings_fields( 'cgb_'.$_GET['tab'] );
		$out .= ob_get_contents();
		ob_end_clean();
		$out .= '
				<div style="padding:0 10px">';
		switch( $_GET['tab'] ) {
			case 'comment_list' :
				$out .= '
					<table class="form-table">';
				$out .= cgb_admin::show_options( 'comment_list' );
				$out .= '
					</table>';
				break;
			case 'comment_form' :
				$out .= '
						<p>This is an early version of this plugin. No settings are available yet.</p>';
				break;
			case 'comment_form_html' :
				$out .= '
						<p>This is an early version of this plugin. No settings are available yet.</p>';
				break;
			case 'comment_html' :
				$out .= '
					<table class="form-table">';
				$out .= cgb_admin::show_options( 'comment_html', 'newline' );
				$out .= '
					</table>';
				break;
			default : // 'general'
				$out .= '
					<table class="form-table">';
				$out .= cgb_admin::show_options( 'general' );
				$out .= '
					</table>';
				break;
		}
		$out .=
				'</div>';
		ob_start();
		submit_button();
		$out .= ob_get_contents();
		ob_end_clean();
		$out .='
				</form>
			</div>';
		echo $out;
	}

	private static function create_tabs( $current = 'general' )  {
		$tabs = array( 'general' => 'General settings', 'comment_list' => 'Comment-list settings', /*'comment_form' => 'Comment-form settings',*/
						/*'comment_form_html' => 'Comment-form html code',*/ 'comment_html' => 'Comment html code' );
		$out = '<h3 class="nav-tab-wrapper">';
		foreach( $tabs as $tab => $name ){
			$class = ( $tab == $current ) ? ' nav-tab-active' : '';
			$out .= "<a class='nav-tab$class' href='?page=cgb_admin_main&tab=$tab'>$name</a>";
		}
		$out .= '</h3>';
		return $out;
	}

	// $desc_pos specifies where the descpription will be displayed.
	// available options:  'right'   ... description will be displayed on the right side of the option (standard value)
	//                     'newline' ... description will be displayed below the option
	private static function show_options( $section, $desc_pos='right' ) {
		global $cgb;
		$out = '';
		foreach( $cgb->options->options as $oname => $o ) {
			if( $o['section'] == $section ) {
				$out .= '
						<tr valign="top">
							<th scope="row">';
				if( $o['label'] != '' ) {
					$out .= '<label for="'.$oname.'">'.$o['label'].':</label>';
				}
				$out .= '</th>
						<td>';
				switch( $o['type'] ) {
					case 'checkbox':
						$out .= cgb_admin::show_checkbox( $oname, $cgb->options->get( $oname ), $o['caption'] );
						break;
					case 'text':
						$out .= cgb_admin::show_text( $oname, $cgb->options->get( $oname ) );
						break;
					case 'textarea':
						$out .= cgb_admin::show_textarea( $oname, $cgb->options->get( $oname ) );
						break;
				}
				$out .= '
						</td>';
				if( $desc_pos == 'newline' ) {
					$out .= '
					</tr>
					<tr>
						<td></td>';
				}
				$out .= '
						<td class="description">'.$o['desc'].'</td>
					</tr>';
				if( $desc_pos == 'newline' ) {
					$out .= '
						<tr><td></td></tr>';
				}
			}
		}
		return $out;
	}

	private static function show_checkbox( $name, $value, $caption ) {
		$out = '
							<label for="'.$name.'">
								<input name="'.$name.'" type="checkbox" id="'.$name.'" value="1"';
		if( $value == 1 ) {
			$out .= ' checked="checked"';
		}
		$out .= ' />
								'.$caption.'
							</label>';
		return $out;
	}

	private static function show_text( $name, $value ) {
		$out = '
							<input name="'.$name.'" type="text" id="'.$name.'" value="'.$value.'" />';
		return $out;
	}

	private static function show_textarea( $name, $value ) {
		$out = '
							<textarea name="'.$name.'" id="'.$name.'" rows="20" class="large-text code">'.$value.'</textarea>';
		return $out;
	}
}
?>
