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
use Throwable;

class AppConfig
{
    
    /**
     * Método constructro.
     * @param object $config Información decodificado del api.json
     */
    public function __construct(private object $config)
    {
        
    }

    public static function init():AppConfig
    {
        $config = (object)[
            "name" => "Applicaction name",
            "timezone"=>"UTC",
            "user" => (object)["username"=>"admin", "password"=>"", "email"=>null],
            "developers" => [],
            "debug" =>false,
            "databases"=>(object)[],
            "apps"=>(object)[]
        ];

        return new AppConfig($config);
    }

    /**
     * Retorna el nombre de la aplicación.
     */
    public function getName():string{
        return $this->config->name;
    }

    /** Retorna la zona horaria por defecto */
    public function getTimezone():string
    {
        return $this->config->timezone;
    }

    /**
     * Retrona los datos de usuario para el acceso.
     */
    public function getUser():ConfigUser{
        return new ConfigUser($this->config->user);
    }

    /**
     * Retorna la configuraciones de los desarrolladores
     */
    public function getDevelopers():ConfigDevelopers
    {
        return new ConfigDevelopers($this->config);
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

    /**
     * Retorna la configuracion de las base de datos.
     */
    public function getDatabases():ConfigDatabases
    {
        return new ConfigDatabases($this->config->databases);
    }

    /**
     * Retrona el número de APPs registradas en la api.
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

    /**
     * Guarda la condifuraciones
     * @throws Throwable En caso de ocurrir un error al momento de guardar la información del api.json
     */
    public function salve():object
    {
        $dir = Api::getDir() . "/api.json";
        $file = fopen($dir, 'a');
        fputs($file, str_replace('\/', '/', json_encode($this->config, 128)));
        fclose($file);
        return $this->config;
    }

    /** 
     * Retorna el objeto de api.json
     */
    public function getObject():object
    {
        return $this->config;
    }
}