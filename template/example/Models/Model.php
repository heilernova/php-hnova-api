<?php
/**
 * Clase modelo para intercturar con la base de datos.
 */
namespace Example\Controllers;

use Example\ExampleBaseModel;
use HNova\Api\Response;

class NameModel extends ExampleBaseModel
{

    public function __construct()
    {
        parent::__construct("table");
    }
}