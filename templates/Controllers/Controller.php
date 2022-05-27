<?php

namespace HNnamespace\ControllersLong;

use HNova\Api\req;
use \HNova\Api\res;

class Name
{
    /**
     * Método constructor
     */
    function __construct()
    {
        
    }
    
    function get()
    {
        return res::json('Hola mundo');
    }
}