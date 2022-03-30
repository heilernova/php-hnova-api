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

class Api
{
    /**
     * Ejecuta la API
     */
    public static function run(string $url):Response
    {
        $url = trim($url, '/');
        
        $route = Routes::find($url);
        $result = self::callActions($route);
        return new Response($result);
    }

    /**
     * 
     */
    private static function callActions(object $route):mixed
    {
        return "params error";
    }
}