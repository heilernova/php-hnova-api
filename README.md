# HNova - Api

Librería de PHP para el desarrollo de API's REST

package de compsoser [Aquí](https://packagist.org/packages/hnova/api)

### Programas requeridos
* [XAMMP](https://www.apachefriends.org/es/download.html) Instalar la versión con php 8.1.0 o superior
* [Composer](https://getcomposer.org/download/)
* Editor de código puede ser [Visual Studio Code](https://code.visualstudio.com/);
### Programas opcionales
* [Postman](https://www.postman.com) : Applicación para testear las rutas de la API, pude realizar la descarga [aquí](https://www.postman.com/downloads/)

## Recomendaciones
En caso de utilizar [Visual Studio Code](https://code.visualstudio.com/), pude instalar las siguienstes extenciones para facilitar el desarrollo y la comprensión del código.
* [Bracket Pair Colorizer 2](https://marketplace.visualstudio.com/items?itemName=CoenraadS.bracket-pair-colorizer-2)
* [Material Icon Theme ](https://marketplace.visualstudio.com/items?itemName=PKief.material-icon-theme)
* [PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client)

## Instalación

Para instalar la librería en tu proyecto debe ejecutar el siguiente comando en la consola de composer, aun no hay primara versión por lo tanto de debe instalar la versión en desarrollo con dev-main
```powershell
composer require hnova/api dev-main
```
Una vez terminada la ejecución del comando anterior se debe agregar los scripts al composer.json de su proyecto para acceder a los script de la libreria HNova\A pi.

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
* `composer nv g c (name)` => Crea un controlador.
* `composer nv g m (name)` => Crea un modelo.
* `composer nv g r (name)` => Crea una nueva ruta de acceso ejeml "admin/panel".

#### composer nv install / composer nv i
Ambos formatos son valitos "i" y "install", al ejecutar este comando creara la carpeta src donde alojara el código para gestion de la API.

Nota: En caso de la carpeta src este creada y tenga contenido no se ejecutara la instalación.

Importante: Una vez ejecutado el script de debe actualizar el autoload de composer ejecutando el comando `composer dump-autoload`

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
\HNova\Api\Routes::get(string $path, callable|array $action, array $canActive);
```

### Parametros
* string : ruta de acceso
* callable|array : Acción a ejecutar , el array se agregar en el primer item el namespace de la clase y le segundo item el método a ejecturar.
* array : un array de callable los cuales se ejecutan hacer de la acción de la ruta, en caso de que uno de ellos retorne un valor diferente a null no se continuara con la acción de la ruta

### Ejemplos

En este archivo entraremos el siguiente código.

```php
/**
 * Ingrese aquí las rutas de acceso de la api
 */
namespace App\Controllers;

use HNova\Api\{Routes, res, req};

Routes::get("test", function(){
  return res::json('Hola mundo');
});

// El signo de "?" define el parametro con opcional en la URL
Routes::get("test/hello/:name?", function(string $name = ''){
  return res::send("Hola como estas $name");
});

// Para hacer el llamdo a un cotrolador
Routes::get("test/controller", [NameController::class, 'get']);

// En caso de no definir el método a ejecutar buscar acorde al método de la ruta en este caso  "get"
Routes::get("test/controller", [NameController::class]);
```

## Protección de rutas
Para protejer la ruta y limitar el acceso a ellas al momento de definirla en agregaremos un al parametro canActive de los métodos de `HNova\Api\Routes` 
Para limitar el acceso a las rutas utilizaremos los guards los cuales estan alojado en la clase `AppGuard` alojada en el archivo `src/Guards/AppGuards.php`.

Esta es un clase estatica como métodos que retorna `callable` "funciones" para ejecutarse antes ingresar a la acción de la ruta, estas funciones deben retorna null o un resultado, donde null es permitir el acceso y Objeto es negar el acceso.

```php
/**
 * 
 * todos los método deverar retorna un callable
 */

namespace App\Guards;

use HNova\Api\res;
use HNova\Api\Response;

class AppGuard
{
   public static function authenticate():callable
    {
        /**
         * Guard is a function that returns null to give access to the system.
         * in case of returning a HNova\Api\Response, it will not be possible access the acction of the route
         *
         * In this case, all routes that implement guard::authenticate() deny access if the random_int function returns 2
         */
        return function(){
            if (random_int(1,2) == 1){

                // We return null to give access
                return null;
            }else{

                // We return a HNova\Api\Response to deny access
                return res::send('access denied')->status(401);
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

## Utilidades integradas

### Api
Clase estatica con información de la solicitud y respuesta HTTP, asi como configuración de ruta, contiene los siguientes metodos
#### Api::config()
Retorna un objeto con la configuración de la ruta en ejecución
#### Api::request()
Retorna un objeto con información de la dela solicitud HTTP del cliente
#### Api::response()
Retorna un objeto para configurar la respuesta a la solicitud HTTP

#### Clase res
`HNova\Api\res` clase estatica utilizada para retorna los resultados de las rutas

#### Clase req
`HNova\Api\req` clase estatica utilizada para obtener datos de la las request.

### Database
`HNova\Api\Database`, Clase para ejecutar consulta SQL en la base de datos cuenta con fucniones de ayuda par afacilitar a ejecutar consultas preparadas.
#### Database::insert($params, $tabla)
Esta función solicita dos valores, los parametrso a insertar y el nombre de la tabla a la caul se le insertaran los datos.

Ejemplo.

Tabla para MYSQL
```sql
drop table if exists `tb_computadores`;
create table `tb_computadores`
(
    `id` int primary key auto_increment,
    `date` datetime not null default current_timestamp,
    `description` varchar(40) not null,
    `marca` varchar(20) not null,
    `ref` varchar(20) not null,
    `ram` int not null,
    `alacenamiento` int not null,
    `stock` int not null,
    `precio` decimal(19,2) not NULL,
    `descuento` decimal(4, 4) not null default 0
);

insert into `tb_computadores` values
(default, default, 'ROG Zephyrus M16 GU603', 'ASUS', 'GU603HE-K8024T', 16, 516, 4, 9880000, default),
(default, default, 'ROG Zephyrus M16 GU60', 'ASUS', 'GU603HE-K8025T', 16, '1 Tera', 3, 6790000, default),
(default, default, 'ROG Strix G15 G513', 'ASUS', 'G511MT', 16, '1 Tera', 5, 13000000, default),
(default, default, 'Legion 7i 16" 6ta Gen - Storm Grey', 'LENOVO', '', 16, '1 Tera', 5, 9299900, default),
(default, default, 'ThinkBook 13s Gen 2 (13.3", Intel)', 'LENOVO', '20V90060LM', 16, '1 Tera', 1, 4699000, default),
(default, default, 'Laptop Lenovo ThinkBook 16p 2da Gen (16", AMD)', 'LENOVO', '20YM002CLM', 16, '1 Tera', 5, 7799000, default);

```
Tabla para PostgreSQL
```sql
drop table if exists "tb_computadores";
create table "tb_computadores"
(
    "id" serial primary key,
    "date" timestamp not null default now(),
    "description" varchar(40) not null,
    "marca" varchar(20) not null,
    "ref" varchar(20) not null,
    "ram" int not null,
    "alacenamiento" int not null,
    "stock" int not null,
    "precio" decimal(19,2) not null
    "descuento" decimal(4,4) not null default 0
);

insert into "tb_computadores" values
(default, default, 'ROG Zephyrus M16 GU603', 'ASUS', 'GU603HE-K8024T', 16, 516, 4, 9880000, default),
(default, default, 'ROG Zephyrus M16 GU60', 'ASUS', 'GU603HE-K8025T', 16, '1 Tera', 3, 6790000, default),
(default, default, 'ROG Strix G15 G513', 'ASUS', 'G511MT', 16, '1 Tera', 5, 13000000, default),
(default, default, 'Legion 7i 16" 6ta Gen - Storm Grey', 'LENOVO', '', 16, '1 Tera', 5, 9299900, default),
(default, default, 'ThinkBook 13s Gen 2 (13.3", Intel)', 'LENOVO', '20V90060LM', 16, '1 Tera', 1, 4699000, default),
(default, default, 'Laptop Lenovo ThinkBook 16p 2da Gen (16", AMD)', 'LENOVO', '20YM002CLM', 16, '1 Tera', 5, 7799000, default);
```


```php
use HNova\Api\Dadabase;

// Establecemos los datos de conexión
$data_connection = [
    'type' => 'mysql',
    'dataConnection' => [
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'test'
    ]
];

// Incializamos la clase con los datos de conexión
$db = new Database($data_connection);

// Definimos los parametros a insertar
// El nombre de la key deben hacer referencia al nombre de los campose en la base de datos.
$params = [
    'description'   => 'Laptop Lenovo ThinkBook 13x (13.3", Intel)',
    'marca'         => 'LENOVO',
    'refe'          => '20WJ001WLM',
    'ram'           => 16,
    'alacenamiento' => '512gb',
    'stock'         => 1,
    'precio'        => 7000000
];

$db->insert($params, 'tb_computadores');

```

#### Database::update($params, $condition, $tabla)
Función para actualizar los datos de una tabla consta de 3 parametros para su fucnionamiento: campos a modificar, codición para aplicar los cambios y nombre de la tabla

Ejemplo.
```php
use HNova\Api\Dadabase;

// Establecemos los datos de conexión
$data_connection = [
    'type' => 'mysql',
    'dataConnection' => [
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'test'
    ]
];

// Incializamos la clase con los datos de conexión
$db = new Database($data_connection);


// Campos a modiciar
$params = [
    'descuento' => 0.15
];

// Condifcion, en esta caso sera un array donde el primer item es el sql y el segundo los parametros

// La codicion es para todos los productos de marca LENOVO y que su stock este es 1
$condition = [
    'marca=:marca AND stock=:stock',
    [
        'marca' => 'LENOVO',
        'stock'  => 1
    ]
]

$db->update($params, $condition, 'tb_computadores');

```


#### Database::delete($condition, $tabla)
Función para eliminar registros en la base de datos.

Ejemplo.
```php
use HNova\Api\Dadabase;

// Establecemos los datos de conexión
$data_connection = [
    'type' => 'mysql',
    'dataConnection' => [
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'test'
    ]
];

// Incializamos la clase con los datos de conexión
$db = new Database($data_connection);

// Establecemos la condición
$condition = [
    'id=:id',
    [
        'stock'  => 1
    ]
]

$db->delete($condition, 'tb_computadores');
```

## Utilidades integración con Angular
En caso de validar los mensaje del sistema en Angular, se recomienta utilizar un interceptor para el manejo de errores

Código para el interceptor
```ts

```