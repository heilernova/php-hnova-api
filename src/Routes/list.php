<?php

use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

// Routes::any('nv/errors', function(){ return new Response('Hola'); }, [function(){ return null; }]);
// Routes::any('nv/errors', function(){ return new Response('Hola'); });
Routes::get('nv-panel', function(){
    require __DIR__.'/../view/nv-panel/index.php';
    exit;
    // return new Response('hola');
});