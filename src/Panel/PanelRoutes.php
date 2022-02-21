<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Panel\Controllers;

use Phpnv\Api\Panel\PanelGuard;
use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

Routes::parents('nv-panel/api');
Routes::post('auth', [AuthController::class]);

Routes::parents('nv-panel/api', [PanelGuard::authenticate()]);
Routes::get('user', [UserController::class, 'getUsername']);
Routes::put('user/change-username', [UserController::class, 'changeUsername']);
Routes::put('user/change-password', [UserController::class, 'changePassword']);

Routes::get('errors', [ErrorsController::class]);
Routes::delete('errors', [ErrorsController::class, 'clear']);

