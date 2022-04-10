<?php
/**
 * esta clase contendra las funcionalidades que herederan los controladores de la API.
 */

namespace HNova\Api;

use HNova\Api\Data\Database;

class ObjectDB
{
    /** Método constructor */
    public function __construct(string $table, string $db , public ?Database $_database = null)
    {
        $db = Api::getConfig()->getConfigData()->databases->$db ?? null;
        if ($db){

            $this->_database =  new Database((array)$db->dataConnection, $table);
        }else{
            throw new ApiException(["No se encontro la configuración de la base de datos $db"]);
        }
    }
}