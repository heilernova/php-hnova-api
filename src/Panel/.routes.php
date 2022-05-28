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

use HNova\Api\ApiLog;
use HNova\Api\Panel\PanelGuard;
use HNova\Api\Routes;
use HNova\Api\Routes\Methods;

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

// Routes::add('db', Methods::Get, [DbController::class]);
// Routes::add('db/{name:string}', Methods::Post, [DbController::class]);
// Routes::add('db/{name:string}', Methods::Put, [DbController::class]);
// Routes::add('db/{name:string}', Methods::Delete, [DbController::class]);

// Routes::add('db/{db:string}/table-info/{name:string}', Methods::Get, [DbTableController::class]);