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

class AppInfo
{
    /**
     * Método constructro.
     * @param object $config Información decodificado del api.json
     */
    public function __construct(private object $config){}

    /**
     * Retorna el nombre de la aplicación.
     */
    public function getName():string{
        return $this->config->name;
    }

    /**
     * Retrona los datos de usuario para el acceso.
     * @return object<username, password>
     */
    public function getUser():object{
        return $this->config->user;
    }

    /**
     * Estable el nombre del usuario de acceso. para aplicar los cambios recuerde darle salve
     */
    public function setUserName(string $username):void
    {
        $this->config->user->username = $username;
    }

    
    /**
     * Estable la constraseña del usuario de acceso. para aplicar los cambios recuerde darle salve
     */
    public function setUserPassword(string $password):void
    {
        $this->config->user->password = password_hash($password, PASSWORD_DEFAULT, ['cost'=>3]);
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

    public function getDatabases()
    {

    }

    /**
     * Retrona el número de apps usasa en la api.
     */
    public function getAppsCount():int
    {
        return count((array)$this->config->apps);
    }

    public function getApps()
    {

    }

}