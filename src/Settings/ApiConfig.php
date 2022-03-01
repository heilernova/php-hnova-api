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

class ApiConfig
{

    
    /**
     * Método constructro.
     * @param object $config Información decodificado del api.json
     */
    public function __construct(private object $config)
    {
        
    }

    public static function init():ApiConfig
    {
        $config = (object)[
            "name" => "Applicaction name",
            "user" => (object)["username"=>"admin", "password"=>""],
            "developers" => [
                (object)["name"=>"name", "email"=>"email@email"]
            ],
            "debug" =>false,
            "databases"=>(object)[],
            "apps"=>(object)[]
        ];

        return new ApiConfig($config);
    }

    /**
     * Retorna el nombre de la aplicación.
     */
    public function getName():string{
        return $this->config->name;
    }

    /**
     * Retrona los datos de usuario para el acceso.
     */
    public function getUser():ConfigUser{
        return new ConfigUser($this->config->user);
    }

    /**
     * Establece y retorna es estado del debug, establecerlo es opcional.
     * Si el debug es true, la api retornara el error al body de la cosulta HTTP.
     */
    public function debug(bool $bool = null):bool
    {
        if ($bool) $this->config->debug = $bool;
        return $this->config->debug;
    }

    public function getDatabases():ConfigDatabases
    {
        return new ConfigDatabases($this->config->databases);
    }

    /**
     * Retrona el número de apps usasa en la api.
     */
    public function getAppsCount():int
    {
        return count((array)$this->config->apps);
    }

    /**
     * Retorna la configuyraciona de las appas
     */
    public function getApps():ConfigApps
    {
        return new ConfigApps($this->config->apps);
    }

    public function salve():object
    {
        return $this->config;
    }

    public function getObject():object
    {
        return $this->config;
    }
}