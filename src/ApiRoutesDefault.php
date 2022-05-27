<?php

use HNova\Api\Http\ResponseView;
use HNova\Api\res;
use HNova\Api\Routes;

Routes::get('', function(){
    return res::php(__DIR__ . '/Views/index.php');
});