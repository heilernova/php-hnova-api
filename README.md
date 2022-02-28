# Phpnv-Api
Librería para el desarrollo de api rest en php

# Requerimientos
* Xammp
* PHP ^8.0.13
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
## Creación del entorno de trabajo
Abrir la consola en el directorio raiz y ejecutamos el siguiente comando.
```
composer nv install
```
Una ves ejecutado el script nos preguntara si el proyecto es de multi api, al darle si en multi api
se crearan ramas independiente para el acceso a la api.
Una vez escojido no se podra modificar el desarrollo del proyecto.

Importante actualizar el autoload de composer una vez terminado el proceso acterior
```
composer dump-autoload
```

## Definición de rutas de acceso

Las rutas se deben de definir en el archivo api-routes.php, en caso de utilizar multi api cada directorio tendra un archivo similar que inia con el nombre ejm. [ name-routes.php ]

### Definir una ruta que ejecuta una función.
```PHP
use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

Routes::get('test', function(){ return new Response('Hola mundo'); });
```

### Definir un ruta que ejecuta un controlador.
En este caso tendremos que definir un array con el namespace de la clase y el método a ejecutar, en caso de no definir el método buscara si la clase contiene un método que se llame igual que la peticion http [ get, post, put, pacth, delete ].
En caso no encontrar el método retornara un error 404.
```php
namespace Api\Http\Controllers;

use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

Routes::get('test', [TestController::class, 'get']);
```

### Definir una ruta con parametros.
Para definir un parametro se debera escribir entre parentecios el nombre del parametro y separado por ":" el tipo de dato esperado
int, string, float. en caso de no definirse se asignara por defecto se tomara como string.

en caso de que tipo de dato del parametro con concuerde con el tipo de dato esperado la función que ejecuta la acción retornara un error.
```PHP
use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

Routes::get('test/{name:string}', function(string $name){ return new Response("Hola: $name"); });
```

## Protección de rutas
Para protejer la rutas y evitar el acceso a cualquir dispositivo utilizaremos la clase guard que se genera en cada api, el archivo encontraremos en el direcotrio Http con el nombre Guard.php

El guard es una clase con métodos estaticos que retornan un callable para ser ejecutadas en la rutas antes de ralizar la acción
si callable retorna null se dara acceso a la ruta, en caso contrario retornara un objeto Response.

Ejemplo del método authenticate del Guard. todos las rutas que lo utilicen solo daran acceso a la ruta cuando el resultado del random_int sea igual a 1.
```php
<?php
namespace Api\Http;

use Phpnv\Api\Response;

class Guard
{
    public static function authenticate():callable
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
Se encuentran alojadas en la carpeta `Http/Controllers` de cada api quenerada.

### Utilidades
Cada clase controlador herdad la propiedades y métodos que la clase BaseController de cada api la cual
se encuentra alojdada en la carpeta Http/BaseController.php y que a su vez herdad todas las funcionalidades de
la clase `Phpnv\Api\Http\Controller`

Entre lo metodos que contiene por defector encontramos
#### getBody()
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
#### objeto database

Clase utilizada para interactuar con la base de datos, en ella encontraremos métodos que no ayudara a realizar consultas 
preparadas a fin de mejorar la seguridad evitando inyección sql, todos los métodos estan documentado para facilitar su uso.

Cabe recalcar que en esta clase el auto commit esta desactivado por lo tanto par hacer efectivos los cambios en la base de datos se debe ejecuta el commit de la clase database, "database->commit()".

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
#### Objecto ResponseBody.
Clase para implementar respuestas estructuras para el fronted.

Varibales de la clase:
* stutus: bool, sierve para responder si la solicitud de ejeucto rectamente.
* statusCode: int, en caso estadarizar respuesta pude utilizar código númerios
* data: contiene el valor a responder por defecto es null.
* message: es un objeto con que representa un mensaje del sistema para ser mostrador por le fronted, este objeto contiene: title, type, content, y type.

## Funcionalidades

### ApiFunctions
Clase estatica la cual contiene algunas funciones utilies para el desarrollo de al aplicación

### ClientInfo
Clase estatica la cual contiene 3 método:
* `ClientInfo::getIp()`: retorna la Ip del cliente.
* `ClientInfo::getPlatform()`: retorna la plataforma desde la cual se realiza la solicituda HTTP (windows, mac-os, ios, android).
* `ClientInfo::getDevice()`: retorna el tipo de dispositivo desde el cual se raliza la solicitud HTTP, pc, movil, table

## Frontend
Para validar las respuesta en Angular se recomienda utilizar un interceptor.
```ts
import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor,
  HttpErrorResponse,
  HttpResponse
} from '@angular/common/http';
import { catchError, map, Observable, throwError } from 'rxjs';
import { ApiMessage } from './interfaces/api-message';
import { ApiResponse } from './interfaces/api-response';
import { NvMessageBoxService } from './nv-message-box.service';

@Injectable()
export class NvApiInterceptor implements HttpInterceptor {

  urlDefault:string = "http://localhost/test-api"; // ruta de la api

  constructor() {}

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {

    let url:string = request.url;
    
    if (NvApiInterceptor.urlDefault){
      if (!url.match(/^http(s)?:\/\/(.*)$/)){
        url = `${this.urlDefault}/${url}`;
      }
    }

    let requestClone:HttpRequest<unknown> = request.clone({url});

    return next.handle(requestClone).pipe(
      map((event:HttpEvent<any>)=>{
          if (event instanceof HttpResponse ){
            let bodyObject:ApiResponse|null = this.getBodyResponse(event.body);

            if (bodyObject){

              if (bodyObject.status){
                
                if (bodyObject.message.content.length > 0){
                  // Si hay mensajes del servidor
                  console.log(bodyObject.message);
                }

                // En caso de ser true establecemos el body con el contenido de la data.
                event = event.clone({body: bodyObject.data});
              }else{
                
                // En caso de retornar false devolvemos un error
                throw new HttpErrorResponse({error: bodyObject.message, url: event.url ?? '', status: event.status});
              }
            }
          }
        return event;
      }),
      catchError((e:HttpErrorResponse)=>{
        
        let text:string = "Error inesperado";
        let ms = this.getMessage(e.error);
        if (ms){
          if (ms.content.length > 0){
            
            // Aqui el código para el  manejo del mensaje de la api.
            console.log(ms);

          }
        }else{
          if (typeof e.error == 'object'){

            text = e.message;
            console.log("Error con la petición HTTP");

          }else if (typeof e.error == "string"){
            text = e.error;
            
            console.log(text);
          }
        }
        
        return throwError(()=>e);
      })
    );
  }

  private getMessage(data:any):{title:string, content:(string|string[])[], type:number}|null{
    try {
      if ('title' in data && 'content' in data && 'type' in data){
        return data;
      }else{
        return null;
      }
    } catch (error) {
      return null;
    }
  }

  private getBodyResponse(data:any):{
    status:boolean,
    statusCode:number,
    message:{title:string, content:(string|string[])[], type:number},
    data:any
  }|null{
    try {
      if ('status' in data && 'statusCode' in data && 'message' in data && 'data' in data){
        return data;
      }else{
        return null;
      }
    } catch (error) {
      return null;
    }
  }
}
```

## Comandos de consola

* [ `composer nv intall` ] : Inicia el procesos de creación de los directorio y componentes necesarios para el funcionamiento de la api

* [ `composer nv c api (name)` ] : Crea una nueva nueva rama de la api en caso de que se halla seleccionado el tipo multi api

* [ `composer nv g c (name) (api name)` ] : Crea un controlador para asignar a una ruta. en caso de que sea multi api debe espificapar a cual api hace referencia.

* [` composer nv g m (name) (name_table) (api name)` ] : Crea un modelo para el manejo de datos. en caso de que sea multi api debe espificapar a cual api hace referencia.

* [ ` composer nv g o (name) (api name solo si es multi api)` ] : Crea un objecto o clase en la carpeta objects de cada api.

# Licencia

GNU GENERAL PUBLIC LICENSE

---
> Github [@heilernova](https://github.com/heilernova)
