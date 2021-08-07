<?php
/**
 * OptionAdminData Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook;

if ( ! defined( 'WPINC' ) ) {
	exit();
}


/**
 * Option Admin Data Class
 *
 * This class handles an option which can be used for shortcodes, widgets and the plugin config.
 */
class OptionAdminData {

	/**
	 * Display Type
	 *
	 * The type how to display the option on an admin page.
	 * Possible values:
	 *  * text
	 *  * textarea
	 *  * number
	 *  * checkbox
	 *  * radio
	 *  * section
	 *
	 * @var string
	 */
	public $display_type = '';

	/**
	 * Section
	 *
	 * @var string
	 */
	public $section = '';

	/**
	 * Permitted values
	 *
	 * An array of all allowed values as key, and the help text description as value
	 *
	 * @var array<string,string>
	 */
	public $permitted_values = [];

	/**
	 * Label
	 *
	 * @var string
	 */
	public $label = '';

	/**
	 * Caption
	 *
	 * @var string
	 */
	public $caption = '';

	/**
	 * Caption after widget
	 *
	 * @var string
	 */
	public $caption_after = '';

	/**
	 * Description
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Tooltip
	 *
	 * @var string
	 */
	public $tooltip = '';

	/**
	 * Number of rows in textarea
	 *
	 * @var int
	 */
	public $rows = 0;

	/**
	 * Value range in number field
	 *
	 * @var array<string,int>
	 */
	public $range = [];

	/**
	 * Form styles
	 *
	 * @var null|string
	 */
	public $form_style = null;

	/**
	 * Form width
	 *
	 * @var null|string
	 */
	public $form_width = null;


	/**
	 * Default class constructor
	 *
	 * @param string              $display_type The display
	 * @param array<string,mixed> $data All data fields to set in an array where the key is the field name
	 * @return void
	 */
	private function __construct( $display_type, $data ) {
		$this->display_type = $display_type;
		foreach ( $data as $name => $value ) {
			if ( isset( $this->$name ) && $display_type !== $name ) {
				$this->$name = $value;
			} else {
				// Trigger error is allowed in this case.
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				trigger_error( 'The requested option admin data field "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
			}
		}
	}


	/**
	 * Create a new OptionAdminData with display type 'text'.
	 *
	 * @param array<string,mixed> $data All data fields to set in an array where the key is the field name
	 * @return static
	 */
	public static function new_text( $data ) {
		$obj = new static( 'text', $data );
		return $obj;
	}


	/**
	 * Create a new OptionAdminData with display type 'textarea'.
	 *
	 * @param array<string,mixed> $data All data fields to set in an array where the key is the field name
	 * @return static
	 */
	public static function new_textarea( $data ) {
		$obj = new static( 'textarea', $data );
		return $obj;
	}


	/**
	 * Create a new OptionAdminData with display type 'number'.
	 *
	 * @param array<string,mixed> $data All data fields to set in an array where the key is the field name
	 * @return static
	 */
	public static function new_number( $data ) {
		$obj = new static( 'number', $data );
		return $obj;
	}


	/**
	 * Create a new OptionAdminData with display type 'checkbox'.
	 *
	 * @param array<string,mixed> $data All data fields to set in an array where the key is the field name
	 * @return static
	 */
	public static function new_checkbox( $data ) {
		$obj = new static( 'checkbox', $data );
		return $obj;
	}


	/**
	 * Create a new OptionAdminData with display type 'radio'.
	 *
	 * @param array<string,mixed> $data All data fields to set in an array where the key is the field name
	 * @return static
	 */
	public static function new_radio( $data ) {
		$obj = new static( 'radio', $data );
		return $obj;
	}


	/**
	 * Create a new OptionAdminData with display type 'section'.
	 *
	 * @param array<string,mixed> $data All data fields to set in an array where the key is the field name
	 * @return static
	 */
	public static function new_section( $data ) {
		$obj = new static( 'section', $data );
		return $obj;
	}

}
