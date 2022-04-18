<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Settings\Routes;

use HNova\Api\Api;
use HNova\Api\ApiException;
use HNova\Api\Data\Database;
use HNova\Api\Settings\HTTP\Cors;

class ConfigRoute
{
    /**
     * @param object $_data Objeto con la información de la ruta.
     * @param Cors|null $_cors El caso de personalizar la configuración de los CORS
     */
    public function __construct(private string $name, private object $_data, private ?Cors $_cors = null)
    {

        $this->_cors = new Cors($_data->cors);
    }

    public function loadRoutes():void
    {
        try {
            $this->loadCORS();
            if ($this->name != "nv-panel") require Api::getConfig()->getDir() . "/Routes/$this->name.php";
        } catch (\Throwable $th) {
            throw new ApiException([
                'Error al cargar las rutas',
                "No se encotron: src" . "/Routes/$this->name.php"
            ], $th);
        }
    }

    /**
     * Retorna uno objeto con los CORS
     */
    public function getCORS():Cors
    {
        return $this->_cors;    
    }

    /**
     * Retorna si la rutas esta desactivada.
     */
    public function disabled():bool
    {
        return $this->_cors->disable ?? false;
    }

    /**
     * Retorna la conexión de la base de datos.
     */
    public function getDatabase(string $table = null):Database
    {
        return Api::getDatabase($this->_data->database, $table);
    }

    /**
     * Retorna el nombre de la base de datos por default para la ruta.
     */
    public function getDatabaseName():string
    {
        return $this->_data->database;
    }

    /**
     * Carga los CORS de la ruta
     */
    public function loadCORS():void
    {
        try {
            $string_origin = $this->getCORS()->getOrigin()->getValueString();
            $string_headers = $this->getCORS()->getHeaders()->getValueString();
            $string_methods = $this->getCORS()->getMetods()->getValueString();

            if ($string_origin) header("Access-Control-Allow-Origin:  $string_origin");
            if ($string_headers) header("Access-Control-Allow-Headers: $string_headers");
            if ($string_methods) header("Access-Control-Allow-Methods: $string_methods");
            // ------------------------ CORS

            if (isset($_SERVER['HTTP_Origin'])) {
                header("Access-Control-Allow-Origin: {$_SERVER['HTTP_Origin']}");
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 86400');    // cache for 1 day
            }

            // Access-Control headers are received during OPTIONS requests
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])){
                    if ($string_methods) header("Access-Control-Allow-Methods: $string_methods");
                }
                
                if ($string_headers){
                    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
                        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                    }
                }
                
                exit(0);
            }
        } catch (\Throwable $th) {
            throw new ApiException(['Error al establecer los CORS'], $th);
        }
    }

    public function setPath($route):void
    {
        $this->_data->path = $route;
    }

    public function getData():object
    {
        return $this->_data;
    }
}