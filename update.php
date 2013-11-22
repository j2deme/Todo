<?php
include "DB.php";
$id = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;
if($id != 0){
  $db = new DB("root","root","localhost","todo");
  $tarea = $db->find('tasks',array('id'=>$id);
?>
<html>
<head>
  <title>To Do List</title>
</head>
<body>
  <h1>To Do List</h1>
  <form action="save.php" method="POST">
    <input type="text" id="task" name="task" value="<?php echo $tarea['task']; ?>">
    <input type="hidden" id="id" name="id" value="<?php echo $tarea['id']; ?>">
    <button type="submit">Guardar</button>
  </form>
</body>
</html>
<?php
} else {
  header("Location:index.php");
}
?>
