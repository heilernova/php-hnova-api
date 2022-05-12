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
    /**
     * Crea controladores
     */
    public static function controller():void{
        $name = Script::getArgment();
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
    
            $name_tempo_1 =  ltrim(dirname($name_tempo),"/|.");
            
            $namespace_long = "";

            if ($name_tempo_1){
                $namespace_long = '\\' . str_replace('/', '\\', $name_tempo_1);
            }
            
            $name_tempo = ltrim($name_tempo,"/|.") . "Controller";

            $path = Script::getConfig()->getDir() . "/Controllers/$name_tempo.php";
            
            if (file_exists($path)){
                Console::error("Comflito: el nombre del controlador ya esta en usuo");
            }else{
                Files::addFile($path, Templates::getController(basename($name_tempo, '.php'), "App", $namespace_long));
            }

            Files::loadFiles();
        }else{
            Console::error("Faltan argumentos");
        }
    }

    /** Crea modelos */
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

        $name_tempo_1 =  ltrim(dirname($name_tempo),"/|.");
        
        $namespace_long = "";

        if ($name_tempo_1){
            $namespace_long = '\\' . str_replace('/', '\\', $name_tempo_1);
        }
        
        $name_tempo = ltrim($name_tempo,"/|.") . "Model";
  
        $name_tempo = Script::getConfig()->getDir() . "/Models/$name_tempo.php";
        if (file_exists($name_tempo)){
            Console::error("Comflito: el nombre del Modelo ya esta en uso.");
            exit;
        }

        Files::addFile($name_tempo, Templates::getModel(basename($name_tempo, '.php'), "App", $namespace_long));
        
        Files::loadFiles();
    }

    /**
     * Crea una nueva ruta de accesso
     */
    public static function route()
    {
        $dir = Script::getConfig()->getDir();
        $name_route = strtolower(Script::getArgment());

        $name_file = str_replace('/', '-', $name_route);
       

        $data = [
            'database'=>'name',
            'disable'=>false,
            'cors' => [
                'origin'=> null,
                'headers'=>null,
                'methods'=>null
            ]
        ];

        if (file_exists("$dir/Routes/$name_file.routes.php")){
            Console::error("Conflicto: la rauta ya existe");
            exit;
        }

        // Creamos los ficheros
        $config = $_ENV['api-rest']->config;
        $config->routes->$name_route = $data;
        $new_config = str_replace('\/', '/', json_encode($config, 128));
        Files::addFile("$dir/app.json", $new_config);
        Files::addFile("$dir/Routes/$name_file.routes.php", Templates::getRoute());
        Files::loadFiles();
    }
}