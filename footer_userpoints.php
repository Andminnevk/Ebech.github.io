<?

switch($page) {

  // CODE FOR SIGNUP VERIFICATION PAGE
  case "signup_verify":
    
    // AWARD ACTIVITY POINTS, IF EMAIL VERIFICATION IS SET
    if( ($is_error == 0) && ($task == "main") ) {

      // FIND REFERER - can make it all in one query, but then admin (userid=0) will get recorded as well, though it's ok
      $referer_user_id = semods::db_query_count( "SELECT user_referer FROM se_users WHERE user_id = " . $new_user->user_info['user_id'] );

      if( $referer_user_id  ) {
        userpoints_update_points( $referer_user_id, "refer" );  
      }

    }
    
    break;

  // Code for adding signup referrer points, Part II (Part I is in header_userpoints.php)
  case "signup":
    
    // entrance as step2do, exit !"step2" => user created
    if( ($userpoints_signup_step == "step2do") && ($task != "step2") && $referer && ($referer->user_exists == 1)) {

        // AWARD ACTIVITY POINTS, DEFER TO EMAIL VERIFICATION, IF SET. 
        if($setting['setting_signup_verify'] == 0) {
          userpoints_update_points( $referer->user_info['user_id'], "refer" );  
        }
      
    }
    
    break;
  
  
  // TBD: check what's with group media
  // AWARD POINTS FOR ALBUM UPLOAD
  case "user_album_upload":
	
	if($task == "doupload" && count($file_result)) {
	  userpoints_update_points( $user->user_info['user_id'], 'newmedia', count($file_result) );
	}
	
	break;
  
  
  

  // CODE FOR PROFILE PAGE
  case "profile":

	
    if($userpoints_enabled) {
    
      $points_all = userpoints_get_all( $owner->user_info['user_id'] );
      if($points_all) {
        $user_points = $points_all['userpoints_count'];
        $user_points_totalearned = $points_all['userpoints_totalearned'];
      } else {
        $user_points = 0;
        $user_points_totalearned = 0;
      }
    
      $userpoints_enable_topusers = semods::get_setting('userpoints_enable_topusers');
      if( $userpoints_enable_topusers != 0) {
        $user_rank = userpoints_get_rank($owner->user_info['user_id']);
        $smarty->assign('user_rank', $user_rank);
      }
      
      $smarty->assign('userpoints_enable_pointrank', semods::get_setting('userpoints_enable_pointrank') );
      $smarty->assign('userpoints_enable_topusers', $userpoints_enable_topusers);
      $smarty->assign('user_points', $user_points);
      $smarty->assign('user_points_totalearned', $user_points_totalearned);
    }
    
	break;



  // CODE FOR USER HOME PAGE
  case "user_home":

    if($userpoints_enabled) {
      
      $points_all = userpoints_get_all( $user->user_info['user_id'] );
      if($points_all) {
        $user_points = $points_all['userpoints_count'];
        $user_points_totalearned = $points_all['userpoints_totalearned'];
      } else {
        $user_points = 0;
        $user_points_totalearned = 0;
      }
  
      $userpoints_enable_topusers = semods::get_setting('userpoints_enable_topusers');
      if( $userpoints_enable_topusers != 0) {
        $user_rank = userpoints_get_rank($user->user_info['user_id']);
        $smarty->assign('user_rank', $user_rank);
      }
      
      $smarty->assign('userpoints_enable_pointrank', semods::get_setting('userpoints_enable_pointrank') );
      $smarty->assign('userpoints_enable_topusers', $userpoints_enable_topusers);
      $smarty->assign('user_points', $user_points);
      $smarty->assign('user_points_totalearned', $user_points_totalearned);
    }
    
	break;



  // CODE FOR HOME PAGE
  case "home":

    if($userpoints_enabled) {
      $userpoints_enable_topusers = semods::get_setting('userpoints_enable_topusers');

		if($userpoints_enable_topusers != 0) {
		
		  $sql = "SELECT P.userpoints_totalearned,
						 U.user_username, U.user_photo, U.user_id
				  FROM
					se_semods_userpoints P
					JOIN se_users U
					  ON P.userpoints_user_id = U.user_id
				  ORDER BY userpoints_totalearned DESC
				  LIMIT 5";
				  
		  $rows = $database->database_query( $sql );
		  $dummy_user = new se_user();
		  $dummy_user->user_exists = 0;
		  while($row = $database->database_fetch_assoc($rows)) {
			$dummy_user->user_info['user_id'] = $row['user_id'];
			$dummy_user->user_info['user_photo'] = $row['user_photo'];
			$row['user_photo'] = $dummy_user->user_photo( './images/nophoto.gif' );
			$up_topusers[] = $row;
		  }
		  //var_dump($up_top_users);exit;
		  
		  $smarty->assign('up_topusers', $up_topusers);
		
		}
      
      $smarty->assign('userpoints_enable_topusers', $userpoints_enable_topusers);
    }
    
	break;






  // CREATING CLASSIFIED PART 2/3 
  case "user_classified_new":
    if($userpoints_classified_abort) {
      $is_error = 1;
      $result = $functions_userpoints[1];
      $smarty->assign('result', $result);
      $smarty->assign('is_error', $is_error);

      // cache values
      foreach($_POST as $key => $value) {
        if(substr($key, 0, 11) == 'classified_') {
          $smarty->assign( $key, $value );
        }
      }
    }
    
    break;



  // CREATING EVENT PART 2/3 
  case "user_event_add":
    if($userpoints_event_abort) {
      $is_error = 1;
      $error_message = $functions_userpoints[2];
      $smarty->assign('error_message', $error_message);
      $smarty->assign('is_error', $is_error);
      
      // Thank you very much for "Initializing variables"!
      
      // cache values
      foreach($_POST as $key => $value) {
        if(substr($key, 0, 6) == 'event_') {
          $smarty->assign( $key, $value );
        }
      }

      // Ouch!
      $eventcat_id = $_POST['eventcat_id'];
      $subeventcat_id = $_POST['subeventcat_id'];
      if($_POST['event_date_start_hour'] == "12") { $_POST['event_date_start_hour'] = 0; }
      if($_POST['event_date_start_ampm'] == "PM") { $_POST['event_date_start_hour'] += 12; }
      $event_date_start = mktime($_POST['event_date_start_hour'], $_POST['event_date_start_minute'], 0, $_POST['event_date_start_month'], $_POST['event_date_start_day'], $_POST['event_date_start_year']);
      if($_POST['event_date_end_hour'] == "12") { $_POST['event_date_end_hour'] = 0; }
      if($_POST['event_date_end_ampm'] == "PM") { $_POST['event_date_end_hour'] += 12; }
      $event_date_end = mktime($_POST['event_date_end_hour'], $_POST['event_date_end_minute'], 0, $_POST['event_date_end_month'], $_POST['event_date_end_day'], $_POST['event_date_end_year']);

      $smarty->assign('eventcat_id', $eventcat_id);
      $smarty->assign('subeventcat_id', $subeventcat_id);
      $smarty->assign('event_date_start', $event_date_start);
      $smarty->assign('event_date_end', $event_date_end);
      
    }    
    
    break;



  // CREATING GROUP PART 2/3 
  case "user_group_add":
    if($userpoints_group_abort) {
      $is_error = 1;
      $error_message = $functions_userpoints[3];
      $smarty->assign('error_message', $error_message);
      $smarty->assign('is_error', $is_error);
      
      // Thank you very much for "Initializing variables"!
      
      // cache values
      foreach($_POST as $key => $value) {
        if(substr($key, 0, 6) == 'group_') {
          $smarty->assign( $key, $value );
        }
      }

      // Consistency, please!
      $groupcat_id = $_POST['groupcat_id'];
      $subgroupcat_id = $_POST['subgroupcat_id'];

      $smarty->assign('groupcat_id', $groupcat_id);
      $smarty->assign('subgroupcat_id', $subgroupcat_id);
    }    
    
    break;



  // CREATING POLL PART 2/3 
  case "user_poll_new":
    if($userpoints_poll_abort) {
      $is_error = 1;
      $result = $functions_userpoints[4];
      $smarty->assign('result', $result);
      $smarty->assign('is_error', $is_error);
      
      // cache values
      foreach($_POST as $key => $value) {
        if(substr($key, 0, 5) == 'poll_') {
          $smarty->assign( $key, $value );
        }
      }

    }    
    
    break;


}

?>