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
     * Obtiene un array de objectos representando las información de los campos
     * Parametros que contienen los objecto [ name, orgname, table, orgtable, max_length, length, charsetnr, flags, type, decimals ]
     * @return object[]
     * @throws Throwable Retorna un exepción en caso de que el resultado de la cosulta no sea de tipo mysqli_result
     */
    public function fetchFields():array
    {
        return $this->result->fetch_fields();
    }

    /**
     * Obtiene un array con el nombre de los campos resultantes de la cosulta SQL.
     * @return string[]
     * @throws Throwable Retorna un exepción en caso de que el resultado de la cosulta no sea de tipo mysqli_result
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
     * @throws Throwable Retorna un exepción en caso de que el resultado de la cosulta no sea de tipo mysqli_result
     */
    public function fetchAll(bool $assoc = true):array
    {
        return $this->result->fetch_all($assoc ? MYSQLI_ASSOC : MYSQLI_NUM);
    }

    /**
     * Obtine array de la filas mapeados a un objeto
     * @param string $class namespace de la clase a la cual deseamos cargar los valores.
     * @param array|null valores de controstructor de la clase
     * @return object[]
     * @throws Throwable Retorna un exepción en caso de que el resultado de la cosulta no sea de tipo mysqli_result
     */
    public function fetchAllObjects(string $class = 'stdClass', ?array $constructor_args = null):array
    {
        $array = [];
        if ($constructor_args){
            while($object = $this->result->fetch_object($class, $constructor_args)){
                $array[] = $object;
            }
        }else{
            while($object = $this->result->fetch_object($class)){
                $array[] = $object;
            }
        }
        return $array;
    }

    /**
     * Obtiene un array asosiativo de la primera fila del resulta de la consulta SQL.
     * @throws Throwable Retorna un exepción en caso de que el resultado de la cosulta no sea de tipo mysqli_result
     */
    public function fetchAssoc():array|false|null{
        return $this->result->fetch_assoc();
    }

    /**
     * Obtiene un array númerico de la primera fila del resultado de la cosulta SQL.
     * @throws Throwable Retorna un exepción en caso de que el resultado de la cosulta no sea de tipo mysqli_result
     */
    public function fetchArray():array|false|null{
        return $this->result->fetch_array(MYSQLI_NUM);
    }

    /**
     * Obtiene un objeto de la primera fila del resultado de la consulta SQL.
     * @throws Throwable Retorna un exepción en caso de que el resultado de la cosulta no sea de tipo mysqli_result
     */
    public function fecthObject(string $class = 'stdClass', ?array $constructor_args = null):object|false|null
    {
        return $constructor_args ? $this->result->fetch_object($class, $constructor_args) : $this->result->fetch_object($class);
    }
}