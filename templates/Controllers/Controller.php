<?php

namespace HNnamespace\ControllersLong;

use HNnamespace\BaseController;
use HNova\Api\Api;

class Name extends BaseController
{
    /**
     * Método constructor
     */
    function __construct()
    {
        parent::__construct();
    }
    
    function get()
    {
        return "Hello world";
    }
}