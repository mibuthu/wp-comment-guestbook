<?php

// This class handles all available options
class cgb_options {

   public static $group = 'comment-guestbook';

   public static $options = array(
      'cgb_clist_adjust'		=> array(	'section'   => 'comment_list',
                                          'type'      => 'checkbox',
						                        'std_val'	=> '1',
						                        'label'     => 'Adjust comment list output',
						                        'desc'		=> 'This option specifies if the comment list in the guestbook page should be adjusted or if the standard list specified in the theme should be used.' ) );/*,

		'cgb_clist_html'	      => array(	'section'	=> 'comment_list',
						                        'type'	   => 'textarea',
						                        'std_val'   => '--file--php/comments-template.php',
						                        'label'     => 'Comment list HTML-code',
						                        'desc'		=> 'This option specifies the html code for the adjusted comment list.' )
						               );
*/
   public static function register() {
      foreach( cgb_options::$options as $oname => $o ) {
         register_setting( cgb_options::$group, $oname );
      }
   }
/*
   public static function set( $name, $value ) {
      if( isset( cgb_options::$options[$name] ) ) {
         return update_option( $name, $value );
      }
      else {
         return false;
      }
   }
*/
   public static function get( $name ) {
      if( isset( cgb_options::$options[$name] ) ) {
         return get_option( $name, cgb_options::$options[$name]['std_val'] );
      }
      else {
         return null;
      }
   }
}
?>
