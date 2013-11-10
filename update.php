<?php
include "DB.php";
$id = (isset($_GET['id'])) ? $_GET['id'] : 0;
if($id != 0){
  $db = new DB("root","root","localhost","todo");
  $tareas = $db->find('tasks',['id'=>$id]);
?>
<html>
<head>
  <title>To Do List</title>
</head>
<body>
  <h1>To Do List</h1>
  <form action="save.php" method="POST">
    <input type="text" id="task" name="task" value="<?php echo $tareas['task']; ?>">
    <input type="hidden" id="id" name="id" value="<?php echo $tareas['id']; ?>">
    <button type="submit">Guardar</button>
  </form>
</body>
</html>
<?php
} else {
  header("Location:index.php");
}
?>
