<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use HNova\Api\ApiException;
use HNova\Api\ApiResponse;
use HNova\Api\Http\Request;
use HNova\Api\Http\Response;
use HNova\Api\Routes\RouteActive;
use HNova\Api\Settings\ApiConfig;

try {
    $_ENV['api-rest'] = (object)[
        'timeStar'=> 0,
        'dir'=>'',
        'config' => (object)[],
        'routes' => (object)[
            'list'=>[],
            'routeActive' => null
        ],
        'request' => (object)[
            'date'      => date('Y-m-d h:i:s', time()),
            'headers'   => apache_request_headers(),
            'method'    => $_SERVER['REQUEST_METHOD'],
            'url'       => $url,
            'ip'        => \HNova\Api\Http\HttpFuns::getIp(),
            'platform'  => \HNova\Api\Http\HttpFuns::getPlatform(),
            'device'    => \HNova\Api\Http\HttpFuns::getDevice(),
            'body'      => file_get_contents('php://input'),
        ],
        'response'=>    (object)[
            'code'          => 200,
            'headers'       => ['nv-data'=>''],
            'message'       => ['title'=>null, 'content'=>[]],
            'contentType'   => 'json',
            'file'          => null,
            'body'          => ''
        ]
    ];


    $_ENV['api-rest-objects'] = (object)[
        'response' => new Response(),
        'request'  => new Request(),
        'route'  => new RouteActive(),
        'config' => new ApiConfig()
    ];
    
    // Establecemos los directorios donde esten alojado el código
    foreach (get_required_files() as $required){
        
        if (str_ends_with($required, 'app.php')){
            $_ENV['app-src-dir'] = dirname($required); // Dirrectorio de l codigo
    
            if (!file_exists($_ENV['app-src-dir'] . "/app.json")) throw new ApiException(['No se encontro el archivo app.json']);

            // Cargamos la configuracion del app.json
            $json = json_decode(file_get_contents($_ENV['app-src-dir'] . "/app.json"));

            if (is_null($json)) throw new ApiException(['No se pudo de condificar el archivo app.json']);

            $_ENV['api-rest']->config = $json;
        }
        
        if (str_ends_with($required, 'autoload.php')){
            
            $_ENV['api-rest']->dir = dirname($required, 2); // Guardamos el directorio principal
            break;
        }
    }
    
} catch (\Throwable $th) {
    throw new ApiResponse(['Error al establecer la variables de entorno'],$th);
}