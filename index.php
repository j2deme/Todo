<?php
include "DB.php";
?>
<html>
<head>
  <title>To Do List</title>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">
  <!-- Optional theme -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap-theme.min.css">
</head>
<body>
  <h1>To Do List</h1>
  <form action="save.php" method="POST">
    <input type="text" id="task" name="task" placeholder="Write a task">
    <button type="submit">Guardar</button>
  </form>
  <a href="json.php">Ver en JSON</a>
  <hr/>
<?php
$db = new DB("root","root","localhost","todo");
$tareas = $db->findAll('tasks');
//$db->pretty();
foreach ($tareas as $tarea){
  echo $tarea['id']." - ".$tarea['task']." <a style=\"color:red\" href=\"delete.php?id=".$tarea['id']."\">&times;</a> | <a href=\"update.php?id=".$tarea['id']."\">Editar</a><br/>";
}
?>

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://code.jquery.com/jquery.js"></script>
  <!-- Latest compiled and minified JavaScript -->
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>
</body>
</html>
