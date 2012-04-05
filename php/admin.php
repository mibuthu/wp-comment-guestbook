<?php

// This class handles all available admin pages
class cgb_admin {

   public static $options = array(
      'clist_adjusted'		=> array(	'name'      => 'cgb_clist_adjusted',
                                       'section'	=> 'comment_list',
                                       'type'      => 'checkbox',
						                     'std_val'	=> '1',
						                     'label'     => 'Adjust comment list output',
						                     'desc'		=> 'This option specifies if the comment list in the guestbook page should be adjusted or if the standard list specified in the theme should be used.' ),

		'clist_html'	      => array(	'name'      => 'cgb_clist_html',
		                                 'section'	=> 'comment_list',
						                     'type'	   => 'textarea',
						                     'std_val'   => '--file--php/comments-template.php',
						                     'label'     => 'Comment list HTML-code',
						                     'desc'		=> 'This option specifies the html code for the adjusted comment list.' )
						               );

	// show the main admin page as a submenu of "Comments"
	public static function show_main() {
		if (!current_user_can('edit_posts'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		// Read options
		cgb_admin::read_options(); 

		$out ='
			<div class="wrap nosubsub" style="padding-bottom:15px">
			<div id="icon-edit-comments" class="icon32"><br /></div><h2>Comment Guestbook</h2>
			</div>
			<h3>Create a guestbook page</h3>
			<div style="padding:0 15px">
				<p>"Comment guestbook" works using a "shortcode" in a page. Shortcodes are snippets of pseudo code that are placed in blog posts or pages to easily render HTML output.</p>
				<p>To create a guestbook goto "Pages" -> "Add new" in the admin menu to create a new page. Choose your page title e.g. "Guestbook" and add the shortcode <code>[comment-guestbook]</code> in the text field.<br />
				You can add additional normal text if you want to display something else on the top of this page. That´s all you have to do. Save and publish the page to finish the guestbook creation.</p>
				<p>The shortcode will be replaced by the comment form. </p>
				<br />
			</div>';
		$out .= '
			<h3>Comment Guestbook Options</h3>
			<div style="padding:0 10px">
				<h4>Comment form options:</h4>
				<div style="padding:0 10px">
					<p>This is an early version of this plugin. No options are available yet.</p>
				</div>
				<h4>Comments list options:</h4>
				<div style="padding:0 10px">
					<p>'.cgb_admin::show_options( 'comment_list' ).'</p>
				</div>
			</div>';
		echo $out;
	}
	
	private static function show_options( $section ) {
	   $out = '';
	   foreach( cgb_admin::$options as $oname => $o ) {
	      $out .='
	         <p>';
	      switch( $o['type'] ) {
	         case 'checkbox':
	            $out .= cgb_admin::show_checkbox( $o['name'], $o['std_val'], $o['label'] );
	            break;
	         case 'textarea':
	            $out .= cgb_admin::show_textarea( $o['name'], $o['std_val'], $o['label'] );
	            break;
	      }
	      $out .='
	         </p>';
	   }
	   return $out;
	}
	
	private static function show_checkbox( $name, $value, $label ) {
	   $out = '
	         <label for="'.$name.'">
               <input name="'.$name.'" type="checkbox" id="'.$name.'" value="'.$value.'"  />
               '.$label.'
            </label>';
      return $out;
   }
   
   private static function show_textarea( $name, $value, $label ) {
      $out = '';
      if( $label != '' ) {
         $out = '
            <label for="'.$name.'">
               '.$label.': 
            </label>';
      }
      $out .= '
            <textarea name="'.$name.'" id="'.$name.'" rows="15" class="large-text code">'.$value.'</textarea>';
      return $out;
   }
   
   private static function read_options() {
      foreach( cgb_admin::$options as $oname => $o ) {
         // TODO
      }
   }
}
?>
