<?php
namespace App\Controllers;

use HNova\Api\{Routes, res};

Routes::get('test', function(){ 
    return res::json("Hola mundo"); 
});