<?php
include "DB.php";
?>
<html>
<head>
  <title>To Do List</title>
</head>
<body>
  <h1>To Do List</h1>
  <form action="save.php" method="POST">
    <input type="text" id="task" name="task" placeholder="Write a task">
    <button type="submit">Guardar</button>
  </form>
  <hr/>
<?php
$db = new DB("root","root","localhost","todo");
$tareas = $db->findAll('tasks');
foreach ($tareas as $tarea) {
  echo $tarea['id']." - ".$tarea['task']." <a style=\"color:red\" href=\"delete.php?id=".$tarea['id']."\">&times;</a> | <a href=\"update.php?id=".$tarea['id']."\">Editar</a><br/>";
}
?>
</body>
</html>
