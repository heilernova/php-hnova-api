<?php
/**
 * This class will contain the functionalities that the controllers inherit from the API.
 */

namespace HNova\Api;

use HNova\Api\Data\Database;

class ObjectDB
{
    public ?Database $_database;
    
    /**
     * Constructro method
     * @param string $table Default table name in SQL queries
     * @param string $db Name of the database to connect
     */
    public function __construct(string $table, string $db)
    {
        $db = Api::getConfig()->getConfigData()->databases->$db ?? null;
        if ($db){

            $this->_database =  new Database((array)$db->dataConnection, $table);
        }else{
            throw new ApiException(["Database configuration not found '$db' in api.json"]);
        }
    }
}