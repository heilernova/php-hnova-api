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




}