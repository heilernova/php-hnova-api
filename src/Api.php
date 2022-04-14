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
use HNova\Api\Data\Database;
use HNova\Api\Settings\ApiConfig;
use HNova\Api\Settings\Routes\ConfigRoute;
use ReflectionFunction;
use ReflectionMethod;

class Api
{
    private static $_routeConfig;
    /**
     * Ejecuta la API
     */
    public static function run(string $url):Response
    {
        $_ENV['api-time-start'] = time();
        try {
            
            $url = trim($url, '/');
            
            $_ENV['api-http-request-url'] = $url;
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
            if (str_starts_with($url, "nv-panel")){
                $url = str_replace("nv-panel", "", $url);
                require __DIR__ . './Panel/panel-routes.php';
                $routeConfig = new ConfigRoute((object)['cors'=>(object)['origin'=>'*', 'methods'=>'*', 'headers'=>'*']]);
            }else{

                if (self::getConfig()->getRoutes()->getCount() == 1){
                    
                    require $_ENV['api-dir-src'] . "/routes.php";
                    $routeConfig = self::getConfig()->getRoutes()->get();
                }else{
                    
                    $index_char = strpos($url, '/');
                    if ($index_char){
                        $name_api = $index_char ? substr($url,0, $index_char) : $url;
                        $route = self::getConfig()->getRoutes()->get($name_api);
                    }else{
                        $name_api = $url;
                        $routeConfig = self::getConfig()->getRoutes()->get();
                    }
                }
            }

            if ($routeConfig->disabled()){
                Response::SetHttpResponseCode(404);
                return new Response("path not access");
            }
            $routeConfig->loadCORS();
            self::$_routeConfig = $routeConfig;
            $route = self::routeFind($url);
            $result = 0;
            if ($route){
                $routeConfig->setPath($route);
                $result = self::callActions($route);
            }else{
                Response::addMessage("Invalid route");
                Response::SetHttpResponseCode(404);
                $result = null;
            }
            return new Response($result);

            
        } catch(ApiException $th){
            Response::SetHttpResponseCode($th->getHttpResponseCode());

            $body = "Error";
            if (self::getConfig()->getDebug()){
                Response::setMenssage($th->getMessageDeveloper());
                $body = $th->getError();
            }

            return new Response($body);
        }catch (\Throwable $th) {
            Response::SetHttpResponseCode(500);

            $body = "--";
            if (self::getConfig()->getDebug()) {
                Response::addMessage("Error inesperado de la API");
                $body = $th->getMessage();
            }
            return new Response("Error inesperado: $body");
        }
    }

    /**
     * Retorna la configuración de la ruta.
     */
    public static function getRouteConfig():ConfigRoute
    {
        return self::$_routeConfig;
    }

    /**
     * Retorna la conexión de la base de datos en caso de dejarse el null devolvera el labase de datos
     * @param string $db Nombre de la base de datos a la cual se desea conectar.
     * @param string $table Nombre de la tabla por defecto.
     */
    public static function getDatabase(string $db = 'default', string $table = null):Database
    {
        $db = Api::getConfig()->getConfigData()->databases->$db ?? null;
        if ($db){
            return new Database((array)$db->dataConnection, $table);
        }else{
            throw new ApiException(["No se encontro la configuración de la base de datos $db"]);
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
        if ($route->paramsErrors){
            Response::SetHttpResponseCode(400);
            return "params error";
        }else{

            // Validamos los canActive
            foreach ($route->canActive as $canActive){
                
                $res = $canActive();
                // echo json_encode($res); echo "\n";
                if ($res) return $res;
            }

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
                        try{
                            return $reflection->invokeArgs($params);
                        }catch(\ReflectionException $th){

                            $params_received = [];

                            foreach ($params as $key=>$value){
                                $params_received[] = '$' . "$key : " . gettype($value) . ' = ' . $value;
                            }

                            $parameters_expected = [];
                            foreach ($reflection->getParameters() as $value){
                                $default = "";
                                try {
                                    $default = " = " . json_encode($value->getDefaultValue());
                                } catch (\Throwable $th) {}

                                $parameters_expected[] = "$" . $value->getName() . " : " . ($value->getType() ?? 'mixed') . $default;
                            }

                            throw new ApiException(
                                [
                                    'Error con el llamado de la función', 
                                    'Parametros recibidos:', 
                                    $params_received,
                                    'Parametros esperados:'
                                ],
                                $th
                            );
                        }
                    }else{
                        try {
                            return $reflection->invokeArgs($controller, $params);
                        }catch (\ReflectionException $th) {
                            $params_received = [];

                            foreach ($params as $key=>$value){
                                $params_received[] = '$' . "$key : " . gettype($value) . ' = ' . $value;
                            }

                            $parameters_expected = [];
                            foreach ($reflection->getParameters() as $value){
                                $default = "";
                                try {
                                    $default = " = " . json_encode($value->getDefaultValue());
                                } catch (\Throwable $th) {}

                                $parameters_expected[] = "$" . $value->getName() . " : " . ($value->getType() ?? 'mixed') . $default;
                            }

                            throw new ApiException(
                                [
                                    'Error con el llamado del método del controlador',
                                    'Parametros recibidos (' . count($params_received) .') :',
                                    $params_received,
                                    'Parametros experados (' . count($parameters_expected) .') :',
                                    $parameters_expected 
                                ],
                                $th
                            );
                        }
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

    /**
     * Buscar la ruta correspondiente a la url ingresada
     * @return null|object
     */
    public static function routeFind(string $url)
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

                if ($valid){
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