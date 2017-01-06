<?php

switch($page) {

  // CODE FOR PROFILE PAGE
  case "profile":

  $rc_education = new rc_education($owner->user_info[user_id]);
  $educations = $rc_education->get_educations();
	$total_educations = count($educations);

	$smarty->assign('educations', $educations);
	$smarty->assign('total_educations', $total_educations);
	break;

}
