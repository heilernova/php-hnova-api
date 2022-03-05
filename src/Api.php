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

use HNova\Api\Settings\AppConfig;
use HNova\Api\Routes\Router;
use HNova\Api\Settings\ApiConfig;
use HNova\Api\Settings\ApiJson;

class Api
{
    /**
     * Directorio principal del proyecto en ejecución.
     */
    private static string $dir = '';

    /**
     * Contiene un objeto que represent la configuraciones del api.json.
     */
    private static ApiConfig $config;

    /**
     * Alamacena la información de la api en ejecución
     */
    private static AppConfig $apiJson;

    /**
     * Inicia la app y procesa la URL
     * @param string $url URL de la petición HTTP realizada.
     */
    public static function run(string $url):Response
    {
        try {
            
            $url = strtolower(trim($url, "/"));
            
            /**
             * Obtenemos el direcotrio principal donde esta alojada en vendor de composer.
             * con el fin de obtener el directorio princial del proyecto.
             */
            foreach (get_required_files() as $require){
                if (str_ends_with( $require, "autoload.php")){
                    self::$dir = dirname($require, 2);
                    break;
                }
            }
            
            // Validamos que se encuentre el archivo api.json en caso de no encontrarse retoranamos un exception
            if (!file_exists(self::$dir . "/api.json")){
                throw new ApiException(['no se encontrol el archivo: ' . self::$dir . "/api.json"]);
            }
            
            // Cargamos el archivo api.json
            self::$apiJson = new AppConfig(json_decode(file_get_contents(self::$dir . "/api.json")));
            
            // Definimos la zona horaria
            date_default_timezone_set(self::getAppConfig()->getTimezone());

            if (empty($url)){
                if ($_SERVER['REQUEST_METHOD'] == "GET"){

                    /**
                     * En caso de que la URL este vacia y la petición sea de tipo GET retornamos 
                     * la páguina de inico de la API
                     */
                    require __DIR__.'./Views/homepage.php';
                    exit;
                }else{
                    return new Response("Not found - empy URL", 404);
                }
            }

            if (str_starts_with($url, "nv-panel")){

                $api = new ApiConfig("nv-panel", (object)[
                    "cors"=>(object)[
                        "origin"=> "*",
                        "methods"=>"*",
                        "headers"=>"*"
                    ]
                ]);

                $url = ltrim(str_replace("nv-panel", "", $url), "/");
                // Requerimos las rutas.
                require __DIR__."./Panel/Panel-routes.php";

            }else{


                if (self::getAppConfig()->getAppsCount() > 1){
                    
                    // extraemos el nombre de la api con el inicio de la URL.
                    $index_char = strpos($url, '/');
                    if ($index_char){
                        $name_api = $index_char ? substr($url,0, $index_char) : $url;
                        $url = substr($url, $index_char + 1);
                    }else{
                        $name_api = $url;
                    }
                    $api = self::getAppConfig()->getApps()->get($name_api);
                }else{
                    $api = self::getAppConfig()->getApps()->get();
                }
            }

            if (!$api){

                /** en caso de no encontrase la API en el api.json */
                return new Response("not - api", 404);
            }

            self::$config = $api;
            $api->loadCORS();
            $api->loadRoutes();

            $route = Router::find($url);

            if ($route){
                return $route->callAction();
            }else{
                return new Response("not - path - routes", 404);
            }


            return new Response("not - path", 404);

        } catch (\HNova\Api\ApiException $apiEx) {
            $apiEx->echo();
        } catch (\Throwable $th) {
            $ApiExcepton = new \HNova\Api\ApiException(["Error inesperado"], $th, "Error - inesperado");
            $ApiExcepton->echo();
        }
    }

    /**
     * Retorna el directorio principal del proyecto.
     */
    public static function getDir():string
    {
        return self::$dir;
    }

    /**
     * Retorna la configuración de la APP en uso.
     */
    public static function getAppConfig():AppConfig
    {
        return self::$apiJson;
    }

    /**
     * Retorna un objeto con la configuración del archivo api.json
     */
    public static function getConfig():ApiConfig
    {
        return self::$config;
    }
}