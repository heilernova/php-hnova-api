<?php
/**
 * esta clase contendra las funcionalidades que herederan los controladores de la API.
 */

namespace HNnamespace;

use HNova\Api\ObjectDB;

class BaseDB extends ObjectDB
{
    /** Método constructor */
    public function __construct(string $table, string $db = 'default')
    {
        
        parent::__construct($table, $db);
    }
}