<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HNova\Api\Routes;

use HNova\Api\ApiException;


class Router
{
    /**
     * Alamacena la rutas de la API
     * @var Route[]
     */
    private static array $routes = [];

    /** Almacena le método HTTP de la petición realizada */
    private static string $method = '';

    /**
     * Agrega una ruta al router
     */
    public static function addRoute(Route $route):void
    {
        try{
            self::$routes[] = $route;
        } catch (\Throwable $th){
            throw new ApiException(['Error al agregar una ruta en el router'], $th);
        }
    }

    /**
     * Obtiene el método http resquest del cliente en minuscula.
     */
    public static function getMethod():string
    {
        return self::$method;
    }

    /**
     * Busca la route corresponiente a la url ingresada por el usuario, en caso de no encontrase la ruta retorna null. 
     */
    public static function find(string $url):?Route
    {
        self::$method = strtolower($_SERVER['REQUEST_METHOD']);
        $url = trim($url, '/');
        $url_items = explode('/', $url);
        $url_num = count($url_items);

        $routes = array_filter(self::$routes, function(Route $route) use ($url, $url_items, $url_num){
            return $route->validRoute($url, $url_items, $url_num);
        });

        uasort($routes, function($a, $b){ return (strcmp($b->pathActionsCount, $a->pathActionsCount)); });
        return array_shift($routes);
    }

    /**
     * Retorna un array de las rutas establecidas.
     * @return Route[]
     */
    public static function getRoutes():array
    {
        return self::$routes;
    }
}