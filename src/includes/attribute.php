<?php
/**
 * Attribute Class
 *
 * @package comment-guestbook
 */

declare( strict_types=1 );
if ( ! defined( 'WPINC' ) ) {
	exit();
}


/**
 * Attribute Class
 *
 * This class handles the attributes for shortcode, widget options and plugin options.
 */
class CGB_Attribute {

	/**
	 * Attribute (default) value
	 *
	 * @var string
	 */
	public $value;

	/**
	 * Attribute value options
	 *
	 * @var string|array
	 */
	public $value_options = '';

	/**
	 * Attribute section
	 *
	 * @var string
	 */
	public $section = '';

	/**
	 * Attribute type
	 *
	 * @var string
	 */
	public $type = '';

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
	public $range = array();

	/**
	 * Attribute label
	 *
	 * @var string
	 */
	public $label = '';

	/**
	 * Attribute caption
	 *
	 * @var string
	 */
	public $caption = '';

	/**
	 * Attribute captions
	 *
	 * @var string[]
	 */
	public $captions = array();

	/**
	 * Attribute caption after widget
	 *
	 * @var null|string
	 */
	public $caption_after = '';

	/**
	 * Attribute description
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Attribute tooltip
	 *
	 * @var string
	 */
	public $tooltip = '';

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
	 * Class constructor which sets the required variables
	 *
	 * @param string            $default_value Standard attribute value.
	 * @param null|string|array $value_options Attribute value (optional).
	 * @param null|string       $section Attribute section (optional).
	 * @return void
	 */
	public function __construct( $default_value, $value_options = null, $section = null ) {
		$this->value = $default_value;
		if ( ! is_null( $value_options ) ) {
			$this->value_options = $value_options;
		}
		if ( ! is_null( $section ) ) {
			$this->section = $section;
		}
	}


	/**
	 * Update several fields at once with the values given in an array
	 *
	 * @param array<string,string> $attributes Fields with values to update.
	 * @return void
	 * @throws Exception Option not available.
	 */
	public function update( $attributes ) {
		foreach ( $attributes as $name => $value ) {
			if ( property_exists( $this, $name ) ) {
				$this->$name = $value;
			} else {
				// Not available attribute.
				throw new Exception( 'Requested attribute "' . $name . '" not available!' );
			}
		}
	}

}
