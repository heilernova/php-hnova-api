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
                Files::addFile($_ENV['api-dir']. "/nv-panel/.access-token.txt", $token);
                Files::loadFiles();
            }else{
                Response::addMessage("Contraseña incorrecta");
            }
        }else{
            Response::addMessage("Usuario incorrecto");
        }
    }
}