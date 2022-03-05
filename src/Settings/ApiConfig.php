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
namespace HNova\Api\Settings;

use HNova\Api\Api;
use HNova\Api\ApiException;
use HNova\Api\Data\Database;

class ApiConfig
{
    /**
     * @param string $name Nombre de la API
     * @param object $config Configuraciones de la API
     */
    public function __construct(private string $name, private $config = null)
    {
    }

    /**
     * Retorna el nombre de la app
     */
    public function getName():string 
    { 
        return $this->name;
    }


    /**
     * Retorna le namespace de la app
     */
    public function getNamespace():string
    {
        return $this->config->namespace ?? "";
    }

    /**
     * Retorna la base de datos.
     * @param string|null $default_table Difine la tabla por defecto para ejecutar las cosulta SQL.
     * @return null|Database Retorna el objeto Database o null en caso de que no tenga una base de datos establecida.
     * @throws ApiException retorna una exception en caso de no encontrarse las base de datos solicitada al api.json.
     */
    public function getDatabase(string $default_table = null):?Database
    {
        $db_name = $this->config->database;
        if ($db_name){
            $db = Api::getAppConfig()->getDatabases()->get($this->config->database);
            if (!$db){
                throw new ApiException(["No se encuentra la información de la base de datos [ $db_name ] solicitada."]);
            }

            if ($db->type != "mysql"){
                throw new ApiException(["Actualmento no se cuenta soporte para las bases de datos de MYSQL"]);
            }

            return new Database((array)$db->dataConnection);
        }else{
            return null;
        }
    }

    /**
     * Estable la base de datos por defecto a utilizar en la app.
     */
    public function setDatabase(string $name):void
    {
        $this->config->database = $name;
    }

    /**
     * Desactiva el acceso la aplicación
     */
    public function disable():void
    {
        $this->config->disable = true;
    }
    
    /**
     * Habilita el acceso la aplicación
     */
    public function eneble():void
    {
        $this->config->disable = false;
    }

    /**
     * Retorna la configuración de los cors
     */
    public function getCors():CorsConfig
    {
        return new CorsConfig($this->config->cors);
    }

    /**
     * Retorna un object con la infromación de la API
     */
    public function getInfo():object
    {
        return $this->config;
    }

    /**
     * Carga la rutas de la API
     */
    public function loadRoutes():bool
    {
        $name = $this->getNamespace();
        if ($name != ""){
            require Api::getDir() . "\\app\\$name\\$name-routes.php";
        }
        return true;
    }

    /**
     * Carga los CORS de la API
     */
    public function loadCORS()
    {
        try {
            $string_origin = $this->getCors()->origin()->getValueString();
            $string_headers = $this->getCors()->headers()->getValueString();
            $string_methods = $this->getCors()->methods()->getValueString();

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
                
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
                    header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                }
                
                exit(0);
            }
        } catch (\Throwable $th) {
            throw new ApiException(['Error al establecer los CORS'], $th);
        }
    }
}