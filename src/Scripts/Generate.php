<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Scripts;

class Generate
{
    public static function controller():void{
        $name = Script::getArgment();
        if ($name){

            if (str_contains($name, '-')){
                $names = explode('-', $name);
                $name = "";
                foreach ($names as $v){
                    $name .= ucfirst($v);
                }
            }
            $name .= "Controller";
            $name = ucfirst($name);
            $path = Script::getConfig()->getDir() . "/Controllers/$name.php";
            
            if (file_exists($path)){
                Console::error("Comflito: el nombre del controlador ya esta en usuo");
            }else{
                Files::addFile($path, Templates::getController($name, "ApiRest"));
            }

            Files::loadFiles();
        }else{
            Console::error("Faltan argumentos");
        }
    }

    public static function db():void
    {
        $name = Script::getArgment();
        if ($name){

            if (str_contains($name, '-')){
                $names = explode('-', $name);
                $name = "";
                foreach ($names as $v){
                    $name .= ucfirst($v);
                }
            }
            $name .= "DB";
            $name = ucfirst($name);
            $path = Script::getConfig()->getDir() . "/DB/$name.php";
            
            if (file_exists($path)){
                Console::error("Comflito: el nombre del ObjectDB ya esta en usuo");
            }else{
                Files::addFile($path, Templates::getObjectDB($name, "ApiRest"));
            }

            Files::loadFiles();
        }else{
            Console::error("Faltan argumentos");
        } 
    }

    public static function model():void
    {
        $name = trim(Script::getArgment(), "/");

        $name_explode = explode('/', $name);

        $name_tempo = ".";
        foreach ($name_explode as $value){
            $name_tempo .= "/" . ucfirst($value);
        }

        $name_explode = explode('-', $name_tempo);

        $name_tempo = "";
        foreach ($name_explode as $value){
            $name_tempo .= ucfirst($value);
        }

        $namespace = "ApiRest";
        
        $namespace_long = "";
        $namespace_long = '\\' . str_replace('/', '\\', ltrim(dirname($name_tempo),"/|."));
        
        $name_tempo = ltrim($name_tempo,"/|.") . "Model";
  
        $name_tempo = Script::getConfig()->getDir() . "/Models/$name_tempo.php";
        if (file_exists($name_tempo)){
            Console::error("Comflito: el nombre del ObjectDB ya esta en uso.");
            exit;
        }

        Files::addFile($name_tempo, Templates::getModel(basename($name_tempo, '.php').PHP_EOL, "ApiRest", $namespace_long));
        
        Files::loadFiles();
    }
}