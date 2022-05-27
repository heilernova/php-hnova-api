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
use HNova\Api\ApiRoot;
use HNova\Api\Panel\Panel;
use HNova\Api\res;

class AuthController
{
    function post(){

        $data = Api::request()->getData();
        if (ApiRoot::getConfig()->authenticate($data->username, $data->password)){
            return res::json(Panel::generateToken());
        }else{
            return res::json(null)->addMessage("Credenciales incorrectas");
        }
    }   
}