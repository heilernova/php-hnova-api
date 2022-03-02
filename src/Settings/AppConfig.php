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

class AppConfig
{
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
        return $this->config->namespace;
    }

    /**
     * Retorna la base de datos.
     * @return null|Database Retorna el objeto Database o null en caso de que no tenga una base de datos establecida.
     * @throws ApiException retorna una exception en caso de no encontrarse las base de datos solicitada al api.json.
     */
    public function getDatabase():?Database
    {
        $db_name = $this->config->database;
        if ($db_name){
            $db = Api::getConfig()->getDatabases()->get($this->config->database);
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


    public function loadRoutes():bool
    {
        $name = $this->getNamespace();
        require Api::getDir() . "\\app\\$name\\$name-routes.php";
        return true;
    }



}