<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api;

use Phpnv\Api\Config\Apis\ApiInfo;
use Phpnv\Api\Http\Resources;
use Phpnv\Api\Routes\Router;

class Api
{
    private static string $dir = '';
    private static ApiInfo $currentApi;
    private static ApiConfig $config;

    /**
     * establece el directorio donde esta alojado los script de la api.
     */
    public static function setDir(string $dir):void
    {
        self::$dir = $dir;
    }

    /**
     * @param string $url enviada por el cliente
     * @param string $dir Ruta del direcctorio donde se ejecuta la api se recomiendo usar __DIR__
     */
    public static function run(string $url, string $dir)
    {
        self::$dir = $dir;
        try {
            // Limpiamos la url de los "/" en los extremo y la pasamos a minuscula.
            $url = strtolower(trim($url, '/'));

            // Cargamos la configuracion del api.json
            self::$config = new ApiConfig("$dir/api.json", $dir);

            if (empty($url)){
                require __DIR__.'/view/index.php';
            }else{
                require __DIR__ . '/Routes/list.php';
                Api::loadApi($url)->echo();
            }
        } catch (\Phpnv\Api\ApiException $apiEx) {
            $apiEx->echo();
        } catch (\Throwable $ex){
            $r = new \Phpnv\Api\ApiException(['Error inesperado.'], $ex,'Error - server');
            $r->echo();
        }
    }

    /**
     * Retorna el directorio dende se encuentra alogado los scripts de la api.
     */
    public static function getDir():string
    {
        return self::$dir;
    }

    /**
     * Retorna la configuración del archivo api.json
     */
    public static function getConfig():ApiConfig
    {
        return self::$config;
    }

    /**
     * Retorna la información de la api en ejecucion.
     */
    public static function getApi():ApiInfo
    {
        return self::$currentApi;
    }

    /**
     * Valida la url y ejecuta la api correspondiente.
     */
    private static function loadApi(string $url):Response
    {
        if (str_starts_with($url, 'nv-panel')){

            // En caso de que la url inicie por "nv-panel"
            Api::$currentApi = new ApiInfo(
                'nv-error',
                'Phpnv\\Errors','',
                '',
                (object)['origin'=>'*', 'headers'=>'*', 'methods'=>'*']
            );

            // Habilitamos los CORS para el acceso.
            ApiCors::load('*', '*', '*');

            require __DIR__.'/Panel/PanelRoutes.php';
        }else{
            
            // Verificamos si es multi api
            if (Api::getConfig()->isMultiApi()){

                // extraemos el nombre de la api con el inicio de la api.
                $index_char = strpos($url, '/');
                if ($index_char){
                    $name_api = $index_char ? substr($url,0, $index_char) : $url;
                    $url = substr($url, $index_char + 1);
                }else{
                    $name_api = $url;
                }

                $api = self::$config->getApis()->find($name_api);
            }else{
                $api = self::$config->getApis()->find('api');
            }

            // En caso de que no encuentre una api asociada a la url.
            if (!$api) return new Response("Not Found - api", 404);

            // Validamos que la api no se encuetre desactivada.
            if ($api->getDisable()) return new Response("api - disable", 404);
    
            self::$currentApi = $api;
    
            // Cargamos los CORS
            ApiCors::loadApi();
    
            // Valimos si se solicita un recurso.
            if (str_starts_with($url, 'resources/public/')){
                return Resources::public(substr($url, strlen('resources/public/')));
            }elseif(str_starts_with($url, 'resources/private')){
                return Resources::private(substr($url, strlen('resources/public/')));
            }
    
            self::getApi()->getLoadRoutes();
        }


        // Buscamos la ruta 
        $route = Router::find($url);
        
        if ($route){
            // Ejecutamos la acción de la ruta.
            return $route->callAction();
        }else{
            // En caso de no encontras la ruta retornamos un 404
            return new Response("Not Found - path", 404);
        }
    }
}