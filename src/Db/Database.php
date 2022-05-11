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

    /**
     * Soporte para MySQl y PostgreSQl
     * @param object $dataConnection array associativo de la conecion
     */
    public function __construct(object $data, string $table = null)
    {
        try {
            $dns = "";
            $username = $data->dataConnection->username;
            $password = $data->dataConnection->password;

            if ($data->type == "mysql"){
                $dns = "";
            }else if ($data->type == "postgresql"){

                $host = $data->dataConnection->hostname;
                $db =   $data->dataConnection->database;
                $dns = "pgsql:host=$host; dbname=$db";
            }

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

    /**
     * Realiza un commit de los cambios
     */
    public function commit():bool{
        return $this->_pdo->commit();
    }

    public function query($sql, $params = null):?PDOStatement{

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
            }
        } catch (\Throwable $th) {
           
            return new ApiException(["Error al ejecuta la consulta SQL: $sql"],$th);
        }
    }

    /**
     * @param array|object Objeto o array asosiativo de los valores
     */
    public function insert(object|array $params, string $table = null):PDOStatement{

        if (!$table) $table = ($this->defaultTable ?? '');
        $fields = '';
        $values = '';
        foreach ($params as $key=>$value){
            $fields .= ", $key";
            $values .= ", :$key";
        }
        $fields = ltrim($fields, ', ');
        $values = ltrim($values, ', ');

        return $this->query("INSERT INTO $table($fields) VALUES($values)", $params);
    }

    /**
     * Ejecuta un update de la base de datos
     */
    public function update(object|array $params, array|string $condition, string $table = null):PDOStatement {

        if (!$table) $table = ($this->defaultTable ?? '');

        $params = (array)$params;
        $values = '';
        foreach ($params as $key=>$value){
            $values .= ", $key=:$key";
        }

        $values = ltrim($values, ', ');
        if (is_string($condition)){
            $condition = [$condition,null];
        }

        if (isset($condition[1])) {

            $condition[0] = str_replace(':', ':cnd_', $condition[0]);

            foreach ($condition[1] as $key => $value){
                $params["cnd_$key"] = $value;
            }
        };

        return $this->query("UPDATE $table SET $values WHERE " . $condition[0], $params);
    }

    /**
     * Ejecutar un delete
     */
    public function delete(array|string $condition, $table):PDOStatement{
        if (!$table) $table = ($this->defaultTable ?? '');
        if (is_string($condition)){
            $condition = [$condition];
        }

        return $this->query("DELETE FROM $table WHERE " . $condition[0], $condition[1] ?? null);
    }
}