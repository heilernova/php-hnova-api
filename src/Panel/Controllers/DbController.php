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
use HNova\Api\Funs;
use HNova\Api\Panel\PanelBaseController;
use HNova\Api\Response;
use HNova\Api\Scripts\Files;

class DbController extends PanelBaseController
{
    /**
     * Retorna un array con la información de la base de datos. 
     * */
    function get(){
        $databases = [];

        foreach (Api::getConfig()->getConfigData()->databases as $key=>$value){
            $value->name = $key;
            
            
            // Obtenemos la estructura
            $dir = $_ENV['api-dir'] . "/databases/$key";
            $value->structure = (object)[
                'tables'    =>[],
                'views'     =>[],
                'procedures'=>[],
                'functions' =>[]
            ];

            $structure_files = [];
            $structure = "";

            // Recoremos todos los ficheros si existen
            if (file_exists($dir) && (filesize($dir) > 0)){

                $open_dir = opendir($dir);
                while ($element = readdir($open_dir)){
                    if ($element != "." && $element  != ".."){

                        if (is_dir("$dir/$element")){
                            $o = opendir("$dir/$element");
                            
                            while ($element_sub_1 = readdir($o)){
                                if ($element_sub_1 != "." && $element_sub_1  != ".."){
                                    if (is_dir($element_sub_1)){

                                    }else{
                                        $structure_files[] = "$element_sub_1";
                                        $structure .= file_get_contents("$dir/$element/$element_sub_1");
                                    }
                                }
                            }
                        }else{

                            // Si es un fichero obtenermos us contenido
                            $structure_files[] = $element;
                            $structure .= file_get_contents("$dir/$element");
                        }
                    }
                }

            }
            $structure = explode(';', $structure);
            $tables = [];
            $views = [];
            foreach ($structure as $sql){
                $sql = trim($sql);
                if (str_starts_with($sql, 'CREATE TABLE')){
                    $temp = substr($sql, strpos($sql, '`') + 1);
                    $temp = substr($temp, 0, strpos($temp, '`'));
                    // $tables[] = $temp . ' :: ' .$sql ;
                    $tables[] = [
                        'name'=>$temp,
                        'drop'=>"DROP TABLE IF EXISTS `$temp`",
                        'creationCode'=>$sql,
                    ];
                }else if(str_starts_with($sql, 'CREATE VIEW')){
                    $temp = substr($sql, strpos($sql, '`') + 1);
                    $temp = substr($temp, 0, strpos($temp, '`'));
                    // $tables[] = $temp . ' :: ' .$sql ;
                    $views[] = [
                        'name'=>$temp,
                        'drop'=>"DROP VIEW IF EXISTS `$temp`",
                        'creationCode'=>$sql,
                    ];
                }
            }
            $value->structure->tables = $tables;
            $value->structure->views = $views;
            // echo json_encode($views); exit;
            $value->files = $structure_files;
            // $path = $_ENV['api-dir'] . "/databases/$key/tables/";

            // $dir = opendir($_ENV['api-dir'] . "/databases/$key/tables");
            // while ($elemento = readdir($dir)){
            //     // Tratamos los elementos . y .. que tienen todas las carpetas
            //     if( $elemento != "." && $elemento != ".."){

            //         if( !is_dir($path.$elemento) ){
            //             $value->structure->tables[] = [
            //                 'name' => basename($elemento, '.sql'),
            //                 'creationCode' =>file_get_contents($path.$elemento)
            //             ];

            //         }
            //     }
            // }
            // $path = $_ENV['api-dir'] . "/databases/$key/views/";

            // $dir = opendir($_ENV['api-dir'] . "/databases/$key/views");
            // while ($elemento = readdir($dir)){
            //     // Tratamos los elementos . y .. que tienen todas las carpetas
            //     if( $elemento != "." && $elemento != ".."){

            //         if( !is_dir($path.$elemento) ){
            //             $value->structure->views[] = [
            //                 'name' => basename($elemento, '.sql'),
            //                 'creationCode' =>file_get_contents($path.$elemento)
            //             ];

            //         }
            //     }
            // }

            $databases[] = $value;

        }

        return $databases;
    }

    /**
     * Crea una nueva base de datos.
     */
    function post(){
        $data = $this->getBody();
        $config = Api::getConfig();
        $name = $data->name;

        if (isset($config->getConfigData()->databases->$name)){
            return false;
        }else{

            try {
                $connection = mysqli_connect(
                    $data['hostname'],
                    $data['username'],
                    $data['password'],
                    $data['database']
                );
                            
                $config->getConfigData()->databases->$name = [
                    'type'=>'mysql',
                    'dataConnection'=>$data->dataConnection
                ];

                $config->salve();
                return true;
            } catch (\Throwable $th) {

                Response::addMessage("Error con los datos de conexión");
                Response::addMessage("Mensaje: " . $th->getMessage());
                return false;
            }


        }
    }

    /**
     * Actualiza la los datos de conexión de la base de datos.
     */
    function put(string $name){
        $data = $this->getBody();
        $config = Api::getConfig();
        
        if (isset($config->getConfigData()->databases->$name)){

            try {
                $d = mysqli_connect(
                    $data['hostname'],
                    $data['username'],
                    $data['password'],
                    $data['database']
                );

                $config->getConfigData()->databases->$name = $data;
                $config->salve();
                return true;
            } catch (\Throwable $th) {

                Response::addMessage("Error con los datos de conexión");
                Response::addMessage("Mensaje: " . $th->getMessage());
                return false;
            }

        }else{
            Response::addMessage("No se encontro la base de datos a la cual aplicar los cambios.");
            return false;
        }
    }

    /**
     * Elimina una base de datos.
     */
    function delete(string $name){
        $config = Api::getConfig();
        
        if (isset($config->getConfigData()->databases->$name)){
            unset($config->getConfigData()->databases->$name);
            return true;
        }else{
            Response::addMessage("No existe el nombre de la base de datos.");
            return false;
        }
    }
}