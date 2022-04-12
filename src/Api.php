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
            $route = Routes::find($url);
            $result = 0;
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
                        } catch(\Throwable $th){

                            $params_text = [];
                            foreach ($params as $key=>$value){
                                $params_text = "$key =  $value : " . gettype($value);
                            }

                            throw new ApiException(
                                ['Error con el llamado de la función', 'parametros', $params_text],
                                $th
                            );
                        }
                    }else{
                        try {
                            return $reflection->invokeArgs($controller, $params);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $params_text = [];
                            foreach ($params as $key=>$value){
                                $params_text = "$key =  $value : " . gettype($value);
                            }

                            throw new ApiException(
                                ['Error con el llamado del método del controlador', 'parametros', $params_text],
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


}