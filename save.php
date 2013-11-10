<?php
include "DB.php";
$tarea = $_POST['task'];
$id = (isset($_POST['id'])) ? $_POST['id'] : 0;

if($tarea != ""){
  $db = new DB("root","root","localhost","todo");
  $data = [];
  if($id == 0){
    $data = ['task' => $tarea];
  } else {
    $data = ['task' => $tarea, 'id' => $id];
  }
  $db->save("tasks",$data);
}
header("Location:index.php");

?>
