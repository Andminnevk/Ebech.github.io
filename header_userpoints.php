<?

// INCLUDE LANGUAGE FILE
include "./lang/lang_".$global_lang."_userpoints.php";

// INCLUDE FUNCTIONS FILE
include_once "./include/functions_userpoints.php";

// INCLUDE SEMODS CLASS FILE
include_once "./include/class_semods.php";

// INCLUDE CLASS FILE
include_once "./include/class_userpoints.php";
include_once "./include/class_semods_actionsex.php";



// hook actions
$actions = new se_actionsex();


$userpoints_enabled = semods::get_setting('userpoints_enable_activitypoints');
$smarty->assign('userpoints_enabled', $userpoints_enabled);


switch($page) {



  /* ACTION POINTS */
  
  
  
  // Code for adding signup referrer points, Part I (Part II is in footer_userpoints.php)
  case "signup":
    $userpoints_signup_step = semods::post('task', 'main');
    
    break;
  
  
  // ad click
  case "ad":
    $ad_id_ = semods::get('ad_id',0);
    if( ($user->user_exists !=0) && $ad_id_ ) {
      userpoints_update_points( $user->user_info['user_id'], "adclick" );  
    }
    
    break;




  /* PURCHASING */

  
  
  // CREATING CLASSIFIED PART 1/3 
  case "user_classified_new":
    
    if(semods::get_setting('userpoints_charge_postclassified')) {
        
      $userpoints_classified_step = semods::getpost('task', 'main');
      if(($userpoints_classified_step == 'dosave') || ($userpoints_classified_step == 'main')) {
        
        // check if has enough credits
        if(!userpoints_try_deduct_bytype($user->user_info['user_id'], 1)) {
          $_POST['task'] = 'main';
          $userpoints_classified_abort = true;
  
        /*
          // Done in footer, left for possible future compatibility?
          
          // cache values
          foreach($_POST as $key => $value) {
            if(substr($key, 0, 11) == 'classified_') {
              $$key = $value;
            }
          }
        */
        
        }
      }       
    }

    break;
  


  // CREATING EVENT PART 1/3 
  case "user_event_add":

    if(semods::get_setting('userpoints_charge_newevent')) {

      $userpoints_event_step = semods::getpost('task', 'main');

      if( ($userpoints_event_step == 'doadd') || ($userpoints_event_step == 'main') ) {

        // check if has enough credits
        if(!userpoints_try_deduct_bytype($user->user_info['user_id'], 2)) {
          $_POST['task'] = 'main';
          $userpoints_event_abort = true;
        }
        
      }
      
    }

    break;
  
  

  // CREATING GROUP PART 1/3 
  case "user_group_add":

    if(semods::get_setting('userpoints_charge_newgroup')) {

      $userpoints_group_step = semods::getpost('task', 'main');

      if( ($userpoints_group_step == 'doadd') || ($userpoints_group_step == 'main')) {
        // check if has enough credits
        if(!userpoints_try_deduct_bytype($user->user_info['user_id'], 3)) {
          $_POST['task'] = 'main';
          $userpoints_group_abort = true;
        }
        
      }
      
    }

    break;
  



  // CREATING POLL PART 1/3 
  case "user_poll_new":
    
    if(semods::get_setting('userpoints_charge_newpoll')) {
      
      $userpoints_group_step = semods::getpost('task', 'main');
      
      if( ($userpoints_group_step == 'doadd') || ($userpoints_group_step == 'main') ) {

        // check if has enough credits
        if(!userpoints_try_deduct_bytype($user->user_info['user_id'], 4)) {
          $_POST['task'] = 'main';
          $userpoints_poll_abort = true;
        }
        
      }
      
    }

    break;
  

  

}

?>