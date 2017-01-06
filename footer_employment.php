<?php

switch($page) {

  // CODE FOR PROFILE PAGE
  case "profile":

  $rc_employment = new rc_employment($owner->user_info[user_id]);
  $employments = $rc_employment->get_employments();
	$total_employments = count($employments);

  
  foreach (explode(',',$header_employment[3]) as $letter) {
    $months[++$i] = $letter;
  }
  
  foreach ($employments as $k=>$employment) {
    $time_period = array();
    if ($employment['employment_from_month'] > 0) {
      $time_period[] = $months[$employment['employment_from_month']];
    }
    if ($employment['employment_from_year'] > 0) {
      $time_period[] = $employment['employment_from_year'];
    }
   
    if ($employment['employment_is_current'] || $employment['employment_to_month'] > 0 || $employment['employment_to_year'] > 0) {
      $time_period[] = $header_employment[10];
      
      if ($employment['employment_is_current']) {
        $time_period[] = $header_employment[9];
      }
      else {
        if ($employment['employment_to_month'] > 0) {
          $time_period[] = $months[$employment['employment_to_month']];
        }
        if ($employment['employment_to_year'] > 0) {
          $time_period[] = $employment['employment_to_year'];
        }
      }
    }
    
    $employments[$k]['time_period'] = trim(join(' ',$time_period));
  }


	$smarty->assign('employments', $employments);
	$smarty->assign('total_employments', $total_employments);
	break;
	    

}
