<?php
$user = "root";
$pass = "root";
$host = "127.0.0.1";
$dbname = "deathstar";

/*
Descomenten las secciones que necesiten para ver los resultados de cada comando
*/

# Imprime los drivers disponibles para PDO
echo "<pre>";
print_r(PDO::getAvailableDrivers());
echo "</pre>";

try {
  # Conexión a MySQL
  $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

  # Conexión a PostgreSQL
  #$db = new PDO("pgsql:host=$host;dbname=$dbname;user=$user;password=$pass")

  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  #INSERT & UPDATE
  # Forma 1
  /*
  $st = $db->prepare("INSERT INTO trabajadores (nombre,id_nivel) values ('Jaime',1)");
  $st->execute();
  */
  //$st = $db->prepare("INSERT INTO trabajadorses (nombre,id_nivel) values ('Jaime',1)");//ERROR a propósito para ver el mensaje que devuelve.

  # Forma 2 - Recomendada
  /*
  $st = $db->prepare("INSERT INTO trabajadores (nombre, id_nivel) values (:nombre, :id_nivel)");
  $data = array(
    'nombre' => 'Cathy',
    'id_nivel' => 1
  );
  $st->execute($data);
  */

  # SELECT
  # Forma 1 - Cuando no se tienen parámetros, es decir, no hay un WHERE
  /*
  $st = $db->query('SELECT id, nombre from trabajadores');
  $st->setFetchMode(PDO::FETCH_ASSOC);
  while($row = $st->fetch()) {
    echo $row['id']." - ".$row['nombre']."<br/>";
  }
  */

  # Forma 2 - Cuando se tienen parámetros
  /*
  $st = $db->prepare("SELECT * FROM trabajadores WHERE id=:id");
  $st->bindValue(":id", 1);
  #$data = array ('id'=>1)//También se puede usar el arreglo $data

  $st->setFetchMode(PDO::FETCH_ASSOC);
  $st->execute();
  #st->execute($data);//Solo si se usa con el arreglo $data
  while($row = $st->fetch()) {
    echo $row['id']." - ".$row['nombre']."<br/>";
  }
  */

  # DELETE
  #$db->exec('DELETE FROM trabajadores WHERE 1');//exec() no devuelve resultados

  # Saber las filas afectadas por el query
  $rows_affected = $st->rowCount();
  echo "Filas afectadas: ".$rows_affected;
  $db = null; //Cierra la conexión
} catch(PDOException $e) {
    echo $e->getMessage();
    die();
}
?>
