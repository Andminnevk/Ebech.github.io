<?php
include "header.php";
include "./include/billing.php";

$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$shp_item = $_REQUEST["Shp_item"];
$crc = $_REQUEST["SignatureValue"];
$crc = strtoupper($crc);
$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));

if ($my_crc !=$crc)
{
  echo "bad sign\n";
  exit();
}
$calc_sum=ceil($out_summ/$point)
$database->database_query("INSERT INTO se_gifts_point VALUES (NULL,'$shp_item','1','$calc_sum','Robox'");
$database->database_query("UPDATE se_users SET user_points=user_points+$calc_sum WHERE user_id='$shp_item'");
echo "OK$inv_id\n";


?>