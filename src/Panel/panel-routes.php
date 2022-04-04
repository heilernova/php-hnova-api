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

use HNova\Api\Panel\PanelGuard;
use HNova\Api\Routes;
use HNova\Api\Routes\Methods;

Routes::add('auth', Methods::Post, [AuthController::class]);

Routes::add('db', Methods::Get, [DbController::class]);
Routes::add('db/{name:string}', Methods::Post, [DbController::class]);
Routes::add('db/{name:string}', Methods::Put, [DbController::class]);
Routes::add('db/{name:string}', Methods::Delete, [DbController::class]);