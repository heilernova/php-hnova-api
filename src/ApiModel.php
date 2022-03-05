<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api;

use HNova\Api\Data\Database;

class ApiModel
{
    /**
     * Objecto Database para ejecutar instrucciones a la base de datos.
     */
    public Database $database;

    /**
     * Método constructror.
     * @param string $table El nombre de la tabla por defecto a la cueal se le realizaran las consultas SQL.
     * @param string $database Nombre de la base de datos a la cual se ralizara la conexión 
     * en caso de ser null se tomará la base de datos de app en ejecución.
     * @throws ApiException Retorna una exception en caso de que no se encuentre la condifuración de la base de datos para el modelo.
     */
    public function __construct(string $table, string $database = null)
    {
        $class = $this::class;

        if ($database){
            $db = Api::getAppConfig()->getDatabases()->get($database);
            if (!$db){
                throw new ApiException(["No se pude inicializar el modelo [ $class ] ya que la configuración de la base de datos no existe. [ $database ]"]);
            }
            $db = new Database((array)$db->dataConnection, $table);
        }else{
            $db = Api::getConfig()->getDatabase($table);
        }
        
        if ($db){
            $this->database = $db;
        }else{
            throw new ApiException(["No se encontra la configuración de la base de datos para el modelo [ $class ] en el api.json"]);
        }
    }


    /**
     * Confirma y aplica los cambios ejecutados en la conexión de la base de datos.
     */
    public function commit():bool
    {
        return $this->database->commit();
    }
}