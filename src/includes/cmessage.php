<?php
/**
 * Handles the message after new comment
 *
 * @package comment-guestbook
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\CommentGuestbook;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/config.php';



/**
 * Class for handling of cmessages (Messages after a new comment)
 */
class CMessage {

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
	 * @return void
	 */
	public function __construct( &$config_instance ) {
		$this->config = $config_instance;
	}


	/**
	 * Initializes the required scripts for the cmessage
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ &$this, 'register_scripts' ] );
		add_action( 'wp_footer', [ &$this, 'print_scripts' ] );
	}


	/**
	 * Registers the cmessage script
	 *
	 * @return void
	 */
	public function register_scripts() {
		wp_register_script( 'cgb_cmessage', PLUGIN_URL . 'includes/js/cmessage.js', [ 'jquery' ], '1.0', true );
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
		if ( ( $this->config->page_cmessage_enabled->to_bool() && ! $is_cgb_comment )
			|| ( $this->config->cmessage_enabled->to_bool() && $is_cgb_comment )
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
				var cmessage_text = "' . wp_kses_post( $this->config->cmessage_text->to_str() ) . '";
				var cmessage_type = "' . wp_kses_post( $this->config->cmessage_type->to_str() ) . '";
				var cmessage_duration = ' . wp_kses_post( $this->config->cmessage_duration->to_str() ) . ';
				var cmessage_styles = "' . wp_kses_post( str_replace( [ '&#10;&#13;', "\r\n", '&#10;', '&#13;', "\r", "\n" ], ' ', $this->config->cmessage_styles->to_str() ) ) . '";
			</script>';
	}

}
