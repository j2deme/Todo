<?php
include "DB.php";
# json.php
$db = new DB("root","root","localhost","todo");
$rows = [];
$result = [];
$num_rows = 0;
if(isset($_GET['id'])){
  $result = $db->find('tasks',['id'=>$_GET['id']]);
  $num_rows = $db->count();
} else {
  $result = $db->findAll('tasks');
  $num_rows = $db->count();
}
if(count($result) > 1){
  foreach($result as $row) {
    $row['task'] = utf8_encode($row['task']);
    $rows[] = $row;
  }
} else {
  $result['task'] = utf8_encode($result['task']);
  $rows[] = $result;
}
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json; charset=utf-8');
echo json_encode($rows, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT);
?>
