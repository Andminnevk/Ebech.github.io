<?

switch($page) {

  // CODE FOR PROFILE PAGE
  case "profile":
	// CHECK IF OWNER IS ALLOWED TO HAVE A CLASSIFIED
	$listings = Array();
	$total_listings = 0;
	if($owner->level_info[level_classified_allow] != 0) {

	  // START CLASSIFIED
	  $classified = new se_classified($owner->user_info[user_id]);
	  $listings_per_page = 5;
	  $sort = "classified_date DESC";

	  // GET PRIVACY LEVEL AND SET WHERE
	  $privacy_level = $owner->user_privacy_max($user, $owner->level_info[level_classified_privacy]);
	  $where = "(classified_privacy<='$privacy_level')";

	  // GET TOTAL LISTINGS
	  $total_listings = $classified->classifieds_total($where);

	  // GET LISTING ARRAY
	  $listings = $classified->classifieds_list(0, $listings_per_page, $sort, $where);

	}

	// ASSIGN ENTRIES SMARY VARIABLE
	$smarty->assign('listings', $listings);
	$smarty->assign('total_listings', $total_listings);
	break;



}
?>