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

use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

Routes::parents('nv-panel/api');
Routes::post('auth', [AuthController::class]);

Routes::get('user', [UserController::class, 'getUsername']);
Routes::get('user/change-username', [UserController::class, 'changeUsername']);

Routes::get('errors', [ErrorsController::class]);
Routes::delete('errors', [ErrorsController::class, 'clear']);

