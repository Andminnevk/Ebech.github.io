<?
  
// SET ERROR REPORTING
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

// INITIATE SMARTY
$folder = "base";
include "./include/smarty/smarty_config.php";

// CHECK FOR PAGE VARIABLE
if(!isset($page)) { $page = ""; }

// DEFINE SE PAGE CONSTANT
define('SE_PAGE', true);

// INCLUDE DATABASE INFORMATION
include "./include/database_config.php";

// INCLUDE CLASS/FUNCTION FILES
include "./include/class_database.php";
include "./include/class_datetime.php";
include "./include/class_comment.php";
include "./include/class_upload.php";
include "./include/class_user.php";
include "./include/class_url.php";
include "./include/class_misc.php";
include "./include/class_ads.php";
include "./include/class_actions.php";
include "./include/functions_general.php";
include "./include/functions_email.php";
include "./include/functions_stats.php";
include "./include/class_myguests.php";

// INITIATE DATABASE CONNECTION
$database = new se_database($database_host, $database_username, $database_password, $database_name);

// GET SETTINGS
$setting = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_settings LIMIT 1"));

// IF GD IS NOT ENABLED, TURN OFF IMAGE VERIFICATION
if(!is_callable('gd_info')) {
  $setting[setting_comment_code] = 0;
  $setting[setting_signup_code] = 0;
  $setting[setting_invite_code] = 0;
  $setting[setting_group_discussion_code] = 0;
}


// ENSURE NO SQL INJECTIONS THROUGH POST OR GET ARRAYS
$_POST = security($_POST);
$_GET = security($_GET);

// UPDATE STATS TABLE
update_stats("views");

// CREATE URL CLASS
$url = new se_url();

// CREATE DATETIME CLASS
$datetime = new se_datetime();

// CREATE MISC CLASS
$misc = new se_misc();

// CREATE ACTIONS CLASS
$actions = new se_actions();

// CREATE GLOBAL CSS STYLES VAR (USED FOR CUSTOM USER-DEFINED PROFILE/PLUGIN STYLES)
$global_css = "";

// SET GLOBAL DEFAULT LANGUAGE VAR
$global_lang = $setting[setting_lang_default];

// CHECK FOR PAGE OWNER
function say_list() { 
  global $database; 
  $say_array = Array(); 
  $say_query = "select user_id, user_username, user_photo  
  from se_users WHERE user_want='1' 
  ORDER by RAND() LIMIT 1"; 
  $says = $database->database_query($say_query); 
  while($say_info = $database->database_fetch_assoc($says)) { 
$user =  new se_user();  
$user->user_info[user_id] =  $say_info[user_id];  
$user->user_info[user_username] =  $say_info[user_username]; 
$user->user_info[user_photo] =  $say_info[user_photo];  
    $say_array[] = Array('user' => $user); } 
  return $say_array; 
} 
$smarty->assign('says', say_list()); 
if(isset($_POST['user'])) { $user_username = $_POST['user']; } elseif(isset($_GET['user'])) { $user_username = $_GET['user']; } else { $user_username = ""; }
if(isset($_POST['user_id'])) { $user_id = $_POST['user_id']; } elseif(isset($_GET['user_id'])) { $user_id = $_GET['user_id']; } else { $user_id = ""; }
$owner = new se_user(Array($user_id, $user_username));


// CREATE USER OBJECT AND ATTEMPT TO LOG USER IN
$user = new se_user();
$user->user_checkCookies();

// USER IS LOGGED IN
if($user->user_exists != 0) { 

  // SET TIMEZONE IF USER IS LOGGED IN
  $global_timezone = $user->user_info[user_timezone];

  // INCLUDE THIS USER'S LANGUAGE FILE IF ALLOWED
  if($setting[setting_lang_allow] == 1) { $global_lang = $user->user_info[user_lang]; }

// USER IS NOT LOGGED IN
} else { 

  // SEND USER TO LOGIN IF TRYING TO ACCESS USER CONTROL PANEL
  if(substr($page, 0, 5) == "user_") { header("Location: login.php?return_url=".$url->url_current()); exit(); }

  // SET TIMEZONE IF USER IS LOGGED OUT
  $global_timezone = $setting[setting_timezone]; 
}


// INCLUDE LANGUAGE FILE
include "./lang/lang_".$global_lang.".php";


// CREATE ADS CLASS
$ads = new se_ads();




// INCLUDE RELEVANT PLUGIN FILES
// AND SET PLUGIN HEADER TEMPLATES
$global_plugins = Array();
$plugins = $database->database_query("SELECT plugin_type, plugin_icon FROM se_plugins ORDER BY plugin_id DESC");
while($plugin_info = $database->database_fetch_assoc($plugins)) { 
  if(file_exists("header_".$plugin_info[plugin_type].".php")) { include "header_".$plugin_info[plugin_type].".php"; } 
  $global_plugins[] = $plugin_info[plugin_type];
}




// CHECK IF LOGGED-IN USER IS ON OWNER'S BLOCKLIST
if($user->user_exists == 1) {
  if($owner->user_blocked($user->user_info[user_id])) {
    // ASSIGN VARIABLES AND DISPLAY ERROR PAGE
    $page = "error";
    $smarty->assign('error_header', $header[23]);
    $smarty->assign('error_message', $header[24]);
    $smarty->assign('error_submit', $header[25]);
    include "footer.php";
    exit();
  }
}
// GET TOTAL FRIEND REQUESTS
$total_friend_requests = $user->user_friend_total(1, 0);
$smarty->assign('total_friend_requests', $total_friend_requests); 

// GET NEWS ITEMS
$news = $database->database_query("SELECT * FROM se_announcements ORDER BY announcement_order DESC LIMIT 3");
$news_array = Array();
$news_count = 0;
while($item = $database->database_fetch_assoc($news)) {

  // CONVERT BODY BACK TO HTML
  $item[announcement_body] = htmlspecialchars_decode($item[announcement_body], ENT_QUOTES);
  $item['announcement_subject'] = htmlspecialchars_decode($item['announcement_subject'], ENT_QUOTES);

  $news_array[$news_count] = Array('item_id' => $item[announcement_id],
				   'item_date' => $item[announcement_date],
				   'item_subject' => $item[announcement_subject],
				   'item_body' => $item[announcement_body]);
  $news_count++;
}
$smarty->assign('news', $news_array);
$smarty->assign('news_total', $news_count);
?>