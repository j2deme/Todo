<?php
include "DB.php";

$db = new DB("root","root","localhost","todo");
$db->create('test',array(
  'id' => 'int PRIMARY KEY AUTO_INCREMENT',
  'name' => 'varchar(50) NOT NULL',
  'age' => 'int',
  'address' => 'text'
  ));
$db->save('test',array(
  'id' => 1,
  'name' => 'Jaime J. Delgado',
  'age' => 26,
  'address' => 'Río Niño Eufrates #151'
  ));
  $db->pretty(json_encode($db->find('test',array('id'=>1))));
?>
