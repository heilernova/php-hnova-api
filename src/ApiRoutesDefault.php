<?php

use HNova\Api\Http\ResponseView;
use HNova\Api\Routes;

Routes::get('', function(){
    return new ResponseView(__DIR__.'/Views/index.php');
});