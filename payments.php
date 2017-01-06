<? error_reporting (E_ALL ^ E_NOTICE);

include "header.php";



///////////////////////////////////////////////////////////////
if($_GET['act'] == "" or $_GET['act'] == "procent"){
$page = "payments_procent";
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['cup'])) { $cup = $_POST['cup']; } elseif(isset($_GET['cup'])) { $cup = $_GET['cup']; } else { $cup = "main"; }
// DISPLAY ERROR PAGE IF NO OWNER
if($user->user_exists == 0 & $setting[setting_permission_profile] == 0) {
  $page = "error";
  $smarty->assign('error_header', 'Ошибка');
  $smarty->assign('error_message', 'Для просмотра этой страницы вам нужно авторизироваться!');
  $smarty->assign('error_submit', 'Назад');
}
if($user->user_info['user_points'] <= 1){ header("Location: ./rate.php?user=".$owner->user_info[user_username].""); exit(); }
if($task == "procent") {
$database->database_query("UPDATE se_users SET user_points = user_points - 1 WHERE user_id = ".$user->user_info[user_id]."");
$database->database_query("UPDATE se_users SET user_rate  = user_rate + 1 WHERE user_id = ".$owner->user_info[user_id]."");




header("Location: ./rate.php?user=".$owner->user_info[user_username].""); exit();
}

$smarty->assign('randoms', $random_array);
}
///////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////
elseif($_GET['act'] == "obmen"){
$page = "payments_obmen";
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['cup'])) { $cup = $_POST['cup']; } elseif(isset($_GET['cup'])) { $cup = $_GET['cup']; } else { $cup = "main"; }
// DISPLAY ERROR PAGE IF NO OWNER
if($user->user_exists == 0 & $setting[setting_permission_profile] == 0) {
  $page = "error";
  $smarty->assign('error_header', 'Ошибка');
  $smarty->assign('error_message', 'Для просмотра этой страницы вам нужно авторизироваться!');
  $smarty->assign('error_submit', 'Назад');
}
if($user->user_info['user_rate'] <= 1){ header("Location: ./rate.php?user=".$user->user_info[user_username].""); exit(); }
if($task == "obmen") {
$database->database_query("UPDATE se_users SET user_points = user_points +1 WHERE user_id = ".$user->user_info[user_id]."");
$database->database_query("UPDATE se_users SET user_rate  = user_rate - 1 WHERE user_id = ".$user->user_info[user_id]."");
header("Location: ./rate.php?user=".$user->user_info[user_username].""); exit();
}
$smarty->assign('randoms', $random_array);
}
///////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////
elseif($_GET['act'] == "lider"){
$page = "payments_lider";
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['cup'])) { $cup = $_POST['cup']; } elseif(isset($_GET['cup'])) { $cup = $_GET['cup']; } else { $cup = "main"; }
// DISPLAY ERROR PAGE IF NO OWNER
if($user->user_exists == 0 & $setting[setting_permission_profile] == 0) {
  $page = "error";
  $smarty->assign('error_header', 'Ошибка');
  $smarty->assign('error_message', 'Для просмотра этой страницы вам нужно авторизироваться!');
  $smarty->assign('error_submit', 'Назад');
}
if($user->user_info['user_points'] <= 19){ header("Location: ./user_home.php"); exit(); }
if($task == "lider") {
$database->database_query("UPDATE se_users SET user_points = user_points - 20 WHERE user_id = " . $user->user_info[user_id] . "");
$database->database_query("INSERT se_liders (user_id,dtime) VALUES ('" . $user->user_info[user_id] . "',NOW()) ");
header("Location: ./user_home.php"); exit();
}
$smarty->assign('randoms', $random_array);
}
///////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////
elseif($_GET['act'] == "topphoto"){
$page = "my_balans";
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['cup'])) { $cup = $_POST['cup']; } elseif(isset($_GET['cup'])) { $cup = $_GET['cup']; } else { $cup = "main"; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['cup'])) { $cup = $_POST['cup']; } elseif(isset($_GET['cup'])) { $cup = $_GET['cup']; } else { $cup = "main"; }

// DISPLAY ERROR PAGE IF NO OWNER
if($user->user_exists == 0 & $setting[setting_permission_profile] == 0) {
  $page = "error";
  $smarty->assign('error_header', 'Ошибка');
  $smarty->assign('error_message', 'Для просмотра этой страницы вам нужно авторизироваться!');
  $smarty->assign('error_submit', 'Назад');
  include "footer.php";
}

if($user->user_info['user_points'] <= 4){ header("Location: ./profile.php?user=" . $user->user_info[user_username] . ""); exit(); }

if($task == "topphoto") {
$database->database_query("UPDATE se_users SET user_points = user_points - 40 WHERE user_id = " . $user->user_info[user_id] . "");
$database->database_query("INSERT se_topers (user_id,dtime) VALUES ('" . $user->user_info[user_id] . "',NOW()) ");
header("Location: ./user_home.php"); exit();
}


$smarty->assign('randoms', $random_array);
}
///////////////////////////////////////////////////////////////








include("footer.php");

?>