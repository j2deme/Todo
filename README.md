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

Recibe como mínimo un parámetro `$table`, que indica la tabla sobre la que se hará la búsqueda (`SELECT * FROM`), el segundo parámetro es opcional, y debe estar dado por un arreglo que contenga los pares de llave-valor que indicarán las condiciones de búsqueda (`WHERE`) todas unidas mediante una conjunción (`AND`).

```php
$db->find("tasks"); //Devuelve el primer registro del resultado

$data = [
    'id' => 1
];
$db->find("tasks",$data); //Devuelve el primer registro que coincida con el id = 1;

$db->find("tasks", ['id'=>1])//Devuelve el mismo resultado que el anterior.
```

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
