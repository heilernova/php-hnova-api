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
$controller_name = array_shift($arguments);

if (!$controller_name){
    Console::log('Falta ingresar el nombre del controlador.');
    return null;
}
$controller_name = ucfirst( strtolower($controller_name));

if (Script::getApiConfig()->isMultiApi()){
    $api_name = array_shift($arguments);
    if ($api_name){
        $api = Script::getApiConfig()->getApis()->find($api_name);
        if (!$api){
            Console::log("  No se encontro la api ingresada [ $api_name ]");
            return null;
        }
    }else{
        Console::log('Debe espeficicar el nombre de la api');
        return null;
    }
}else{
    $api = Script::getApiConfig()->getApis()->find('api');
}
// echo json_encode($api, 128); exit;
$dir_controllers = $api->getDir() . "/Http/Controllers";
// echo $dir_controllers; exit;
if (!file_exists($dir_controllers)){
    mkdir($dir_controllers);
}


if (file_exists("$dir_controllers/$controller_name" . "Controller.php")){
    Console::log('El nombre del controlador ya esta en uso.');
    return null;
}

$content = file_get_contents(__DIR__.'/../templates/api-http-controller.txt');
$content = str_replace('$Api' , $api->getNamespace(), $content);
$content = str_replace('$Class' , $controller_name, $content);

Script::addFile("$dir_controllers/$controller_name" . "Controller.php", $content);
Script::createFiles();