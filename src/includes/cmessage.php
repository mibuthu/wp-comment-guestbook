<?php
/**
 * Handles the message after new comment
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!
if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once CGB_PATH . 'includes/options.php';



/**
 * Class for handling of cmessages (Messages after a new comment)
 */
class CGB_CMessage {

	/**
	 * Class singleton instance reference
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * Options class instance reference
	 *
	 * @var CGB_Options
	 */
	private $options;


	/**
	 * Singleton provider and setup
	 *
	 * @return self
	 */
	public static function &get_instance() {
		// There seems to be an issue with the self variable in phan.
		// @phan-suppress-next-line PhanPluginUndeclaredVariableIsset.
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Class constructor which initializes required variables
	 *
	 * @return void
	 */
	protected function __construct() {
		$this->options = &CGB_Options::get_instance();
	}


	/**
	 * Initializes the required scripts for the cmessage
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', array( &$this, 'register_scripts' ) );
		add_action( 'wp_footer', array( &$this, 'print_scripts' ) );
	}


	/**
	 * Registers the cmessage script
	 *
	 * @return void
	 */
	public function register_scripts() {
		wp_register_script( 'cgb_cmessage', CGB_URL . 'includes/js/cmessage.js', array( 'jquery' ), '1.0', true );
	}


	/**
	 * Prints the cmessage script including the required script variables
	 *
	 * @return void
	 */
	public function print_scripts() {
		$this->print_script_variables();
		wp_print_scripts( 'cgb_cmessage' );
	}


	/**
	 * Adds a cmessage indicator to the URL
	 *
	 * @param  string $url The actual url.
	 * @return string
	 */
	public function add_cmessage_indicator( $url ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$is_cgb_comment = isset( $_POST['is_cgb_comment'] ) ? (bool) intval( $_POST['is_cgb_comment'] ) : false;
		if ( ( '' !== $this->options->get( 'cgb_page_add_cmessage' ) && ! $is_cgb_comment )
			|| ( '' !== $this->options->get( 'cgb_add_cmessage' ) && $is_cgb_comment )
		) {
			$url_array       = explode( '#', $url );
			$query_delimiter = ( false !== strpos( $url_array[0], '?' ) ) ? '&' : '?';
			$url             = $url_array[0] . $query_delimiter . 'cmessage=1#' . $url_array[1];
		}
		return $url;
	}


	/**
	 * Prints the required cmessage script parameters to the html code
	 *
	 * @return void
	 */
	private function print_script_variables() {
		echo '
			<script type="text/javascript">
				var cmessage_text = "' . wp_kses_post( $this->options->get( 'cgb_cmessage_text' ) ) . '";
				var cmessage_type = "' . wp_kses_post( $this->options->get( 'cgb_cmessage_type' ) ) . '";
				var cmessage_duration = ' . intval( $this->options->get( 'cgb_cmessage_duration' ) ) . ';
				var cmessage_styles = "' . wp_kses_post( str_replace( array( '&#10;&#13;', "\r\n", '&#10;', '&#13;', "\r", "\n" ), ' ', $this->options->get( 'cgb_cmessage_styles' ) ) ) . '";
			</script>';
	}

}
