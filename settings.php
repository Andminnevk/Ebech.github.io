<?
error_reporting (E_ALL ^ E_NOTICE);


///////////////////////////////////////////////////////
if($_GET['act'] == "" or $_GET['act'] == "main"){

$page = "user_account";
include "header.php";


$page_title = "Мои Настройки";
$smarty->assign('page_title', $page_title);


if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

if(isset($_POST['changeServices'])) { $changeServices = $_POST['changeServices']; } elseif(isset($_GET['changeServices'])) { $changeServices = $_GET['changeServices']; } else { $changeServices = "main"; }


// SET RESULT VARIABLES
$result = "";
$is_error = 0;
$error_message = "";

// SAVE ACCOUNT SETTINGS
if($task == "dosave") {
  $user_email = $_POST['user_email'];
  $user_username = $_POST['user_username'];
  $name = $_POST['user_name'];
  $name = $_POST['user_lastname'];
  $user_timezone = $_POST['user_timezone'];

  // VALIDATE ACCOUNT INFO
  $user->user_account($user_email, $user_username, $user_name, $user_lastname);
  $is_error = $user->is_error;
  $error_message = $user->error_message;

  // GET BLOCKED LIST
  $num_blocked = $_POST['num_blocked'];
  if($user->level_info[level_profile_block] != 0 & is_numeric($num_blocked) === TRUE) {

    $block_list = "";
    for($b=0;$b<$num_blocked;$b++) {
      $var = "blocked".$b;
      if(str_replace(" ", "", $_POST[$var]) != "" AND $_POST[$var] != $user->user_info[user_username]) {
        $blocked_user = new se_user(Array(0, $_POST[$var]), Array('user_id'));
        if($blocked_user->user_exists != 0) {
  	  $block_list .= $blocked_user->user_info[user_id].",";
          $user->user_friend_remove($blocked_user->user_info[user_id]);
	}
      }
    }
    // REMOVE DUPLICATES
    $block_list = implode(",", array_unique(explode(",", $block_list)));
  }

  // SAVE NEW ACCOUNT SETTINGS IF THERE WAS NO ERROR
  if($is_error == 0) {

    // SET SUBNETWORK
    $subnet_changed = "";
    $subnet_changed_verify = "";
    $subnet_id = $user->user_info[user_subnet_id];
    if($user_email != $user->user_info[user_email] & ($setting[setting_subnet_field1_id] == 0 | $setting[setting_subnet_field2_id] == 0)) { 
      $new_subnet = $user->user_subnet_select($user_email, $user->profile_info);
      $new_subnet_id = $new_subnet[0];
      $subnet_changed = "<br>".$new_subnet[2];
      $subnet_changed_verify = "<br>".$new_subnet[3];
    }

    // USER DOESN'T NEED TO VERIFY THEIR EMAIL
    if($setting[setting_signup_verify] == 0) {
      $user_email = $user_email;
      $user_newemail = $user_email;
      $subnet_id = $new_subnet_id;

    // USER MUST VERIFY THEIR EMAIL
    } else {
      $user_newemail = $user_email;
      $user_email = $user->user_info[user_email];
      $subnet_id = $user->user_info[user_subnet_id];
    }

    // MAKE SURE LANG FILE EXISTS
    if($setting[setting_lang_allow] == 1) {
      $user_lang = strtolower($_POST['user_lang']);
      if(!file_exists("./lang/lang_".$user_lang.".php")) { $user_lang = $setting[setting_lang_default]; }
    } else {
      $user_lang = $setting[setting_lang_default];
    }

    // DETERMINE WHICH ACTION TYPES ARE ALLOWED
    $actiontypes_max_id = $_POST['actiontypes_max_id'];
    $actiontypes_allowed = "";
    $count = 0;
    for($c = 1; $c <= $actiontypes_max_id; $c++) {
      $var = "actiontype_id_$c";
      if(isset($_POST[$var])) {
        $count++;
        $actiontype_id = $_POST[$var];
	if($count > 1) { $actiontypes_allowed .= ","; }
	$actiontypes_allowed .= $actiontype_id;
      }
    }
    // NOW GET THE REST OF THE ACTION TYPES
    $actiontypes_disallowed_query = $database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_id NOT IN ($actiontypes_allowed)");
    $actiontypes_disallowed = "";
    $count = 0;
    while($actiontype_disallowed = $database->database_fetch_assoc($actiontypes_disallowed_query)) {
      $count++;
      if($count > 1) { $actiontypes_disallowed .= ","; }
      $actiontypes_disallowed .= "$actiontype_disallowed[actiontype_id]";
    }
    // SAVE DISALLOWED ACTION TYPES IN THE USER SETTINGS TABLE
    $database->database_query("UPDATE se_usersettings SET usersetting_actions_dontpublish='$actiontypes_disallowed' WHERE usersetting_user_id='".$user->user_info[user_id]."' LIMIT 1");

    // UPDATE DATABASE
    $database->database_query("UPDATE se_users SET user_subnet_id='$subnet_id', user_email='$user_email', user_newemail='$user_newemail', user_username='$user_username', user_name='$user_name', user_lastname='$user_lastname', 
user_timezone='$user_timezone', 
user_lang='$user_lang', 
user_blocklist='$block_list' WHERE user_id='".$user->user_info[user_id]."'");

    // IF USERNAME HAS CHANGED, DELETE OLD RECENT ACTIVITY
    if($user->user_info[user_username] != $user_username) { $database->database_query("DELETE FROM se_actions WHERE action_user_id='".$user->user_info[user_id]."'"); }

    // IF NAME HAS CHANGED, DELETE OLD RECENT ACTIVITY
    if($user->user_info[user_name] != $user_name) { $database->database_query("DELETE FROM se_actions WHERE action_user_id='".$user->user_info[user_id]."'"); }

    // IF LASTNAME HAS CHANGED, DELETE OLD RECENT ACTIVITY
    if($user->user_info[user_lastname] != $user_lastname) { $database->database_query("DELETE FROM se_actions WHERE action_user_id='".$user->user_info[user_id]."'"); }


    // RESET USER INFO
    $user = new se_user(Array($user->user_info[user_id]));

    // UPDATE COOKIES
    $user->user_setcookies(); 

    // SEND VERIFICATION EMAIL IF NECESSARY AND SET RESULT
    if($user->user_info[user_newemail] != $user->user_info[user_email]) {
      send_verification($user->user_info);
      $result = $user_account[10].$subnet_changed_verify;
    } else {
      $result = $user_account[11].$subnet_changed;
    }
  }
}


// GET LANGUAGE FILE OPTIONS
$lang_options = Array();
$lang_count = 0;
if($dh = opendir("./lang/")) {
  while(($file = readdir($dh)) !== false) {
    if($file != "." & $file != "..") {
      if(preg_match("/lang_([^_]+)\.php/", $file, $matches)) {
        $lang_options[$lang_count] = ucfirst($matches[1]);
        $lang_count++;
      }
    }
  }
  closedir($dh);
}

// CREATE ARRAY OF ACTION TYPES
$user->user_settings();
$actiontypes_disallowed = explode(",", $user->usersetting_info[usersetting_actions_dontpublish]);
$actiontypes_query = $database->database_query("SELECT * FROM se_actiontypes");
$actiontypes_array = Array();
$actiontypes_max_id = 0;

while($actiontype = $database->database_fetch_assoc($actiontypes_query)) {

  // MAKE THIS ACTION TYPE SELECTED IF ITS NOT DISALLOWED BY USER
  $actiontype_selected = 0;
  if(!in_array($actiontype[actiontype_id], $actiontypes_disallowed) AND $user->usersetting_info[usersetting_actions_dontpublish] != "") { 
    $actiontype_selected = 1; 
  } elseif($user->usersetting_info[usersetting_actions_dontpublish] == "") {
    $actiontype_selected = 1; 
  }
  $actiontypes_array[] = Array('actiontype_id' => $actiontype[actiontype_id],
			       'actiontype_selected' => $actiontype_selected,
			       'actiontype_desc' => $actiontype[actiontype_desc]);
  $actiontypes_max_id = $actiontype[actiontype_id];

}


// CREATE ARRAY OF BLOCKED USERS
$blocked_users = explode(",", $user->user_info[user_blocklist]);
$blocked_array = Array();
while(list($key, $blocked_user_id) = each($blocked_users)) {
  $blocked_user = new se_user(Array($blocked_user_id), Array('user_username'));
  if($blocked_user->user_exists != 0) { $blocked_array[] = $blocked_user->user_info[user_username]; }
}
$blocked_array[] = "";


// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('result', $result);
$smarty->assign('error_message', $error_message);
$smarty->assign('lang_options', $lang_options);
$smarty->assign('actiontypes', $actiontypes_array);
$smarty->assign('actiontypes_max_id', $actiontypes_max_id);
$smarty->assign('blocked_users', $blocked_array);
$smarty->assign('num_blocked', count($blocked_array));
include "footer.php";
}
///////////////////////////////////////////////////////


///////////////////////////////////////////////////////
elseif($_GET['act'] == "delete"){

$page = "user_account_delete";
include "header.php";

if(isset($_POST['privacy'])) { $privacy = $_POST['privacy']; } elseif(isset($_GET['privacy'])) { $privacy = $_GET['privacy']; } else { $privacy = "main"; }


// DELETE THIS USER
if($privacy == "dodelete") {
  $user->user_delete();
  $user->user_setcookies();
  cheader("index.php");
  exit;
}


// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
include "footer.php";
}
///////////////////////////////////////////////////////


///////////////////////////////////////////////////////
elseif($_GET['act'] == "privacy"){

$page = "user_editprofile_settings";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// SET VARS
$result = 0;


// SAVE NEW SETTINGS
if($task == "dosave") {
  $style_profile = addslashes(strip_tags(htmlspecialchars_decode($_POST['style_profile'], ENT_QUOTES)));
  $privacy_profile = $_POST['privacy_profile'];
  $comments_profile = $_POST['comments_profile'];
  $search_profile = $_POST['search_profile'];
  $usersetting_notify_profilecomment = $_POST['usersetting_notify_profilecomment'];

  // SET STYLE TO NOTHING IF NOT ALLOWED
  if($user->level_info[level_profile_style] != 1) { $style_profile = ""; }

  // MAKE SURE SUBMITTED PRIVACY OPTIONS ARE ALLOWED, IF NOT, SET TO EVERYONE
  if(!strstr($user->level_info[level_profile_privacy], $privacy_profile)) { $privacy_profile = 0; }
  if(!strstr($user->level_info[level_profile_comments], $comments_profile)) { $comments_profile = 0; }

  // UPDATE DATABASE
  $database->database_query("UPDATE se_users SET user_privacy_profile='$privacy_profile', user_privacy_comments='$comments_profile', user_privacy_search='$search_profile' WHERE user_id='".$user->user_info[user_id]."'");
  $database->database_query("UPDATE se_usersettings SET usersetting_notify_profilecomment='$usersetting_notify_profilecomment' WHERE usersetting_user_id='".$user->user_info[user_id]."'");
  $database->database_query("UPDATE se_profilestyles SET profilestyle_css='$style_profile' WHERE profilestyle_user_id='".$user->user_info[user_id]."'");
  $user->user_lastupdate();
  $user = new se_user(Array($user->user_info[user_id]));
  $result = 1;
}



// GET TABS TO DISPLAY ON TOP MENU
$user->user_fields(1);
$tab_array = $user->profile_tabs;

// GET THIS USER'S PROFILE CSS
$style_query = $database->database_query("SELECT profilestyle_css FROM se_profilestyles WHERE profilestyle_user_id='".$user->user_info[user_id]."' LIMIT 1");
if($database->database_num_rows($style_query) == 1) { 
  $style_info = $database->database_fetch_assoc($style_query); 
} else {
  $database->database_query("INSERT INTO se_profilestyles (profilestyle_user_id, profilestyle_css) VALUES ('".$user->user_info[user_id]."', '')");
  $style_info = $database->database_fetch_assoc($database->database_query("SELECT profilestyle_css FROM se_profilestyles WHERE profilestyle_user_id='".$user->user_info[user_id]."' LIMIT 1")); 
}


// GET AVAILABLE PROFILE PRIVACY OPTIONS
$privacy_count = 0;
$privacy_profile_options = Array();
for($p=0;$p<strlen($user->level_info[level_profile_privacy]);$p++) {
  $privacy_level = substr($user->level_info[level_profile_privacy], $p, 1);
  if(user_privacy_levels($privacy_level) != "") {
    $privacy_profile_options[$privacy_count] = Array('privacy_id' => "privacy_profile".$privacy_level,
					    	     'privacy_value' => $privacy_level,
					   	     'privacy_option' => user_privacy_levels($privacy_level));
    $privacy_count++;
  }
}


// GET AVAILABLE PROFILE COMMENTS OPTIONS
$privacy_count = 0;
$comments_profile_options = Array();
for($p=0;$p<strlen($user->level_info[level_profile_comments]);$p++) {
  $privacy_level = substr($user->level_info[level_profile_comments], $p, 1);
  if(user_privacy_levels($privacy_level) != "") {
    $comments_profile_options[$privacy_count] = Array('privacy_id' => "comments_profile".$privacy_level,
					    	      'privacy_value' => $privacy_level,
					   	      'privacy_option' => user_privacy_levels($privacy_level));
    $privacy_count++;
  }
}

// ASSIGN USER SETTINGS
$user->user_settings();



// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
$smarty->assign('result', $result);
$smarty->assign('tabs', $tab_array);
$smarty->assign('style_profile', htmlspecialchars($style_info[profilestyle_css]));
$smarty->assign('privacy_profile', true_privacy($user->user_info[user_privacy_profile], $user->level_info[level_profile_privacy]));
$smarty->assign('comments_profile', true_privacy($user->user_info[user_privacy_comments], $user->level_info[level_profile_comments]));
$smarty->assign('privacy_profile_options', $privacy_profile_options);
$smarty->assign('comments_profile_options', $comments_profile_options);
include "footer.php";

}
///////////////////////////////////////////////////////


///////////////////////////////////////////////////////
elseif($_GET['act'] == "change_pass"){


$page = "user_account";
include "header.php";

if(isset($_POST['m'])) { $m = $_POST['m']; } elseif(isset($_GET['m'])) { $m = $_GET['m']; } else { $m = 0; }

$smarty->assign('m', $m);


if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// SET EMPTY VARS
$is_error = 0;
$error_message = "";
$result = 0;


// SAVE NEW PASSWORD
if($task == "dosave") {
  $password_old = $_POST['password_old'];
  $password_new = $_POST['password_new'];
  $password_new2 = $_POST['password_new2'];

  $user->user_password($password_old, $password_new, $password_new2);
  $is_error = $user->is_error;
  $error_message = $user->error_message;

  // IF THERE WAS NO ERROR, SAVE CHANGES
  if($is_error == 0) {

    // ENCRYPT NEW PASSWORD WITH MD5
    $password_new_crypt = crypt($password_new, $user->user_salt);

    // UPDATE DATABASE AND RESET USER INFO
    $database->database_query("UPDATE se_users SET user_password='$password_new_crypt' WHERE user_id='".$user->user_info[user_id]."' LIMIT 1");
    $user = new se_user(Array($user->user_info[user_id]));

    // UPDATE COOKIES
    $user->user_setcookies(); 

    // SET RESULT
    $result = 1;

  }
}


// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('error_message', $error_message);
include "footer.php";

}
///////////////////////////////////////////////////////
?>