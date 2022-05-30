<?php
/**
 * Entry scriupt for handling the API
 */

use HNova\Api\ApiRoot;

require_once __DIR__.'./../vendor/autoload.php'; // We call composer autoload

/**
 * The path is defined by the .htaccess file in the www
 */
ApiRoot::run($_GET['api-rest-path'])->echo();