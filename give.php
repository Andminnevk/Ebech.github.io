<?
$page = "give";
include "header.php";

// CHECK FOR ADMIN ALLOWANCE OF MESSAGES
if($user->level_info[level_message_allow] == 0) { header("Location: user_home.php"); exit(); }

if($user->user_info['user_points'] < $summ){ 
 header("Location: give.php");
} 
else 
{ 
$database->database_query("UPDATE se_users SET user_points = user_points - $summ WHERE user_id = ".$user->user_info[user_id]."");
$database->database_query("UPDATE se_users SET user_points = user_points + $summ WHERE user_username='$user_username'");
}
 header("Location: give.php");

// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
include "footer.php";
?>
