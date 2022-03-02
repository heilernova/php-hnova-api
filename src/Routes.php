<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 */
namespace HNova\Api;

use HNova\Api\Routes\Route;
use HNova\Api\Routes\Router;

class Routes
{
    /**
     * Almacenos los canActives para las rutas hijas.
     * @var callable[]|null
     */
    private static array|null $parentCanActivate =  null;
    private static string|null $parentPath =  null;

    /**
     * Agregar los parametros que heredaran la rutas definidas en adelantes
     * @param string $path
     * @param callable[] $canActivate
     */
    public static function parents(string $path, array $canActivate = null):void
    {
        self::$parentPath = $path;
        self::$parentCanActivate = $canActivate;
    }
    
    /**
     * Borra los parents
     */
    public static function parentsClear():void
    {
        self::$parentCanActivate = null;
        self::$parentPath = null;
    }

    /**
     * Agrega la ruta al router.
     */
    private static function add(string $path, array|string|callable $controller, string $method, array $canActivate = [])
    {
        try {
            
            // Agregamos los parents si los tienen.
            if (self::$parentPath) $path = self::$parentPath . "/$path";
            if (self::$parentCanActivate) $canActivate = array_merge(self::$parentCanActivate, $canActivate);

            $route = new Route($path, $controller, $method, $canActivate);
            Router::addRoute($route);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Agrega una ruta de acceso a una función expecifica de un controlador en le HTTP METHOD GET.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function get(string $path, array|callable $controller, array $canActivate = []):void
    {
        self::add($path, $controller,'get', $canActivate);
    }

    /**
     * Agrega una ruta de acceso a una función expecifica de un controlador en le HTTP METHOD POST.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function post(string $path, array|callable $controller, array $canActivate = []):void
    {
        self::add($path, $controller,'post', $canActivate);
    }

    /**
     * Agrega una ruta de acceso a una función expecifica de un controlador en le HTTP METHOD PUT.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function put(string $path, array|callable $controller, array $canActivate = []):void
    {
        self::add($path, $controller,'put', $canActivate);
    }


    /**
     * Agrega una ruta de acceso a una función expecifica de un controlador en le HTTP METHOD PATCH.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function patch(string $path, array|callable $controller, array $canActivate = []):void
    {
        self::add($path, $controller,'patch', $canActivate);
    }


     /**
     * Agrega una ruta de acceso a una función expecifica de un controlador en le HTTP METHOD DELETE.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function delete(string $path, array|callable $controller, array $canActivate = []):void
    {
        self::add($path, $controller,'delete', $canActivate);
    }
}