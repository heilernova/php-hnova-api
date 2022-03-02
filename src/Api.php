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
    private static AppConfig $api;

    /**
     * Inicia la app
     * @param string $url URL de la petición HTTP realizada.
     */
    public static function run(string $url):Response
    {
        try {
            $url = strtolower(trim($url, "/"));

            // Obtenemos el direcotrio principal dende esta alojada en vendor de composer.
            foreach (get_required_files() as $require){
                if (str_ends_with( $require, "autoload.php")){
                    self::$dir = dirname($require, 2);
                    break;
                }
            }

            // Validamos que se encuentre el archivo api.json
            if (!file_exists(self::$dir . "/api.json")){
                throw new ApiException(['no se encontrol el archivo: ' . self::$dir . "/api.json"]);
            }

            // Cargamos el archivo api.json
            self::$config = new ApiConfig(json_decode(file_get_contents(self::$dir . "/api.json")));


            self::$config->getApps()->get("app")->disable();
            if (empty($url)){
                if ($_SERVER['REQUEST_METHOD'] == "GET"){
                    require __DIR__.'./Views/homepage.php';
                    exit;
                }else{
                    return new Response("Not found", 404);
                }
            }

            if (str_starts_with($url, "nv-panel")){
                // self::$api = new AppConfig("nv-panel");
            }else{
                if (self::$config->getAppsCount() > 1){
                    
                    // extraemos el nombre de la api con el inicio de la URL.
                    $index_char = strpos($url, '/');
                    if ($index_char){
                        $name_api = $index_char ? substr($url,0, $index_char) : $url;
                        $url = substr($url, $index_char + 1);
                    }else{
                        $name_api = $url;
                    }
                    $api = self::$config->getApps()->get($name_api);
                }else{
                    $api = self::$config->getApps()->get();
                }
            }

            if (!$api){
                return new Response("not - api", 404);
            }
            self::$api = $api;
            $api->loadRoutes();

            $route = Router::find($url);

            if ($route){
                return $route->callAction();
            }else{
                return new Response("not - path", 404);
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
     * Retorna el direcotorio principal del proyecto.
     */
    public static function getDir():string
    {
        return self::$dir;
    }

    /**
     * Retorna al AppConfig en uso.
     */
    public static function getAppConfig():AppConfig
    {
        return self::$api;
    }

    /**
     * Retorna un objeto con la configuración del archivo api.json
     */
    public static function getConfig():ApiConfig
    {
        return self::$config;
    }

    // /**
    //  * Retorna un objeto con la información de la api en ejecución.
    //  */
    // public static function getApi():AppInfoClass
    // {
    //     return self::$api;
    // }
}