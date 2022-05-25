<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Settings;

use HNova\Api\Settings\Db\DbInfo;

class Databases
{
    public function add(string $name, string $type, array $dataConnection):void{
        $_ENV['api-rest']->config->databases->$name = (object)[
            'type' => $type,
            'dataConnection' => $dataConnection
        ];
    }

    public function delete(string $name):void{
        unset($_ENV['api-rest']->config->databases->$name);
    }

    /**
     * Busca la informacion de una base de datos.
     */
    public function get(string $name):?DbInfo{

        if (isset($_ENV['api-rest']->config->databases->$name)){
            $db = $_ENV['api-rest']->config->databases->$name;
            return new DbInfo($name, $db->type, $db->dataConnection);
        }else{
            return null;
        }
    }

    public function update():void{
        
    }

    /**
     * Retorna un array de las bases de datos.
     * @return DbInfo[]
     */
    public function getAll():array{

        $arr = [];

        foreach ($_ENV['api-rest']->config->databases as $key => $value){
            $arr[] = new DbInfo($key, $value->type, $value->dataConnection);
        }

        return $arr;
    }
}