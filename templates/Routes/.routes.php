<?php
namespace App\Controllers;

use HNova\Api\Routes;

Routes::get('test', function(){ 
    return "Hola mundo"; 
});