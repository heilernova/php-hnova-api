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