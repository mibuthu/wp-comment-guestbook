<?php
/**
 * If this file is attempted to be accessed directly, we'll exit.
*/
if(!defined('ABSPATH')) {
	exit;
}

/**
 * Includes
*/
require_once(CGB_PATH.'php/options.php');



/**
 * Class for the cmessages (Messages after a new comment)
*/
class CGB_CMessage {
	/**
	 * Single instance of the class
	 *
	 * @var object
	 */
	protected static $instance;

	/**
	 * Instance of the options class
	 *
	 * @var object
	 */
	private $options;


	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return PluginName A single instance of this class.
	 */
	public static function &get_instance() {
		if(!isset(self::$instance)) {
			self::$instance = new CGB_CMessage();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	protected function __construct() {
		$this->options = &cgb_options::get_instance();
	}

	/**
	 * Initializes the required scripts for the cmessage
	 */
	public function init() {
		add_action('init', array(&$this, 'register_scripts'));
		add_action('wp_footer', array(&$this, 'print_scripts'));
	}

	/**
	 * Registers the cmessage script
	 */
	public function register_scripts() {
		wp_register_script('cgb_cmessage', CGB_URL.'js/cmessage.js', array('jquery'), true);
	}

	/**
	 * Prints the cmessage script
	 */
	public function print_scripts() {
		$this->print_script_variables();
		wp_print_scripts('cgb_cmessage');
	}

	/**
	 * Adds a cmessage indicator to the URL
	 *
	 * @param  string  $url  The URL in which the indicator should be added
	 *
	 * @return The URL with additional cmessage indicator
	 */
	public function add_cmessage_indicator($url) {
		if('always' === $this->options->get('cgb_cmessage') ||
				('guestbook_only' === $this->options->get('cgb_cmessage') && isset($_POST['is_cgb_comment']))) {
			$url_array = explode('#', $url);
			$query_delimiter = (false !== strpos($url_array[0], '?')) ? '&' : '?';
			$url = $url_array[0].$query_delimiter.'cmessage=1#'.$url_array[1];
		}
		return $url;
	}

	/**
	 * Prints the required cmessage script parameters to the html code
	 */
	private function print_script_variables() {
		$out = '
			<script type="text/javascript">
				var cmessage_text = "'.$this->options->get('cgb_cmessage_text').'";
				var cmessage_type = "'.$this->options->get('cgb_cmessage_type').'";
			</script>';
		echo $out;
	}
}