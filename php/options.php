<?php

// This class handles all available options
class cgb_options {

   public static $group = 'comment-guestbook';

   public static $options = array(
      'cgb_clist_adjust'		      => array(	'section'   => 'comment_list',
                                                'type'      => 'checkbox',
						                              'std_val'	=> '',
						                              'label'     => 'Comment list adjustment',
						                              'caption'   => 'Adjust the comment list output',
						                              'desc'		=> 'This option specifies if the comment list in the guestbook page should be adjusted or if the standard list specified in the theme should be used.' ),
						                          
		'cgb_clist_comment_callback'  => array(   'section'   => 'comment_list',
		                                          'type'      => 'text',
		                                          'std_val'   => '--func--comment_callback',
		                                          'label'     => 'Comment callback function',
		                                          'desc'      => 'This option sets the name of a custom function to use to display each comment, if comment list adjustment is enabled.<br />
		                                                            Using this will make your custom function get called to display each comment, bypassing all internal WordPress functionality in this respect.<br />
		                                                            Normally this function is set through the selected theme. Comment Guestbook searches for the theme-function and uses this as default, if it was found. <br />
		                                                            If the theme-function wasn´t found this field will be empty, then the WordPress internal functionality will be used.<br />
		                                                            If you want to insert the function of your theme manually, you can find the name in "functions.php" in your theme directory.<br />
		                                                            Normally it is called themename_comment, e.g. for twentyeleven theme: twentyeleven_comment.' )
/*
		   'cgb_clist_comment_html'   => array(	'section'	=> 'comment_list',
						                              'type'	   => 'textarea',
						                              'std_val'   => '--file--php/comments-template.php',
						                              'label'     => 'Comment list HTML-code',
						                              'desc'		=> 'This option specifies the html code for the adjusted comment list.' )
*/						               );

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
         // set std_val, if a function is used to set the value
         if( substr( cgb_options::$options[$name]['std_val'], 0, 8 ) == '--func--' ) {
            cgb_options::$options[$name]['std_val'] = call_user_func( array('cgb_options', substr( cgb_options::$options[$name]['std_val'], 8 ) ) );
         }
         return get_option( $name, cgb_options::$options[$name]['std_val'] );
      }
      else {
         return null;
      }
   }

   private static function comment_callback() {
      $func = get_stylesheet().'_comment';
      if( function_exists( $func ) ) {
         return $func;
      }
      else {
         return '';
      }
   }
}
?>
