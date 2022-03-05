<?php
/**
 * Clase modelo para intercturar con la base de datos.
 */
namespace Example\Controllers;

use Example\ExampleBaseModel;

class NameModel extends ExampleBaseModel
{

    public function __construct()
    {
        parent::__construct("table");
    }
}