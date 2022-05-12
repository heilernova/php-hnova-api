<?php
/**
 * Script de entrada para el manejo de la API.
 */

use HNova\Api\ApiRoot;

require_once __DIR__.'./../vendor/autoload.php'; // Llamamos el autoload de composer.

/**
 * La ruta esta definida por el archivo .htaccess de la carpeta www
 */
ApiRoot::run($_GET['api-rest-path'])->echo();