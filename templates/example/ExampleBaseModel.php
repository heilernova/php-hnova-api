<?php
/**
 * esta clase contendra las funcionalidades que herederan los modelos de la api.
 */

namespace Example;

use HNova\Api\ApiModel;

class ExampleBaseModel extends ApiModel
{
    public function __construct(string $table, string $database = null)
    {
        parent::__construct($table, $database);
    }
}