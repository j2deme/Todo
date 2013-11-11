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

```php
$db->delete("tasks",['id'=>2]); //Elimina registro con id = 2
$db->delete("tasks"); //Elimina todos los registros - Se recomienda usar la función truncate()
```

### count()

No contiene parámetros, y devuelve el número de filas contenidas en el resultado de la última consulta.

```php
$db->find("tasks",['id'=>1]); //Primer registro que coincida con id = 1
echo $db->count(); //Devuelve 1
$db->findAll("tasks"); //Suponiendo que existan 10 registros
echo $db->count(); //Devuelve 10
```

### lastId()

Devuelve el __id__ del último registro insertado, no requiere de parámetros.

```php
//Suponiendo 10 registros consecutivos, con ids del 1 al 10
$db->save("tasks",['task' => "Test lastId() Function"]); //Una inserción, puesto que no se indica id
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
  'col1' => "name varchar(50) NOT NULL",
  'col2' => "age int NOT NULL",
  'col3' => "sex varchar(1) DEFAULT 'H'"
];
$db->sql("CREATE TABLE :tabla (id int PRIMARY KEY AUTO_INCREMENT,:col1,:col2,:col2)", $data);
```

## Transacciones

- `begin()`
- `end()`
- `cancel()`

Permiten el manejo de transacciones, y deben estar contenidas dentro de un __try-catch__.

```php
try {
  $db->begin(); //Inicia la transacción
  ... //Inserciones, actualizacionesy borrados
  $db->end(); //Finaliza la transacción sino hubo problemas
} catch(Exception $e){
  $db->rollback(); //Deshace los cambios si hubo algún problema
  return $e->getMessage();
}
```

También es posible lanzar una excepción si alguna condición deseada no se cumple.

```php
try {
  $db->begin(); //Inicia la transacción
  $db->find("users",['username' => "j2deme", 'password'=> "12345"]); //Busca a un usuario específico
  if($db->count() == 0){ //No se encontró al usuario
    throw new MyException("Usuario no válido");
  }
  $db->end(); //Finaliza la transacción sino hubo problemas
} catch(Exception $e){
  $db->rollback(); //Deshace los cambios si hubo algún problema
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
$db->drop("contacts"); //Elimina la tabla 'contacts' y todos sus datos
$db->drop("tabla_no_existente"); //Devuelve 'false'
```

### truncate()

Elimina todos los registros de la tabla indicada como parámetro, sino se indica parámetro devuelve `false`. A diferencia de ejecutar un comando `DELETE FROM tabla WHERE 1`, esta función ejecuta un `TRUNCATE TABLE tabla` que además de eliminar los registros, también reinicia los valores autonúmericos.

Usar `truncate()` es más rápido y eficiente computacionalmente que usar `delete()`.

```php
$db->delete("contacts"); //Elimina todos los registros
$db->truncate("contacts"); //Elimina todos los registros y reinicia contadores
```

## Conversión de formatos

- `toJson()`
- `getJson()`
- `toArray()`

### toJson()
### getJson()
### toArray()

## Debugging

- `debug()`
- `pretty()`

### debug()
### pretty()