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
     * @param string $name Nombre de la api
     * @param object $config Información decodificado del api.json
     */
    public function __construct(private string $name, private object $config){}

    /**
     * Retorna el nombre de la api en ejecución
     */
    public function getName():string
    {
        return $this->name;
    }

    /**
     * Retorna el namespace de la api en ejecución.
     */
    public function getNamespace():string
    {
        return $this->config->namespace;
    }
}