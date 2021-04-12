<?php
/**
 * Option Class
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook;

if ( ! defined( 'WPINC' ) ) {
	exit();
}


/**
 * Option Class
 *
 * This class handles an option which can be used for shortcodes, widgets and the plugin config.
 */
class Option {

	/**
	 * Actual or default value
	 *
	 * @var string
	 */
	public $value;

	/**
	 * Permitted values
	 *
	 * @var string|array
	 */
	public $permitted_values = '';

	/**
	 * Section
	 *
	 * @var string
	 */
	public $section = '';

	/**
	 * Type
	 *
	 * @var string
	 */
	public $type = '';

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
	 * Captions
	 *
	 * @var string[]
	 */
	public $captions = [];

	/**
	 * Caption after widget
	 *
	 * @var null|string
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
	 * @var null|int
	 */
	public $rows = null;

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
	 * The boolean TRUE value option
	 *
	 * @var string[]
	 */
	const TRUE = 'true';

	/**
	 * The boolean FALSE value option
	 *
	 * @var string[]
	 */
	const FALSE = 'false';

	/**
	 * The boolean value options
	 *
	 * @var string[]
	 */
	const BOOLEAN = [ self::TRUE, self::FALSE ];

	/**
	 * The boolean TRUE value option
	 *
	 * @var string[]
	 */
	const TRUE_NUM = '1';

	/**
	 * The boolean FALSE value option
	 *
	 * @var string[]
	 */
	const FALSE_NUM = '';

	/**
	 * The boolean value options
	 *
	 * @var string[]
	 */
	const BOOLEAN_NUM = [ self::TRUE_NUM, self::FALSE_NUM ];


	/**
	 * Class constructor which sets the required variables
	 *
	 * @param string            $default_value Standard value for the option.
	 * @param null|string|array $permitted_values Available values for the option (optional).
	 * @param null|string       $section Section of the option (optional).
	 * @return void
	 */
	public function __construct( $default_value, $permitted_values = null, $section = null ) {
		$this->value = $default_value;
		if ( ! is_null( $permitted_values ) ) {
			$this->permitted_values = $permitted_values;
		}
		if ( ! is_null( $section ) ) {
			$this->section = $section;
		}
	}


	/**
	 * Modify several fields at once with the values given in an array
	 *
	 * @param array<string,string> $option_fields Fields with values to modify.
	 * @return void
	 */
	public function modify( $option_fields ) {
		foreach ( $option_fields as $field_name => $field_value ) {
			if ( property_exists( $this, $field_name ) ) {
				$this->$field_name = $field_value;
			} else {
				// Trigger error is allowed in this case.
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				trigger_error( 'The requested field name "' . esc_attr( $field_name ) . '" does not exist!', E_USER_WARNING );
			}
		}
	}


	/**
	 * Return the option value as boolean
	 *
	 * @return bool
	 */
	public function to_bool() {
		if ( $this->is_true() ) {
			return true;
		}
		if ( $this->is_false() ) {
			return false;
		}
		return boolval( $this->value );
	}


	/**
	 * Return the option value as integer
	 *
	 * @return int
	 */
	public function to_int() {
		if ( $this->is_true() ) {
			return 1;
		}
		if ( $this->is_false() ) {
			return 0;
		}
		return intval( $this->value );
	}


	/**
	 * Return the option value as float
	 *
	 * @return float
	 */
	public function to_float() {
		if ( $this->is_true() ) {
			return 1.0;
		}
		if ( $this->is_false() ) {
			return 0.0;
		}
		return floatval( $this->value );
	}


	/**
	 * Return the option value as string
	 *
	 * @return string
	 */
	public function to_str() {
		return strval( $this->value );
	}


	/**
	 * Return if the option is a boolean true value
	 *
	 * @return bool
	 */
	private function is_true() {
		return in_array( $this->value, [ self::TRUE, self::TRUE_NUM ], true );
	}


	/**
	 * Return if the option is a boolean false value
	 *
	 * @return bool
	 */
	private function is_false() {
		return in_array( $this->value, [ self::FALSE, self::FALSE_NUM ], true );
	}

}
