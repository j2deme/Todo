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
//Devuelve el primer registro de la tabla "tasks"
$db->find("tasks");
$data = [
  'id' => 1
];
//Devuelve el primer registro que coincida con el id = 1
$db->find("tasks",$data); 

//Devuelve el mismo resultado que la consulta anterior
$db->find("tasks", ['id'=>1]);
```

Es __importante__ notar que las llaves del arreglo que se pasa como segundo parámetro, deben coincidir con los nombres de las columnas existentes en la tabla, caso contrario generará un error.

### findAll()

Funciona de manera similar al método `find()`, recibiendo dos parámetros, el segundo opcional, con la diferencia de que devuelve todos los registros obtenidos en el resultado, en forma de un arreglo indizado de arreglos asociativos.

```php
//Devuelve todos los registros de la tabla "tasks"
$db->findAll("tasks");
$data = [
  'date' => "29/11/13"
];
//Devuelve todos los registros coincida con date = "29/11/13"
$db->findAll("tasks",$data);

//Devuelve el mismo resultado que la consulta anterior
$db->findAll("tasks", ['date' => "29/11/13"]); 
```

### save()

Recibe 2 parámetros, siendo el primero el nombre de la tabla, y el segundo un arreglo asociativo, el cual en este caso __no__ es opcional.

Si el arreglo pasado como segundo parámetro contiene la llave __id__, se considera como una actualización (`UPDATE`), en caso de que la llave no este presente se supone una inserción (`INSERT INTO`), es __importante__ que todas las columnas que no permitan valores nulos esten presentes en el arreglo dado.

```php
//Devolvería false, porque no se pasa el segundo parámetro
$db->save("tasks");

$data = [
  'task'=>"Learn PDO",
  'date'=>"29/11/13"
];

//Crea un nuevo registro
$db->save("tasks", $data);

$data = [
  'id' => 1
  'task'=>"Learn PDO",
  'date'=>"29/11/13"
];

//Actualiza el registro con id = 1
$db->save("tasks", $data);
```

### delete()
Recibe 2 parámetros, el primero la tabla sobre la cual se trabajará (`DELETE FROM`), el segundo un arreglo con las condiciones para el borrado (`WHERE`) que se uniran de manera conjuntiva, este último parámetro es semi-opcional.

Es __importante__ notar que si no se envía el segundo parámetro, por default se asume un 1, ejecutandose un `DELETE FROM tabla WHERE 1`, eliminando __todos__ los registros de la tabla.

```php
//Elimina registro con id = 2
$db->delete("tasks",['id'=>2]);

//Elimina todos los registros - Se recomienda usar la función truncate()
$db->delete("tasks");
```

### count()

No contiene parámetros, y devuelve el número de filas contenidas en el resultado de la última consulta.

```php
//Primer registro que coincida con id = 1
$db->find("tasks",['id'=>1]);
echo $db->count(); //Devuelve 1

//Suponiendo que existan 10 registros
$db->findAll("tasks");
echo $db->count(); //Devuelve 10
```

### lastId()

Devuelve el __id__ del último registro insertado, no requiere de parámetros.

```php
//Suponiendo 10 registros consecutivos, con ids del 1 al 10
$db->save("tasks",['task' => "Test lastId() Function"]);
echo $db->lastId(); //Devolvería 11
```

### sql()

Permite manejar consultas más complejas, como _selects_ con condiciones unidas por mezclas de disyunciones (`OR`), conjunciones (`AND`) y negaciones (`NOT`), o comparaciones de valores menores, mayores o diferentes a un rango o valor (`id < 5` o `id > 10` o `id <> 4`), inserciones, actualizaciones o borrados complejos, e incluso comandos DDL.

Recibe dos parámeros, el primero un _string_ conteniendo un _prepared statement_ (preferentemente), y el segundo (opcional), un arreglo de pares llave-valor que reemplazen los espacios en el _prepared statement_, si así corresponde.

```php
$data = [
  'min' => 2,
  'max' => 5
];

//Selecciona una columna específica con una condición dada por un rango
$db->sql("SELECT task FROM tasks WHERE id > :min AND id < :max",$data);

//Creación de una tabla - Se recomienda usar la función create()
$data = [
  'col1' => "id int PRIMARY KEY AUTO_INCREMENT",
  'col2' => "name varchar(50) NOT NULL",
  'col3' => "age int NOT NULL",
  'col4' => "sex varchar(1) DEFAULT 'H'"
];
$db->sql("CREATE TABLE :tabla (:col1,:col2,:col3,:col4)", $data);
```

## Transacciones

- `begin()`
- `end()`
- `cancel()`

Permiten el manejo de transacciones, y deben estar contenidas dentro de un __try-catch__.

```php
try {
  //Inicia la transacción
  $db->begin();
  //Inserciones, actualizacionesy borrados
  ...
  //Finaliza la transacción sino hubo problemas
  $db->end();
} catch(Exception $e){
  //Deshace los cambios si hubo algún problema
  $db->cancel();
  return $e->getMessage();
}
```

También es posible lanzar una excepción si alguna condición deseada no se cumple.

```php
try {
  //Inicia la transacción
  $db->begin();
  //Busca a un usuario específico
  $db->find("users",['username' => "j2deme", 'password'=> "12345"]);
  //No se encontró al usuario
  if($db->count() == 0){
    throw new MyException("Usuario no válido");
  }
  //Finaliza la transacción sino hubo problemas
  $db->end();
} catch(Exception $e){
  //Deshace los cambios si hubo algún problema
  $db->cancel();
  return $e->getMessage();
}
```

## Manejo básico de tablas

- `create()`
- `drop()`
- `truncate()`

### create()

Permite la creación de tablas, sino existen, en caso contrario no realiza la creación.

Recibe como entrada dos parámetros, el primero el nombre de la tabla, y el segundo (opcional) un arreglo conteniendo las definiciones de las columnas, en pares llave-valor, donde la llave indica el nombre de la columna y el valor indica tipo, restricciones y valores default.

En caso de que no se indique el segundo parámetro, por default se crea el campo __id__ entero, autonumérico como llave primaria (`id int PRIMARY KEY AUTO_INCREMENT NOT NULL`).

```php
$cols = [
  'id' => 'int PRIMARY KEY AUTO_INCREMENT NOT NULL',
  'name' => 'varchar(50) NOT NULL',
  'age' => 'int',
  'address' => 'text'
];
$db->create('contacts', $cols);
```
### drop()

Recibe un único parámetro, el cual indica el nombre de la tabla a eliminar. Antes de intentar la eliminación, se hace la verificación de que la tabla exista, caso contrario no se elimina nada. Si no se envía el parámetro necesario, devuelve `false`.

```php
//Elimina la tabla 'contacts' y todos sus datos
$db->drop("contacts");

//Devuelve false
$db->drop("tabla_no_existente");
```

### truncate()

Elimina todos los registros de la tabla indicada como parámetro, sino se indica parámetro devuelve `false`. A diferencia de ejecutar un comando `DELETE FROM tabla WHERE 1`, esta función ejecuta un `TRUNCATE TABLE tabla` que además de eliminar los registros, también reinicia los valores autonúmericos.

Usar `truncate()` es más rápido y eficiente computacionalmente que usar `delete()`.

```php
//Elimina todos los registros
$db->delete("contacts");

//Elimina todos los registros y reinicia contadores
$db->truncate("contacts");
```

## Conversión de formatos

- `toJson()`
- `getJson()`
- `toArray()`

### toJson()

Devuelve el resultado de la última consulta en formato JSON.

```php
//Para forzar el formato JSON en UTF-8
header('Content-type: application/json; charset=utf-8');

$db->findAll('tasks');
echo $db->toJson();
```

Devolvería por ejemplo:
```json
[
  {"id":1,"task":"Ense\u00f1ar a los alumnos de BDD PHP"},
  {"id":2,"task":"Publicar material de PHP"},
  {"id":3,"task":"Texto corregido"},
  {"id":4,"task":"Tarea de prueba correcta"}
]
```

### getJson()

Recibe como parámetro un `string` que contiene la dirección desde donde se obtendrá el archivo JSON, la cual puede ser un archivo JSON estático o un archivo PHP que produzca un JSON.

Puede recibir un segundo parámetro booleano opcional, que indica si se utiliza `curl` o `file_get_contents` para leer el archivo, por default se utiliza `curl`. Devuelve un `string` con el JSON obtenido o un `false` si hay algún error.

```php
$url = "https://api.github.com/users/j2deme/repos";

//Devuelve una string conteniendo la respuesta en JSON
$db->getJson($url);
```

La url de ejemplo muestra en formato JSON todos los repositorios relacionados a mi cuenta: [j2deme](https://api.github.com/users/j2deme/repos).

### toArray()

Recibe como parámetro un `string` conteniendo un arreglo codificado en JSON, y devuelve un arreglo asociativo análogo al JSON suministrado.

```php
$url = "https://api.github.com/users/j2deme/repos";
$db->toArray($db->getJson($url));
```

## Debugging

- `debug()`
- `pretty()`

### debug()

Muestra los parámetros pasados a la conexión generada mediante PDO, por la última consulta realizada, tales como el _prepared statement_ y sus parámetros. Útil cuando hay duda al utilizar la función `sql()`.

__No__ lleva ningún parámetro.

```php
$db->find("contacts",['id'=>1]);
$db->debug();
```

Devolvería:

```
SQL: [33] SELECT * FROM contacts WHERE id=:id 
Params:  1
Key: Name: [3] :id
paramno=0
name=[3] ":id"
is_param=1
param_type=2
```


### pretty()

Útil al momento de hacer debugging, con el propósito de imprimir en pantalla el resultado devuelto por una consulta, por default devuelve el resultado de la última consulta, sin embargo, puede recibir un parámetro opcional, como un objeto, variable o arreglo.

```php
$db->findAll("contacts");
$db->pretty();

$url = "https://api.github.com/users/j2deme/repos";
$db->pretty($db->toArray($db->getJson($url)));
```
