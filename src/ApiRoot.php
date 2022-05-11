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
use Reflection;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

class ApiRoot{

    /** 
     * Retorna el objeto de la configuración de la API del app.json
    */
    public static function getConfig(){
        return $_ENV['api-rest']->config;
    }

    /**
     * Retorna el directorio principal del proyecto.
     */
    public static function getDir():string{
        return $_ENV['api-rest']->dir;
    }

    /**
     * Ejecuta la API
     * @param string $url 
     */
    public static function run(string $url):ApiResponse{

        try {
            
            // Cargamos la las variables de entorno
            require __DIR__.'/root/environments.php';
            // return new ApiResponse($url);

            // Establecemos la URL
            $_ENV['api-rest']->request->url = trim($url, '/');
            
            $result = self::routeExecute();

        } catch (ApiException $ex) {

            $result = $ex;
            $_ENV['api-rest-exception'] = $ex;
        } catch (\Throwable $th) {
            
            $result =  new ApiException(['Error inesperado'], $th);
            $_ENV['api-rest-exception'] = $result;
        }
        return new ApiResponse($result);
    }

    /**
     * Buscar la configuración de la ruta.
     */
    private static function routeExecute():mixed{
        $url = (string)$_ENV['api-rest']->request->url;

        $routes_config = (array)$_ENV['api-rest']->config->routes;
        $route_select = null;
        foreach ($routes_config as $key => $value){
            if (str_starts_with($url, $key)){
                $value->baseURL = $key;
                $route_select = $value;
                break;
            }
        }

        if ($route_select == null){
            $route_select = $routes_config['./'];
            $route_select->baseURL = './';
        }

        if ($route_select == null) throw new  ApiException(['No se encontrol la configuracion de rauta el app.json']);
        
        if ($route_select->baseURL != './'){
            $url = ltrim(substr($url, strlen($route_select->baseURL)), '/');
        }

        $_ENV['api-rest']->routes->routeActive = $route_select;

        /////  Cargamos la configuración de la ruta. /////

        if ($route_select->disable){
            // En caso de que el acceso a ruta se encuentre bloqueado.
            Api::response()->httpResponseCode(503);
            return "route disable - ";
        }
        
        Api::config()->loadCORS();

        $url_items = explode('/', $url);
        $http_method = $_SERVER['REQUEST_METHOD'];
        
        $routes_file = str_replace('/', '-', ltrim($route_select->baseURL, './'));

        require Api::getDir() . "/Routes/$routes_file.routes.php";

        $rotes_valids = [];

        // Recorremos la rutas del sistema.
        foreach ($_ENV['api-rest']->routes->list as $key => $item){
            $valid = true;
            $key_items = explode('/', $key);
            $params = [];

            $paramsRequiredNum = 0;
            $paramsOptionalNum = 0;
            $key_resoucres = 0;
            $path_formats = explode('/', $item->format);

            foreach ($key_items as $index => $key_item){

                if ($key_item == ':p'){
                    // Parametro extricto
                    if (array_key_exists($index, $url_items)){

                        $format = explode(':', preg_replace('/[:,?]/', '', $path_formats[$index]));
                        $params[$format[0]] = $url_items[$index];


                        $paramsRequiredNum++;
                    }else{
                        // En caso no tener le item correcpondiente
                        $valid = false;
                        break;
                    }
                }else if ($key_item == ':p?'){
                    // Parametro apcional
                    $format = explode(':', preg_replace('/[:,?]/', '', $path_formats[$index]));
                    $params[$format[0]] = $url_items[$index] ?? null;

                    $paramsOptionalNum++;
                }else{
                    // Identificador de recurso
                    if ($key_item != ($url_items[$index] ?? null)){
                        $valid = false;
                        break;
                    }else{
                        $key_resoucres++;
                    }
                }
            }
        
            // Validación final
            if ($valid &&  ($paramsRequiredNum + $paramsOptionalNum)  >= (count($url_items) - $key_resoucres)){

                $rotes_valids[] = (object)[
                    'numKeys' => $key_resoucres,
                    'format'  => $item->format,
                    'methods' => $item->methods,
                    'params'  => $params
                ];
            }
        }

        if ($rotes_valids){
            uasort($rotes_valids, function($a, $b){ return (strcmp($b->numKeys, $a->numKeys)); });
            
            $route = array_shift($rotes_valids);


            // Validamos que soporte el método HTTP solicitado
            if (array_key_exists($http_method, $route->methods)){

                $route->methods[$http_method]->method =  $http_method;
                $route->methods[$http_method]->params =  $route->params;

                return self::routeCallAction($route->methods[$http_method]);
            }else{
                Api::response()->httpResponseCode(405);
                return "not allowed";
            }
        }else{
            // No existe la ruta
            Api::response()->httpResponseCode(404);
            return "not found";
        }
    }

    private static function routeCallAction(object $route):mixed{

        // Validamos los canactives
        foreach ($route->canActive as $value){
            $res = $value();
            if ($res){
                return $res;
            }
        }

        $action = $route->action;
        // Ejecutamos el script de la ruta
        if (is_callable($action)){

            $reflection = new ReflectionFunction($action);
        }else{
            // Es un array o un string
            $controller_namespace = $action[0] ?? '';
            $controller_method = $action[1] ?? $route->method;

            // Inicalizamos el controlador
            try {
                $controller =  new $controller_namespace();
            } catch (\Throwable $th) {
                throw new ApiException(['Error al inizalicar el controlador de la ruta: ' . $controller_namespace], $th);
            }

            if (!method_exists($controller, $controller_method)){
                throw new ApiException(["El método no existe en el controlador: ' . $controller_namespace::$controller_method"]);
            }

            $reflection =  new ReflectionMethod($controller, $controller_method);
        }

        $reflection_num_params = $reflection->getNumberOfParameters();
        $reflection_num_params_required = $reflection->getNumberOfRequiredParameters();
        $route_num_params = count($route->params); // Número de parametros de la ruta

        if ($reflection_num_params_required > $route_num_params){
            Api::response()->httpResponseCode(400);
            return "incorret num params";
        }else{

            // Extraemos los parametso

            $params = $route->params;

            // Validamos el tipo de parametro recivido con los espererados.
            $invalid_params = [];
            foreach ($reflection->getParameters() as $value){
                $name = $value->getName();
                if (array_key_exists($name, $params)){
                    
                    if ($value->isOptional() && $params[$name] == null){
                        $params[$name] = $value->getDefaultValue();
                    }else{

                        // Validamos si el tipo de valores
                        if ($value->getType() == "int" || $value->getType() == 'float'){
                            if (!is_numeric($params[$name])){
                                $value_param = $params[$name];

                                $invalid_params[] = "[TYPE ERROR] name: $name, Value: [$value_param], Type: " . $value->getType();

                            }
                        }
                    }
                }else{
                    $invalid_params[] = "[PARAM NO EXISTS] name: $name";
                }
            }

            if ($invalid_params){
                throw new ApiException([
                    'Error los tipos de los parametro recibido:',
                    $invalid_params
                ], null, null, 400);
            }

            if ($reflection::class == ReflectionFunction::class){
                return $reflection->invokeArgs($route->params);
            }else{
                return $reflection->invokeArgs($controller, $params);
            }
        }
    }
}