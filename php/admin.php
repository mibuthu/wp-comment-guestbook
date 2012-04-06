<?php

require_once( CGB_PATH.'php/options.php' );

// This class handles all available admin pages
class cgb_admin {

	// show the main admin page as a submenu of "Comments"
	public static function show_main() {
		if (!current_user_can('edit_posts'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}

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
	      <form method="post" action="options.php">
	         ';
      ob_start();
		settings_fields( cgb_options::$group );
		//do_settings_sections( cgb_options::$group );
		$out .= ob_get_contents();
		ob_end_clean ();
	   $out .= '
		      <h3>Comment Guestbook Options</h3>
		      <div style="padding:0 10px">
			      <h4>Comment form options:</h4>
			      <div style="padding:0 10px">
				      <p>This is an early version of this plugin. No options are available yet.</p>
			      </div>
			      <h4>Comments list options:</h4>
			      <div style="padding:0 10px">';
	   $out .= cgb_admin::show_options( 'comment_list' );
	   $out .= '
			      </div>
            </div>
		      ';
		ob_start();
		submit_button();
		$out .= ob_get_contents();
		ob_end_clean ();
      $out .='
         </form>';
		echo $out;
	}
	
	private static function show_options( $section ) {
	   $out = '';
	   foreach( cgb_options::$options as $oname => $o ) {
	      if( $o['section'] == $section ) {
	         $out .='
                        <p>';
	         switch( $o['type'] ) {
	            case 'checkbox':
	               $out .= cgb_admin::show_checkbox( $oname, cgb_options::get( $oname ), $o['label'] );
	               break;
	            case 'textarea':
	               $out .= cgb_admin::show_textarea( $oname, cgb_options::get( $oname ), $o['label'] );
	               break;
	         }
	         $out .='
                     </p>';
         }
	   }
	   return $out;
	}
	
	private static function show_checkbox( $name, $value, $label ) {
      $out = '
                        <label for="'.$name.'">
                           <input name="'.$name.'" type="checkbox" id="'.$name.'" value="1"';
      if( $value == 1 ) {
         $out .= ' checked="checked"';
      }
      $out .= ' />
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
}
?>
