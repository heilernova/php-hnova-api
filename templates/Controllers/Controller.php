<?php

namespace HNnamespace\ControllersLong;

use HNnamespace\BaseController;

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
        return "Hola mundo";
    }
}