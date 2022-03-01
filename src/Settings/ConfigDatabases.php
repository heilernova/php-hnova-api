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

use HNova\Api\Data\DatabaseInfo;

class ConfigDatabases
{
    public function __construct(private object $data)
    {
        
    }

    /**
     * Retorna un array de DatabaseInfo de los 
     */
    public function getAll():array
    {
        $list = [];
        foreach($this->data as $key => $element){
            $list[$key] = new DatabaseInfo($element->type, $element->dataConnection);
        }
        return $list;
    }

    public function get(string $name):?DatabaseInfo
    {
        if (isset($this->data->$name)){
            return new DatabaseInfo($this->data->$name->type, $this->data->$name->dataConnection);
        }else{
            return null;
        }
    }

    public function add($name, $type, $dataConnection)
    {
        $this->data->$name = (object)[
            "type"=> $type,
            "dataConnection"=> $dataConnection
        ];
    }
}