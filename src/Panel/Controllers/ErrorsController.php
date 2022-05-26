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
use HNova\Api\ApiRoot;
use HNova\Api\Funs;
use HNova\Api\Panel\PanelBaseController;
use HNova\Api\Response;
use HNova\Api\Scripts\Files;
use mysqli;

class ErrorsController
{
    /**
     * Retorna un array con la información de la base de datos. 
     * */
    function get(){
        $path = Api::getDir() . "/Bin/error.log";
        if (file_exists($path)){
            $content = file_get_contents($path);

            $lines = explode("\n", $content);

            $logs = [];
            foreach ($lines as $line){
                $logs[] = json_decode(substr($line, strpos($line, '{') - 1));
            } 


            return $logs;
        }
        return [];
    }

    
}