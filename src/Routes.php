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

use HNova\Api\Routes\Methods;

class Routes
{
    /**
     * @param string $path Ruta de acceso
     * @param Methods $method Método HTTP de acceso
     * @param string[]|callable $action Acción ajecutar 
     * @param callable[] $can_active
     */
    public static function add(string $path, Methods $method, array|callable $action, $canActive = [])
    {   
        $patFormat = $path;

        /**
         * Enlace para probrar caractes regulares
         * https://regex101.com/
         */
        $patterns[] = "/(:\w+)/i";
        $replacement[] = ':p';

        $path = preg_replace($patterns, $replacement, $path);

        if (!array_key_exists($path, $_ENV['api-rest']->routes->list)){
            $_ENV['api-rest']->routes->list[$path] = (object)[
                'format'    => $patFormat,
                'methods'   => []
            ];
        }

        $_ENV['api-rest']->routes->list[$path]->methods[$method->value] = (object)[
            'pathFormat'=>$patFormat,
            'action'    =>$action,
            'canActive' =>$canActive
        ];
    }

     /**
     * Agrega una ruta de acceso a una función expecifica de un controlador mediante el método HTTP GET.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function get(string $path, array|callable $action, array $canActive = [])
    {
        self::add($path, Methods::Get, $action, $canActive);
    }

     /**
     * Agrega una ruta de acceso a una función expecifica de un controlador mediante el método HTTP POST.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function post(string $path, array|callable $action, array $canActive = [])
    {
        self::add($path, Methods::Post, $action, $canActive);
    }

    /**
     * Agrega una ruta de acceso a una función expecifica de un controlador mediante el método HTTP PUT.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function put(string $path, array|callable $action, $canActive = [])
    {
        self::add($path, Methods::Put, $action, $canActive);
    }

     /**
     * Agrega una ruta de acceso a una función expecifica de un controlador mediante el método HTTP PATCH.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function patch(string $path, array|callable $action, $canActive = [])
    {
        self::add($path, Methods::Patch, $action, $canActive);
    }

     /**
     * Agrega una ruta de acceso a una función expecifica de un controlador mediante el método HTTP DELETE.
     * @param string                                $path url a por la cual se accedera al controlador.
     * @param array<namespace, method>|callable     $controller función o array del namespace y el metodo del controlador
     * @param callable[]                            $canActivate
     * @throws ApiException  Retorna error en caso de que la ruta ya este en uso, o en caso
     * de que el parametro controller este incompleto.
     */
    public static function delete(string $path, array|callable $action, $canActive = [])
    {
        self::add($path, Methods::Delete, $action, $canActive);
    }
}