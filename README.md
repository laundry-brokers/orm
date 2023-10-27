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

SELECT simple
~~~
Ejemplo: Mostrar todos los usuarios SELECT * FROM users

Ejemplo con el ORM:
$db->table('users')
   ->select('*')
   ->get();
~~~

~~~
Ejemplo: Mostrar un usuario por id = 1

Uso con el ORM:
$db->table('users')
   ->select('*')
   ->where('id', 1)
   ->get();
~~~

~~~
Ejemplo: Mostrar nombre y apellidos

Uso con el ORM:
$db->table('users')
   ->select(['name', 'last_name'])
   ->get();
~~~
