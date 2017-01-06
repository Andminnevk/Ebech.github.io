<?php
/*
 * Apps v1.0
 *
 * Copyright (c) 2008 SocialEngineMods.Net
 *
 *****************************************/

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

// INCLUDE DATABASE INFORMATION
include "./include/database_config.php";

// INCLUDE CLASS/FUNCTION FILES
include "./include/class_database.php";
include "./include/class_user.php";
//include "./include/class_url.php";
//include "./include/class_misc.php";
//include "./include/class_actions.php";
include "./include/functions_general.php";
include "./include/functions_stats.php";

include './include/functions_semods_compat_jsonencode.php';
include './include/class_semods.php';


/* Expire now */

header("Cache-Control: no-cache");
header("Pragma: nocache");
header("Expire: 0");


// <Smarty hack> - need language but not smarty

class Smarty {
  function assign() {}
};

$smarty = new Smarty();

// </Smarty hack>


// INITIATE DATABASE CONNECTION
$database = new se_database($database_host, $database_username, $database_password, $database_name);

// GET SETTINGS
$setting = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_settings LIMIT 1"));

// ENSURE NO SQL INJECTIONS THROUGH POST OR GET ARRAYS
$_POST = security($_POST);
$_GET = security($_GET);

// SET GLOBAL DEFAULT LANGUAGE VAR
$global_lang = $setting['setting_lang_default'];

// CREATE USER OBJECT AND ATTEMPT TO LOG USER IN
$user = new se_user();
$user->user_checkCookies();

// CHECK IF USER IS LOGGED IN
if($user->user_exists != 0) {

  // SET TIMEZONE IF USER IS LOGGED IN
  $global_timezone = $user->user_info[user_timezone];

  // INCLUDE THIS USER'S LANGUAGE FILE IF ALLOWED
  if($setting['setting_lang_allow'] == 1) { $global_lang = $user->user_info['user_lang']; }

// USER IS NOT LOGGED IN
} else {

// defer exit until have language

}

// INCLUDE LANGUAGE FILE
//include "./lang/lang_".$global_lang."_ajaxsemods.php";








// Initialize response object
$response = array();

if($user->user_exists == 0) {

  $text = 'Please <a href="./login.php">login</a> first.';

  // empty response
  $response['status'] = 1;
  $response['msg'] = $text;

  echo json_encode($response);
  exit;
}

$task = semods::request('task', 'get');


/* GET FRIENDS LIST */

if($task == "get") {

  $where = '';

  $friends = $user->user_friend_list( 0,    // start
                                      500,  // limit
                                      0,    // direction (def)
                                      1,    // friend_status (def)
                                      'se_users.user_username ASC',  // sort by
                                      $where
                                      );

  $friends_list = array();

  foreach($friends as $friend) {
    $friends_list[] = array(  'i'   =>  $friend->user_info['user_id'],
                              't'  =>  $friend->user_info['user_username'] );
  }

  $friends_all['friends'] = $friends_list;

  echo json_encode($friends_all);

}




?>