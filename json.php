<?php
include "DB.php";
# json.php
$db = new DB("root","root","localhost","todo");
$rows = array();
$result = array();
$num_rows = 0;
if(isset($_GET['id'])){
  $result = $db->find('tasks',array('id'=>$_GET['id']));
  $num_rows = $db->count();
} else {
  $result = $db->findAll('tasks');
  $num_rows = $db->count();
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json; charset=utf-8');
//$db->pretty();
echo $db->toJson();
?>
