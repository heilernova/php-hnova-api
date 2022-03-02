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
     */
    public function getDatabase():?Database
    {
        return Api::getConfig()->getDatabases()->get($this->config->database);
    }

    /**
     * Desactiva la aplicación
     */
    public function disable():void
    {
        $this->config->disable = true;
    }
    
    /**
     * Habilita la aplicación
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