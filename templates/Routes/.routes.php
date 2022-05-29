<?php
namespace App\Controllers;

use HNova\Api\res;
use HNova\Api\Routes;

Routes::get('test', function(){ 
    return res::json("Hola mundo"); 
});