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


<img src="img/ejemplo-script.png" width="600px">

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

## Rutas
Las rutas de acceso a la api de deberan definir en el archivo `App-routes.php` encontrado en la carpeta de cada app, cabe recalcar el nombre del archivo hace referencia al nombre de app por lo tanto el nombre biene definido por el namespace mas -routes.php.

Para definir las ruta utilizaresmo la clase estaticasa `HNova\Api\Routes`, la cual contiene métodos para agrear rutas según el HTTP METOHOD requerido.

Ejemplo del archivo routes: allí podremos detallar el el nombre hacer referencia a la API en este caso de nombre App

<img src="img/ejemplo-routes.png"  width="600px">

En este archivo entraremos el siguiene código.

```php
/**
 * Ingrese aquí las rutas de acceso de la api
 */
namespace App\Controllers;

use App\AppGuards;
use HNova\Api\Response;
use HNova\Api\Routes;

Routes::get("test", function(){ return new Response("Hola mundo tetst"); });
Routes::get("test/saludo/{nombre:string}", function(string $nombre){ return new Response("Hola mundo  $nombre"); });
```

## Protección de rutas
Para limitar el acceso a las rutas utilizaremos los guards los cuales estan alojado en la clase `AppGuards` alojada en el archivo `app/App/AppGuards.php`.

Esta es un clase estatica como métodos que retorna `callable` "funciones" para ejecutarse antes ingresar a la acción de la ruta, estas funciones deben retorna null o un objeto `HNova\Api\Response`, donde null es permitir el acceso y Objeto es negar el acceso.

```php
/**
 * En esta clase puede agregar las restrcicioines de aceceso a las rutas del sistema
 * mediante el uso del los guard almacenso el calse estatica.
 * 
 * todos los método deverar retorna un callable
 */

namespace App;

use HNova\Api\Response;

class AppGuards
{
    public static function authenticate():callable
    {
        /**
         * el guard es un función que retunr null para dar acceso al sistema.
         * en caso de retrona un Response no se podra accesder al controlador de la ruta.
         *
         * en este caso todas la ruta que implemento el el guard authenticate dependerar de que el
         * randim_int sea igual a 1 para acceder al controlador asignado a la ruta. 
         */
        return function(){
            if (random_int(1,2) == 1){

                // Retornamos null para que dar acceso.
                return null;
            }else{

                // Retornameos un Response para negar el acceso.
                return new Response('No access',  401);
            }
        };
    }
}
```

Para implementar el guard el ruta debebmos ingresar el llamdo del métodos de los guards a utilizar en el parametro canActivate, el cual espera un array con los guards a utilizar.

Nota: podremos utilizar mas de un gards en la misma ruta.
```php
Routes::get("test", function(){ return new Response("Hola mundo tetst"); } , [AppGuards::authenticate()]);
```

### Ruta con parámetros
Lo parametros se asigna entre llaves el nombre del parametro y serparado por ":" el tipo de dato esperado por defecto se un string ejemplo : "test/{name:string}" , "test/{year:int}"; los tipos de datos que soporta son enteros, decimales y strings (int, float, string)

Importante que los parametros de la funcion ya sean de callable o la clse controlador concuerden con el nombre y el tipo de parametro esperado en la función o métohod

```php
Routes::get("name/{name:string}", function(string $name){ return new Response("Hola $name"); });
Routes::get("year/{year:int}", function(int $year){ return new Response("El año es: $name"); });

// Retornaria un error porue el parámetro de la URL es de tipo string y el parámetro de la funcion es int.
Routes::get("error/{num:string}", function(int $num){ 
    $num++;
    return new Response("Numero mas 1; $num"); 
});
```
#### LLamado a un controlador.
Para asignarle un controlador a la ruta ingresaremos en el parámetro action un array con el nombre de la clase y el método a ejecutar.

En caso de no definir el método de la clase a ejecutar por defector buscara el método que concuerde la el tipo de la petición HTTP realizada (get, post, delete, put, patch).s

Nota: para obtener le nombre de la clase puese utiliar ::class

Error: retorna error 404 en caso de que el método no se encutre en la clase
```php
Routes::get("test/hello", [TestController::class, "hello"]);

// Buscara el método post de la clas TesController
Routes::post("test/hello", [TestController::class]);
```
