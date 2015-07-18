<?php
/*
    Plugin Name: Encryption contest
    Description: You can create list of tasks, what will be show to logged users in time what you set. So you can make for example 3 tasks for future 3 weeks. Every week will be displayed one task. Plugin decide if user answer is OK and if yes, it displays user avatar under task. On the end of task (expiration) plugin will send you email with solve users - You can publish it, add points to user accounts or do whatever else. 
    Author: Marek 'ShippeR' Vach
    License: GNU General Public License v2 or later
    License URI: http://www.gnu.org/licenses/gpl-2.0.html
    Version: 1.0
    */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
add_shortcode( 'encryption-contest', array('EncryptionContest', 'displayShortcode') );
add_action( 'plugins_loaded', array('EncryptionContest', 'loadMultilangualDomain') );
require_once ( 'ec-codes.php' );    // Class for translating right solution to code
require_once ( 'ec-options.php' );  // Class for generating options page in admin site
require_once ( 'ec-menu.php' );     // Class for generating page in main admin menu - place, where admin can create and manage tasks


class EncryptionContest {

  public function loadMultilangualDomain() {
      load_plugin_textdomain( 'encrypt', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
  }

  // Include default settings for plugin options and data for creating testing task number 1
  public function defaults() {
      $classMenu = new EcMenu();
      
      $current_time = current_time( 'Y-m-d');   
      $plus_seven_days = date('Y-m-d', strtotime($current_time. ' + 7 days'));           
    
      // Create Task 1 displayed in shortcode after plugin activation
      $data['from1'] = $current_time;
      $data['to1'] = $plus_seven_days;
      $data['right_solution1'] = 'Testing sentence for task one.';
      $data['use_code1'] = 9;
      update_option( 'EcData', $data ); 
      
      // Default settings for plugin option page
      $settings['showTaskList'] = 1;
      $settings['showCountdown'] = 1;
      $settings['showUsers'] = 1;
      $settings['sendEmail'] = 1;
      $settings['emailAdress'] = get_option('admin_email');
      $settings['pageLink'] = site_url( '/encryption-contest/' );
      $settings['rightSolutionPicture'] = plugin_dir_url( __FILE__  ) .'smile.png';
      $settings['capabilityList'] = 'activate_plugins';
      $settings['highest_task'] = 1;
      $settings['published_task'] = 1;
      update_option( 'EcSettings', $settings );
      
      // Answers on tasks are saved in user meta. When admin hit default button in options page, this meta will be deleted.
      $meta_type  = 'user';
      $user_id    = 0;
      $meta_key   = 'ec_user_answer';
      $meta_value = '';
      $delete_all = true;
      delete_metadata( $meta_type, $user_id, $meta_key, $meta_value, $delete_all );
        
      $meta_key   = 'ec_user_notes';
      delete_metadata( $meta_type, $user_id, $meta_key, $meta_value, $delete_all );        
  }
  
  // Check if plugin was been ever installed in wordpress. If it wasn't, default settings will be aplicated.
  public function ifSetDefaults() {
      $classEc = new EncryptionContest();
      if (get_option('EcData') === false && get_option('EcSettings') === false)
          $classEc->defaults();
  }
  
  // Allows use html tags in email
  public function setEmailToHtml() {
	   return 'text/html';
  }
  
  // When task will expirate, results will be send to defined email in plugin options.
  public function sendEmail($next_task_number) {
      $classEc = new EncryptionContest();  
      $classMenu = new EcMenu();
      $data = get_option('EcData');
      $settings = get_option('EcSettings');

      $ending_task_number = $settings['published_task'];
      $highest_task_number = $settings['highest_task'];
      $right_solution = $data['right_solution' . $ending_task_number];
      $link = get_bloginfo('siteurl') . '/wp-admin/admin.php?page=ec-menu';
      $next_task_date = $data['to' . $next_task_number];
      $next_task_date = $classMenu->transformDate($next_task_date, false);
      
      $results = $classEc->resultEmailList($ending_task_number);
      $correct_answer = $results[0];
      $wrong_answer = $results[1];
    
      $to = $settings['emailAdress'];
      $subject = __('Task ', 'encrypt').$ending_task_number.__(' results', 'encrypt');
      $headers = 'From: '.get_option( 'blogname' ).' - Encryption contest plugin <'.get_option( 'admin_email' ).'> "\r\n"';

      $body .= __('Dear user,<br>time for displaying task ', 'encrypt') .$ending_task_number. __(' has already ended. ', 'encrypt');
      if ( $ending_task_number < $next_task_number )
          $body .= __('From this moment it is displaying task ', 'encrypt') .$next_task_number. __('. It will be displaying to midnight ', 'encrypt').$next_task_date;
      else
          $body .= __('On website is not displaying any futher task from this moment. Task is not set or delay between tasks is longer than 1 day.', 'encrypt');
      $body .= '<br>'.__('Here are results of ended task ', 'encrypt').$ending_task_number.':';
      $body .= '<br><b>'.__('Correct answer', 'encrypt').'</b>'.$correct_answer;
      $body .= '<b>'.__('Wrong answer', 'encrypt').'</b>'.$wrong_answer;
      $body .= __('Correct answer was:', 'encrypt').' <b>'.$right_solution.'</b> <br>'.__('Answers can be accepted in ', 'encrypt').'<a href="'.$link.'"> '.__('administration interface', 'encrypt').'</a>.<br><br>';
      $body .= __('This email has been automaticaly generated by site ', 'encrypt') . get_bloginfo('siteurl');
      $body .= '<br>'.__('Have a nice day.', 'encrypt');       
      add_filter('wp_mail_content_type', array('EncryptionContest', 'setEmailToHtml'));
      if ( $settings['sendEmail'] && $ending_task_number )
          wp_mail( $to, $subject, $body, $headers );
      remove_filter('wp_mail_content_type', array('EncryptionContest', 'setEmailToHtml'));        
      return true;
  }
  
  // Generate list Right solution and Wrong solution for sending email function
  public function resultEmailList($current_task) {
      $classMenu = new EcMenu();
      $data = get_option('EcData');
      
      $list_users = $data['list_of_users_answers' . $current_task];
      $array_input = $classMenu->getStoredAnswer($current_task, $list_users);
      foreach ( $array_input as $user_data ) {
          $user_data = explode ( ';', $user_data );     
          if ( $user_data[0] == 'T' ) 
              $correct_answer .= '<li>'.$user_data[1].'</li>';
 
          if ( $user_data[0] == 'F' ) 
              $wrong_answer .= '<li>'.$user_data[1].' - '.$user_data[2].'</li>';  
      }
      $correct_answer = '<ul>'.$correct_answer.'</ul>';
      $wrong_answer = '<ul>'.$wrong_answer.'</ul>';
      return array ($correct_answer, $wrong_answer);
  }
  
  // When user visit page with shortcode, plugin check expirate dates for all tasks and compare, if tasks may be changed (Task 1 -> Task 2).
  // If task should be changed, plugin show Task 2 to user and send result email of Task 1.
  public function checkIfTaskIsUpToDate() {
      $classEc = new EncryptionContest();
      $classMenu = new EcMenu();
      $settings = get_option('EcSettings');
      $data = get_option('EcData');
      $current_time = current_time( 'Y-n-j');
      $current_time = strtotime($current_time); 
            
      for ($i = 0; $i <= $settings['highest_task']; $i++) {
          $from = $data['from'.$i];
          $from = strtotime($from);
          $to = $data['to'.$i];
          $to = strtotime($to);
          
          if ($current_time >= $from and $current_time <= $to)
              $published_task = $i;                               
      }
      if ($published_task == '')
          $published_task = 0;
          
      $saved_published_task = $settings['published_task'];
      if ($saved_published_task != $published_task && !isset($_GET['task_preview'])) {
          $classEc->sendEmail($published_task);
          $classMenu->updateSettings('published_task', $published_task, 'EcSettings');          
      }
      return $published_task;
  }
  
  // When user create task in admin menu page and hit preview button, post will contain task_preview number of task. Update mechanism for email
  // sending will not work and other users will normaly see actual task. When user in frontend choose task from list and hit Choose button,
  // number of current task will be changed by list and user will see wanted Task.
  public function checkForCurrentTask() {
      $classEc = new EncryptionContest();
      $current_task = $classEc->checkIfTaskIsUpToDate(); 
      
      if ( isset($_POST['task_list']) )
          $current_task = $_POST['task_list'];
      
      if ( isset($_GET['task_preview']) )
          $current_task =  $_GET['task_preview'];    
      return $current_task;
  }
  
  // Countdown for expirate of Task. It can be hidden by plugin options page
  public function getCountdown($current_task) {
      $classMenu = new EcMenu();
      $data = get_option('EcData');
      $expiration = $data['to'.$current_task];
      $expiration = $classMenu->plusOneDay($expiration);
      $expiration = explode('-', $expiration);
      $expiration = "$expiration[1]/$expiration[2]/$expiration[0]";     // MM-DD-YYYY
      
      require_once ( 'countdown.php' );
      $content .= '<div id="odpocet" data-konec="'.$expiration.'" data-hlaska="" data-zbyva="';
      $content .= __('Task will expirate in', 'encrypt');
      $content .= '">You must allow JavaScript for view countdown!</div>';
      $content .= '<script>odpocet(document.getElementById(\'odpocet\'));</script>';        
      return $content;
  }
  
  // It converts text before ec-codes.php will work with text
  public function editTextForOutput($text) {
      $text = htmlspecialchars($text);
      $text = mb_strtolower($text);
      $conversion_table = array ('ě'=>'e', 'š'=>'s', 'č'=>'c', 'ř'=>'r', 'ž'=>'z', 'ý'=>'y', 'á'=>'a', 'í'=>'i', 'é'=>'e', 
                              'ú'=>'u', 'ů'=>'u', 'ó'=>'o', 'ť'=>'t', 'ď'=>'d', 'ň'=>'n', '. '=>'.', ','=>' ', '!'=>'', '?' => '', '-' => '', );
      $text = strtr($text, $conversion_table);
      return $text;  
  }
  
  // When answer should be resulted, this function will delete enter from textarea.
  public function prepareTextForResult($text) {
      $classEc = new EncryptionContest();
      $text = $classEc->editTextForOutput($text);
      $text = str_replace( array("\r","\n", " ", "."), "", $text );
      return $text; 
  }
  
  // Function comunicate with ec-codes.php functions. 
  public function generateCode($current_task) {
      $classEc    = new EncryptionContest();
      $classMenu  = new EcMenu();
      $classCodes = new EcCodes();
      $data = get_option('EcData');
      
      $use_code = $data['use_code'.$current_task];
      $right_solution = $data['right_solution'.$current_task];
      $right_solution = $classEc->editTextForOutput($right_solution);     
      $name_of_functions = $classMenu->nameOfFunctionsForCodes();
      $count = count($name_of_functions);
      
      for ($i = 1; $i < $count; $i++) {
              if ($use_code == $i) {
                  $function_name = $name_of_functions[$i];                
                  if ( $function_name == 'ownTextCode' )
                      $right_solution = $data['own_code'.$current_task];                     
                  if ( $function_name == 'ownPicture' )
                      $right_solution = $data['picture_code'.$current_task];                     
                  $content = $classCodes->$function_name($right_solution);                
              }             
      }
      return $content;      
  }
  
  // CSS for textarea max width in frontend
  public function insertCssForMaxWidth() {
      $content = '<style type="text/css">
                    textarea {
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;
                        width: 100%;
                    }
                  </style>';  
      return $content;                  
  }
  
  // Notes are same by all tasks. It isn't changing any way.
  public function saveNotes() {
      if ( isset($_POST['user_notes']) ) {
          $user_id = get_current_user_id();          
          $user_notes = htmlspecialchars($_POST['user_notes']);
          
          update_user_meta( $user_id, 'ec_user_notes', $user_notes );
      }
      return true;  
  }
  
  // Function is called by evaluateUserAnswer. It convert text to systematic written string and save it to user_meta
  // User_meta looks like F0->Testing text;T1->Hello world;T2->Hello world etc
  // T is True, 1 is number of task, -> is character for separate text, Hello world is typed by user
  public function saveAnswer($status, $current_task) {
      $classMenu = new EcMenu();
      $data    = get_option('EcData');
      
      $user_answer = htmlspecialchars( $_POST['user_answer'] );  
      $user_id = get_current_user_id();
      $key     = 'ec_user_answer';
      
      $text_for_save = $status . $current_task . '->' . $user_answer; /*T5->Hello world*/
      $get_meta = get_user_meta( $user_id, $key, true );
      if ( $get_meta == '' ) {
          update_user_meta( $user_id, $key, 'F0->Testing text;' );    
          $get_meta = get_user_meta( $user_id, $key, true );
      }
      $meta_value = $get_meta . $text_for_save . ';';     
      update_user_meta( $user_id, $key, $meta_value );
      
      $old_content = $data['list_of_users_answers' . $current_task];
      $new_content = $old_content . ',' . $user_id;
      $classMenu->updateSettings('list_of_users_answers'.$current_task, $new_content, 'EcData');           
      return true;
  }
  
  // It compare, if right solution in admin menu page is same as user answer
  public function evaluateUserAnswer($current_task) {
      $classEc = new EncryptionContest();
      $data    = get_option('EcData');
      
      if ( isset($_POST['user_answer']) && $_POST['user_answer'] ) {         
          $user_answer = $classEc->prepareTextForResult($_POST['user_answer']);
          $right_solution = $classEc->prepareTextForResult($data['right_solution' . $current_task]);

          if ( $right_solution == $user_answer ) 
              $status = 'T';                          
          else
              $status = 'F';
            
          $classEc->saveAnswer($status, $current_task);
          return $status;         
      }       
  }
  
  // If user did not respond, will be shown textarea. If he responded, will be shown True or False string.
  public function showAnswerContent($current_task) {
      $classEc = new EncryptionContest();
      $classMenu = new EcMenu();
      $settings = get_option('EcSettings');
           
      $user_id = get_current_user_id();
      $array_input = $classMenu->getStoredDataById($current_task, $user_id);
      $status = $array_input[0];
      $user_answer = $array_input[2];      
      
      if ( $status == '' && $settings['published_task'] == $current_task )
          $content = '<textarea name="user_answer" id="user_answer" rows="4" placeholder="' . __('You can use all letters of english, commas and points.', 'encrypt') .'"></textarea>';          
      elseif ($settings['published_task'] != $current_task)
          $content = __('Time to do the task has expired. You can solve cypher only out of competition.', 'encrypt');      
      
      if ( $status == 'T' )
          $content = __('Your answer is absolutely right. Good job!', 'encrypt') .'<br><img src="'.$settings['rightSolutionPicture'].'">';
          
      if ( $status == 'F' ) {            
          $content = __('In shelled cipher is a mistake! Just outside the game try to find where is mistake. <br>Your answer:', 'encrypt') .'<b>' .$user_answer. '</b>
                     <br><br><small>';
          $content .= __('If your answer seems written properly, contact us via email.', 'encrypt') .'</small>';                                                       
      }
      return $content;    
  }
  
  // Only logged users can result tasks
  public function generateSubmitButton() {
      if ( is_user_logged_in() )
          $content .= '<p style="text-align: right"><input type="submit" class="button-primary" value="'. __('Submit', 'encrypt') .'" /></p>';
      else
          $content .= '<p style="text-align: right">'. __('Try to solve it,', 'encrypt') .'<a href="'.site_url( '/wp-login.php/' ).'">'. __('login!', 'encrypt') .'</a></p>';
      return $content;
  }

  // It generate profile avatars for frontend, if user already answered right 
  public function generateAvatars($current_task) {
      $classMenu = new EcMenu();
      $data = get_option('EcData');
      include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Included for function is plugin active
      
      $list_users = $data['list_of_users_answers' . $current_task];
      $array_input = $classMenu->getStoredAnswer($current_task, $list_users);
      
      $content .= '<div width="100%" align="center">';
      foreach ( $array_input as $user_data ) {
          $user_data = explode (';', $user_data);
          $user_info = get_user_by('id', $user_data[3]);    
          $status = $user_data[0];
      
          if ($status == 'T') {
              $link = '<a href="'.site_url( '/author/' ).$user_info->user_login.'?id='.$user_data[3] .'">';
              $content .= '<div style="width: 16%; float: left;">'; 
              
              if ( is_plugin_active( 'wp-user-avatar/wp-user-avatar.php' ) )
                  $content .=  $link.get_wp_user_avatar($user_data[3], 96).'</a>';
              else    
                  $content .=  $link.get_avatar($user_data[3], 96).'</a>';
                  
              $content .=  $link . $user_info->display_name .'</a><br/>';
              $content .= '</div>';
          }              
      }
      $content .= '</div><br style="clear:both;" />';
      return $content;
  }

  // Main function of plugin. Generate frontend
  public function displayShortcode( $atts ) {
      $classEc = new EncryptionContest();
      $classEc->ifSetDefaults();
      $classOptions = new EcOptions();
      $settings = get_option('EcSettings');
      $classMenu = new EcMenu();
      $classCodes = new EcCodes();      
      $current_task = $classEc->checkForCurrentTask(); 

      if ( isset($_GET['task_preview']) ) {
          $content .= '<p><b>';
          $content .= __('Preview of task number ', 'encrypt');
          $content .= $current_task.'</b></p>';
      }    
      
      if ( $current_task ) {
      
          if ( $settings['showTaskList'] ) { 
              $content .= '<p>';        
              $content .= __('List of published tasks. You can browse previous tasks and get inspirate.', 'encrypt');
              $content .= '</p><form method="post" action=""><p>';      
              $content .= $classMenu->taskList('frontend', $current_task);
              $content .= '</p></form>';
          }
 
          if ( $settings['showCountdown'] )
              $content .= '<p>'.$classEc->getCountdown($current_task).'</p>';
      
          
          $content .= '<h6>' . __('Assignment', 'encrypt') . '</h6>'; 
          $content .= $classEc->generateCode($current_task);
          $content .= '<br><br>';
            
          $content .= $classEc->insertCssForMaxWidth(); 
          $content .= '<form method="post" action="">';
      
          $classEc->evaluateUserAnswer($current_task);
          $content .= '<h6><label for="user_answer">' . __('Decoded result', 'encrypt') . '</label></h6>';
          $content .= '<p>'. $classEc->showAnswerContent($current_task) .'</p>';
      
          $classEc->saveNotes();
          $content .= '<h6><label for="user_notes">' . __( 'Place for notes', 'encrypt') . '</label></h6>';
          $content .= '<textarea name="user_notes" id="user_notes" rows="3">'.get_user_meta( get_current_user_id(), 'ec_user_notes', true ).'</textarea>';
      
          $content .= $classEc->generateSubmitButton();
          $content .= '</form>';
      
          if ( $settings['showUsers'] ) {
              $content .= '<h6>' . __('Successfully resolved', 'encrypt') . '</h6>';
              $content .= $classEc->generateAvatars($current_task);
          }
      }
      else
          $content .= __('Sorry, currently there is no cyphre for answering.', 'encrypt');
      
      return $content;
  }



}