<?
error_reporting (E_ALL ^ E_NOTICE);


///////////////////////////////////////////////////////
if($_GET['act'] == "" or $_GET['act'] == "other"){

$page = "settings_real";
include "header.php";

$m = $_GET['m'];
$smarty->assign('m', $m);


$smarty->assign('pg', 1);

$page_title = "Дополнительные сервисы";
$smarty->assign('page_title', $page_title);



include "footer.php";
}
//////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////

elseif($_GET['act'] == "realed"){

$page = "settings_real";
include "header.php";


if(isset($_POST['real'])) { $real = $_POST['real']; } elseif(isset($_GET['real'])) { $real = $_GET['real']; } else { $real = "0"; }


if($real == 0){
header("Location: ./user_account_other.php?act=other&m=1"); exit(); 
}
else
{

if($user->user_info['user_points'] <= 9){ header("Location: ./settings.php?act=other&m=1"); exit(); }

if($user->user_info['user_points'] >= 10){

$database->database_query("UPDATE se_users SET user_points = user_points - 10 WHERE user_id = ".$user->user_info[user_id]."");

$database->database_query("UPDATE se_users SET user_real = ".$real." WHERE user_id = ".$user->user_info[user_id]."");

header("Location: ./user_account_other.php?act=other&m=2"); exit(); 
}

}




}
//////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////

elseif($_GET['act'] == "realdel"){

$page = "settings_real";
include "header.php";



$database->database_query("UPDATE se_users SET user_real = 0 WHERE user_id = ".$user->user_info[user_id]."");

header("Location: ./user_account_other.php?act=other&m=3"); exit(); 







}
///////////////////////////////////////////////////////




///////////////////////////////////////////////////////
if($_GET['act'] == "" or $_GET['act'] == "other"){

$page = "settings_vip";
include "header.php";

$m = $_GET['m'];
$smarty->assign('m', $m);


$smarty->assign('pg', 1);

$page_title = "Дополнительные сервисы";
$smarty->assign('page_title', $page_title);



include "footer.php";
}
//////////////////////////////////////////////////////////////








//////////////////////////////////////////////////////////////

elseif($_GET['act'] == "viped"){

$page = "settings_vip";
include "header.php";


if(isset($_POST['vip'])) { $vip = $_POST['vip']; } elseif(isset($_GET['vip'])) { $vip = $_GET['vip']; } else { $vip = "0"; }


if($vip == 0){
header("Location: ./user_account_other.php?act=other&m=1"); exit(); 
}
else
{

if($user->user_info['user_points'] <= 29){ header("Location: ./settings.php?act=other&m=1"); exit(); }

if($user->user_info['user_points'] >= 30){

$database->database_query("UPDATE se_users SET user_points = user_points - 20 WHERE user_id = ".$user->user_info[user_id]."");

$database->database_query("UPDATE se_users SET user_vip = ".$vip." WHERE user_id = ".$user->user_info[user_id]."");

header("Location: ./user_account_other.php?act=other&m=2"); exit(); 
}

}




}
//////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////

elseif($_GET['act'] == "vipdel"){

$page = "settings_vip";
include "header.php";



$database->database_query("UPDATE se_users SET user_vip = 0 WHERE user_id = ".$user->user_info[user_id]."");

header("Location: ./user_account_other.php?act=other&m=3"); exit(); 







}
///////////////////////////////////////////////////////























//////////////////////////////////////////////////////////////

elseif($_GET['act'] == "wanted"){

$page = "settings_want";
include "header.php";


if(isset($_POST['want'])) { $want = $_POST['want']; } elseif(isset($_GET['want'])) { $want = $_GET['want']; } else { $want = "0"; }


if($want == 0){
header("Location: ./user_account_other.php?act=other&m=1"); exit(); 
}
else
{

if($user->user_info['user_points'] <= 0){ header("Location: ./settings.php?act=other&m=1"); exit(); }

if($user->user_info['user_points'] >= 1){

$database->database_query("UPDATE se_users SET user_points = user_points - 1 WHERE user_id = ".$user->user_info[user_id]."");

$database->database_query("UPDATE se_users SET user_want = ".$want." WHERE user_id = ".$user->user_info[user_id]."");

header("Location: ./user_account_other.php?act=other&m=2"); exit(); 
}

}




}
//////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////

elseif($_GET['act'] == "wantdel"){

$page = "settings_want";
include "header.php";



$database->database_query("UPDATE se_users SET user_want = 0 WHERE user_id = ".$user->user_info[user_id]."");

header("Location: ./user_account_other.php?act=other&m=3"); exit(); 







}
///////////////////////////////////////////////////////

































?>