<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Phpnv\Api\Scripts;

try {
    $io = Script::getEvent()->getIO();

    $api_name = '';
    $api_name_ucf = '';
    $api_namespace = "Api";
    $api_dir = "";
    $api_default_database = "default";
    $api_cors = (object)['origin'=>null, 'headers'=>null, 'methods'=>null];

    if (Script::getApiConfig()->isMultiApi()){
        $api_name = '';
        
        while ($api_name == '' || $api_name == 'api'){
            $api_name = strtolower($io->ask("Nombre de api: ", ''));
        }
        $api_name_ucf = ucfirst($api_name);
        $api_namespace .= "\\$api_name_ucf";
        $api_dir .= "/$api_name_ucf";
        // mkdir($api_dir);
    }else{
        if (Script::getApiConfig()->getApis()->count() == 0){
            $api_name = 'api';
            $api_name_ucf = 'Api';
        }else{
            Console::log('Ya hay una api creada.');
        }
    }
    $api_dir = trim($api_dir, '/');

    $dir_tempo = Script::getApiConfig()->getDir() .  ($api_dir != '' ? "/$api_dir" : '');

    Script::addFile("$dir_tempo/$api_name-routes.php", str_replace('$Api', $api_namespace, file_get_contents(__DIR__.'/../templates/api-routes.txt')));
    Script::addFile("$dir_tempo/Http/BaseController.php", str_replace('$Api', $api_namespace, file_get_contents(__DIR__.'/../templates/api-http-basecontroller.txt')));
    Script::addFile("$dir_tempo/Http/BaseModel.php", str_replace('$Api', $api_namespace, file_get_contents(__DIR__.'/../templates/api-http-basemodel.txt')));
    Script::addFile("$dir_tempo/Http/Guard.php", str_replace('$Class', $api_name_ucf, str_replace('$Api', $api_namespace, file_get_contents(__DIR__.'/../templates/api-http-guard.txt'))));

    $api_namespace = ltrim($api_namespace, "Api\Api\\");

    
    Script::getApiConfig()->getApis()->add($api_name, $api_namespace,  trim("$api_dir/resources", '/'), $api_default_database, $api_cors);
} catch (\Throwable $th) {
    throw $th;
}