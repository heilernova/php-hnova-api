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

class req
{
    /**
     * Retorna el contenido del body de la petición HTTP
     */
    public static function body():mixed{
        return $_ENV['api-rest']->request->body;
    }

    public static function file():array{
        return $_FILES;
    }

    public static function method():string{
        return $_ENV['api-rest']->request->method;
    }

    public static function content_type():string{
        return $_ENV['api-rest']->request->headers['Content-Type'] ?? '';
    }

    public static function ip():string{
        return $_ENV['api-rest']->request->ip;
    }

    public static function platform():string{   
        return $_ENV['api-rest']->request->platform;
    }

    public static function device():int{   
        return $_ENV['api-rest']->request->device;
    }
    
    public static function headers():array{
        return [];
    }    
}