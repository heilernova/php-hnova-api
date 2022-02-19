<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Data;

use mysqli_result;

class DatabaseResult
{
    public function __construct(public bool|mysqli_result $result, public int $insertId = 0, public int  $affectedRows = 0)
    {}

    /**
     * Obtiene le objeto mysqli_result de la consulta realizada
     * @throws ApiException Retorna un exeptcion en caso de que la cosulta no retornara un mysqli_result
     */
    public function getResult():mysqli_result{
        return $this->result;
    }

    /**
     * Obtiene un array de objecto representado las información de los campos
     * Parametros que contienen los objecto [ name,orgname, table, orgtable, max_length, length, charsetnr, flags, type, decimals ]
     * @return object[]
     */
    public function fetchFields():array
    {
        return $this->result->fetch_fields();
    }

    /**
     * Obtiene un array con el nombre de los campos de la cosulta sql.
     * @return string[]
     */
    public function fetchFieldsName():array
    {
        return array_map(function($element){ return $element->name; }, $this->fetchFields());
    }

    /**
     * Obtiene todas la filas como un array asosiativo o númerico como un array.
     * @param bool $assoc true para que la finas sean un
     * un array numerico.
     * @return array[]
     */
    public function fetchAll(bool $assoc = true):array
    {
        return $this->result->fetch_all($assoc ? MYSQLI_ASSOC : MYSQLI_NUM);
    }

    /**
     * Obtine todas las filas como un objeto en un array
     * @param string $class namespace de la clase a la cual deseamos cargar los valores.
     * @return object[]
     */
    public function fetchAllObjects(string $class = 'stdClass'):array
    {
        $array = [];
        while($object = $this->result->fetch_object($class)){
            $array[] = $object;
        }
        return $array;
    }

    /**
     * Obtiene un array de la primera fila del resulta de la consulta sql.
     */
    public function fetchAssoc():array|false|null{
        return $this->result->fetch_assoc();
    }

    /**
     * Obtiene un array numerico de la primera fila del resultado de la cosulta sql.
     */
    public function fetchArray():array|false|null{
        return $this->result->fetch_array(MYSQLI_NUM);
    }

    /**
     * Obtiene un objeto de la primera fila del resultado de la consulta sql.
     */
    public function fecthObject(string $class = 'stdClass', ?array $constructor_args = null):object|false|null
    {
        return $this->result->fetch_object($class, $constructor_args);
    }
}