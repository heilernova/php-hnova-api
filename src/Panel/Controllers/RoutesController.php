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
use HNova\Api\Funs;
use HNova\Api\Panel\PanelBaseController;
use HNova\Api\Response;
use HNova\Api\Scripts\Files;
use mysqli;

class RoutesController
{
    /**
     * Retorna un array con la información de la base de datos. 
     * */
    function get(){
        $config = $_ENV['api-rest']->config;

        $res = [];

        foreach ($config->routes as $key => $value){
            $value->path = $key;
            $res[] = $value;
        }

        return $res;
    }

    
}