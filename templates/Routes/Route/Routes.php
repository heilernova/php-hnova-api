<?php

use HNova\Api\Routes;
use HNova\Api\Routes\Methods;

Routes::add('test', Methods::Get, function(){ return "Hola mundo"; });