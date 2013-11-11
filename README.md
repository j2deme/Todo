# Todo

Proyecto de ejemplo que muestra la implementación de un sistema CRUD, haciendo uso de una clase propia basada en PDO.

La clase `DB` referenciable mediante el archivo `DB.php`, ofrece un manejo simple de base de datos basada en PDO, con funciones CRUD, de manejo básico de tablas, transacciones, conversión de formatos y debugging.

Se instancia pasando 4 parámetros:

1. Usuario
2. Contraseña
3. Url o IP del host
4. Nombre de la base de datos.

```php
$db = new DB("root","root","localhost","todo");
```

## CRUD

- `find()`
- `findAll()`
- `save()`
- `delete()`
- `count()`
- `lastId()`
- `sql()`

### find()

Recibe como mínimo un parámetro, que indica la tabla sobre la que se hará la búsqueda (`SELECT * FROM`), el segundo parámetro es opcional, y debe estar dado por un arreglo que contenga los pares de llave-valor que indicarán las condiciones de búsqueda (`WHERE`) todas unidas mediante una conjunción (`AND`).

Devuelve el primer registro que devuelva el resultado (`LIMIT 1`) en forma de un arreglo asociativo.

```php
$db->find("tasks"); //Devuelve el primer registro de la tabla "tasks"
$data = [
  'id' => 1
];
$db->find("tasks",$data); //Devuelve el primer registro que coincida con el id = 1;
$db->find("tasks", ['id'=>1])//Devuelve el mismo resultado que el anterior.
```

Es __importante__ notar que las llaves del arreglo que se pasa como segundo parámetro, deben coincidir con los nombres de las columnas existentes en la tabla, caso contrario generará un error.

### findAll()

Funciona de manera similar al método `find()`, recibiendo dos parámetros, el segundo opcional, con la diferencia de que devuelve todos los registros obtenidos en el resultado, en forma de un arreglo indizado de arreglos asociativos.

```php
$db->findAll("tasks"); //Devuelve todos los registros de la tabla "tasks"
$data = [
  'date' => "29/11/13"
];
$db->findAll("tasks",$data); //Devuelve todos los registros coincida con date = "29/11/13";
$db->findAll("tasks", ['date' => "29/11/13"]); //Devuelve el mismo resultado que el anterior.
```

### save()

Recibe 2 parámetros, siendo el primero el nombre de la tabla, y el segundo un arreglo asociativo, el cual en este caso __no__ es opcional.

Si el arreglo pasado como segundo parámetro contiene la llave __id__, se considera como una actualización (`UPDATE`), en caso de que la llave no este presente se supone una inserción (`INSERT INTO`), es __importante__ que todas las columnas que no permitan valores nulos esten presentes en el arreglo dado.

```php
$db->save("tasks"); //Genera un "false"
$data = [
  'task'=>"Learn PDO",
  'date'=>"29/11/13"
];
$db->save("tasks", $data); //Crea un nuevo registro
$data = [
  'id' => 1
  'task'=>"Learn PDO",
  'date'=>"29/11/13"
];
$db->save("tasks", $data); //Actualiza el registro con id = 1
```

### delete()
Recibe 2 parámetros, el primero la tabla sobre la cual se trabajará (`DELETE FROM`), el segundo un arreglo con las condiciones para el borrado (`WHERE`) que se uniran de manera conjuntiva, este último parámetro es semi-opcional.

Es __importante__ notar que si no se envía el segundo parámetro, por default se asume un 1, ejecutandose un `DELETE FROM tabla WHERE 1`, eliminando __todos__ los registros de la tabla.

## Transacciones

- `begin()`
- `end()`
- `cancel()`

## Manejo básico de tablas

- `create()`
- `drop()`
- `truncate()`

## Conversión de formatos

- `toJson()`
- `getJson()`
- `toArray()`

## Debugging

- `debug()`
- `pretty()`
