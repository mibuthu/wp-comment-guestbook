<?php
if(!defined('ABSPATH')) {
	exit;
}

require_once(CGB_PATH.'includes/options.php');

// This class handles all data for the admin settings page
class CGB_Admin_Settings {
	private static $instance;
	private $options;

	private function __construct() {
		$this->options = &CGB_Options::get_instance();
	}

	public static function &get_instance() {
		// Create class instance if required
		if(!isset(self::$instance)) {
			self::$instance = new CGB_Admin_Settings();
		}
		// Return class instance
		return self::$instance;
	}

	// show the admin settings page as a submenu of "Settings"
	public function show_settings() {
		if(!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		// define the tab to display
		if(isset($_GET['tab']) && isset($this->options->sections[$_GET['tab']])) {
			$tab = $_GET['tab'];
		}
		else {
			$tab = 'general';
		}
		// show options
		$out = '
			<div class="wrap nosubsub" style="padding-bottom:15px">
			<div id="icon-edit-comments" class="icon32"><br /></div><h2>Comment Guestbook Settings</h2>
			</div>';
		$out .= $this->show_sections($tab);
		$out .= '
			<div id="posttype-page" class="posttypediv">
			<form method="post" action="options.php">
			';
		ob_start();
		settings_fields('cgb_'.$tab);
		$out .= ob_get_contents();
		ob_end_clean();
		$out .= '
				<div class="cgb-settings">
				<table class="form-table">';
		$out .= $this->show_options($tab);
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

	private function show_sections($current = 'general') {
		$out = '<h3 class="nav-tab-wrapper">';
		foreach($this->options->sections as $tabname => $tab) {
			$class = ($tabname == $current) ? ' nav-tab-active' : '';
			$out .= '<a class="nav-tab'.$class.'" href="?page=cgb_admin_options&amp;tab='.$tabname.'">'.$tab['caption'].'</a>';
		}
		$out .= '</h3>
				<div class="section-desc">'.$this->options->sections[$current]['desc'].'</div>';
		return $out;
	}

	private function show_options($section) {
		// define which sections should show the description in a new line instead on to right side of the option
		$desc_new_line = false;
		if('comment_html' === $section) {
			$desc_new_line = true;
		}
		$out = '';
		foreach($this->options->options as $oname => $o) {
			if($o['section'] == $section) {
				$out .= '
						<tr style="vertical-align:top;">
							<th>';
				if($o['label'] != '') {
					$out .= '<label for="'.$oname.'">'.$o['label'].':</label>';
				}
				$out .= '</th>
						<td>';
				switch($o['type']) {
					case 'checkbox':
						$out .= $this->show_checkbox($oname, $this->options->get($oname), $o['caption']);
						break;
					case 'radio':
						$out .= $this->show_radio($oname, $this->options->get($oname), $o['caption']);
						break;
					case 'text':
						$out .= $this->show_text($oname, $this->options->get($oname));
						break;
					case 'textarea':
						$out .= $this->show_textarea($oname, $this->options->get($oname), (isset($o['rows']) ? $o['rows'] : null));
						break;
				}
				$out .= '
						</td>';
				if($desc_new_line) {
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

	private function show_checkbox($name, $value, $caption) {
		$out = '
							<label for="'.$name.'">
								<input name="'.$name.'" type="checkbox" id="'.$name.'" value="1"';
		if($value == 1) {
			$out .= ' checked="checked"';
		}
		$out .= ' />
								'.$caption.'
							</label>';
		return $out;
	}

	private function show_radio($name, $value, $caption) {
		$out = '
							<fieldset>';
		foreach($caption as $okey => $ocaption) {
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

	private function show_text($name, $value) {
		$out = '
							<input name="'.$name.'" type="text" id="'.$name.'" style="width:230px" value="'.$value.'" />';
		return $out;
	}

	private function show_textarea($name, $value, $rows=null) {
		$rows_text = (null == $rows) ? '' : ' rows="'.$rows.'"';
		$out = '
							<textarea name="'.$name.'" id="'.$name.'"'.$rows_text.' class="large-text code">'.htmlentities($value).'</textarea>';
		return $out;
	}
}
?>
