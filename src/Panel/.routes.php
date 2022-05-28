<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Panel\Controllers;

use HNova\Api\Api;
use HNova\Api\ApiLog;
use HNova\Api\Panel\PanelGuard;
use HNova\Api\res;
use HNova\Api\Routes;
use HNova\Api\Routes\Methods;

# Ruta para la aplicación del Panel
Routes::get('', function(){
    return res::php(__DIR__.'/Html/index.php');
});

Routes::post ('auth', [AuthController::class]);
Routes::put  ('auth', [AuthController::class]);

// <<< Rutas de las base de datos >>>
Routes::get     ('db', [DbController::class, 'get']);
Routes::post    ('db', [DbController::class, 'post']);
Routes::post    ('db/test-connection', [DbController::class, 'test']);
Routes::put     ('db/:name', [DbController::class, 'put']);
Routes::delete  ('db/:name', [DbController::class, 'delete']);

###### RUTAS PAR VER LA OCNDIFURACION DE LAUTA #######
Routes::get('routes', [RoutesController::class, 'get']);
Routes::put('routes/:id', []);

Routes::patch('routes/:id/disable', []);
Routes::patch('routes/:id/enable', []);

## Errores
Routes::get('errors', [ErrorsController::class, 'get']);

## Logs
Routes::get('logs/request', function(){
    return ApiLog::getRequest();
});

// Elimina todos los registros
Routes::delete('logs/request', function(){
    $dir = Api::getDir() . "/Bin/request.log";
    if (file_exists($dir)) unlink($dir);
    return res::json(true);
});

# assents
Routes::get('assets/:name', function(string $name){

    $path = __DIR__ . "/Html/assets/$name";

    if (file_exists($path)){
        return res::file($path);
    }else{
        return res::send('not-found')->status(404);
    }

});