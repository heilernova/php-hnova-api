<?php
/**
 * esta clase contendra las funcionalidades que herederan los modelos de la api.
 */

namespace Example;

use HNova\Api\Api;
use HNova\Api\ApiException;
use HNova\Api\Data\Database;

class ExampleBaseModel
{
    public Database $database;

    /**
     * Método constructror.
     * @param string $table El nombre de la tabla por defecto a la que se hara referencia.
     * @param string $database Nombre de la base de datos a la cual se ralizara la conexión 
     * en caso de ser null se tomará la base de datos de app en ejecución.
     * @throws ApiException Retrona un exception en caso de que no se encuentre una base de datos para el modelo.
     */
    public function __construct(string $table, string $database = null)
    {
        $class = $this::class;

        if ($database){
            $db = Api::getConfig()->getDatabases()->get($database);
            if (!$db){
                throw new ApiException(["No se pude inicializar el modelo [ $class ] ya que la configuración de la base de datos no existe. [ $database ]"]);
            }
        }else{
            $db = Api::getAppConfig()->getDatabase();
        }
        
        if ($db){
            $this->database = $db;
        }else{
            throw new ApiException(["No se encontrol una base de datos para el modelo [ $class ]"]);
        }
    }


    /**
     * Confirma los cambios ejecutados en la base ded atos.
     */
    public function commit():bool
    {
        return $this->database->commit();
    }
}