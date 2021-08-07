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
	 * Actual value as string
	 *
	 * @var string
	 */
	public $value;

	/**
	 * Default value
	 *
	 * @var string
	 */
	public $default_value;

	/**
	 * Permitted values
	 *
	 * @var string[]
	 */
	public $permitted_values = null;

	/**
	 * Section
	 *
	 * @var string
	 */
	public $section = '';

	/**
	 * The boolean TRUE value
	 *
	 * @var string
	 */
	const TRUE = 'true';

	/**
	 * The boolean FALSE value
	 *
	 * @var string
	 */
	const FALSE = 'false';

	/** The permitted boolean value options
	 *
	 * @var string[]
	 */
	const BOOLEAN = [
		self::TRUE,
		self::FALSE,
	];

	/**
	 * The boolean TRUE value as a number
	 *
	 * @var string
	 */
	const TRUE_NUM = '1';

	/**
	 * The boolean FALSE value as a number
	 *
	 * @var string
	 */
	const FALSE_NUM = '';

	/** The permitted boolean value options as a number
	 *
	 * @var array<string,string>
	 */
	const BOOLEAN_NUM = [
		self::TRUE_NUM,
		self::FALSE_NUM,
	];


	/**
	 * Default class constructor
	 *
	 * @param string $default_value The default value of the option
	 * @return void
	 */
	private function __construct( $default_value ) {
		$this->default_value = $default_value;
		$this->value         = $default_value;
	}


	/**
	 * Create a new option with a given default value.
	 *
	 * @param string $default_value The default value of the option
	 * @param string $section The section of the option (optional)
	 * @return static
	 */
	public static function new( $default_value, $section = null ) {
		$option          = new static( $default_value );
		$option->section = $section;
		return $option;
	}


	/**
	 * Create a new boolean option with default value true.
	 *
	 * @param string $section The section of the option (optional)
	 * @return static
	 */
	public static function new_true( $section = '' ) {
		$option                   = new static( static::TRUE );
		$option->permitted_values = static::BOOLEAN;
		$option->section          = $section;
		return $option;
	}


	/**
	 * Create a new boolean option with default value false.
	 *
	 * @param string $section The section of the option (optional)
	 * @return static
	 */
	public static function new_false( $section = '' ) {
		$option                   = new static( static::FALSE );
		$option->permitted_values = static::BOOLEAN;
		$option->section          = $section;
		return $option;
	}


	/**
	 * Create a new boolean option as a number with default value true.
	 *
	 * @param string $section The section of the option (optional)
	 * @return static
	 */
	public static function new_true_num( $section = '' ) {
		$option                   = new static( static::TRUE_NUM );
		$option->permitted_values = static::BOOLEAN_NUM;
		$option->section          = $section;
		return $option;
	}


	/**
	 * Create a new boolean option as a number with default value false.
	 *
	 * @param string $section The section of the option (optional)
	 * @return static
	 */
	public static function new_false_num( $section = '' ) {
		$option                   = new static( static::FALSE_NUM );
		$option->permitted_values = static::BOOLEAN_NUM;
		$option->section          = $section;
		return $option;
	}


	/**
	 * Return the option value as string
	 * This is a alternative way to get the value directly via $option->value.
	 *
	 * @return string
	 */
	public function as_str() {
		return $this->value;
	}


	/**
	 * Return the option value as integer
	 *
	 * @return int
	 */
	public function as_int() {
		// Boolean values
		if ( $this->is_boolean_true() ) {
			return 1;
		}
		if ( $this->is_boolean_false() ) {
			return 0;
		}
		// All other types
		return intval( $this->value );
	}


	/**
	 * Return the option value as float
	 *
	 * @return float
	 */
	public function as_float() {
		if ( $this->is_boolean_true() ) {
			return 1.0;
		}
		if ( $this->is_boolean_false() ) {
			return 0.0;
		}
		return floatval( $this->value );
	}


	/**
	 * Return if the option value is true
	 *
	 * @return bool
	 */
	public function is_true() {
		if ( $this->is_boolean_true() ) {
			return true;
		}
		if ( $this->is_boolean_false() ) {
			return false;
		}
		return boolval( $this->value );
	}


	/**
	 * Return if the option value is false
	 *
	 * @return bool
	 */
	public function is_false() {
		return ! $this->is_true();
	}


	/**
	 * Return if the option is true
	 *
	 * @return bool
	 */
	private function is_boolean_true() {
		return in_array( $this->value, [ self::TRUE, self::TRUE_NUM ], true );
	}


	/**
	 * Return if the option is false
	 *
	 * @return bool
	 */
	private function is_boolean_false() {
		return in_array( $this->value, [ self::FALSE, self::FALSE_NUM ], true );
	}

}
