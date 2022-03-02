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
    /**
     * Método constructor
     * @param object $data Objecto que almacena las base de datos del api.json
     */
    public function __construct(private object $data)
    {
        
    }

    /**
     * Retorna un array de DatabaseInfo de los 
     * @return DatabaseInfo[]
     */
    public function getAll():array
    {
        $list = [];
        foreach($this->data as $key => $element){
            $list[$key] = new DatabaseInfo($element->type, $element->dataConnection);
        }
        return $list;
    }

    /**
     * Rertonar la datos de configuracion para la conexión con la base de datos.
     * @return null|DatabaseInfo Retorna null en caso de que no se encuetre la configuración con la base de datos.
     */
    public function get(string $name):?DatabaseInfo
    {
        if (isset($this->data->$name)){
            return new DatabaseInfo($this->data->$name->type, $this->data->$name->dataConnection);
        }else{
            return null;
        }
    }

    /**
     * Agrega una base de datos a la configuraciones del sistema.
     * @param string $name Nombre de la base de datos y el nombre ya existe cambiara los valores
     * @param string $type Tipo de la base de datos MYSQL, SQLSERVE, MONGODB
     * @param array $dataConnection Datos de conexión con la base de datos, en un array asociativo.
     */
    public function add(string $name, string $type, array $dataConnection):void
    {
        $this->data->$name = (object)[
            "type"=> $type,
            "dataConnection"=> $dataConnection
        ];
    }
}