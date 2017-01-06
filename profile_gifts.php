<?
$page = "gifts_profile";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 & $setting[setting_permission_profile] == 0) {
  $page = "error";
  $smarty->assign('error_header', $profile_comments[20]);
  $smarty->assign('error_message', $profile_comments[22]);
  $smarty->assign('error_submit', $profile_comments[23]);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
if($owner->user_exists == 0) {
  $page = "error";
  $smarty->assign('error_header', $profile_comments[20]);
  $smarty->assign('error_message', $profile_comments[21]);
  $smarty->assign('error_submit', $profile_comments[23]);
  include "footer.php";
}

// GET PRIVACY LEVEL
$privacy_level = $owner->user_privacy_max($user, $owner->level_info[level_profile_privacy]);
$allowed_privacy = true_privacy($owner->user_info[user_privacy_profile], $owner->level_info[level_profile_privacy]);
if($privacy_level < $allowed_privacy) { header("Location: ".$url->url_create('profile', $owner->user_info[user_username])); exit(); }


$gifts_per_page = 20;
$gifts = new se_gifts();
$where = "gifts_tuser_id='".$owner->user_info[user_id]."'";
$sort="";
$total_gifts=$gifts->gifts_user_total($where);
$page_vars = make_page($total_gifts, $gifts_per_page, $p);
// GET gifts ARRAY
$gifts = $gifts->gifts_user_list($page_vars[0], $gifts_per_page, $sort, $where);
$smarty->assign('total_gifts', $total_gifts);
$smarty->assign('gifts', $gifts);


// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('comments', $comments);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($comments));
include "footer.php";
?>