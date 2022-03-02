<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Panel\Controllers;

use HNova\Api\Api;
use HNova\Api\Http\ResponseApi;
use HNova\Api\Response;
use HNova\Api\Routes;

Routes::get("test", function(){
    return new Response(date_default_timezone_get());
});


// Retornamos la lista de apis
Routes::get("apis", function(){
    return new Response([]);
});

Routes::get("apis/{name}", function($name){
    $info = Api::getConfig()->getApps()->get($name);

    return new Response($info ? $info->getInfo() : null);
});

// Desabiluita el acceso a una api
Routes::patch("apis/{name}/disable", function(string $name){
    $re = Api::getConfig()->getApps()->get($name);
    return new Response($re);
});

// Habilita el acceso a una api
Routes::patch("api/{name}/enable", function(string $name){
    return new Response(true);
});