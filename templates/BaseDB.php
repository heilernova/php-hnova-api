<?php
/**
 * esta clase contendra las funcionalidades que herederan los controladores de la API.
 */

namespace HNnamespace;

use HNova\Api\ObjectDB;

class BaseDB extends ObjectDB
{
    /** Método constructor */
    /**
     * @param string $table Nombre de la tabla por defecto para las cunsultas SQL
     * @param string $db Nombre de la base de datos a la cual se le realizara las consultas SQL.
     */
    public function __construct(string $table, string $db)
    {
        
        parent::__construct($table, $db);
    }
}