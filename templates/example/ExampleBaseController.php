<?php
/**
 * esta clase contendra las funcionalidades que herederan los controladores de la api.
 */

namespace Example;

use HNova\Api\ApiController;

class ExampleBaseController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }
}