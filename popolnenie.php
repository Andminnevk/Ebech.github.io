<?
$page = "popolnenie";
include "header.php";
// CHECK FOR ADMIN ALLOWANCE OF MESSAGES
if($user->level_info[level_message_allow] == 0) { header("Location: user_home.php"); exit(); }
// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
include "footer.php";
?>
