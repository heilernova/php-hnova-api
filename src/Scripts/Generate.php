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
}