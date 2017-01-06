<? 
error_reporting (E_ALL ^ E_NOTICE); 
/////////////////////////////////////////////////////// 
if($_GET['section'] == "" or $_GET['section'] == "main"){ 


$page = "gsearch"; 
include "header.php"; 


// GET RANDOM USERS 
$randoms_query = $database->database_query(" 
  
  SELECT  
    user_id,  
    user_username,  
    user_photo  
  FROM  
    se_users  
  WHERE  
    user_verified='1'  
  AND  
    user_enabled='1'   
  ORDER BY  
    rand()  
  DESC LIMIT  
    1000 
  "); 
   
$random_array = Array(); 
while($random = $database->database_fetch_assoc($randoms_query)) { 

  $random_user = new se_user($random[user_id]); 
  $random_user->user_info[user_id] = $random[user_id]; 
  $random_user->user_info[user_username] = $random[user_username]; 
  $random_profile->profile_info[profile_8] = $random[profile_8]; 
  $random_profile->profile_info[profile_10] = $random[profile_10]; 
  $random_user->user_info[user_photo] = $random[user_photo]; 
  $random_user->user_info[user_lastlogindate] = $random[user_lastlogindate]; 

  $random_array[] = $random_user; 
}   



$smarty->assign('randoms', $random_array); 
include "footer.php"; 

} 
/////////////////////////////////////////////////////// 


/////////////////////////////////////////////////////// 
if($_GET['section'] == "" or $_GET['section'] == "people"){ 


$page = "gsearch_people"; 
include "header.php"; 


// GET RANDOM USERS 
$randoms_query = $database->database_query(" 
  
  SELECT  
    user_id,  
    user_username,  
    user_photo  
  FROM  
    se_users  
  WHERE  
    user_verified='1'  
  AND  
    user_enabled='1'   
  ORDER BY  
    rand()  
  DESC LIMIT  
    1000 
  "); 
   
$random_array = Array(); 
while($random = $database->database_fetch_assoc($randoms_query)) { 

  $random_user = new se_user($random[user_id]); 
  $random_user->user_info[user_id] = $random[user_id]; 
  $random_user->user_info[user_username] = $random[user_username]; 
  $random_user->user_info[user_photo] = $random[user_photo]; 
  $random_user->user_info[user_lastlogindate] = $random[user_lastlogindate]; 

  $random_array[] = $random_user; 
}   



$smarty->assign('randoms', $random_array); 
include "footer.php"; 

} 
/////////////////////////////////////////////////////// 



if($_GET['section'] == "video" or $_GET['section'] == "serch"){

$page = "user_classified_browse";
include "header.php";


$page_title = "Поиск Видео";
$smarty->assign('page_title', $page_title);


if(isset($_POST['classifiedcat_id'])) { $classifiedcat_id = $_POST['classifiedcat_id']; } elseif(isset($_GET['classifiedcat_id'])) { $classifiedcat_id = $_GET['classifiedcat_id']; } else { $classifiedcat_id = 0; }
if(isset($_POST['q'])) { $q = $_POST['q']; } elseif(isset($_GET['q'])) { $q = $_GET['q']; } else { $q = ""; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }


// CREATE CLASSIFIED OBJECT
$entries_per_page = 10;
$classified = new se_classified();
$sort = "classified_date DESC";

// SET WHERE CLAUSE
$where = "";
if($q != "") { 
  $where .= "(classified_title LIKE '%$q%' OR classified_body LIKE '%$q%')"; 
}

// FIND OUT WHICH CATEGORY TO OPEN
$open_cat = "";
$open_cat_query = $database->database_query("SELECT * FROM se_classifiedcats WHERE classifiedcat_id='$classifiedcat_id'");
if($database->database_num_rows($open_cat_query) == 1) {

  $open_cat = $database->database_fetch_assoc($open_cat_query);
  $open_cat_info = $open_cat;

  // SHOW ALL LISTINGS IN A MAIN CATEGORY
  if($open_cat[classifiedcat_dependency] == 0) {
    $subcatarray_query = $database->database_query("SELECT classifiedcat_id FROM se_classifiedcats WHERE classifiedcat_dependency='$open_cat[classifiedcat_id]'");
    $subcatarray = Array();
    $subcatarray[0] = $open_cat[classifiedcat_id];
    while($subcat = $database->database_fetch_assoc($subcatarray_query)) {
      $subcatarray[] = $subcat[classifiedcat_id];
    }
    $subcatarray = implode(",", $subcatarray);
    $parent_cat = $open_cat[classifiedcat_id];
    $open_cat = $open_cat[classifiedcat_id];
    if($where != "") { $where .= "AND "; }
    $where .= "(classified_classifiedcat_id IN ($subcatarray))";

  // SHOW LISTINGS IN A SUBCAT ONLY
  } else {
    $parent_cat = $open_cat[classifiedcat_dependency];
    $open_cat = $open_cat[classifiedcat_id];
    if($where != "") { $where .= "AND "; }
    $where .= "(classified_classifiedcat_id='$open_cat')";
  }

  // ADD TO WHERE CLAUSE
  $classified->classified_fields(0, 0, 1, $parent_cat);
  if($classified->field_query != "") { if($where != "") { $where .= "AND "; } $where .= "(".$classified->field_query.")"; }
  $url_string = $classified->url_string;
}

// GET CLASSIFIED CATEGORIES
$classifiedcats = $database->database_query("SELECT se_classifiedcats.*, count(se_classifieds.classified_id) AS total_cat_classifieds FROM se_classifiedcats LEFT JOIN se_classifieds ON se_classifiedcats.classifiedcat_id=se_classifieds.classified_classifiedcat_id WHERE se_classifiedcats.classifiedcat_dependency='0' GROUP BY se_classifiedcats.classifiedcat_id");

// LOOP THROUGH CLASSIFIED CATEGORIES AND SUBCATEGORIES
$categories = Array();
$classifieds_totalincats = 0;
while($classifiedcat = $database->database_fetch_assoc($classifiedcats)) {

  // LOOP THROUGH SUBCATS AND PUT THEM INTO AN ARRAY
  $classifiedsubcats = $database->database_query("SELECT se_classifiedcats.*, count(se_classifieds.classified_id) AS total_subcat_classifieds FROM se_classifiedcats LEFT JOIN se_classifieds ON se_classifiedcats.classifiedcat_id=se_classifieds.classified_classifiedcat_id WHERE se_classifiedcats.classifiedcat_dependency='$classifiedcat[classifiedcat_id]' GROUP BY se_classifiedcats.classifiedcat_id");
  $total_subcat_classifieds = 0;
  $subcategory_array = Array();
  while($classifiedsubcat = $database->database_fetch_assoc($classifiedsubcats)) {
    $subcategory_array[] = Array('subcategory_id' => $classifiedsubcat[classifiedcat_id],
				 'subcategory_title' => $classifiedsubcat[classifiedcat_title],
				 'subcategory_totalclassifieds' => $classifiedsubcat[total_subcat_classifieds]);
    $total_subcat_classifieds += $classifiedsubcat[total_subcat_classifieds];
  }

  // GET TOTAL CLASSIFIEDS IN THIS CATEGORY AND ALL ITS SUBCATS
  $total_cat_classifieds = $classifiedcat[total_cat_classifieds]+$total_subcat_classifieds;

  // SEE IF THIS CATEGORY HAS ALREADY BEEN EXPANDED WITH JAVASCRIPT
  $classifiedcat_expanded = 0;
  $classifiedcatvar = "subcats$classifiedcat[classifiedcat_id]";
  if((isset($_COOKIE[$classifiedcatvar]) AND $_COOKIE[$classifiedcatvar] == 1) OR $open_cat == $classifiedcat[classifiedcat_id]) {
    $classifiedcat_expanded = 1;
  }

  // PUT CATEGORY DATA INTO AN ARRAY
  $categories[] = Array('classifiedcat_id' => $classifiedcat[classifiedcat_id],
  			'classifiedcat_title' => $classifiedcat[classifiedcat_title],
			'classifiedcat_totalclassifieds' => $total_cat_classifieds,
			'classifiedcat_expanded' => $classifiedcat_expanded,
			'classifiedcat_subcats' => $subcategory_array);
  $classifieds_totalincats += $total_cat_classifieds;
}

// DETERMINE HOW MANY CLASSIFIEDS EXIST THAT ARE UNCATEGORIZED
$total_classifieds = $classifieds_totalincats;
$classifieds_totalnocat = 0;

// GET TOTAL ENTRIES
$total_classifieds = $classified->classifieds_total($where, 1);

// MAKE ENTRY PAGES
$page_vars = make_page($total_classifieds, $entries_per_page, $p);

// GET ENTRY ARRAY
$classifieds = $classified->classifieds_list($page_vars[0], $entries_per_page, $sort, $where, 1);



// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('categories', $categories);
$smarty->assign('classifieds_totalnocat', $classifieds_totalnocat);
$smarty->assign('fields', $classified->fields);
$smarty->assign('q', $q);
$smarty->assign('url_string', $url_string);
$smarty->assign('classifieds', $classifieds);
$smarty->assign('total_classifieds', $total_classifieds);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($classifieds));
$smarty->assign('classifiedcat_id', $open_cat_info[classifiedcat_id]);
$smarty->assign('classifiedcat_title', $open_cat_info[classifiedcat_title]);
include "footer.php";


}
///////////////////////////////////////////////////////





/////////////////////////////////////////////////////// 



if($_GET['section'] == "audio" or $_GET['section'] == "serch"){

$page = "user_classified_browse";
include "header.php";


$page_title = "Поиск Видео";
$smarty->assign('page_title', $page_title);


if(isset($_POST['classifiedcat_id'])) { $classifiedcat_id = $_POST['classifiedcat_id']; } elseif(isset($_GET['classifiedcat_id'])) { $classifiedcat_id = $_GET['classifiedcat_id']; } else { $classifiedcat_id = 0; }
if(isset($_POST['q'])) { $q = $_POST['q']; } elseif(isset($_GET['q'])) { $q = $_GET['q']; } else { $q = ""; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }


// CREATE CLASSIFIED OBJECT
$entries_per_page = 10;
$classified = new se_classified();
$sort = "classified_date DESC";

// SET WHERE CLAUSE
$where = "";
if($q != "") { 
  $where .= "(classified_title LIKE '%$q%' OR classified_body LIKE '%$q%')"; 
}

// FIND OUT WHICH CATEGORY TO OPEN
$open_cat = "";
$open_cat_query = $database->database_query("SELECT * FROM se_classifiedcats WHERE classifiedcat_id='$classifiedcat_id'");
if($database->database_num_rows($open_cat_query) == 1) {

  $open_cat = $database->database_fetch_assoc($open_cat_query);
  $open_cat_info = $open_cat;

  // SHOW ALL LISTINGS IN A MAIN CATEGORY
  if($open_cat[classifiedcat_dependency] == 0) {
    $subcatarray_query = $database->database_query("SELECT classifiedcat_id FROM se_classifiedcats WHERE classifiedcat_dependency='$open_cat[classifiedcat_id]'");
    $subcatarray = Array();
    $subcatarray[0] = $open_cat[classifiedcat_id];
    while($subcat = $database->database_fetch_assoc($subcatarray_query)) {
      $subcatarray[] = $subcat[classifiedcat_id];
    }
    $subcatarray = implode(",", $subcatarray);
    $parent_cat = $open_cat[classifiedcat_id];
    $open_cat = $open_cat[classifiedcat_id];
    if($where != "") { $where .= "AND "; }
    $where .= "(classified_classifiedcat_id IN ($subcatarray))";

  // SHOW LISTINGS IN A SUBCAT ONLY
  } else {
    $parent_cat = $open_cat[classifiedcat_dependency];
    $open_cat = $open_cat[classifiedcat_id];
    if($where != "") { $where .= "AND "; }
    $where .= "(classified_classifiedcat_id='$open_cat')";
  }

  // ADD TO WHERE CLAUSE
  $classified->classified_fields(0, 0, 1, $parent_cat);
  if($classified->field_query != "") { if($where != "") { $where .= "AND "; } $where .= "(".$classified->field_query.")"; }
  $url_string = $classified->url_string;
}

// GET CLASSIFIED CATEGORIES
$classifiedcats = $database->database_query("SELECT se_classifiedcats.*, count(se_classifieds.classified_id) AS total_cat_classifieds FROM se_classifiedcats LEFT JOIN se_classifieds ON se_classifiedcats.classifiedcat_id=se_classifieds.classified_classifiedcat_id WHERE se_classifiedcats.classifiedcat_dependency='0' GROUP BY se_classifiedcats.classifiedcat_id");

// LOOP THROUGH CLASSIFIED CATEGORIES AND SUBCATEGORIES
$categories = Array();
$classifieds_totalincats = 0;
while($classifiedcat = $database->database_fetch_assoc($classifiedcats)) {

  // LOOP THROUGH SUBCATS AND PUT THEM INTO AN ARRAY
  $classifiedsubcats = $database->database_query("SELECT se_classifiedcats.*, count(se_classifieds.classified_id) AS total_subcat_classifieds FROM se_classifiedcats LEFT JOIN se_classifieds ON se_classifiedcats.classifiedcat_id=se_classifieds.classified_classifiedcat_id WHERE se_classifiedcats.classifiedcat_dependency='$classifiedcat[classifiedcat_id]' GROUP BY se_classifiedcats.classifiedcat_id");
  $total_subcat_classifieds = 0;
  $subcategory_array = Array();
  while($classifiedsubcat = $database->database_fetch_assoc($classifiedsubcats)) {
    $subcategory_array[] = Array('subcategory_id' => $classifiedsubcat[classifiedcat_id],
				 'subcategory_title' => $classifiedsubcat[classifiedcat_title],
				 'subcategory_totalclassifieds' => $classifiedsubcat[total_subcat_classifieds]);
    $total_subcat_classifieds += $classifiedsubcat[total_subcat_classifieds];
  }

  // GET TOTAL CLASSIFIEDS IN THIS CATEGORY AND ALL ITS SUBCATS
  $total_cat_classifieds = $classifiedcat[total_cat_classifieds]+$total_subcat_classifieds;

  // SEE IF THIS CATEGORY HAS ALREADY BEEN EXPANDED WITH JAVASCRIPT
  $classifiedcat_expanded = 0;
  $classifiedcatvar = "subcats$classifiedcat[classifiedcat_id]";
  if((isset($_COOKIE[$classifiedcatvar]) AND $_COOKIE[$classifiedcatvar] == 1) OR $open_cat == $classifiedcat[classifiedcat_id]) {
    $classifiedcat_expanded = 1;
  }

  // PUT CATEGORY DATA INTO AN ARRAY
  $categories[] = Array('classifiedcat_id' => $classifiedcat[classifiedcat_id],
  			'classifiedcat_title' => $classifiedcat[classifiedcat_title],
			'classifiedcat_totalclassifieds' => $total_cat_classifieds,
			'classifiedcat_expanded' => $classifiedcat_expanded,
			'classifiedcat_subcats' => $subcategory_array);
  $classifieds_totalincats += $total_cat_classifieds;
}

// DETERMINE HOW MANY CLASSIFIEDS EXIST THAT ARE UNCATEGORIZED
$total_classifieds = $classifieds_totalincats;
$classifieds_totalnocat = 0;

// GET TOTAL ENTRIES
$total_classifieds = $classified->classifieds_total($where, 1);

// MAKE ENTRY PAGES
$page_vars = make_page($total_classifieds, $entries_per_page, $p);

// GET ENTRY ARRAY
$classifieds = $classified->classifieds_list($page_vars[0], $entries_per_page, $sort, $where, 1);



// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('categories', $categories);
$smarty->assign('classifieds_totalnocat', $classifieds_totalnocat);
$smarty->assign('fields', $classified->fields);
$smarty->assign('q', $q);
$smarty->assign('url_string', $url_string);
$smarty->assign('classifieds', $classifieds);
$smarty->assign('total_classifieds', $total_classifieds);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($classifieds));
$smarty->assign('classifiedcat_id', $open_cat_info[classifiedcat_id]);
$smarty->assign('classifiedcat_title', $open_cat_info[classifiedcat_title]);
include "footer.php";


}
///////////////////////////////////////////////////////






?>