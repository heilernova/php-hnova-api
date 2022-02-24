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

use mysqli;
use mysqli_result;
use mysqli_stmt;
use Phpnv\Api\ApiException;
use ReflectionMethod;
use Throwable;

class Database
{
    private mysqli|null $dbMysql = null;
    private array $errorList = [];
    private mysqli_stmt $stmt;
    private string|null $lastCommand = null;
    private string $defaultTable;

    /**
     * @param string $type Tipo de base de datos [ mysql, sqlserver.].
     * @param array $data_connet array de la conexión.
     */
    public function __construct(private string $type,private array $dataConnection)
    {
        if ($type != 'mysql') throw new ApiException(['No hay soperte para el tipo de la base de datos', "tipo: $type"]);
    }

    /**
     * Retorna el objecto msqli que representa una conexion de una base de datos de MySQL.
     */
    public function getConnection():mysqli
    {
        if ($this->dbMysql){
            return $this->dbMysql;
        }else{
            try {
                $dataConnection = $this->dataConnection;
                $this->dbMysql = @mysqli_connect(
                    $dataConnection['hostname'],
                    $dataConnection['username'],
                    $dataConnection['password'],
                    $dataConnection['database']
                );
                $this->dbMysql->autocommit(false);
                return $this->dbMysql;
            } catch (Throwable $th) {
                $message = [
                    'Error al extablecer la conexión Mysqli con la base de datos MySql.',
                    'Datos de conexion:',
                    [
                        'Hostname: ' . ($dataConnection['hostname'] ?? ' [ variable indefinida ]'),
                        'username: ' . ($dataConnection['username'] ?? ' [ variable indefinida ]'),
                        'password: ' . ($dataConnection['password'] ?? ' [ variable indefinida ]'),
                        'database: ' . ($dataConnection['database'] ?? ' [ variable indefinida ]'),
                    ]
                ];
                throw new ApiException($message, $th);
            }
        }
    }

    /**
     * Ejecuta una consulta preparada en la base de datos.
     * @param string $slq comando sql a ejecutar.
     * @param array $params Paramentros de la consulta sql.
     */
    public function query(string $sql, ?array $params = null):bool|mysqli_result
    {
        return $this->queryMySql($sql, $params);
    }

    /**
     * Retorna un array de los errores obtenidos durecnte las consultas sql.
     */
    public function getErrors():array
    {
        return $this->errorList;
    }

    /**
     * Confima y ejecuta los cambios realizados en la en la base de datos.
     */
    public function commit():bool{
        $this->lastCommand = null;
        return $this->dbMysql->commit();
    }

    private function queryMySql(string $sql, array $params = null):bool|mysqli_result
    {
        if ($sql == $this->lastCommand){
            $stmt = $this->stmt;
        }else{
            $stmt = $this->getConnection()->prepare($sql);
            try {
                $this->stmt = $stmt;
            } catch (\Throwable $th) {
                throw new ApiException([
                    'Error con la prepación de la consulta sql.',
                    "Sql command:   " . $sql,
                    "Error:         " . ($this->dbMysql->error ?? 'null')
                ], $th);
            }
        }
        if ($params){
            try {
                $refValues = ['']; // Creamos un array con un valor vacio
                foreach($params as $key=>$value){
                    $refValues[0] .= is_string($value) ? 's' : (is_int($value) ? 'i' : 'd');
                    $refValues[] = &$params[$key];
                }
                $ref = new ReflectionMethod($stmt, 'bind_param');
                $ref->invokeArgs($stmt, $refValues);
            } catch (\Throwable $th) {
                throw new ApiException(['Error con el bind_param', $sql], $th);
            }
        }
        try{
            $ok = $stmt->execute();
            $result = $stmt->get_result();
            return $result ? $result : $ok;
        } catch(\Throwable $th){
            $this->errorList[$stmt->id] = (object)[
                "sqlCommand" =>$sql, 
                "params" => $params,
                "message" => $stmt->error
            ];
            return false;
        }
    }

    /**
     * Estable la tabla por defecto para realizar consultas sql en la base de datos.
     */
    public function setDefaultTable(string $table):void
    {
        $this->defaultTable = $table;
    }

    // Métodos de ayuda para ejecuciones simples insert, update, delete, procedura, function.
    // --------------------------------------------------------------------------------------

    /**
     * Ejecuta una cosulta sql en la base de datos
     */
    public function execute(string $slq, array $params = null):DatabaseResult
    {
        return new DatabaseResult($this->queryMySql($slq, $params), $this->stmt->insert_id, $this->stmt->affected_rows);
    }

    /**
     * Arma y ejecuta un select en la base de datos con los parametros ingresados.
     * @param string $table Nombre de la tabla por default es la tabla establecida por defecto con setDefaultTable
     * @param string|array $fields Campos o array de los campos a seleccionar de la tabla por defecto es "*".
     * @param string|array $condition Condición where de la filas a seleccionas, puese ser un string o un array 
     * donde el primer item es un string con la condicion preparadao y el segundo un array con los parametros.
     */
    public function select($table = null, array $fields = null, string|array $condition = null):DatabaseResult
    {
        if (!$table) $table = $this->defaultTable;
        if ($fields){
            $fields_tempo = '';
            foreach ($fields as $element){
                $fields_tempo .= ", `$element`";
            }
            $fields .= ltrim($fields_tempo, ', ');
        }else{
            $fields = '*';
        }
        $cond = '';
        if ($condition){
            if (is_string($condition)) $condition[] = $condition;
            $cond = " WHERE " . $condition[0];
            $params = $condition[1] ?? null;
        }
        $result = $this->queryMySql("SELECT $fields FROM $table $cond", $params ?? null);
        return new DatabaseResult($result, $this->stmt->insert_id, $this->stmt->affected_rows);
    }

    /**
     * @param array|object $params Un array asositativo o un objeto con los parametros.
     * @param string $table Por defecto es el nombre de la tabla establecido.
     */
    public function insert(array|object $params, string $table = null):DatabaseResult
    {
        if (!$table) $table = $this->defaultTable;
        $fields = '';
        $values = '';
        foreach ($params as $key=>$value){
            $fields .= ", `$key`";
            $values .= ", ?";
        }
        $fields = ltrim($fields, ', ');
        $values = ltrim($values, ', ');
        $result = $this->queryMySql("INSERT INTO $table($fields) VALUES($values)", (array)$params);
        return new DatabaseResult($result, $this->stmt->insert_id, $this->stmt->affected_rows);
    }

    /**
     * @param array|object $params array asociativo o un objecto con los parametros a editar.
     * @param string|array $condition Condición where de la filas a seleccionas, puese ser un string o un array 
     * donde el primer item es un string con la condicion preparadao y el segundo un array con los parametros.
     */
    public function update(array|object $params, string|array $condition, string $table = null):DatabaseResult
    {
        if (!$table) $table = $this->defaultTable;
        $values = '';
        foreach ($params as $key=>$value){
            $values .= ", `$key`=?";
        }
        $values = ltrim($values, ', ');
        if (is_string($condition)){
            $condition = [$condition,null];
        }
        if (isset($condition[1])) $params = array_merge((array)$params, (array)$condition[1]);
        $result = $this->queryMySql("UPDATE $table SET $values WHERE " . $condition[0], $params);
        return new DatabaseResult($result, $this->stmt->insert_id, $this->stmt->affected_rows);
    }

    /**
     * @param string|array $condition Condición where de la filas a seleccionas, puese ser un string o un array 
     * donde el primer item es un string con la condicion preparadao y el segundo un array con los parametros.
     */
    public function delete(array $condition, string $table = null):DatabaseResult
    {
        if (!$table) $table = $this->defaultTable;
        $result = $this->queryMySql("DELETE FROM $table WHERE " . $condition[0], $condition[1] ?? null);
        return new DatabaseResult($result, $this->stmt->insert_id, $this->stmt->affected_rows);
    }

    /**
     * Ejecuta una función en la base de datos.
     * @param string $name Nombre de la funcion a ejecutar.
     * @param array $params array de los parametros
     */
    public function function(string $name, array $params = null):DatabaseResult
    {
        $p = '';
        if ($params) $p = ltrim(str_repeat(', ?', count($params)), ', ');
        return new DatabaseResult($this->queryMySql("SELECT $name($p)", $params), $this->stmt->insert_id, $this->stmt->affected_rows);
    }

    /**
     * Ejecuta un procedimiento en la base de datos.
     * @param string $name Nombre del procedimiento a ejecutar.
     * @param array $params array de los parametros
     */
    public function procedure(string $name, array $params = null):DatabaseResult
    {
        $p = '';
        if ($params) $p = ltrim(str_repeat(', ?', count($params)), ', ');
        return new DatabaseResult($this->queryMySql("CALL $name($p)", $params), $this->stmt->insert_id, $this->stmt->affected_rows);
    }
}