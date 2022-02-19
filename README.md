# Phpnv-Api
Librería para el desarrollo de api rest en php

# Requerimientos
* xammp
* PHP 8.0.13 en adelante
* Instalar composer en el equipo

# Recomendaciones
Trabajar namespace en las clases, importante que el namespace con concuerde con ruta donde esta alojada la clase
para que el autoload de composer la pueda utilizar.

Instalar PHP Intelephense, cualquier otra extención para php que ayuda en la lectura y escritura de código

# Instalción

En el directorio raíz del proyecto abrimos la consala y ejecutamos el siguiente comando
```
composer require phpnv/api
```
Una vez ejecutado de descagaran los paquetes necesarios para el desarrollo de la api.

# Creación del proyecto
Antes de iniciar debemos agregar los scripts del Phpnv/Api al composer.json del proyecto.

Scrits
```JSON
"scripts":{
    "nv":"Phpnv\\Api\\Scripts\\Script::execute"
}
```
## Pasos para la creación del entorno de trabajo
* Paso 1
Abrimos la consola en el directorio raiz y ejecutamos el siguiente comando.
```
composer nv install
```
Una ves ejecutado el script nos preguntara si el proyecto es de multi api, al darle si en multi api
se crearan ramas independiente para el acceso a la api.
Una vez escojido no se podra modificar el desarrollo del proyecto.

Importante actualizar el autoload de composer
```
composer dump-autoload
```

## Definición de rutas de acceso

Las rutas se deben de definir en el archivo api-routes.php, en caso de utilizar multi api cada directorio tendra un archivo similar que inia con el nombre ejm. [ name-routes.php ]

* Definir una ruta que ejecuta una función.
```PHP
use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

Routes::get('test', function(){ return new Response('Hola mundo'); });
```

* Definir un ruta que ejecuta un controlador.
En este caso tendremos que definir un array con el namespace de la clase y el método a ejecutar, en caso de no definir
el método buscara si la clase contiene un método que se llame igual que la peticion http [ get, post, put, pacth, delete ].
En caso no encontrar el método retornara un error 404.
```php
namespace Api\Http\Controllers;

use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

Routes::get('test', [TestController::class, 'get']);
```

* Definir una ruta con parametros.
Para definir un parametro se debera escribir entre parentecios el nombre del parametro y separado por ":" el tipo de dato esperado
int, string, float. en caso de no definirse se asignara por defecto se tomara como string.
en caso de que tipo de dato del parametro con concuerde con el tipo de dato esperado la función que ejecuta la acción retornara
un error.
```PHP
use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

Routes::get('test/{name:string}', function(string $name){ return new Response("Hola: $name"); });
```

## Protección de rutas
Para protejer la rutas y evitar el acceso a cualquir dispositivo utilizaremos la clase guard que se genera en cada api, el archivo encontraremos en el direcotrio Http con el nombre Guard.php

El guard es una clase con métodos estaticos que retornan un callable para ser ejecutadas en la rutas antes de ralizar la acción
si callable retorna null se dara acceso a la ruta, en caso contrario retornara un objeto Response.

Ejemplo del método autenticado del Guard. todos las rutas que lo utilece solo daran acceso a la ruta cuando el resultado del random_int sea igual a 1.
```php
<?php
namespace Api\Http;

use Phpnv\Api\Response;

class Guard
{
    public static function autenticate():callable
    {
        return function(){
            if (random_int(1,2) == 1){
                return null; // Accesso permitido
            }else{
                // Denega el acceso y reponse no access.
                return new Response('No access - random_int no es 1',  401);
            }
        };
    }
}
```

Implementación del guard en la ruta
```php
namespace Api\Http\Controllers;

use Api\Http\Guard;
use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

Routes::get('test', [TestController::class, 'get'], [Guard::autenticates()]);
```

## Controladores
Son la clases que contienen las acciones a ejecutar por la ruta ingresada en la api.
Se encuentran alojadas en la carpeta Http/Controllers de cada api quenerada.

### Utilidades
Cada clase controlador herdad la propiedades y métodos que la clase BaseController de cada api la cual
se encuentra alojdada en la carpeta Http/BaseController.php y que a su vez herdad todas las funcionalidades de
la clase Phpnv\Api\Http\Controller

Entre lo metodos que contiene por defector encontramos
* getBody()

que retorna el contenido del body envido en la petición http, este contenido debe ser enviado en formato JSON, este contendo se decoficia en un objeto o un array asositaivo, en caso de que no se pueda decodificar el contenido retornara null.

Ejemplo de uso.
```php
<?php
namespace Api\Http\Controllers;

use Api\Http\BaseController;
use Phpnv\Api\Response;

class TestController extends BaseController
{
    /** Método constructor */
    function __construct()
    {
        parent::__construct();
    }

    function get():Response
    {
        $data = $this->getBody();
        return new Response($data);
    }
}
```
* objeto database
Clase utilizada para interactuar con la base de datos, en ella encontraremos métodos que no ayudara a realizar consultas 
preparadas a fin de mejorar la seguridad evitando inyección sql, todos los métodos estan documentado para facilitar su uso.

En esta clase el auto commit esta desactivado por lo tanto par hacer efectivos los cambios en la base de datos
se debe ejecuta el commit de la clase database, "database->commit()".

Ejmplo.
```php
<?php
namespace Api\Http\Controllers;

use Api\Http\BaseController;
use Phpnv\Api\Response;

class TestController extends BaseController
{
    /** Método constructor */
    function __construct()
    {
        parent::__construct();
    }

    function get():Response
    {
        $data = [
            'document'=>989989,
            'typeDocument'=>'CC',
            'name'=>'NOMBRE DE PRUEBA',
            'sex'=>'M',
            'cellphone'=>'9999999999',
            'email'=>'emial@email.com'
        ];
        
        $this->database->insert($data, 'tb_persons');
        $this->database->commit();
        $result = $this->database->select('tb_persons');
        
        return new Response($result);
    }
}
```



## Comandos de consola

* composer nv intall.
Inicia el procesos de creación de los directorio y componentes necesarios para el funcionamiento de la api

* composer nv c api (name).

crea una nueva nueva rama de la api en caso de que se halla seleccionado el tipo multi api

* composer nv g c (name) (api name).

crea un controlador para asignar a una ruta. en caso de que sea multi api debe espificapar a cual api hace referencia.

* composer nv g m (name) (name_table) (api name).

crea un modelo para el manejo de datos. en caso de que sea multi api debe espificapar a cual api hace referencia.

* composer nv g o (name) (api name solo si es multi api).
Crea un objecto o clase en la carpeta objects de cada api.