<?
$page = "topusers";
include "header.php";

if(semods::get_setting('userpoints_enable_topusers') == 0) {
  header("Location: home.php");
}

// MAXIMUM TOP USERS TO DISPLAY
$max_top_users = 10;

/*

 // THIS ENABLES PAGING, SHOWING MORE THAN 10 USERS
 
$p = intval(semods::request('p', 1)); 

// SEXY PAGINATOR CLASS
$paginator = new semods_paginator( $p,  // page
                                   10,  // items per page
                                   semods::db_query_count( 'SELECT COUNT(*) FROM se_semods_userpoints' )
                                   );
*/


/*
 // This one takes into account if userpoints are enabled for user, just extra data that would better not to get pulled (performance)
$query = "SELECT *
          FROM se_semods_userpoints UP
          JOIN se_users U
            ON UP.userpoints_user_id = U.user_id
          JOIN se_levels L
            ON U.user_level_id = L.level_id
          WHERE UP.userpoints_totalearned  != 0 AND U.user_userpoints_allowed = 1 AND L.level_userpoints_allow = 1
          ORDER BY UP.userpoints_totalearned DESC";
 */ 


$query = "SELECT *
          FROM se_semods_userpoints UP
          JOIN se_users U
            ON UP.userpoints_user_id = U.user_id
          WHERE UP.userpoints_totalearned  != 0
          ORDER BY UP.userpoints_totalearned DESC";

/*
   // THIS ENABLES PAGING, SHOWING MORE THAN 10 USERS

$query .= ' LIMIT ' . $paginator->limit_from . ', ' . $paginator->items_per_page;
*/

$query .= " LIMIT $max_top_users";

$rows = $database->database_query( $query );

// GET THEM INTO AN ARRAY
$items = Array();
$dummy_user = new se_user();
$rank = 1;
while($row = $database->database_fetch_assoc($rows)) {
  
  $dummy_user->user_info['user_id'] = $row['user_id'];
  $dummy_user->user_info['user_photo'] = $row['user_photo'];
  
  $row['user_photo'] = $dummy_user->user_photo('./images/nophoto.gif');
  $row['userpoints_rank'] = $rank++;

  $items[] = $row;
}

/*
  // THIS ENABLES PAGING, SHOWING MORE THAN 10 USERS

$paginator->setActualItems( count($items) );
$paginator->assignStdSmartyVars();

*/

// ASSIGN VARIABLES 
$smarty->assign('items', $items);
include "footer.php";
?>