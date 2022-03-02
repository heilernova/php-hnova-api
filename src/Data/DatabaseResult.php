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
namespace HNova\Api\Data;

use mysqli_result;
use Throwable;

class DatabaseResult
{
    public function __construct(public bool|mysqli_result $result, public int $insertId = 0, public int  $affectedRows = 0)
    {
        
    }

    /**
     * Obtiene le objeto mysqli_result de la consulta realizada
     * @throws Throwable Retorna un exepción en caso de que el result de la cosulta no sea de tipo mysqli_result
     */
    public function getResult():mysqli_result{
        return $this->result;
    }

    /**
     * Obtiene un array de objectos representado las información de los campos
     * Parametros que contienen los objecto [ name, orgname, table, orgtable, max_length, length, charsetnr, flags, type, decimals ]
     * @return object[]
     */
    public function fetchFields():array
    {
        return $this->result->fetch_fields();
    }

    /**
     * Obtiene un array con el nombre de los campos resultantes de la cosulta SQL.
     * @return string[]
     */
    public function fetchFieldsName():array
    {
        return array_map(function($element){ return $element->name; }, $this->fetchFields());
    }

    /**
     * Obtiene todas la filas como un array asosiativo o númerico.
     * @param bool $assoc true para que la información de las filas se un array asosiativo 
     * en caso contrario sera un array númerico.
     * @return array[]
     */
    public function fetchAll(bool $assoc = true):array
    {
        return $this->result->fetch_all($assoc ? MYSQLI_ASSOC : MYSQLI_NUM);
    }

    /**
     * Obtine array de la filas con un objeto
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
     * Obtiene un array asosiativo de la primera fila del resulta de la consulta SQL.
     */
    public function fetchAssoc():array|false|null{
        return $this->result->fetch_assoc();
    }

    /**
     * Obtiene un array númerico de la primera fila del resultado de la cosulta SQL.
     */
    public function fetchArray():array|false|null{
        return $this->result->fetch_array(MYSQLI_NUM);
    }

    /**
     * Obtiene un objeto de la primera fila del resultado de la consulta SQL.
     */
    public function fecthObject(string $class = 'stdClass', ?array $constructor_args = null):object|false|null
    {
        return $this->result->fetch_object($class, $constructor_args);
    }
}