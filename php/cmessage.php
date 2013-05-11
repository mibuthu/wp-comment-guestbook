<?php

require_once( CGB_PATH.'php/options.php' );

// This class handles all available admin pages
class CGB_CMessage {
	protected static $instance;
	private $options;

	public static function &get_instance() {
		if( !isset( self::$instance ) ) {
			self::$instance = new CGB_CMessage();
		}
		return self::$instance;
	}

	protected function __construct() {
		$this->options = &cgb_options::get_instance();
	}

	public function init() {
		add_action( 'init', array( &$this, 'register_scripts' ) );
		add_action( 'wp_footer', array( &$this, 'print_scripts' ) );
	}

	public function register_scripts() {
		wp_register_script( 'block_ui', 'http://malsup.github.com/jquery.blockUI.js', array( 'jquery' ), true );
		wp_register_script( 'cgb_cmessage', CGB_URL.'js/cmessage.js', array( 'block_ui' ), true );
	}

	public function print_scripts() {
		$this->print_script_variables();
		wp_print_scripts( 'cgb_comment_guestbook' );
	}

	public function add_cmessage_indicator( $url ) {
		if( 'always' === $this->options->get( 'cgb_cmessage' ) ||
				( 'guestbook_only' === $this->options->get( 'cgb_cmessage' ) && isset( $_POST['is_cgb_comment'] ) ) ) {
			$url_array = explode( '#', $url );
			$query_delimiter = ( false !== strpos( $url_array[0], '?' ) ) ? '&' : '?';
			$url = $url_array[0].$query_delimiter.'cmessage=1#'.$url_array[1];
		}
		return $url;
	}

	private function print_script_variables() {
		$out = '
			<script type="text/javascript">
				var cmessage_text = "'.$this->options->get( 'cgb_cmessage_text' ).'";
				var cmessage_type = "'.$this->options->get( 'cgb_cmessage_type' ).'";
			</script>';
		echo $out;
	}
}