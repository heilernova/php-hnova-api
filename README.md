# HNova - Api

Librería de PHP para el desarrollo de apis

package compsoser: https://packagist.org/packages/hnova/api

* `XAMMP` https://www.apachefriends.org/es/download.html Instalar la versión con php 8.0.13 o superior
* `Composer` https://getcomposer.org/download/

## Intalación

Para instalar la librería en tu proyecto debe ejecutar el siguiente comando en la consola de composer, aun no hay primara versión por lo tanto de debe instalar la versión en desarrollo con dev-main
```powershell
composer require hnova/api dev-main
```
Una vez terminada la ejecución del comando anterior se debe agregar los scripts al composer.json de su proyecto para acceder a los script de la libreria HNova/api.

Pegue el siguiente código el su archivo composer.json.
```json
"scripts": {
    "nv":"HNova\\Api\\Scripts\\Script::execute"
}
```

Debería quedar de la siguiete manera.


<img src="img/ejemplo-script.png" width="400px">

### Script disponibles

* `composer nv install` o `composer nv i` => Crea los ficheros necesarios para el el funcionamiento de la api
* `composer nv g c <name>` => Crea un controldor en la app
* `composer nv g m <name>` => Crea un modelo en la app
* `composer nv g api <name>` => Crea una nueva app de acceso a la api

## Creación del entorno de trabajo.
Para iniciar con el desarrollo de la api debe ejecutar el siguiente comando mediente consola ne la raiz del proyecto
```
composer nv i
```
El script preguntara que nombre que desae darle a la primera api por defecto de asigna el nombre de `app`, una vez terminado el proceso entraremos en la cartea `app` los archivos PHP para el funcionamiento de la api y el el directorio princiapal un archivo json llamado `api.json` el cual contiene las configuraciones del sistema.

Importante actualizar el autoload de composer mediante el siguiente comando `composer dump-autoload`

## Configuración de api.json
En el fichero `api.json` contiene un objeto JSON con las configuraciones para el funcionamiento de app con el siguiente formato.
```json
{
    "name": "Applicaction name",
    "timezone": "UTC",
    "user": {
        "username": "admin",
        "password": "$2y$04$509QXMvRyLa6c6IkMt/D.exs/.S2UrQIvdl2QJ2pcr.GlYCU3QzrG",
        "email": null
    },
    "developers": [
        {
            "name": "Name developer",
            "email": "email@email",
            "homepage": null
        }
    ],
    "debug": true,
    "databases": {
        "test": {
            "type": "mysql",
            "dataConnection": {
                "hostname": "localhost",
                "username": "root",
                "password": "",
                "database": "test"
            }
        }
    },
    "apps": {
        "app": {
            "namespace": "App",
            "disable": false,
            "dirResources": "",
            "database": "test",
            "cors": {
                "origin": null,
                "headers": null,
                "methods": null
            }
        }
    }
}
```

## Rutas de acceso
Las rutas de acceso a la api de deberan definir en el archivo `App-routes.php` encontrado en la carpeta de cada app, cabe recalcar el nombre del archivo hace referencia al nombre de app por lo tanto el nombre biene definido por el namespace mas -routes.php.

Para definir las ruta utilizaresmo la clase estaticasa `HNova\Api\Routes`, la cual contiene métodos para agrear rutas según el HTTP METOHOD requerido.

### Ejemplo
```php
namespace AppTest\Controllers;

use HNova\Api\Api;
use HNova\Api\Http\ResponseApi;
use HNova\Api\Response;
use HNova\Api\Routes;

/**
 * Definirmos una ruta que ejecuta una función
*/
Routes::get("test", function(){
    return new Response("Hola mundo");
});

/**
 * Definimos una ruta con parametros, 
 * para definir los parametros se de nombre entre llaves " { nombre_del_parametros }",
 * en caso de definir el tipo de datos obtneido {id_number:int} : soport int, float, string por defecto convierte a sctring 
 */
Routes::get("test/{name}", function(string $name){
    return new Response("Hola $name");
});

/**
 * Definiedo el tipo de los parametros, importante el tipo de parametros y le nombre asignado concuerdo con los esperado en
 * la función.
*/
Routes::get("test/{name:string}/{edad:int}", function(string $name, int $edad){
    $edad++;
    return new Response("$name tiene $edad años");
});

/**
 * Definimos una ruta que llama a un controlador seria una clase
 * En este caso debemos asignar un array donde el primer item es la clase y 
 * el segundo su método a ejecutar, en caso de no definirse el método por defecto buscar el motodo get, post, put, pacht, dependeinte 
 * HTTP METHOD definido.
*/
Routes::get("test/class", [Test::class, "hola"])

```