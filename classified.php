<?
$page = "classified";
include "header.php";

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 & $setting[setting_permission_classified] == 0) {
  $page = "error";
  $smarty->assign('error_header', $classified[13]);
  $smarty->assign('error_message', $classified[23]);
  $smarty->assign('error_submit', $classified[22]);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO classified OWNER
if($owner->user_exists == 0) {
  $page = "error";
  $smarty->assign('error_header', $classified[13]);
  $smarty->assign('error_message', $classified[12]);
  $smarty->assign('error_submit', $classified[22]);
  include "footer.php";
}

// ENSURE classifiedS ARE ENABLED FOR THIS USER
if($owner->level_info[level_classified_allow] == 0) { header("Location: ".$url->url_create('profile', $owner->user_info[user_username])); exit(); }

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_GET['classified_id'])) { $classified_id = $_GET['classified_id']; } elseif(isset($_POST['classified_id'])) { $classified_id = $_POST['classified_id']; } else { $classified_id = 0; }

// INITIALIZE CLASSIFIED OBJECT
$classified_object = new se_classified($user->user_info[user_id], $classified_id);
if($classified_object->classified_info[classified_user_id] != $owner->user_info[user_id] || $classified_object->classified_exists == 0) { 
  $page = "error";
  $smarty->assign('error_header', $classified[13]);
  $smarty->assign('error_message', $classified[12]);
  $smarty->assign('error_submit', $classified[22]);
  include "footer.php";
}

// GET PRIVACY LEVEL
$privacy_level = $owner->user_privacy_max($user, $owner->level_info[level_classified_privacy]);
if($privacy_level < true_privacy($classified_object->classified_info[classified_privacy], $owner->level_info[level_classified_privacy])) {
  $page = "error";
  $smarty->assign('error_header', $classified[13]);
  $smarty->assign('error_message', $classified[1]);
  $smarty->assign('error_submit', $classified[22]);
  include "footer.php";
}


// UPDATE ENTRY VIEWS
if($user->user_info[user_id] != $classified_object->classified_info[classified_user_id]) {
  $classified_views = $classified_object->classified_info[classified_views]+1;
  $database->database_query("UPDATE se_classifieds SET classified_views='$classified_views' WHERE classified_id='".$classified_object->classified_info[classified_id]."'");
}

// GET ENTRY COMMENT PRIVACY
$allowed_to_comment = 1;
$comment_level = $owner->user_privacy_max($user, $owner->level_info[level_classified_comments]);
if($comment_level < true_privacy($classified_object->classified_info[classified_comments], $owner->level_info[level_classified_comments])) { $allowed_to_comment = 0; }


// IF A COMMENT IS BEING POSTED
if($task == "dopost" & $allowed_to_comment != 0) {

  $comment_date = time();
  $comment_body = $_POST['comment_body'];

  // RETRIEVE AND CHECK SECURITY CODE IF NECESSARY
  if($setting[setting_comment_code] != 0) {
    session_start();
    $code = $_SESSION['code'];
    if($code == "") { $code = randomcode(); }
    $comment_secure = $_POST['comment_secure'];

    if($comment_secure != $code) { $is_error = 1; }
  }

  // MAKE SURE COMMENT BODY IS NOT EMPTY
  $comment_body = censor(str_replace("\r\n", "<br>", $comment_body));
  $comment_body = preg_replace('/(<br>){3,}/is', '<br><br>', $comment_body);
  $comment_body = ChopText($comment_body);
  if(str_replace(" ", "", $comment_body) == "") { $is_error = 1; $comment_body = ""; }

  // ADD COMMENT IF NO ERROR
  if($is_error == 0) {
    $database->database_query("INSERT INTO se_classifiedcomments (classifiedcomment_classified_id, classifiedcomment_authoruser_id, classifiedcomment_date, classifiedcomment_body) VALUES ('".$classified_object->classified_info[classified_id]."', '".$user->user_info[user_id]."', '$comment_date', '$comment_body')");

    // INSERT ACTION IF USER EXISTS
    if($user->user_exists != 0) {
      $commenter = $user->user_info[user_username];
      $comment_body_encoded = $comment_body;
      if(strlen($comment_body_encoded) > 250) { 
        $comment_body_encoded = substr($comment_body_encoded, 0, 240);
        $comment_body_encoded .= "...";
      }
      $comment_body_encoded = htmlspecialchars(str_replace("<br>", " ", $comment_body_encoded));
      $actions->actions_add($user, "classifiedcomment", Array('[username1]', '[username2]', '[id]', '[comment]'), Array($commenter, $owner->user_info[user_username], $classified_id, $comment_body_encoded));
    } else {
      $commenter = $classified[11];
    }

    // SEND COMMENT NOTIFICATION IF NECESSARY
    $owner->user_settings();
    if($owner->usersetting_info[usersetting_notify_classifiedcomment] == 1 & $owner->user_info[user_id] != $user->user_info[user_id]) { 
      send_generic($owner->user_info[user_email], "$setting[setting_email_fromname] <$setting[setting_email_fromemail]>", $setting[setting_email_classifiedcomment_subject], $setting[setting_email_classifiedcomment_message], Array('[username]', '[commenter]', '[link]'), Array($owner->user_info[user_username], $commenter, "<a href=\"".$url->url_create("classified", $owner->user_info[user_username], $classified_object->classified_info[classified_id])."\">".$url->url_create("classified", $owner->user_info[user_username], $classified_object->classified_info[classified_id])."</a>")); 
    }

  }

  echo "<html><head><script type=\"text/javascript\">";
  echo "window.parent.addComment('$is_error', '$comment_body', '$comment_date');";
  echo "</script></head><body></body></html>";
  exit();
}

// MAKE SURE TITLE IS NOT EMPTY
if($classified_object->classified_info[classified_title] == "") { $classified_object->classified_info[classified_title] = $classified[5]; }

// CONVERT HTML CHARACTERS BACK
$classified_object->classified_info[classified_body] = str_replace("\r\n", "", html_entity_decode($classified_object->classified_info[classified_body]));


// GET CLASSIFIED COMMENTS
$comment = new se_comment('classified', 'classified_id', $classified_object->classified_info[classified_id]);
$total_comments = $comment->comment_total();
$comments = $comment->comment_list(0, $total_comments);

// GET CLASSIFIED ALBUM INFO
$classifiedalbum_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_classifiedalbums WHERE classifiedalbum_classified_id='".$classified_object->classified_info[classified_id]."' LIMIT 1"));

// GET MEDIA ARRAY
$file_array = $classified_object->classified_media_list(0, 10, "classifiedmedia_id ASC", "(classifiedmedia_classifiedalbum_id='$classifiedalbum_info[classifiedalbum_id]')");

// GET TOTAL FILES IN CLASSIFIED ALBUM
$total_files = $classified_object->classified_media_total($classifiedalbum_info[classifiedalbum_id]);

// GET CAT INFO
$cat_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_classifiedcats WHERE classifiedcat_id='".$classified_object->classified_info[classified_classifiedcat_id]."'"));
if($cat_info[classifiedcat_dependency] == 0) { $cat_parent = $cat_info[classifiedcat_id]; } else { $cat_parent = $cat_info[classifiedcat_dependency]; }

// GET FIELDS/VALUES
$classified_object->classified_fields(0, 1, 0, $cat_parent);

// ASSIGN VARIABLES AND DISPLAY CLASSIFIED PAGE
$smarty->assign('fields', $classified_object->fields);
$smarty->assign('comments', $comments);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('classified', $classified_object);
$smarty->assign('cat_info', $cat_info);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('files', $file_array);
$smarty->assign('total_files', $total_files);
include "footer.php";
?>