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

try{
    $model_name = array_shift($arguments);

    if (!$model_name){
        Console::log('Falta ingrsar el nombre del modelo');
        return null;
    }

    $model_name_ucf = ucfirst($model_name);

    $name_table = array_shift($arguments);
    if (!$name_table){
        Console::log('Falta definir el nombre la tabla por defecto del model.', CONSOLE_COLOR_TEXT_ROJO_CLARO);
        return null;
    }

    if (Script::getApiConfig()->isMultiApi()){
        $api_name = array_shift($arguments);
        if ($api_name){
            $api = Script::getApiConfig()->getApis()->find($api_name);
        }else{
            Console::log('Debe espeficicar el nombre de la api');
            return null;
        }
    }else{
        $api = Script::getApiConfig()->getApis()->find('api');
    }

    $dir_model = $api->getDir() . "/Http/Models";
    if (!file_exists($dir_model)){
        mkdir($dir_model);
    }

    if (file_exists("$dir_model/$model_name" . "Model.php")){
        Console::log('El nombre del modelo ya esta en uso.');
        return null;
    }

    $content = file_get_contents(__DIR__.'/../templates/api-http-model.txt');
    $content = str_replace('$Api' , $api->getNamespace(), $content);
    $content = str_replace('$Class' , $model_name, $content);
    $content = str_replace('$Table' , "'$name_table'", $content);

    Script::addFile("$dir_model/$model_name_ucf" . "Model.php", $content);
    
    Script::createFiles();

} catch(\Throwable $th){
    throw $th;
}