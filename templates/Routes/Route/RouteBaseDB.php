<?php
namespace HNnamespace\Routes\Name;

use HNova\Api\ObjectDB;

class NameBaseDB extends ObjectDB
{
    /** Método constructor */
    public function __construct(string $table, string $db = 'default')
    {
        
        parent::__construct($table, $db);
    }
}