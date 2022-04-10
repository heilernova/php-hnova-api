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

        
        
        // $name = Script::getArgment();
        $name = Script::getArgment(); //trim(Script::getArgment(), "/");
        if ($name){
    
            $name = trim($name, "/");
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
            $name_tempo_1 =  ltrim(dirname($name_tempo),"/|.");
            
            $namespace_long = "";

            if ($name_tempo_1){
                $namespace_long = '\\' . str_replace('/', '\\', $name_tempo_1);
            }
            
            $name_tempo = ltrim($name_tempo,"/|.") . "Controller";

            // if (str_contains($name, '-')){
            //     $names = explode('-', $name);
            //     $name = "";
            //     foreach ($names as $v){
            //         $name .= ucfirst($v);
            //     }
            // }
            // $name .= "Controller";
            // $name = ucfirst($name);
            $path = Script::getConfig()->getDir() . "/Controllers/$name_tempo.php";
            // echo $namespace_long; exit;
            
            if (file_exists($path)){
                Console::error("Comflito: el nombre del controlador ya esta en usuo");
            }else{
                Files::addFile($path, Templates::getController(basename($name_tempo, '.php'), "ApiRest", $namespace_long));
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
        $name_tempo_1 =  ltrim(dirname($name_tempo),"/|.");
        
        $namespace_long = "";

        if ($name_tempo_1){
            $namespace_long = '\\' . str_replace('/', '\\', $name_tempo_1);
        }
        
        $name_tempo = ltrim($name_tempo,"/|.") . "Model";
  
        $name_tempo = Script::getConfig()->getDir() . "/Models/$name_tempo.php";
        if (file_exists($name_tempo)){
            Console::error("Comflito: el nombre del ObjectDB ya esta en uso.");
            exit;
        }

        Files::addFile($name_tempo, Templates::getModel(basename($name_tempo, '.php'), "ApiRest", $namespace_long));
        
        Files::loadFiles();
    }

    public static function route()
    {
        $dir = Script::getConfig()->getDir() . "/Routes";
        $name_route = Script::getArgment();

        $name_explode = explode('-', $name_route);
        $name = "";
        foreach ($name_explode as $value)
        {
            $name .= ucfirst($value);
        }

        $data = [
            'namespace'=>$name,
            'disable'=>false,
            'cors' => [
                'origin'=> null,
                'headers'=>null,
                'methods'=>null
            ]
        ];

        if (file_exists("$dir/$name/$name.php")){
            Console::error("Conflicto: la rauta ya existe");
            exit;
        }        
        
        Files::addFile("$dir/$name/$name.php", Templates::getRouteIndex($name));
        Files::addFile("$dir/$name/$name" . "Guard.php", Templates::getRouteGuard($name));
        Files::addFile("$dir/$name/$name" . "BaseController.php", Templates::getRouteBaseController($name));
        Files::addFile("$dir/$name/$name" . "BaseDB.php", Templates::getRouteBaseDB($name));
        // Files::addFile("$dir/$name/$name" . "BaseDB.php", Templates::getRouteGuard($name));
        // Files::addFile("$dir/$name/$name" . "BaseModel.php", Templates::getRouteGuard($name));
        Files::addFile("$dir/$name/Routes.php", Templates::getRouteRoutes());
        Files::loadFiles();

        $config = Script::getConfig()->getConfigData();
        $config->routes->$name_route = $data;

        Script::getConfig()->salve();
    }
}