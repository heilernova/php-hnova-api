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
        $patterns[] = "/({\w+})/i";
        $patterns[] = "/({(\w+:)})/i";
        $patterns[] = "/({(\w+:\w+)})/i";
        $patterns[] = "/({(\w+[?]:\w+)})/i";
        $patterns[] = "/({\w+[?]})/i";
    
        $replacement[] = '{p}';
        $replacement[] = '{p}';
        $replacement[] = '{p}';
        $replacement[] = '{p?}';
        $replacement[] = '{p?}';

        $path = preg_replace($patterns, $replacement, $path);
    
        $_ENV['api-routes'][] = (object)[
            'path'      =>$path,
            'pathFormat'=>$patFormat,
            'method'    =>$method->value,
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

    /**
     * Buscar la ruta correspondiente a la url ingresada
     * @return null|object
     */
    public static function find(string $url)
    {
        $url = trim($url, '/');
        $routes = $_ENV['api-routes'] ?? [];

        $method = strtolower($_SERVER['REQUEST_METHOD']);

        $url_item = explode('/', $url);

        $routes = array_reduce($routes, function($carry, $item)use ($method, $url_item){
            static $carry = [];
            
            
            if ($item->method == $method && str_starts_with($item->path, $url_item[0])){
                
                $value = array_shift($url_item);
                
                $path_item = explode('/', $item->path);
                $path_format_items = explode('/', $item->pathFormat);
                
                // Retinarmos el primeros items de los arrays
                array_shift($path_item);
                array_shift($path_format_items);

                $item->keys = 0;
                $item->paramsErrors = [];
                $params = [];
                $valid = true;

                foreach ($path_item as $i=>$path_item_value){

                    if ($path_item_value == '{p}'){
                        
                        if (!array_key_exists($i,$url_item)){
                            $valid = false;
                            break;
                        }else{
                            $param_url = $url_item[$i];
                            $param_format = explode(':', preg_replace('/[{,},?]/', '',$path_format_items[$i]));

                            $type_param = $param_format[1] ?? 'string';

                            if ($type_param == 'int'){
                                if (is_numeric($param_url)){
                                    $params[$param_format[0]] = (int)$param_url;
                                }else{
                                    $item->paramsErrors[] = "incorrect parameter type (int) name: '$param_format[0]' Value: $param_url";
                                }
                            }else if ($type_param == 'float'){
                                if (is_float($param_url)){
                                    $params[$param_format[0]] = (float)$param_url;
                                }else{
                                    $item->paramsErrors[] = "incorrect parameter type (flat) name: '$param_format[0]' Value: $param_url";
                                }
                            }else{
                                // Si es un string
                                $params[$param_format[0]] = $param_url;
                            }
                        }
                    }else if ($path_item_value == '{p?}'){
                        
                        // En caso de ser un parametro opcional
                        $param_url = ($url_item[$i] ?? null);
                        $param_format = explode(':', preg_replace('/[{,},?]/', '',$path_format_items[$i]));
                        
                        $type_param = $param_format[1] ?? 'string';

                        if ($param_url){

                            if ($type_param == 'int'){
                                if (is_numeric($param_url)){
                                    $params[$param_format[0]] = (int)$param_url;
                                }else{
                                    $item->paramsErrors[] = "incorrect parameter type (int) name: '$param_format[0]' Value: $param_url";
                                }
                            }else if ($type_param == 'float'){
                                if (is_float($param_url)){
                                    $params[$param_format[0]] = (float)$param_url;
                                }else{
                                    $item->paramsErrors[] = "incorrect parameter type (flat) name: '$param_format[0]' Value: $param_url";
                                }
                            }else{
                                // Si es un string
                                $params[$param_format[0]] = $param_url;
                            }
                        }
                        
                    }else{

                        $item->keys++;
                        if ($path_item_value != ($url_item[$i] ?? null)){
                            $valid = false;
                            break;
                        }
                    }
                    
                }

                $item->params = $params;
                $item->paramsRequired = substr_count($item->path, '{p}');
                $item->paramsOptionals = substr_count($item->path, '{p?}');
                $item->paramsNum = ($item->paramsRequired + $item->paramsOptionals);
                
                $numParamsURL = count($url_item)  - $item->keys;
                $numParams = count($item->params);
                $item->paramsURL = $numParamsURL;
                

                if ($item->paramsNum >= $numParamsURL){

                    $valid = ($numParamsURL >= ($item->paramsNum - $item->paramsOptionals))
                    && ($numParamsURL >= $item->paramsRequired) 
                    && ($numParamsURL >= $item->paramsRequired);
                }else{
                    $valid = false;
                }
                
                if ($valid) $carry[] = $item;
            };
            
            return $carry;
        });

        if ($routes){

            uasort($routes, function($a, $b){ return (strcmp($b->keys, $a->keys)); });

            return array_shift($routes);
        }else{
            return null;
        }
        
    }
}