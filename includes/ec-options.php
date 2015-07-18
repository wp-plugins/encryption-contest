<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
add_action( 'admin_menu', array('EcOptions','addOptionsPage' ));
add_action( 'admin_init', array('EcOptions','registerSettings' ));

class EcOptions {

  public function addOptionsPage() {
      $page_title = 'Encryption contest options';
      $menu_title = __('Encryption contest', 'encrypt');
      $capability = 'manage_options';
      $menu_slug  = 'EcOptions';
      $function   = array('EcOptions', 'pageContent');
      add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
  }
  
  public function registerSettings() {
      register_setting( 'EcOptions', 'EcSettings' );
  }
  
  // It controls checkboxs check after save, when value is 1
  public function checkboxStatus($field_name) {
      $settings = get_option('EcSettings');      
      if (isset( $settings[$field_name] ))
          echo ('checked="checked"');           
  }
  
  // Generate capability drop-down list. Sometimes admin needs from redactor or other capatibility possibility to manage tasks instead of admin. Default is admin.
  public function capabilityList() {
      $settings = get_option('EcSettings');
      ?>
      <select name="EcSettings[capabilityList]" id="capabilityList">
          <option value="activate_plugins" <?php selected( $settings['capabilityList'], "activate_plugins" ); ?>><?php echo __('Administrator', 'encrypt') ?></option>
          <option value="publish_pages" <?php selected( $settings['capabilityList'], "publish_pages" ); ?>      ><?php echo __('Editor', 'encrypt') ?></option>
          <option value="publish_posts" <?php selected( $settings['capabilityList'], "publish_posts" ); ?>      ><?php echo __('Author', 'encrypt') ?></option>
          <option value="edit_posts" <?php selected( $settings['capabilityList'], "edit_posts" ); ?>            ><?php echo __('Contributor', 'encrypt') ?></option>
          <option value="read" <?php selected( $settings['capabilityList'], "read" ); ?>                        ><?php echo __('Subscriper', 'encrypt') ?></option>
      </select>
      <?php
  }
  
  // When admin hit default button, plugin will display warning window.
  public function defaultButton() {      
      ?><script type="text/javascript">
          function confirm_delete() {
          return confirm ('Do you really want delete all created tasks and options? Defaults will be restored.');}
      </script>
      <input type="submit" class="button-secondary" value="<?php echo __('Restore Default settings', 'encrypt') ?>" name="restore_default" onclick="return confirm_delete()">
      <?php
  }
  
  // It controls, if admin hit default button
  public function ifDefaultButton() {
      $classEc = new EncryptionContest();
      if ( isset($_POST['restore_default']) && $_POST['restore_default'] )
          $classEc->defaults();   
  }
  
  // Generate drop-down menu for choose what heading level should be use in front-end, default is h6.
  public function titleTag() {
      $settings = get_option('EcSettings');
      ?>
      <select name="EcSettings[titleTag]" id="titleTag">
          <option value="1" <?php selected( $settings['titleTag'], "1" ); ?>><code>&lt;h1&gt; &lt;/h1&gt;</code></option>
          <option value="2" <?php selected( $settings['titleTag'], "2" ); ?>><code>&lt;h2&gt; &lt;/h2&gt;</code></option>
          <option value="3" <?php selected( $settings['titleTag'], "3" ); ?>><code>&lt;h3&gt; &lt;/h3&gt;</code></option>
          <option value="4" <?php selected( $settings['titleTag'], "4" ); ?>><code>&lt;h4&gt; &lt;/h4&gt;</code></option>
          <option value="5" <?php selected( $settings['titleTag'], "5" ); ?>><code>&lt;h5&gt; &lt;/h5&gt;</code></option>
          <option value="6" <?php selected( $settings['titleTag'], "6" ); ?>><code>&lt;h6&gt; &lt;/h6&gt;</code></option>
      </select>
      <?php    
  }

  // Main function for plugin options page
  public function pageContent() {
      $classEc = new EncryptionContest();
      $classEc->ifSetDefaults();
      $classOptions = new EcOptions();
      $classOptions->ifDefaultButton();
      $settings = get_option('EcSettings');      
      ?>
      <div class="wrap">
          <h2>Encryption contest settings</h2>
          <p><?php echo __('Plugin is used by inserting shortcode <b>[encryption-contest]</b> anywhere on page. General settings are set right here. You can create tasks for all users', 'encrypt'). ' ' 
               .'<a href="'.get_bloginfo('siteurl').'/wp-admin/admin.php?page=ec-menu">'. __('in main menu', 'encrypt')?></a>.</p>     
          <form method="post" action="options.php">
              <?php settings_fields('EcOptions'); ?>
              <?php do_settings_sections('EcOptions'); ?>
              
              <table class="form-table">
                  <tr>          
                      <th><label for="showTaskList"><?php echo __('Show list of preview tasks:', 'encrypt') ?></label></th>
                      <td><input type="checkbox" name="EcSettings[showTaskList]" id="showTaskList" value="1" <?php $classOptions->checkboxStatus('showTaskList'); ?>></td>                     
                  </tr>
                  <tr>
                      <th><label for="showCountdown"><?php echo __('Show countdown:', 'encrypt') ?></label></th>
                      <td><input type="checkbox" name="EcSettings[showCountdown]" id="showCountdown" value="1" <?php $classOptions->checkboxStatus('showCountdown'); ?>></td>                   
                  </tr>
                  <tr>
                      <th><label for="showUsers"><?php echo __('Show successful users:', 'encrypt') ?></label></th>
                      <td><input type="checkbox" name="EcSettings[showUsers]" id="showUsers" value="1" <?php $classOptions->checkboxStatus('showUsers'); ?>></td>                   
                  </tr>    
                  <tr>
                      <th><label for="sendEmail"><?php echo __('Send email with answers:', 'encrypt') ?></label></th>
                      <td><input type="checkbox" name="EcSettings[sendEmail]" id="sendEmail" value="1" <?php $classOptions->checkboxStatus('sendEmail'); ?>></td>                   
                  </tr>                       
                  <tr>
                      <th><label for="emailAdress"><?php echo __('Email adress:', 'encrypt') ?></label></th>
                      <td><input type="email" name="EcSettings[emailAdress]" id="emailAdress" value="<?php echo ($settings['emailAdress']); ?>" size="40" placeholder="example@example.com"></td>                   
                  </tr> 
                  <tr>
                      <th><label for="pageLink"><?php echo __('Link to page with shortcode:', 'encrypt') ?></label></th>
                      <td><input type="url" name="EcSettings[pageLink]" id="pageLink" value="<?php echo ($settings['pageLink']); ?>" size="60" placeholder="<?php echo (site_url( '/encryption-contest/' ));?>"></td>                   
                  </tr>  
                  <tr>
                      <th><label for="rightSolutionPicture"><?php echo __('Link to picture displayed after right answer:', 'encrypt') ?></label></th>
                      <td><input type="url" name="EcSettings[rightSolutionPicture]" id="rightSolutionPicture" value="<?php echo ($settings['rightSolutionPicture']); ?>" size="75" placeholder="<?php echo (plugin_dir_url( __FILE__  ) .'smile.png'); ?>"></td>                   
                  </tr>
                  <tr>
                      <th><label for="titleTag"><?php echo __('HTML tag title for shortcode:', 'encrypt') ?></label></th>
                      <td><?php $classOptions->titleTag(); ?></td>                   
                  </tr>                            
                  <tr>
                      <th><label for="capabilityList"><?php echo __('Minimum capability for creating tasks:', 'encrypt') ?></label></th>
                      <td><?php $classOptions->capabilityList(); ?></td>                   
                  </tr>       
              </table>
              <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
              <input type="hidden" name="EcSettings[highest_task]" value="<?php echo $settings['highest_task'] ?>">
              <input type="hidden" name="EcSettings[published_task]" value="<?php echo $settings['published_task'] ?>">
              <br><br>              
          </form> 
          <form method="post" action="">
              <?php $classOptions->defaultButton(); ?>
          </form> 
      </div>
      <?php
  }

}

