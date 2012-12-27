<?php

require_once( CGB_PATH.'php/options.php' );

// This class handles all available admin pages
class cgb_admin {

	public $options;

	public function __construct() {
		// get options instance
		$this->options = &cgb_options::get_instance();
	}
	/**
	 * Add and register all admin pages in the admin menu
	 */
	public function register_pages() {
		add_submenu_page( 'edit-comments.php', 'Comment Guestbook', 'Guestbook', 'edit_posts', 'cgb_admin_main', array( &$this, 'show_main' ) );
	}

	// show the main admin page as a submenu of "Comments"
	public function show_main() {
		if (!current_user_can( 'edit_posts' ))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}

		$out = '
			<div class="wrap nosubsub" style="padding-bottom:15px">
			<div id="icon-edit-comments" class="icon32"><br /></div><h2>Comment Guestbook</h2>
			</div>';
		$out .= $this->show_messages();
		$out .= '
			<h3>Create a guestbook page</h3>
			<div style="padding:0 15px">
				<p>"Comment guestbook" works by using a "shortcode" in a page.</p>
				<p>To create a guestbook goto "Pages" &rarr; "Add new" in the admin menu and create a new page. Choose your page title e.g. "Guestbook" and add the shortcode <code>[comment-guestbook]</code> in the text field.<br />
				You can add additional text and html code if you want to display something else on that page. That´s all you have to do. Save and publish the page to finish the guestbook creation.</p>
				<p>The shortcode will be replaced by the comment form. And the comment list can be adjusted with the options below.</p>
				<br />
			</div>
			<h3>Comment Guestbook Settings</h3>';
		if( !isset( $_GET['tab'] ) ) {
			$_GET['tab'] = 'general';
		}
		$out .= $this->create_tabs( $_GET['tab'] );
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
		// define the tab to display
		$tab = $_GET['tab'];
		if( 'general' !== $tab && 'comment_list' !== $tab && 'comment_html' !== $tab && 'comment_form' !== $tab ) {
			$tab = 'general';
		}
		$out .= '
				<table class="form-table">';
		$out .= $this->show_options( $tab );
		$out .= '
				</table>
				</div>';
		ob_start();
		submit_button();
		$out .= ob_get_contents();
		ob_end_clean();
		$out .='
			</form>
			</div>';
		echo $out;
	}

	private function create_tabs( $current = 'general' )  {
		$tabs = array( 'general' => 'General settings',
		               'comment_list' => 'Comment-list settings',
		               'comment_html' => 'Comment html code',
		               'comment_form' => 'Comment-form settings'
		               /*'comment_form_html' => 'Comment-form html code'*/ );
		$out = '<h3 class="nav-tab-wrapper">';
		foreach( $tabs as $tab => $name ){
			$class = ( $tab == $current ) ? ' nav-tab-active' : '';
			$out .= "<a class='nav-tab$class' href='?page=cgb_admin_main&amp;tab=$tab'>$name</a>";
		}
		$out .= '</h3>';
		return $out;
	}

	private function show_options( $section ) {
		// define which sections should show the description in a new line instead on to right side of the option
		$desc_new_line = false;
		if( 'comment_html' === $section ) {
			$desc_new_line = true;
		}
		$out = '';
		foreach( $this->options->options as $oname => $o ) {
			if( $o['section'] == $section ) {
				$out .= '
						<tr style="vertical-align:top;">
							<th>';
				if( $o['label'] != '' ) {
					$out .= '<label for="'.$oname.'">'.$o['label'].':</label>';
				}
				$out .= '</th>
						<td>';
				switch( $o['type'] ) {
					case 'checkbox':
						$out .= $this->show_checkbox( $oname, $this->options->get( $oname ), $o['caption'] );
						break;
					case 'radio':
						$out .= $this->show_radio( $oname, $this->options->get( $oname ), $o['caption'] );
						break;
					case 'text':
						$out .= $this->show_text( $oname, $this->options->get( $oname ) );
						break;
					case 'textarea':
						$out .= $this->show_textarea( $oname, $this->options->get( $oname ) );
						break;
				}
				$out .= '
						</td>';
				if( $desc_new_line ) {
					$out .= '
					</tr>
					<tr>
						<td></td>';
				}
				$out .= '
						<td class="description">'.$o['desc'].'</td>
					</tr>';
			}
		}
		return $out;
	}

	private function show_checkbox( $name, $value, $caption ) {
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

	private function show_radio( $name, $value, $caption ) {
		$out = '
							<fieldset>';
		foreach( $caption as $okey => $ocaption ) {
			$checked = ($value === $okey) ? 'checked="checked" ' : '';
			$out .= '
								<label title="'.$ocaption.'">
									<input type="radio" '.$checked.'value="'.$okey.'" name="'.$name.'">
									<span>'.$ocaption.'</span>
								</label>
								<br />';
		}
		$out .= '
							</fieldset>';
		return $out;
	}

	private function show_text( $name, $value ) {
		$out = '
							<input name="'.$name.'" type="text" id="'.$name.'" value="'.$value.'" />';
		return $out;
	}

	private function show_textarea( $name, $value ) {
		$out = '
							<textarea name="'.$name.'" id="'.$name.'" rows="20" class="large-text code">'.$value.'</textarea>';
		return $out;
	}

	private function show_messages() {
		$out = '';
		// settings updated
		if( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) {
			$out .= '
					<div id="message" class="updated below-h2"><p><strong>Settings saved.</strong></p></div>';
		}
		return $out;
	}
}
?>
