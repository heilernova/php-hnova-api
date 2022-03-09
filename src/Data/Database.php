<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Data;

use HNova\Api\ApiException;
use mysqli;
use mysqli_result;
use mysqli_stmt;
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
     * Método constructor.
     * @param array $data_connection Array asociativo con los datos para una conexión mysqli 
     * [ hostname:string, username:string, password:string, database:string, port:int, socket:string ]
     * @param string $table Nombre de la tabla por default.
     */
    public function __construct(private array $dataConnection,string $table = null)
    {
        if ($table) $this->defaultTable = $table;
    }

    /**
     * Retorna el objecto msqli que representa una conexion de una base de datos de MySQL.
     * en caso de que el objeto no este inicializa de creara.
     * @throws ApiException Retorna un excepción en caso que no poder establecer una conexión con la base de datos.
     */
    public function getConnection():mysqli
    {
        if ($this->dbMysql){

            // Retornamos el objeto mysqli en caso de que ya este iniciado.
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

                // En caso de que se detecto un error con la conexión.
                $message = [
                    'Error al establecer la conexión con la base de datos MySql.',
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
     * @param string $sql Comando SQL a ejecutar en la base de datos, los parametros se deben definir con "?".
     * @param array $params Array de los parametros de la consulta SQL
     * @throws ApiException En caso de no poder establecer la conexión con la base de datos
     * @throws ApiException En caso de haber un error estableces los bind_params
     */
    public function query(string $sql, ?array $params = null):bool|mysqli_result
    {
        return $this->queryMySql($sql, $params);
    }

    /**
     * Ejecuta un comando en la base de datos.
     * @param string $sql Comando SQL a ejecutar en la base de datos, los parametros se deben definir con "?".
     * @param array $params Array de los parametros de la consulta SQL
     * @throws ApiException En caso de no poder establecer la conexión con la base de datos
     * @throws ApiException En caso de haber un error estableces los bind_params
     */
    private function queryMySql(string $sql, array $params = null):bool|mysqli_result
    {
        if ($sql != $this->lastCommand){

            // En casos de la consulta sea distinta a la anterior ejecutar la preparacion SQL nuevamente
            $connection = $this->getConnection();
            try {
                $this->stmt = $connection->prepare($sql);
            } catch (\Throwable $th) {
                throw new ApiException([
                    'Error con la prepación de la consulta sql.',
                    "Sql command:   " . $sql,
                    "Error:         " . ($this->dbMysql->error ?? 'null')
                ], $th);
            }
        }
        
        $stmt = $this->stmt;

        // En caso de que haber parametro solos cargamos al bind_param.
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
                throw new ApiException(['Error con el bind_param', "SQL:  $sql"], $th);
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
     * Retorna un array de los errores obtenidos durante las consultas sql.
     * @return object[]
     */
    public function getErrors():array
    {
        return $this->errorList;
    }

    /**
     * Confirma y carga los cambios realizados en la en la base de datos.
     */
    public function commit():bool{
        $this->lastCommand = null;
        return $this->dbMysql->commit();
    }

    /**
     * Ejecuta una consulta preparada en la base de datos, es similar query con la diferencia que retorna un objeto DatabaseResutl.
     * @param string $sql Comando SQL a ejecutar en la base de datos, los parametros se deben definir con "?".
     * @param array $params Array de los parametros de la consulta SQL
     * @throws ApiException En caso de no poder establecer la conexión con la base de datos
     * @throws ApiException En caso de haber un error estableces los bind_params
     */
    public function execute(string $slq, array $params = null):DatabaseResult
    {
        return new DatabaseResult($this->queryMySql($slq, $params), $this->stmt->insert_id, $this->stmt->affected_rows);
    }

    /**
     * Ejecuta un select en la base de datos con los parametros ingresados.
     * @param string $table Nombre de la tabla.
     * @param array $fields Campos o array de los campos a seleccionar de la tabla por defecto es "*".
     * @param string|array $condition Condición where de la filas a seleccionas, puese ser un string o un array 
     * donde el primer item es un string con la condicion preparadao y el segundo un array con los parametros.
     */
    public function select($table = null, array $fields = null, string|array $condition = null):DatabaseResult
    {
        if (!$table) $table = ($this->defaultTable ?? '');

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
     * Inserta datos en la tabla designada.
     * @param array|object $params Un array asositativo o un objeto con los parametros, el nombre de las key del array o del objeto deben
     * concordar con los nombre de los campos esperada por la base de datos..
     * @param string $table Nombre de la tabla, por defecto es el nombre de la tabla establecido.
     * @throws ApiException En caso de haber un problema con la conexión, el bind_params, o preparación de la consulta.
     */
    public function insert(array|object $params, string $table = null):DatabaseResult
    {
        if (!$table) $table = ($this->defaultTable ?? '');
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
     * Actualiza los datos de una tabla
     * @param array|object $params array asociativo o un objecto con los parametros a editar.
     * @param string|array $condition Condición where de la filas a seleccionas, puese ser un string o un array 
     * donde el primer item es un string con la condicion preparadao y el segundo un array con los parametros.
     * @throws ApiException En caso de haber un problema con la conexión, el bind_params, o preparación de la consulta
     */
    public function update(array|object $params, string|array $condition, string $table = null):DatabaseResult
    {
        if (!$table) $table = ($this->defaultTable ?? '');
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
     * Elimina un datos de una tabla.
     * @param string|array $condition Condición where de la filas a seleccionas, puese ser un string o un array 
     * donde el primer item es un string con la condicion preparadao y el segundo un array con los parametros.
     * @throws ApiException En caso de haber un problema con la conexión, el bind_params, o preparación de la consulta
     */
    public function delete(array|string $condition, string $table = null):DatabaseResult
    {
        if (!$table) $table = ($this->defaultTable ?? '');
        if (is_string($condition)){
            $condition = [$condition];
        }
        $result = $this->queryMySql("DELETE FROM $table WHERE " . $condition[0], $condition[1] ?? null);
        return new DatabaseResult($result, $this->stmt->insert_id, $this->stmt->affected_rows);
    }

    /**
     * Ejecuta una función en la base de datos.
     * @param string $name Nombre de la funcion a ejecutar.
     * @param array|null $params array de los parametros
     * @throws ApiException En caso de haber un problema con la conexión, el bind_params, o preparación de la consulta
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
     * @param array|null $params array de los parametros.
     * @throws ApiException En caso de haber un problema con la conexión, el bind_params, o preparación de la consulta
     */
    public function procedure(string $name, array $params = null):DatabaseResult
    {
        $p = '';
        if ($params) $p = ltrim(str_repeat(', ?', count($params)), ', ');
        return new DatabaseResult($this->queryMySql("CALL $name($p)", $params), $this->stmt->insert_id, $this->stmt->affected_rows);
    }

}