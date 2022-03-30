<?php
/**
 * Aqui ingrese la rutas de acceso a la api
 */

use HNova\Api\Api;
use HNova\Api\Routes;
use HNova\Api\Routes\Methods;

Routes::add('test', Methods::Get, function(){ return "Hola mundo"; });