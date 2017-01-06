<?
    
$page = "admin";
include "header.php";
$smarty->assign('pg', 1);
    
    $act = $_GET['act'];
    $smarty->assign('do', $do);
    //////////////////////////////
    // Панель администратора
    //////////////////////////////
    if($act == "" or $act == "main")
    {
    $act_title = "Админ Панель";

    $do = $_GET['do'];
    }

    else if($act == 'golos')
    {
        $database->database_query("UPDATE se_users SET user_points=user_points+5 WHERE user_id='".$owner->user_info[user_id]."'");
        header("Location: ./profile.php?user=".$owner->user_info['user_username']."");
        exit();
    }
    else if($act == 'ban')
    {
        $database->database_query("UPDATE se_users SET user_banned=user_banned +1 WHERE user_id='".$owner->user_info[user_id]."'");
        $database->database_query("UPDATE se_users SET user_level_id=user_level_id +2 WHERE user_id='".$owner->user_info[user_id]."'");
        header("Location: ./profile.php?user=".$owner->user_info['user_username']."");
        exit();
    }
      else if($act == 'unban')
    {
        $database->database_query("UPDATE se_users SET user_banned=user_banned -1 WHERE user_id='".$owner->user_info[user_id]."'");
         $database->database_query("UPDATE se_users SET user_level_id=user_level_id -2 WHERE user_id='".$owner->user_info[user_id]."'");
        header("Location: ./profile.php?user=".$owner->user_info['user_username']."");
        exit();
    }
    else if($act == 'rate20')
    {
        $database->database_query("UPDATE se_users SET user_rate=user_rate +20 WHERE user_id='".$owner->user_info[user_id]."'");
        header("Location: ./profile.php?user=".$owner->user_info['user_username']."");

        exit();
    }
    else if($act == 'rate20m')
    {
        $database->database_query("UPDATE se_users SET user_rate=user_rate -20 WHERE user_id='".$owner->user_info[user_id]."'");
        header("Location: ./profile.php?user=".$owner->user_info['user_username']."");

        exit();
    }

    else if($act == 'real')
    {
        $database->database_query("UPDATE se_users SET user_real=user_real +1 WHERE user_id='".$owner->user_info[user_id]."'");
        header("Location: ./profile.php?user=".$owner->user_info['user_username']."");
 
        exit();
    }
    else if($act == 'realDelete')
    {
        $database->database_query("UPDATE se_users SET user_real=user_real -1 WHERE user_id='".$owner->user_info[user_id]."'");
        header("Location: ./profile.php?user=".$owner->user_info['user_username']."");
        exit();
    }
    else if($act == 'warn_add')
    {
        $database->database_query("UPDATE se_users SET user_table=user_table +1 WHERE user_id='".$owner->user_info[user_id]."'");
        header("Location: ./profile.php?user=".$owner->user_info['user_username']."");

        exit();
    }
    else if($act == 'warn_null')
    {
        $database->database_query("UPDATE se_users SET user_table=user_table -1 WHERE user_id='".$owner->user_info[user_id]."'");

        header("Location: ./profile.php?user=".$owner->user_info['user_username']."");
        exit();
    }
    //тут уже твои запросы

    $smarty->assign('act_title', $act_title);
    include "footer.php";

?>