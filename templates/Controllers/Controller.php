<?php

namespace HNnamespace\ControllersLong;

use HNova\Api\{req, res};

class Name
{
    /**
     * Construct method
     */
    function __construct()
    {
        
    }
    
    function get()
    {
        return res::json('Hello world');
    }
}