<?php
 /*
   Plugin Name: social_chat
   Plugin URI: http://www.letscms.com
   description: >- This Plugin is for Social Chat.
   Version: 1.0
   Author: LetsCMS
   Author URI: http://www.letscms.com
   License: GPL
   */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
  define('ABSPATH', dirname(__FILE__) . '/');

/* Function to apply Form Validation*/
  function sc_registration_validation($sc_facebook,$sc_whatsapp,$sc_email,$sc_call,$sc_greeting_message,$sc_call_to_action,$sc_button_color,$sc_position,$sc_order)
    {

        global $reg_errors;
        $reg_errors = new WP_Error;

    if ( empty( $sc_facebook )){
        $reg_errors->add('sc_facebook', 'Required form field : Facebook is missing');
    }
    if ( empty( $sc_button_color )){
        $reg_errors->add('sc_button_color', 'Required form field : Button Color is missing');
    }
    if ( empty( $sc_order )){
        $reg_errors->add('sc_order', 'Required form field : Order is missing');
    }
    if ( empty( $sc_position )){
        $reg_errors->add('sc_position', 'Required form field : Position is missing');
    }
    if ( empty( $sc_call )){
        $reg_errors->add('sc_call', 'Required form field : Call is missing');
    }
    if ( !is_email( $sc_email ) ) {
        $reg_errors->add( 'sc_email_invalid', 'Email is not valid' );
    }

   return $reg_errors;
    }

/*Function to create setting option for plugin*/
function sc_plugin_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=letscms_social_chat">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
    return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'sc_plugin_add_settings_link' );

/*Function to add option page*/
function sc_register_options_page() {
add_options_page('Letscms Social Chat', 'Social Chat', 'manage_options', 'letscms_social_chat', 'sc_social_chat_page');
}
add_action('admin_menu', 'sc_register_options_page');

/*Function social_chat_page*/
function sc_social_chat_page()
  {
    global $reg_errors;
    $reg_errors=sc_registration_validation(
      $_POST['sc_facebook'],
      $_POST['sc_whatsapp'],
      $_POST['sc_email'],
      $_POST['sc_call'],
      $_POST['sc_greeting_message'],
      $_POST['sc_call_to_action'],
      $_POST['sc_button_color'],
      $_POST['sc_position'],
      $_POST['sc_order']
    );

  if($_POST['submit'] && empty($reg_errors->errors)){
  if(wp_verify_nonce($_POST[_nonce],'submit_nonce')){

    // sanitize user form input
    global $sc_facebook,$sc_whatsapp,$sc_email,$sc_call,$sc_greeting_message,$sc_call_to_action,$sc_button_color,$sc_position,$sc_order;
    $sc_facebook =   sanitize_text_field( $_POST['sc_facebook'] );
    $sc_whatsapp  =   sanitize_text_field( $_POST['sc_whatsapp'] );
    $sc_email      =   sanitize_email( $_POST['sc_email'] );
    $sc_call  =   sanitize_text_field( $_POST['sc_call'] );
    $sc_greeting_message  =   esc_textarea( $_POST['sc_greeting_message'] );
    $sc_call_to_action  =   sanitize_text_field( $_POST['sc_call_to_action'] );
    $sc_button_color  =   sanitize_text_field( $_POST['sc_button_color'] );
    $sc_position  =   sanitize_text_field( $_POST['sc_position'] );
    $sc_order  =   sanitize_text_field( $_POST['sc_order'] );

    /***************************FILE UPLOAD*************************************************/

    if(!empty($_FILES['sc_logo'])){
      $sc_uploadfiles  = $_FILES['sc_logo'];

      $sc_filename     = $sc_uploadfiles['name'];
      $sc_filetmp      = $sc_uploadfiles['tmp_name'];
      $sc_filetype     = wp_check_filetype( basename( $sc_filename ), null );
      $sc_filetitle    = preg_replace('/\.[^.]+$/', '', basename( $sc_filename ) );
      $sc_filename     = $sc_filetitle . '.' . $sc_filetype['ext'];

      $sc_upload_dir   = wp_upload_dir();

/**
         * Check if the filename already exist in the directory and rename the
         * file if necessary
**/

      $i = 0;
      while ( file_exists( $sc_upload_dir['path'] .'/' . $sc_filename ) )
      {
        $sc_filename = $sc_filetitle . '_' . $i . '.' . $sc_filetype['ext'];
        $i++;
      }
        $sc_filedest = $sc_upload_dir['path'] . '/' . $sc_filename;
        $sc_imageurl = $sc_upload_dir['url'] . '/' . $sc_filename;
/**
         * Save temporary file to uploads dir
**/
//die($sc_imageurl);
      if (move_uploaded_file($sc_filetmp, $sc_filedest) )
      {

        echo "The file has been uploaded.";
        update_option('sc_logo',$sc_imageurl);
      }

      update_option('sc_facebook',$sc_facebook);
      update_option('sc_whatsapp',$sc_whatsapp);
      update_option('sc_email',$sc_email);
      update_option('sc_call',$sc_call);
      update_option('sc_greeting_message',$sc_greeting_message);
      update_option('sc_call_to_action',$sc_call_to_action);
      update_option('sc_button_color',$sc_button_color);
      update_option('sc_position',$sc_position);
      update_option('sc_order',$sc_order);

      }
    }
    else{
    die('Security check');
  }
  }

    ?>
    <div class="wrap">
       <h2>Social Chat Settings</h2>
        <form action="" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">
        <table class="form-table">
          <tr><th scope="row">
            <label class="col-sm-2 control-label" for="input-status">Facebook</label></th>
            <td>
              <input type="text" name="sc_facebook" value="<?php echo get_option('sc_facebook');?>" id="input-status" class="regular-text code"/>
              <?php
                    if($_POST['submit']){
                      if(!empty($reg_errors->errors['sc_facebook'])){
                         echo '<font color=red>' . $reg_errors->errors['sc_facebook'][0] . '</font>';
                      }
                    }
              ?>
              <input type="hidden" name="_nonce" value="<?php echo wp_create_nonce(submit_nonce) ?>"/>
            </td></tr>


          <tr><th scope="row">
            <label class="col-sm-2 control-label" for="input-status">WhatsApp</label></th>
            <td>
              <input type="text" name="sc_whatsapp" value="<?php echo get_option('sc_whatsapp');?>" id="input-status" class="regular-text code"/>
            </td></tr>

          <tr><th scope="row">
            <label class="col-sm-2 control-label" for="input-status">Email</label></th>
            <td>
              <input type="text" name="sc_email" value="<?php echo get_option('sc_email');?>" id="input-status" class="regular-text code"/>
              <?php
                    if($_POST['submit']){
                      if(!empty($reg_errors->errors['email_invalid'])){
                         echo '<font color=red>' . $reg_errors->errors['email_invalid'][0] . '</font>';
                      }
                    }
              ?>
            </td></tr>

          <tr><th scope="row">
            <label class="col-sm-2 control-label" for="input-status">Call</label></th>
            <td>
              <input type="text" name="sc_call" value="<?php echo get_option('sc_call');?>" id="input-status" class="regular-text code"/>
              <?php
                    if($_POST['submit']){
                      if(!empty($reg_errors->errors['sc_call'])){
                         echo '<font color=red>' . $reg_errors->errors['sc_call'][0] . '</font>';
                      }
                    }
              ?>
            </td></tr>

          <tr><th scope="row">
            <label class="col-sm-2 control-label" for="input-status">Company Logo</label></th>
            <td><img src="<?php echo get_option('sc_logo');?>">
              <input type="file" name="sc_logo" value="" />
            </td>
          </tr>

          <tr><th scope="row">
            <label class="col-sm-2 control-label" for="input-status">Greeting Message</label></th>
            <td>
              <textarea name="sc_greeting_message" value="" cols="24" id="input-status" class="regular-text code"><?php echo get_option('sc_greeting_message');?></textarea></td>
          </tr>

          <tr><th scope="row">
            <label class="col-sm-2 control-label" for="input-status">Call To Action</label></th>
            <td>
              <input type="text" name="sc_call_to_action" value="<?php echo get_option('sc_call_to_action');?>" id="input-status" class="regular-text code"/>

            </td>
          </tr>

          <tr><th scope="row">
            <label class="col-sm-2 control-label" for="input-status">Button Color</label></th>
            <td>
              <input type="text" name="sc_button_color" value="<?php echo get_option('sc_button_color');?>" id="input-status" class="regular-text code"/>
              <?php
                    if($_POST['submit']){
                      if(!empty($reg_errors->errors['sc_button_color'])){
                         echo '<font color=red>' . $reg_errors->errors['sc_button_color'][0] . '</font>';
                      }
                    }
              ?>
            </td></tr>

          <tr><th scope="row">
            <label class="col-sm-2 control-label" for="input-status">Position</label></th>
            <td>
               <select name="sc_position" id="input-status"class="regular-text code">
                <?php
                  if(get_option('sc_position')=='left')
                {
                ?>
                  <option value="left" selected="selected">Left</option>
                  <option value="right">Right</option>
                <?php
                }
                else{
                ?>
                  <option value="left">Left</option>
                  <option value="right" selected="selected">Right</option>
               <?php } ?>
               </select>
              <?php
                    if($_POST['submit']){
                      if(!empty($reg_errors->errors['sc_position'])){
                         echo '<font color=red>' . $reg_errors->errors['sc_position'][0] . '</font>';
                      }
                    }
              ?>
             </td></tr>

          <tr><th scope="row">
            <label class="col-sm-2 control-label" for="input-status">Order</label></th>
            <td>
              <textarea name="sc_order" value="" cols="24" id="input-status" class="regular-text code"><?php echo get_option('sc_order'); ?></textarea>
              <?php
                    if($_POST['submit']){
                      if(!empty($reg_errors->errors['sc_order'])){
                         echo '<font color=red>' . $reg_errors->errors['sc_order'][0] . '</font>';
                      }
                    }
              ?>
            </td></tr>

          <tr>
            <td >
              <input type="submit" name="submit" id="input-submit" value="Save Values" class="button button-primary"/></td>
          </tr>
        </form>
      </div>
<?php
    }
  function sc_script(){ ?>
    <!-- WhatsHelp.io widget -->
    <script type="text/javascript">
        (function () {
            var options = {
              facebook: "<?php echo get_option('sc_facebook');?>", // sc_facebook page ID
              whatsapp: "<?php echo get_option('sc_whatsapp');?>", // sc_whatsapp number
              email: "<?php echo get_option('sc_email');?>", // sc_email
              call: "<?php echo get_option('sc_call');?>", // sc_call phone number
              company_logo_url: "<?php echo get_option('sc_company_logo_url');?>", // URL of company logo (png, jpg, gif)
              greeting_message: "<?php echo get_option('sc_greeting_message');?>", // Text of greeting message
              call_to_action: "<?php echo get_option('sc_call_to_action');?>", // sc_call to action
              button_color: "<?php echo get_option('sc_button_color');?>", // Color of button
              position: "<?php echo get_option('sc_position'); ?>", // sc_position may be 'right' or 'left'
              order: "<?php echo get_option('sc_order'); ?>" // sc_order of buttons
            };
            var proto = document.location.protocol, host = "whatshelp.io", url = proto + "//static." + host;
            var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';
            s.onload = function () { WhWidgetSendButton.init(host, proto, options); };
            var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
        })();
    </script><?php
  }
  add_action('wp_footer','sc_script',10000);
?>
