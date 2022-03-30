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
    public static function add(string $path, Methods $method)
    {
        header("content-type: application/json");
        
        $patFormat = $path;

        $patterns[] = "/({\w+})/i";
        $patterns[] = "/({(\w+:)})/i";
        $patterns[] = "/({(\w+:\w+)})/i";
        $patterns[] = "/({(\w+.*?:\w+)})/i";
        // $patterns[] = '/({(\w+?:\w+)})/i';
    
        $replacement[] = '{p}';
        $replacement[] = '{p}';
        $replacement[] = '{p}';
        $replacement[] = '{p?}';
        // $replacement[] = '{p?}';

        $path = preg_replace($patterns, $replacement, $path);
    
        $_ENV['api-routes'][] = (object)[
            'path'=>$path,
            'pathFormat'=>$patFormat,
            'method'=>$method->value
        ];
    }

    /**
     * Buscar la ruta correspondiente a la url ingresada
     * @return null|object
     */
    public static function find(string $url)
    {
        $url = trim($url, '/');
        $routes = $_ENV['api-routes'];

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
                        
                        // En caso de ser un parametro requerido, verificamos que lo tenga en la URL
                        if (!($url_item[$i] ?? null)){
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
                        
                        // En caso de no ser un parametro validamos que sea igual
                        if ($path_item_value != ($url_item[$i] ?? null)){
                            $valid = false;
                            break;
                        }
                        $item->keys++;
                    }
                    
                }

                $item->params = $params;
                $item->paramNum = substr_count($item->path, '{p}') +  substr_count($item->path, '{p?}');
                
                if ($valid) $carry[] = $item;
            };
            
            return $carry;
        });
        
        uasort($routes, function($a, $b){ return (strcmp($b->keys, $a->keys)); });
        return array_shift($routes);
    }
}