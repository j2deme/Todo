<?php
include "DB.php";
$id = (isset($_GET['id'])) ? $_GET['id'] : 0;
if($id != 0){
  $db = new DB("root","root","localhost","todo");
  $db->delete('tasks',['id'=> $id]);
}
header("Location:index.php");
?>
