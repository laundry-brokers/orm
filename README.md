# orm
Un ORM para el manejo y mapeo de consultas SQL

## Instalación y configuración
Primero que nada lo primero que tienes que hacer para poder utilizar el orm es descargar el repositorio y esto lo pudes hacer de la siguiente manera:

~~~
git clone https://github.com/laundry-brokers/orm.git
~~~

Una vez que hayas descargado el archivo puedes incluir las dos carpetas que vienen dentro del Repositorio para poder utilizarlo
- Primero si tienes tu conexión de la siguiente manera:

~~~
<?php 
    include_once "./libs/database-connection/database.php";

    if(!isset($_SESSION)) {
        session_start();
    }

    $conexion = mysqli_connect("localhost", "user", "password") or die ("Problemas con la conexión de la base de datos");
    mysqli_select_db($conexion, "bd") or die ("Problemas al seleccionar la base de datos");
    mysqli_set_charset($conexion, "utf8");

    // Crear instancia de la clase Database
    $db = new DatabaseConnection($conexion);
?>
~~~

Con está configuración ya podrías utilizar la variable $db para poder utilizar el orm.

## Cómo utilizar los mapeos SQL

SELECT: Mostrar toda la lista de usuarios.
~~~
Ejemplo: Mostrar todos los usuarios SELECT * FROM users

Ejemplo con el ORM:
$db->table('users')
   ->select('*')
   ->get();
~~~

SELECT con WHERE
~~~
Supongamos que queremos realizar la siguiente consulta: Ejemplo: SELECT * FROM users WHERE id = '1';

Se realizaría así:
$db->table('users')
   ->where('id', 1)
   ->select('*')
   ->get();

Supongamos que queremos realizar la siguiente consulta: SELECT * FROM users WHERE status = 'activo';

Se realizaría así:
$db->table('users')
   ->where('status', 'activo')
   ->select('*')
   ->get();

Ahora si quisieramos realizar la siguiente consulta: SELECT * FROM users WHERE edad > '18';

Se realizaría así:
$db->table('users')
   ->where('edad', > '18')
   ->select('*')
   ->get();
~~~

SELECT para mostrar datos especificos
~~~
Ejemplo: Mostrar nombre y apellidos

Si quisieramos extraer solo el nombre y apellidos de la tabla users: SELECT name, last_name FROM users;

Se realizaría así:
$db->table('users')
   ->select(['name', 'last_name'])
   ->get();


Si quisieramos utilizar una consulta con un Alisa: SELECT name AS name_client FROM users:

Lo realizariamos así:
$db->table('users')
   ->select(['name AS name_client'])
   ->get();
~~~

Usando SELECT con AND: Para usar and dentro de una consulta podrías usarlo de la siguiente manera:

Para usar la consulta AND puedes usar el método where las veces que necesites realizar un AND.
~~~
    Si por ejemplo quisieramos usar una consulta para mostrar las ordenes con id = 2 y que también solo estén activas hariamos esto: SELECT * FROM orders WHERE purchaseid = '2' AND status = 'activo';

    Lo realizariamos así:
    $db->table('orders')
       ->where('purchaseid', '2')
       ->where('status', 'activo')
       ->select(*)
       ->get();
~~~

Ejemplo de uso del método whereBetween: esté método lo puedes usar cuando quieras realizar una consulta para buscar entre dos fechas etc.


~~~
    Por ejemplo si quisieramos buscar entre dos fechas en sql lo haríamos de la siguiente manera: SELECT * FROM domains WHERE expiration BETWEEN '2023-10-18' AND '2023-10-27' ORDER BY created_at DESC:

    Con el ORM lo haríamos así:
    $db->table('domains')
       ->whereBetween('expiration', '2023-10-18', '2023-10-27')
       ->orderBy('created_at', 'DESC')
       ->select('*')
       ->get();
~~~

Uso de INNER JOIN: El método JOIN lo puedes usar para poder unir dos o más tablas y enseguida te mostrare el ejemplo de su uso.

~~~
    Por ejemplo, digamos que necesitas unir la tabla users con la tabla domains para poder mostrar todos los dominios que pertenece a un usuario en particular con SQL lo harías de la siguiente manera:
    SELECT * FROM users INNER JOIN domains ON users.id = domains.user_id WHERE domains.user_id = '1':

    Usando el ORM:
    $db->table('users')
       ->join('domains', 'users.id', '=', 'domains.user_id')
       ->where('domains.user_id', '1')
       ->select('*')
       ->get();
~~~
