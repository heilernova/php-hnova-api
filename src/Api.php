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

use HNova\Api\Settings\ApiConfig;
use HNova\Api\Settings\AppInfo;

class Api
{
    /**
     * Directorio principal del proyecto en ejecución.
     */
    private static string $dir = '';

    /**
     * Contiene un objeto que represent la configuraciones del api.json.
     */
    private static object $apiJson;

    /**
     * Alamacena la información de la api en ejecución
     */
    private static ApiConfig $api;

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
            self::$apiJson = json_decode(file_get_contents(self::$dir . "/api.json"));

            if (empty($url)){
                return new Response("Not found", 404);
            }

            if (str_starts_with($url, "nv-panel")){
                self::$api = new ApiConfig("nv-panel", (object)[]);
            }else{
                if (self::getConfig()->getAppsCount() > 1){
                    
                    // extraemos el nombre de la api con el inicio de la URL.
                    $index_char = strpos($url, '/');
                    if ($index_char){
                        $name_api = $index_char ? substr($url,0, $index_char) : $url;
                        $url = substr($url, $index_char + 1);
                    }else{
                        $name_api = $url;
                    }
                }else{
                    // self::getConfig()->getApps()
                }
            }


            return new Response("Hola mundo");

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
     * Retorna un objeto con la configuración del archivo api.json
     */
    public static function getConfig():AppInfo
    {
        return new AppInfo(self::$apiJson);
    }

    /**
     * Retorna un objeto con la información de la api en ejecución.
     */
    public static function getApi():ApiConfig
    {
        return self::$api;
    }
}