<?php
/**
 * Clase controlador que contiene los metodos a ser ejecutados por las rutas.
 * 
 * todos lo métodos deben retornar un HNova\Api\Response
 */
namespace Example\Controllers;

use Example\ExampleBaseController;
use HNova\Api\Http\ResponseApi;
use HNova\Api\Response;

class NameControllerController extends ExampleBaseController
{
    function get():Response
    {
        /**
         * Aqui el código a ejecutar
         */
        return new Response("Hola mundo");
    }
}