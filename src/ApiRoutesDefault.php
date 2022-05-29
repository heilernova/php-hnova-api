<?php

use HNova\Api\{Routes, res, req};

Routes::get('', function(){
    return res::php(__DIR__ . '/Views/index.php');
});