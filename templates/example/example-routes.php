<?php
/**
 * Ingrese aquí las rutas de acceso de la api
 */
namespace Example\Controllers;

use Example\ExampleGuards;
use HNova\Api\Response;
use HNova\Api\Routes;

Routes::get("test", function(){ return new Response("Hola mundo"); });