<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if ( is_admin() ){  
    add_action( 'admin_menu', array('EcMenu','addMenuPage' ));
    add_action( 'admin_init', array('EcMenu','registerSettings' )); 
    register_activation_hook( __FILE__, 'AddDefault' ); 
}

class EcMenu {
  public $actual_task;
 
  public function addMenuPage() {
      $classMenu  = new EcMenu();
      $settings   = get_option('EcSettings');     
      $page_title = 'Encryption contest';
      $menu_title = __('Encryption contest', 'encrypt');
      $capability = $settings['capabilityList'];
      $menu_slug  = 'ec-menu';
      $function   = array('EcMenu', 'pageContent');
      $icon_url   = plugin_dir_url( __FILE__ ) . 'encryption-icon20x20.png';
      add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url );
  } 
 
  public function registerSettings() {
      register_setting( 'EcMenu', 'EcData' );
  }  
  
  // Universal update settings function. It's good because it preserves all others data in settings
  public function updateSettings($option_name, $new_content, $option_type) {  // updateSettings('user1', 'answer', 'EcData'); 
      if ( !isset ($option_type) )
          $option_type = 'EcData';     
      $my_options = get_option($option_type);
      $my_options[$option_name] = $new_content;
      update_option( $option_type, $my_options );    
  } 
  
  // It converts date to user friently style
  public function transformDate($year_month_day, $with_year) { 
      $numbers = explode('-', $year_month_day);
      $day     = $numbers[2];
      $month   = $numbers[1];
      $year    = $numbers[0];
      
      if ($with_year == true)
          $date = "$day.$month.$year";
      else
          $date = "$day.$month.";
      return $date;  
  } 
  
  // User friendly style of from to used in drop-down list of tasks
  public function dateFromTo($task_number) {
      $classMenu = new EcMenu();
      $data = get_option('EcData');
      $from = $data['from' . $task_number];
      $from = $classMenu->transformDate($from, 0);      
      $to   = $data['to' . $task_number];
      $to   = $classMenu->transformDate($to, 1);
      $date = "($from - $to)";
      return $date;    
  } 
  
  // Generate drop-down list. Values are $actual_task or $current_task. It depends if it is displayed in front-end or admin menu page.
  public function taskList($called_from, $actual_task) {  //$called_from = menu/frontend
      $classMenu      = new EcMenu();
      $settings       = get_option('EcSettings');     
      $highest_task   = $settings['highest_task'];
      $published_task = $settings['published_task'];
      if ($called_from == 'menu')
          $list_highest = $highest_task;
      else
          $list_highest = $published_task;                  
      
      $content .= '<select name="task_list">';
      for ($i = $list_highest; $i > 0; $i--) {
          $content .= '<option value="'.$i.'"';
          if ( $actual_task == $i ) 
              $content .= 'selected="selected"'; 
          $content .= '>'. __('Task ', 'encrypt') .$i.' '.$classMenu->dateFromTo($i).'</option>';      
      }
      $content .= '</select>';
      $content .= ' <input type="submit" class="button-primary" value="'. __('Choose', 'encrypt') .'" name="choose_task" />';
      if ($called_from == 'menu')
          echo ($content);
      else
          return $content;
  }
  
  // When admin is managing tasks, it stores $actual_task id from previous page
  public function checkForActualTask() {
      $settings     = get_option('EcSettings');     
      $highest_task = $settings['highest_task'];
      $actual_task  = 0;  
      if (isset ($_POST['create_task']))
          $actual_task = $highest_task + 1;
      
      if (isset($_POST['choose_task']))
          $actual_task = $_POST['task_list']; 
          
      if (isset($_POST['actual_task']))
          $actual_task = $_POST['actual_task'];                  
      return $actual_task;  
  }
  
  // It gets expiration date from previous task, add one day and print to user like date, what should he fill in form
  public function recomentDate($actual_task) {
      $classMenu  = new EcMenu();
      $data       = get_option('EcData');
      $day_before = $actual_task - 1;
      if ($day_before > 0) {
          $recoment_date = $classMenu->plusOneDay($data['to' . $day_before]);
          $recoment_date = $classMenu->transformDate($recoment_date, false);
          $text = __('The recommended start is in', 'encrypt') .' '.$recoment_date;
          return $text;      
      }
      else
          return false;      
  }
  
  public function plusOneDay($year_month_day) {
      return date('Y-m-d', strtotime($year_month_day. ' + 1 days'));    
  }
  
  // Depends on the nameOfFunctionsForCodes(). When user in admin page choose from list Own text code, plugin will work with function ownTextCode() in ec-codes.php
  public function nameOfCodes() {
      $name_of_codes = array( 'Free space', __('Own text code', 'encrypt'), __('Own picture code', 'encrypt'), __('Move letter about X in alphabet', 'encrypt'), __('Text backwards with random gaps', 'encrypt'), __('Every second letter', 'encrypt'), 
                      __('Substitution behind the numbers <small>Z = 26</small>', 'encrypt'), __('Snail from center', 'encrypt'), __('Great poland cross', 'encrypt'), __('Morse code', 'encrypt'), __('Reverse Morse code', 'encrypt'));
      return $name_of_codes;
  }
  
  public function nameOfFunctionsForCodes() {
      $name_of_functions = array( 'Free space', 'ownTextCode', 'ownPicture', 'moveLetter', 'textBackwards', 'everySecond', 
                              'substitution', 'snailFromCenter', 'polandCross', 'toMorse', 'toReversedMorse' );
      return $name_of_functions;
  }
  
  // It generates list of functions, what user can use for translate of right solution to code displayed in frontend
  public function useCode($actual_task) {
      $classMenu = new EcMenu();
      $data = get_option('EcData');
      $name_of_codes = $classMenu->nameOfCodes(); 
      $array_count = count($name_of_codes);                 
      for ($i=1; $i < $array_count; $i++) {
          echo ('<input name="use_code" type="radio" value="'.$i.'" ');
          if ($i == 1)
              echo ('id="1" ');         
          echo (checked($i, $data['use_code' . $actual_task]));
          echo ('/> ' . $name_of_codes[$i] . '<br>');      
      }  
  }
  
  // It generates textarea with Choose image button
  public function pictureCode($actual_task) {                                           // po načtení stránky udělat podmínku, jestli má textarea hodnotu a jestli ano, tak oddělat img src
      $data = get_option('EcData');      
      $args = array("textarea_name" => "picture_code", 
                    "textarea_rows" => "3",
                          "wpautop" => false,
                 "drag_drop_upload" => true, );
			wp_editor( $data['picture_code' . $actual_task], "picture_code", $args );      
  }
  
  // Submit button is for saving. Preview button is for displaying Task in new window. Admin can see how task will look in fucute. It will not affect on current task
  public function submitAndPreviewButton($actual_task) {
      // Prepare URL and javascript function for preview code on new webbrowser page
      $settings = get_option('EcSettings'); 
      $esc_url  = $settings['pageLink'];
      $esc_url .= '?task_preview=' .$actual_task;
      ?> 
      <script>
          function preview() {
          window.open("<?php echo $esc_url ?>");
          }
      </script>           
      <!-- Here is button for Save changes. Second button is for preview - after send there is function what send user to frond-end. -->             
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      <input type="button" class="button-primary" onclick="preview()" value="<?php echo __('Preview', 'encrypt') ?>" />
      <br><i><?php echo __('At first Save, after that Preview.', 'encrypt') ?></i>
      <br><br>                            
      <?php
  }
  
  // All fields are caled by $field array and $actual_task. It provides a unique name for all fields (use_code1, $own_code2).
  // All this fields are saved in optio mechanism of wordpress. So I musn't create own SQL table. Data are saved in own option mechanism called EcSettings
  public function saveFields() {
      $classMenu    = new EcMenu();      
      $settings     = get_option('EcSettings');     
      $highest_task = $settings['highest_task'];      
      $fields       = array('from', 'to', 'right_solution', 'use_code', 'picture_code', 'own_code', 'move_letter', 'list_of_users_answers');
      $actual_task  = $_POST['actual_task'];    
      for ( $i = 0; $i < 7; $i++) {
          $field = $fields[$i];
          if ( isset($_POST[$field]) ) {
              $field_name = $field . $actual_task;
              $classMenu->updateSettings($field_name, htmlspecialchars($_POST[$field]), 'EcData');
          }
      }
      $classMenu->updateSettings('actual_task', $actual_task, 'EcSettings');
      if ($actual_task > $highest_task)
          $classMenu->updateSettings('highest_task', $actual_task, 'EcSettings');
  }
  
  // When user answer on task, his id will be add to $list_users. This function gets all users meta_data (there is saved information if is True or False and answered text) and make array from it.
  public function getStoredAnswer($actual_task, $list_users) {
      $list_users = explode ( ',', $list_users );
      $count_users = count ($list_users);
      $array_output = array();
      
      for ( $i = 0; $i <= $count_users; $i++ ) {
          $user_info = get_user_by('id', $list_users[$i]);
          $all_answers = $user_info->ec_user_answer;
          $all_answers = explode ( ';', $all_answers );
          
          $count_answers = count ($all_answers);
          for ( $y = 0; $y <= $count_answers; $y++ ) {
          
              if ( mb_strpos($all_answers[$y], $actual_task) !== false ) {
                  $without_task_number = str_replace($actual_task, '', $all_answers[$y] );
                  $status = $without_task_number[0];
                  $user_answer = mb_substr($without_task_number, 3);
                  $nickname = $user_info->nickname;
                  $user_id = $user_info->ID;
                
                  $array_output[] .= "$status;$nickname;$user_answer;$user_id";                
            }          
          }            
      } 
      return $array_output;
  }
  
  // Similar like getStoredAnswer(), but it returns data of only one user
  public function getStoredDataById($actual_task, $user_id) {
      $classMenu = new EcMenu();
      $data = get_option('EcData');
      $list_users = $data['list_of_users_answers' . $actual_task];
      $array_input = $classMenu->getStoredAnswer($actual_task, $list_users);
            
      foreach ( $array_input as $user_data ) {
          $user_data = explode (';', $user_data);
          
          if ( $user_id == $user_data[3] )
              $array_output = array ( $user_data[0], $user_data[1], $user_data[2], $user_data[3] );
              
      } 
      return $array_output;      
  }
  
  // Non stop updated list of users, what answered on task. Admin can see user answered text and if is false, he can confirm it.
  public function listOfAnswersTable($actual_task, $list_users) {
      $classMenu = new EcMenu();
      $array_input = $classMenu->getStoredAnswer($actual_task, $list_users);
  
      foreach ($array_input as $input) {
          $input    = explode( ';', $input );
          $status   = $input[0];
          $nickname = $input[1];
          $answer   = $input[2];
          $user_id  = $input[3];
          
          if ($status == 'F') {
              $button = '<button type="submit" class="button-secondary" value="'.$user_id.';'.$actual_task.'" name="acept_answer">'. __('Acept answer', 'encrypt') .'</button>';
              $status = __('False', 'encrypt');
          }         
          if ( $status == 'T' )
              $status = __('True', 'encrypt');
          
          $content .= '<tr><td>'.$status.'</td><td>'.$nickname.'</td><td>'.$answer.'</td><td>'.$button.'</td></tr>';
          unset ($button);     
      }
      return $content;
  }
  
 // If admin hit confirm button, user answer will be updater from F to T  
  public function aceptAnswer() {
      if ( isset($_POST['acept_answer']) && $_POST['acept_answer'] ) {
          $acept_answer = explode (';', $_POST['acept_answer']);
          $user_id = $acept_answer[0];
          $actual_task = $acept_answer[1];
      
          $all_meta = get_user_meta($user_id, 'ec_user_answer', true);
          $all_meta = explode (';', $all_meta);
          $count = count ($all_meta);
          for ( $i = 0; $i < $count; $i++ ) {
              $answer = $all_meta[$i];
              
              if ( mb_strpos($answer, $actual_task) !== false ) {
                  $answer[0] = 'T';
                  $edited_answer = $answer;
                  $edited_index = $i;             
              }
          }
          $all_meta[$edited_index] = $edited_answer;
          $all_meta = implode (';', $all_meta);
          update_user_meta( $user_id, 'ec_user_answer', $all_meta );
          
          $_POST['actual_task'] = $actual_task;     
      }  
  }

  // Main function of ec-menu.php
  public function pageContent() {
      $classEc = new EncryptionContest();
      $classEc->ifSetDefaults();
      $classMenu = new EcMenu();
      if ($_POST['actual_task'] > 0)
          $classMenu->saveFields();
      $classMenu->aceptAnswer();                 
      $actual_task = $classMenu->checkForActualTask();
      $data = get_option('EcData');
      $settings = get_option('EcSettings');
      $use_code = $data['use_code' . $actual_task];
      ?>
      <div class="wrap">
          <h2><?php echo __('Encryption contest', 'encrypt') ?></h2>
          <form method="post" action="">
              <p><input type="submit" class="button-primary" value="<?php echo __('Create new task', 'encrypt') ?>" name="create_task"/></p>
              <p><?php echo __('or edit already existing task', 'encrypt') ?></p>
              <p><?php $classMenu->taskList('menu', $actual_task); ?></p>          
          </form>
          <br>
      <?php if ( $actual_task > 0 ) {?>
          <h2><?php echo __('Task ', 'encrypt') ?><?php echo ($actual_task); ?></h2>
          <p><?php echo __('Task would continue one after another.', 'encrypt') . ' ' ?><?php echo ($classMenu->recomentDate($actual_task)); ?></p>
          <form method="post" action="">
          <table class="form-table">
              <tr>
                  <!-- Form field for insert date, when task may show to user. It's using HTML5 and day have form YYYY-MM-DD --> 
                  <th><label for="from"><?php echo __('Validity time:', 'encrypt') ?></label></th>
                  <td><input type="date" name="from" id="from" value="<?php echo $data['from' . $actual_task] ?>"> -
                  <input type="date" name="to" id="to" value="<?php echo $data['to' . $actual_task] ?>"></td>
              </tr>   
              <tr>
                  <!-- Form field for insert outcome of code. From here front-end take text for comparison with user text --> 
                  <th><label for="right_solution"><?php echo __('Right solution:', 'encrypt') ?></label></th>
                  <td><textarea name="right_solution" id="right_solution" rows="5" cols="80" type='textarea' placeholder="<?php echo __('You can use all letters of english, commas and points.', 'encrypt') ?>"><?php echo $data['right_solution' . $actual_task]; ?></textarea></td>  
              </tr>   
              <tr>
                  <!-- List of codes what user can use for his front-end user. Here user choose number of code, what he wants use. In kamzici-ukoly.php is if what works with functions in ukoly-codes.php. There is place, where text is translating to code. -->
                  <th><label for="1"><?php echo __('Use code:', 'encrypt') ?></label></th>
                  <td><?php $classMenu->useCode($actual_task); ?><i><small><?php echo __('After save changes will be displayed field for text code / picture code / move letter by X.', 'encrypt') ?></small></i></td>   
              </tr>
              <?php if ( $use_code == 1 ) {?> 
              <tr>
                  <!-- Place for own code in text form. Standard textarea... -->
                  <th><label for="own_code"><?php echo __('Own text code:', 'encrypt') ?></label></th>
                  <td><textarea name="own_code" id="own_code" rows="4" cols="80" type='textarea'><?php echo $data['own_code' . $actual_task]; ?></textarea></td>      
              </tr>
              <?php } 
                    if ( $use_code == 2 ) {?>                 
              <tr>
                  <!-- Often it is hidden place, where user can after show insert URL of picture. It uses wordpress editor with Gallery upload button.  -->
                  <th><label for="picture_code"><?php echo __('Link to picture:', 'encrypt') ?></label></th>
			            <td><?php $classMenu->pictureCode($actual_task); ?></td>       
              </tr> 
              <?php }
                    if ( $use_code == 3 ) {?> 
              <tr>
                  <th><label for="move_letter"><?php echo __('Move for:', 'encrypt') ?></label></th>
                  <td><input type="number" name="move_letter" value="<?php echo $data['move_letter' . $actual_task]; ?>" id="move_letter" min="-5" max="5"> <label for="move_letter"><?php echo __('letters', 'encrypt') ?></label></td>                  
              </tr>
              <?php } ?>   
          </table>
          <input type="hidden" name="actual_task" value="<?php echo ($actual_task);?>">
          <?php  $classMenu->submitAndPreviewButton($actual_task); ?>
          </form>
          <h2><?php echo __('List of answers', 'encrypt') ?></h2>
          <p><?php echo __('Results are automatically sent to email', 'encrypt') ?> <?php echo ($settings['emailAdress']) ?> <?php echo __('entered in settings.', 'encrypt') ?></p>
          <form method="post" action="">
          <table style="width:60%">
          <tr><td><b><?php echo __('Status', 'encrypt') ?></b></td><td><b><?php echo __('Nickname', 'encrypt') ?></b></td><td><b><?php echo __('Answered text', 'encrypt') ?></b></td><td><b><?php echo __('Button', 'encrypt') ?></b></td></tr>
          <?php 
          echo ($classMenu->listOfAnswersTable($actual_task, $data['list_of_users_answers' . $actual_task]));                                                  
          ?>
          </table>
          </form>
      <?php } //if $actual_task > 0 ?>
      </div>
      <?php
  }

  

  


}

