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
use HNova\Api\Funs;
use HNova\Api\Panel\PanelBaseController;
use HNova\Api\Response;
use HNova\Api\Scripts\Files;

class AuthController extends PanelBaseController
{
    function post(){
        $data  = $this->getBody();
        $access = Api::getConfig()->getConfigData()->user;

        if (password_verify($data->username, $access->username)){
            if (password_verify($data->password, $access->password)){
                
                $token = Funs::generateToken(50);

                $dir = $_ENV['api-dir']. "/nv-panel";
                if (!file_exists($dir)) mkdir($dir);

                $file = fopen("$dir/.access-token.txt", file_exists("$dir/.access-token.txt") ? 'w' : 'a');
                fputs($file, $token);
                return $token;
            }else{
                Response::message()->addContent("Contraseña incorrecta");
            }
        }else{
            Response::message()->addContent("Usuario incorrecto");
        }
    }
}