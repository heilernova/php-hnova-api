<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Db;

use Exception;
use HNova\Api\ApiException;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Clase para realizar peticion SQL
 * Actualmente cuenta soporta para MYSQL y PostgreSQL
 * */
class Database
{

    private PDO $_pdo;
    private PDOStatement $_statement;
    private string $_lastCommandSQL = "";
    private array $_errors = [];
    private string $defaultTable;
    private string $charField = "";

    /**
     * Soporte para MySQl y PostgreSQl
     * @param object $dataConnection array associativo de la conecion
     */
    public function __construct(object|array $data, string $table = null)
    {
        try {
            if (is_object($data)) $data = (object)$data;
            $dns = "";
            $username = $data->dataConnection->username;
            $password = $data->dataConnection->password;
            $host = $data->dataConnection->hostname;
            $db =   $data->dataConnection->database;

            if ($data->type == "mysql"){
                $dns = "mysql:host=$host; dbname=$db";
                $this->charField = '`';
            }else if ($data->type == "postgresql"){
                $dns = "pgsql:host=$host; dbname=$db";
                $this->charField = '"';
            }else{
                throw new ApiException(['No de idefica el tipo de base de datos: ' . $data->type]);
            }

            if ($table) $this->defaultTable = $table;

            $this->_pdo = new PDO($dns, $username, $password);
            
        } catch (\Throwable $th) {
            throw new ApiException(['Error al inizializar la database', "dns: $dns\nusername: $username\npassword: $password"], $th);
        }
    }

    /**
     * Retorna le objeto PDO
     */
    public function getPDO():PDO{
        return $this->_pdo;
    }

    /**
     * Retorna los errores de de las cosultas SQL realizadas
     */
    public function getErrors():array{
        return $this->_errors;
    }

    public function lastInsertId(string|null $name = null):string|false{
        return $this->_pdo->lastInsertId($name);
    }

    /**
     * Realiza un commit de los cambios
     */
    public function commit():bool{
        return $this->_pdo->commit();
    }

    /**
     * Inicia una transacción, para que los cambios se apliquen en la base de datos se deje ejecutar el commit
     */
    public function beginTransaction():bool{
        return $this->_pdo->beginTransaction();
    }

    /**
     * Ejecuta un consulta SQL en la base de datos
     * @param string $slq Comando SQLa ejecutar
     * @param array|null Array asosiativos
     */
    public function query(string $sql, array $params = null): PDOStatement|false{

        if ($sql != $this->_lastCommandSQL){
            try {
                $this->_statement = $this->_pdo->prepare($sql);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        try {

            if ($this->_statement->execute($params)){
                return $this->_statement;
            }else{
                $this->_errors[] = [
                    'sql' => $sql,
                    'params' => $params,
                    'errorCode' => $this->_statement->errorCode(),
                    'errorInfo' => $this->_statement->errorInfo()
                ];
                return false;
            }
        } catch (\Throwable $th) {
           
            throw new ApiException(["Error al ejecuta la consulta SQL: $sql", $params],$th);
        }
    }

    /**
     * @param array|object Objeto o array asosiativo de los valores,  la key del valor debe
     * hacer referencia al nombre del campo a insertar en la tabla.
     * @param string|null Nombre de la tabla
     */
    public function insert(object|array $params, string $table = null): PDOStatement|false{

        if (!$table) $table = ($this->defaultTable ?? '');
        $fields = '';
        $values = '';
        foreach ($params as $key=>$value){
            $fields .= ', ' . $this->charField . $key . $this->charField;
            $values .= ", :$key";
        }
        $fields = ltrim($fields, ', ');
        $values = ltrim($values, ', ');

        return $this->query("INSERT INTO $table ($fields) VALUES($values)", (array)$params);
    }

    /**
     * Ejecuta un update de la base de datos
     * @param object|array $params Objeto o array asociativo con los valores a actulizar, la key del valor debe
     * hacer referencia al nombre del campo a insertar en la tabla.
     * @param array|string $condition string de la condifiión o array con la condición y los parametros  ejemplo: [ 'id=:id', [ 'id'=>1 ] ]
     * @param string|null Nombre de la tabla
     */
    public function update(object|array $params, array|string $condition, string $table = null): PDOStatement|false {

        if (!$table) $table = ($this->defaultTable ?? '');

        $params = (array)$params;
        $values = '';
        foreach ($params as $key=>$value){
            $filed = $this->charField . $key . $this->charField;
            $values .= ", $filed=:$key";
        }

        $values = ltrim($values, ', ');
        if (is_string($condition)){
            $condition = [$condition,null];
        }

        if (isset($condition[1])) {
            foreach ($condition[1] as $key => $value){
                $params["cnd_$key"] = $value;
            }
        };

        $w = $this->formatSqlWhere($condition[0]);
        return $this->query("UPDATE $table SET $values WHERE $w", (array)$params);
    }

    /**
     * Ejecutar un delete
     * @param array|string $condition string de la condifiión o array con la condición y los parametros  ejemplo: [ 'id=:id', [ 'id'=>1 ] ]
     * @param string|null Nombre de la tabla
     */
    public function delete(array|string $condition, string $table = null): PDOStatement|false{ 
        if (!$table) $table = ($this->defaultTable ?? '');
        
        if (is_string($condition)){
            $condition = [$condition];
        }
        $params = null;
        
        if (isset($condition[1])) {
            $params = [];
            foreach ($condition[1] as $key => $value){
                $params["cnd_$key"] = $value;
            }
        };

        $w = $this->formatSqlWhere($condition[0]);
        return $this->query("DELETE FROM $table WHERE $w", $params);
    }

    private function formatSqlWhere(string $condition):string{
        
        $w = str_replace('  ', ' ', $condition);
        $w = str_replace([' = ', '= ', ' ='], '=', $w);
        $w = str_replace(':', ':cnd_', $w);

        $w = preg_replace_callback('/\w+=/', function($match){
            return ( $this->charField . trim(($match[0]), "\t\n\r\0\x0B=") . $this->charField . '=');
        }, $w);

        return $w;
    }
}
