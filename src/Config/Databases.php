<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Phpnv\Api\Config;

use Phpnv\Api\ApiException;
use Phpnv\Api\Config\ConfigInfo;
use Phpnv\Api\Config\Databases\DatabaseInfo;

class Databases
{
    public function __construct(private $data){}

    /**
     * Agregar una base de datos archivo api.json
     * @param string $name Nombre de la base de datos.
     * @param string $type Tipo de la base de datos ejm. mysqli, sqlserver, pdo
     * @param array $dataConnection Datos de la coneción a la base de datos.
     */
    public function add(string $name, string $type, array $dataConnection):void
    {
        $this->data->databases->$name = new DatabaseInfo($name, $type, $dataConnection);
    }

    /**
     * Buscar y retorna la informcaion de la base de datos
     * @throws ApiException Devuelve una exceptión en caso de que no se encutre la base de datos en el archivo api.json.
     */
    public function find(string $name):DatabaseInfo
    {
        if (!array_key_exists($name, ((array)$this->data->databases))){
            throw new ApiException(["No se encontraron los datos de la base de datos [ $name ] en el archivo api.json"]);
        }
        $data = $this->data->databases->$name;
        return DatabaseInfo::load($name, $data);
    }

    /**
     * Retorna la lista de la bases de datos utilizadas por la api.
     * @return DatabaseInfo[]
     */
    public function getList():array
    {
        return (array)$this->data->databases;
    }
}