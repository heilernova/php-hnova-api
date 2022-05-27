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
use HNova\Api\req;
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

    function put(){
        $data = req::body();
        $_ENV['api-rest']->config->user = (object)[
            'username' => password_hash($data->username, PASSWORD_DEFAULT, ['cos'=>5]),
            'password' => password_hash($data->password, PASSWORD_DEFAULT, ['cos'=>5])
        ];

        ApiRoot::getConfig()->salve();
        return res::json(true);
    }
}