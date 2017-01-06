<?php
$page = "profile_rate";
include "header.php";

$page_title = "Рейтинг";
$smarty->assign('page_title', $page_title);


include 'mods/_percentage.php';
$profile_percent = show_percent($owner->user_info[user_id]);
$myprofile_percent = show_percent($user->user_info[user_id]);
// END OF PERCENT  


$smarty->assign('p_percent', $profile_percent);  
$smarty->assign('my_percent', $myprofile_percent); 




$total_rate = count(0)+$profile_percent+$owner->user_info[user_rate]-1;
$database->database_query("UPDATE se_users SET total_rate = ".$total_rate."  WHERE user_id = ".$owner->user_info[user_id]."");
$smarty->assign('total_rate', $total_rate);  

include "footer.php";
?>
