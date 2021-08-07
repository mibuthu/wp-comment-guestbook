<?php
/**
 * CommentGuestbooks Settings Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook\Admin;

use WordPress\Plugins\mibuthu\CommentGuestbook\Config;
use const WordPress\Plugins\mibuthu\CommentGuestbook\PLUGIN_PATH;

if ( ! defined( 'WP_ADMIN' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/config.php';

/**
 * CommentGuestbooks Settings Class
 *
 * This class handles the display of the admin settings page
 */
class Settings {

	/**
	 * Config class instance reference
	 *
	 * @var Config
	 */
	private $config;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @param Config $config_instance The Config instance as a reference.
	 */
	public function __construct( &$config_instance ) {
		$this->config = $config_instance;
		$this->config->load_admin_data();
	}


	/**
	 * Show the admin settings page
	 *
	 * @return void
	 */
	public function show_page() {
		// Check required privilegs.
		if ( ! current_user_can( 'manage_options' ) ) {
			// phpcs:ignore WordPress.WP.I18n.MissingArgDomainDefault -- Use "default" text domain from WordPress Core.
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}
		// Define the tab to display.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : '';  // @phan-suppress-current-line PhanPartialTypeMismatchArgument
		if ( ! isset( $this->config->admin_data->sections[ $tab ] ) ) {
			$tab = 'general';
		}
		// Show config options.
		echo '
			<div class="wrap nosubsub" style="padding-bottom:15px">
			<div id="icon-edit-comments" class="icon32"><br /></div><h2>' . esc_html__( 'Comment Guestbook Settings', 'comment-guestbook' ) . '</h2>
			</div>';
		$this->show_sections( $tab );
		echo '
			<div id="posttype-page" class="posttypediv">
			<form name=cgb-' . esc_html( $tab ) . '-settings method="post" action="options.php">
			';
		settings_fields( 'cgb_' . $tab );
		echo '
				<div class="cgb-settings">
				<table class="form-table">';
		$this->show_options( $tab );
		echo '
				</table>
				</div>';
		submit_button();
		echo '
			</form>
			</div>';
	}


	/**
	 * Show the section tabs
	 *
	 * @param string $current The currently selected tab.
	 * @return void
	 */
	private function show_sections( $current = 'general' ) {
		echo '<h3 class="nav-tab-wrapper">';
		foreach ( $this->config->admin_data->sections as $tabname => $tab ) {
			$class = ( $tabname === $current ) ? ' nav-tab-active' : '';
			echo wp_kses_post(
				'
				<a class="nav-tab' . $class . '" href="' .
				add_query_arg(
					[
						'page' => 'cgb_admin_settings',
						'tab'  => $tabname,
					],
					admin_url( 'options-general.php' )
				) .
				'">' . $tab->caption . '</a>'
			);
		}
		echo '</h3>
				<div class="section-desc">' . wp_kses_post( strval( $this->config->admin_data->sections[ $current ]->description ) ) . '</div>';
	}


	/**
	 * Show the options of the given section
	 *
	 * @param string $section The currently selected section.
	 * @return void
	 */
	private function show_options( $section ) {
		// Define which sections should show the description in a new line instead on to right side of the option.
		$desc_new_line = false;
		if ( 'comment_html' === $section ) {
			$desc_new_line = true;
		}
		foreach ( $this->config->get_all() as $oname => $o ) {
			$admin_data = $this->config->admin_data->$oname;
			if ( $o->section === $section ) {
				echo '
						<tr style="vertical-align:top;">
							<th>';
				if ( ! empty( $admin_data->label ) ) {
					echo '<label for="' . esc_attr( $oname ) . '">' . esc_html( $admin_data->label ) . ':</label>';
				}
				echo '</th>
						<td>';
				switch ( $admin_data->display_type ) {
					case 'checkbox':
						$this->show_checkbox( $oname, $this->config->$oname->as_str(), $admin_data->caption );
						break;
					case 'radio':
						$this->show_radio( $oname, $this->config->$oname->as_str(), $admin_data->permitted_values );
						break;
					case 'number':
						$this->show_number( $oname, $this->config->$oname->as_str(), $admin_data->range );
						break;
					case 'text':
						$this->show_text( $oname, $this->config->$oname->as_str() );
						break;
					case 'textarea':
						// @phan-suppress-next-line PhanPluginDuplicateConditionalNullCoalescing -- Required due to PHP 5.6 support.
						$this->show_textarea( $oname, $this->config->$oname->as_str(), ( isset( $admin_data->rows ) ? $admin_data->rows : null ) );
						break;
					default:
						// Trigger error is allowed in this case.
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
						trigger_error( 'Unknown display type "' . esc_html( $admin_data->display_type ) . '" provided for option "' . esc_html( $oname ) . '"!', E_USER_WARNING );
				}
				echo '
						</td>';
				if ( $desc_new_line ) {
					echo '
					</tr>
					<tr>
						<td></td>';
				}
				echo '
						<td class="description">' . wp_kses_post( $admin_data->description ) . '</td>
					</tr>';
			}
		}
	}


	/**
	 * Show an option as a checkbox
	 *
	 * @param string $name The name of the option.
	 * @param string $value The value of the option.
	 * @param string $caption The caption of the option.
	 * @return void
	 */
	private function show_checkbox( $name, $value, $caption ) {
		echo '
							<label for="' . esc_attr( $name ) . '">
								<input name="' . esc_attr( $name ) . '" type="checkbox" id="' . esc_attr( $name ) . '" value="1"';
		if ( 1 === intval( $value ) ) {
			echo ' checked="checked"';
		}
		echo ' />
								' . esc_html( $caption ) . '
							</label>';
	}


	/**
	 * Show an option as radio buttons
	 *
	 * @param string               $name The name of the option.
	 * @param string               $value The value of the option.
	 * @param array<string,string> $permitted_values The permitted values [name => description] of the option.
	 * @return void
	 */
	private function show_radio( $name, $value, $permitted_values ) {
		echo '
							<fieldset>';
		foreach ( $permitted_values as $vname => $vdescription ) {
			$checked = ( $value === $vname ) ? 'checked="checked" ' : '';
			echo '
								<label title="' . esc_attr( $vdescription ) . '">
									<input type="radio" ' . esc_html( $checked ) . 'value="' . esc_attr( $vname ) . '" name="' . esc_attr( $name ) . '">
									<span>' . esc_html( $vdescription ) . '</span>
								</label>
								<br />';
		}
		echo '
							</fieldset>';
	}


	/**
	 * Show an option as a number field
	 *
	 * @param string            $name The name of the option.
	 * @param string            $value The value of the option.
	 * @param array<string,int> $range The range of the number input containing the optional fields $range['min_value'] and $range['max_value'].
	 * @return void
	 */
	private function show_number( $name, $value, $range = [ 'min_value' => 0 ] ) {
		$value = intval( $value );
		$step  = esc_attr( isset( $range['step'] ) ? strval( $range['step'] ) : '1' );
		$min   = isset( $range['min_value'] ) ? ' min="' . intval( $range['min_value'] ) . '"' : '';
		$max   = isset( $range['max_value'] ) ? ' max="' . intval( $range['max_value'] ) . '"' : '';
		echo '
							';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- variables already escaped in the assignments above
		echo '<input name="' . esc_attr( $name ) . '" type="number" id="' . esc_attr( $name ) . '" step="' . $step . '"' . $min . $max . ' value="' . $value . '" />';
	}


	/**
	 * Show an option as a text field
	 *
	 * @param string $name The name of the option.
	 * @param string $value THe value of the option.
	 * @return void
	 */
	private function show_text( $name, $value ) {
		echo '
							<input name="' . esc_attr( $name ) . '" type="text" id="' . esc_attr( $name ) . '" style="width:25em" value="' . wp_kses_post( htmlentities( $value ) ) . '" />';
	}


	/**
	 * Show an option as a text area
	 *
	 * @param string   $name The name of the option.
	 * @param string   $value The value of the option.
	 * @param null|int $rows The size (number of rows) of the text area.
	 * @return void
	 */
	private function show_textarea( $name, $value, $rows = null ) {
		$rows_text = ( null === $rows ) ? '' : ' rows="' . $rows . '"';
		echo '
							<textarea name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '"' . wp_kses_post( $rows_text ) . ' class="large-text code">' . wp_kses_post( htmlentities( $value ) ) . '</textarea>';
	}

}

