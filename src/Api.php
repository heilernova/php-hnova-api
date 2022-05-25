<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api;

use HNova\Api\Http\Request;
use HNova\Api\Http\Response;
use HNova\Api\Routes\ActiveRoute;

class Api
{
    /**
     * Configuración de la ruta a la que accede.
     */
    public static function config():ActiveRoute{
        return $_ENV['api-rest-objects']->route;
    }
    
    /**
     * Retrona el directorio que aloga app.php
     */
    public static function getDir():string{
        return $_ENV['app-src-dir'];
    }

    /**
     * Configuración de la respuesta.
     */
    public static function response():Response{
        return $_ENV['api-rest-objects']->response ?? new Response();
    }

    /**
     * Configuración de Solicitud
     */
    public static function request():Request{
        return $_ENV['api-rest-objects']->request ?? new Request();
    }
}