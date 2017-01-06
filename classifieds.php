<?
$page = "classifieds";
include "header.php";


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 & $setting[setting_permission_classified] == 0) {
  $page = "error";
  $smarty->assign('error_header', $classified[12]);
  $smarty->assign('error_message', $classified[13]);
  $smarty->assign('error_submit', $classified[14]);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO classified OWNER
if($owner->user_exists == 0) {
  $page = "error";
  $smarty->assign('error_header', $classified[12]);
  $smarty->assign('error_message', $classified[1]);
  $smarty->assign('error_submit', $classified[14]);
  include "footer.php";
}

// ENSURE classifiedS ARE ENABLED FOR THIS USER
if($owner->level_info[level_classified_allow] == 0) { header("Location: ".$url->url_create('profile', $owner->user_info[user_username])); exit(); }


if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }

// SET PRIVACY LEVEL AND WHERE CLAUSE
$privacy_level = $owner->user_privacy_max($user, $owner->level_info[level_classified_privacy]);
$min_privacy = true_privacy(0, $owner->level_info[level_classified_privacy]);
$where = "((classified_privacy>='$min_privacy' AND classified_privacy<='$privacy_level') OR (classified_privacy<'$min_privacy' AND '$min_privacy'<='$privacy_level'))";


// CREATE classified OBJECT
$entries_per_page = (int)$owner->level_info[level_classified_entries];
if($entries_per_page <= 0) { $entries_per_page = 10; }
$classified = new se_classified($owner->user_info[user_id]);


// GET TOTAL ENTRIES
$total_classifieds = $classified->classifieds_total($where);

// MAKE ENTRY PAGES
$page_vars = make_page($total_classifieds, $entries_per_page, $p);

// GET ENTRY ARRAY
$classifieds = $classified->classifieds_list($page_vars[0], $entries_per_page, "classified_date DESC", $where);



// ASSIGN VARIABLES AND DISPLAY classified PAGE
$smarty->assign('entries', $classifieds);
$smarty->assign('total_classifieds', $total_classifieds);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($classifieds));
include "footer.php";
?>