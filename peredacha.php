<?php

include "header.php";
include "templates/peredacha.php";
if($user->user_info['user_points'] < $summ){ 
 header("Location: give.php");
echo "<b>Введено неверное количество голосов.</b>";
} 
else 
{ 
$database->database_query("UPDATE se_users SET user_points = user_points - $summ WHERE user_id = ".$user->user_info[user_id]."");
$database->database_query("UPDATE se_users SET user_points = user_points - 1 WHERE user_id = ".$user->user_info[user_id]."");
$database->database_query("UPDATE se_users SET user_points = user_points + $summ WHERE user_username='$user_username'");
}
 header("Location: give.php");
echo "<center>Вы передали пользователю <u>$user_username</u> передали <b>$summ</b>.</center>";

?>