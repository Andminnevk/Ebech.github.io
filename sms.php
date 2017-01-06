<?
include "header.php";
include "./include/billing.php";

function get_var($name, $default = 'none') {
if(isset($_GET[$name])){
$name=$_GET[$name];
}elseif(isset($_POST[$name])){
$name=$_POST[$name];
}
  return $name;
}

if($md5 == 1){
$password=md5($sms_password);
}else{
$password=$sms_password;
}

echo"smsid:".get_var('smsid')."\n";
echo"status:reply\n";
echo"content-type:text/plain\n";
echo"\n"; 
if(get_var('skey') == $password){
$op="SMS: ".get_var('operator');
$cost=get_var('cost');
$summ=ceil($cost/$point);
$msg=get_var('msg');
$msgs=eregi_replace($prefix_sms,"",$msg);
$database->database_query("INSERT INTO se_gifts_point VALUES (NULL,'$msgs','1','$calc_sum','$op'");
$database->database_query("UPDATE se_users SET user_points=user_points+$summ WHERE user_id='$msgs'");
echo $sms_otvet." Сумма к зачислению ".$summ;
}
?>
