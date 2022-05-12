<?php
namespace HNnamespace\ModelsLong;

use HNova\Api\Api;
use HNova\Api\Db\Database;

class Name
{
    private Database $db;
    public function __construct()
    {
        $this->db = Api::config()->getDatabase();
    }
}