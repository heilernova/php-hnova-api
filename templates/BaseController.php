<?php
/**
 * esta clase contendra las funcionalidades que herederan los controladores de la API.
 */

namespace HNnamespace;

use HNova\Api\Controller as ApiController;

class BaseController extends ApiController
{
    /** Método constructor */
    public function __construct()
    {
        
        parent::__construct();
    }
}