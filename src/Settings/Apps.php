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

class Apps
{
    public function __construct(private object $apps){}
    
    /**
     * Retorna un array con todas la apis.
     * 
     */
    public function getAll()
    {

    }

    /**
     * Rertorna la api que concuerde con el nombre ingresado.
     */
    public function get(string $name)
    {

    }

    /**
     * Agrega una api a la config
     */
    public function add(string $name)
    {
        
    }
}