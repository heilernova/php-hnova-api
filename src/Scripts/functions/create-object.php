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
    $object_name = array_shift($arguments);

    if (!$object_name){
        Console::log('  Falta ingresar el nombre del objecto');
        return null;
    }else{
        $object_name = ucfirst($object_name);
    }

    if (Script::getApiConfig()->isMultiApi()){
        $api_name = array_shift($arguments);
        if ($api_name){
            $api = Script::getApiConfig()->getApis()->find($api_name);
            if (!$api){
                Console::log("  No se encontro la api ingresada [ $api_name ]");
                return null;
            }
        }else{
            Console::log('  Debe espeficicar el nombre de la api');
            return null;
        }
    }else{
        $api = Script::getApiConfig()->getApis()->find('api');
    }
    
    $dir = $api->getDir() . "/Objects";
    if (file_exists("$dir/$object_name")){
        Console::log("  El nombre del objeto ya esta en uso [ $object_name ]");
        return null;
    }

    if (!$dir){
        mkdir($dir);
    }
    $namespace = $api->getNamespace();

    $content = file_get_contents(__DIR__.'/../templates/api-object.txt');
    $content = str_replace('$Api', $namespace,$content);
    $content = str_replace('$Class', $object_name, $content);
    // echo "$dir/$object_name.php";
    Script::addFile("$dir/$object_name.php", $content);
    Script::createFiles();
} catch (\Throwable $th) {
    throw $th;
}