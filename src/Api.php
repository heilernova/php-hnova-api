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

use Exception;
use HNova\Api\Settings\ApiConfig;
use ReflectionFunction;
use ReflectionMethod;

class Api
{
    /**
     * Ejecuta la API
     */
    public static function run(string $url):Response
    {
        $_ENV['api-time-start'] = time();
        try {
            
            $url = trim($url, '/');
            
            // establecemos los directorios donde esten alojado el código
            foreach (get_required_files() as $required){
                if (str_ends_with($required, 'index.api.php')){
                    $_ENV['api-dir-src'] = dirname($required);
                }
                
                if (str_ends_with($required, 'autoload.php')){
                    
                    $_ENV['api-dir'] = dirname($required, 2);
                    break;
                }
            }

            // Rutas por default de la API
            if (self::getConfig()->getRoutes()->getCount() == 1){
                
                require $_ENV['api-dir-src'] . "/routes.php";

                $routeConfig = self::getConfig()->getRoutes()->get();
                $routeConfig->loadCORS();
            }else{
                

            }

            $route = Routes::find($url);
            if ($route){
                $result = self::callActions($route);
            }else{
                Response::addMessage("Invalid route");
                Response::SetHttpResponseCode(404);
                $result = null;
            }
            return new Response($result);
        } catch(ApiException $th){
            Response::SetHttpResponseCode($th->getHttpResponseCode());

            Response::setMenssage($th->getMessageDeveloper());

            return new Response('error de ejecución');
        }catch (\Throwable $th) {
            Response::SetHttpResponseCode(500);
            return new Response("Error inesperado: \n" . $th);
        }
    }

    /**
     * Retorna la configuraciones del sistema.
     */
    public static function getConfig():ApiConfig
    {
        return new ApiConfig();
    }

    /**
     * 
     */
    private static function callActions(object $route):mixed
    {
        Response::SetHttpResponseCode(400);
        if ($route->paramsErrors){
            return "params error";
        }else{

            // Validasmo el timpo de acción.
            $action = $route->action;

            if (is_callable($action)){
               // Si es un callable 
               $reflection = new ReflectionFunction($action);
            }else{
                // Es un array con la clase controlador. donde el primer item es el namespace del controlador.
                $namespace = $action[0];
                $method = $action[1] ?? $route->method;

                // Inicializamos el controlador
                $controller = new $namespace();
                
                // En caso de que el método no exista en la clase controlador retornamos un error.
                if (!method_exists($controller, $method)){
                    throw new Exception("El método no existe en el controlador $namespace::$method");
                }

                $reflection = new ReflectionMethod($controller, $method);
            }

            $params = $route->params;
            $params_num = $route->paramsNum;

            $num_params = $reflection->getNumberOfParameters();
            $num_params_requered = $reflection->getNumberOfRequiredParameters();

            // En caso de que el metodo no requiera parametro y la urle contentga parametro retornamos un error.
            if ($num_params == 0 && $params_num > 0){
                Response::SetHttpResponseCode(400);
                Response::addMessage('Prametros incorrectos');
                return "incorrect parameters";
            }else{
                
                // Si el número de parametros solicitados es menor al número recibidos
                if ($num_params_requered <= $params_num){
                    
                    if ($reflection::class == ReflectionFunction::class){
                        return $reflection->invokeArgs($params);
                    }else{
                        return $reflection->invokeArgs($controller, $params);
                    }
                }else{
                    Response::addMessage('Prametros incorrectos');
                    Response::SetHttpResponseCode(400);
                    return "incorrect parameters";
                }
            }


            return $action();
        }
    }


}