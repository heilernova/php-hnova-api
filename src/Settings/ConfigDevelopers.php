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

use HNova\Api\Scripts\console;

class ConfigDevelopers
{

    public function __construct(private $cofig)
    {
        
    }

    /**
     * Retorna un array con trodos los desarrolladores
     * @return object[]
     */
    public function getAll():array
    {
        return $this->cofig->developers;
    }

    /**
     * Agrega la información de un desarrollador.
     */
    public function add(string $name, string $email, string $homepage = null){
        $this->cofig->developers[] = (object)[
            "name" => $name,
            "email" => $email,
            "homepage" => $homepage
        ];
    }
}