# HNova - Api

Librería de PHP para el desarrollo de apis

package de compsoser [Aquí](https://packagist.org/packages/hnova/api)

### Programas requeridos
* [XAMMP](https://www.apachefriends.org/es/download.html) Instalar la versión con php 8.1.0 o superior
* [Composer](https://getcomposer.org/download/)
* Editor de código puede ser [Visual Studio Code](https://code.visualstudio.com/);
### Programas opcionales
* [Postman](https://www.postman.com) : Applicación para testear las rutas de la API, pude realizar la descarga [aquí](https://www.postman.com/downloads/)

## Recomendaciones
En caso de utilizar [Visual Studio Code](https://code.visualstudio.com/), pude utilizar las siguienstes extenciones para facilitar el desarrollo y la comprensión del código.
* [Bracket Pair Colorizer 2](https://marketplace.visualstudio.com/items?itemName=CoenraadS.bracket-pair-colorizer-2)
* [Material Icon Theme ](https://marketplace.visualstudio.com/items?itemName=PKief.material-icon-theme)
* [PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client)

## Instalación

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

Debería quedar com se muestra el la imgamen.

<img src="img/ejemplo-script.png" width="600px">

### Script disponibles

* `composer nv install` o `composer nv i` => Crea los ficheros necesarios para el el funcionamiento de la API
* `composer nv g c (name)` => Crea un controldor.
* `composer nv g m (name)` => Crea un modelo.
* `composer nv g r (name)` => Crea una nueva ruta de acceso ejeml "admin/panel".

#### composer nv install / composer nv i
Ambos formatos son valitos "i" y "install", al ejecutar este comando creara la carpeta src donde alojara el código para gestion de la API.

Nota: En caso de la carpeta src este creada y tenga contenido no se ejecutara la instalación.

Importante: Una vez ejecutado el script de debe actualar el autoload de composer ejecutando el comando `composer dump-autoload`

#### composer nv g c (name)
Crea un controlador para ser accedito por la ruta. al ejecutar este comando creara un archivo con el nombre ingredado en caso de que el controlador ya se encuentre creado retornara un error informando que le nombre del controlador ya esta en uso

Nota: En caso de que de hallan creado mas una pai debera agregar en el script el nombre de la API a la cual se le crearar el controlador.

Informacion: el nombre del controladro se definira el formato "PascalCase" donde cada palabra inicia con una mayuscula, recomienda que los nombre se lo mas cortos posibles y que hagan referencias a su acción, por defecto se le agregara la palabra Controller al nombre. 

Ejemplos: `TestController`, `PersonsController` , `UsersLocksController`

#### composer nv g m (name) (table)
Crea un modelo para interactuar con la base de datos, el script costa del cuerpo princiapal `composer nv g m` y dos parametros `name` y `table`, en cado de haber mas de una API debe especificar el nombre a la cual se le creara el modelo.

Nota: Por defecto la conexión de la base de datos utilizara la de la API ejecuta. en caso de personalizar la conexión a la base datos utilize los flaz en en la base de datos --db:name_databse

## Creación del entorno de trabajo.
Para iniciar con el desarrollo de la api debe ejecutar el siguiente comando mediente consola ne la raiz del proyecto
```
composer nv i
```
El script preguntara que nombre que desae darle a la primera api por defecto de asigna el nombre de `app`, una vez terminado el proceso en la cartea `src` encontraremos los archivos PHP para el funcionamiento de la API y el el directorio princiapal un archivo json llamado `api.json` el cual contiene las configuraciones del sistema.

Importante actualizar el autoload de composer mediante el siguiente comando `composer dump-autoload`

## Configuración de api.json
En el fichero `api.json` contiene un objeto JSON con las configuraciones para el funcionamiento de la API con el siguiente formato.
```json
{
    "name": "Applicaction name",
    "timezone": "UTC",
    "user": {
        "username": "$2y$04$509QXMvRyLa6c6IkMt/D.exs/.S2UrQIvdl2QJ2pcr.GlYCU3QzrG",
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
    "routes": {
        "./": {
            "database": "test",
            "disable": false,
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
Las rutas de acceso a la API de deberan definir en el archivo del directorio `Routes` encontrado en la carpeta de cada src.

Para definir las ruta utilizaresmo la clase estaticasa `HNova\Api\Routes`, la cual contiene métodos para agrear rutas según el HTTP METHOD requerido.

### Descripion
Clase con métodos estaticos para agregar rutas de accesoo
```php
\HNova\Api\Routes::get(string $path, callable|array $action, array canActive);
```

### Parametros
* string : ruta de acceso
* callable|array : Acción a ejecutar , el array se agregar en el primer item el namespace de la clase y le segundo item el método a ejecturar.
* array : un array de callable los cuales se ejecutan hacer de la acción de la ruta, en caso de que uno de ellos retorne un valor diferente a null no se continuara con la acción de la ruta

### Ejemplops

En este archivo entraremos el siguiente código.

```php
/**
 * Ingrese aquí las rutas de acceso de la api
 */
namespace App\Controllers;

use HNova\Api\Routes;

Routes::get("test", function(){
  return "Hola mundo";
});

// El signo de "?" define el parametro con opcional en la URL
Routes::get("test/hello/:name?", function(string $name = ''){
  return "Hola como estas $name";
});

// Para hacer el llamdo a un cotrolador
Routes::get("test/controller", [NameController::class, 'get']);

// En caso de no definir el método a ejecutar buscar acorde al método de la ruta en este caso  "get"
Routes::get("test/controller", [NameController::class]);
```

## Protección de rutas
Para protejer la ruta y limitar el acceso a ellas al momento de definirla en agregaremos un al parametro canActive de los métodos de `HNova\Api\Routes` 
Para limitar el acceso a las rutas utilizaremos los guards los cuales estan alojado en la clase `AppGuards` alojada en el archivo `src/App/AppGuards.php`.

Esta es un clase estatica como métodos que retorna `callable` "funciones" para ejecutarse antes ingresar a la acción de la ruta, estas funciones deben retorna null o un resultado, donde null es permitir el acceso y Objeto es negar el acceso.

```php
/**
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
                Response::SetHttpResponseCode(401); // Código de estado para usuario si acceso
                return "No access";
            }
        };
    }
}
```

Para implementar el guard el ruta debebmos ingresar el llamdo del métodos de los guards a utilizar en el parametro canActivate, el cual espera un array con los guards a utilizar.

Nota: podremos utilizar mas de un gards en la misma ruta.
```php
Routes::get("test", function(){ return "Hola mundo tetst"; } , [AppGuards::authenticate()]);
```

## Utilidades integración con Angular
En caso de validar los mensaje del sistema en Angular, se recomienta utilizar un interceptor para el manejo de errores

Código para el interceptor
```ts

```