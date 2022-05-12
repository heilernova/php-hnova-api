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
use mysqli;

class DbController extends PanelBaseController
{
    /**
     * Retorna un array con la información de la base de datos. 
     * */
    function get(){
        $databases = [];

        foreach (Api::getConfig()->getConfigData()->databases as $key=>$value){
            $dbInfo = (object)[
                'name'=>$key,
                'status'=>false,
                'type'=>'mysql',
                'dataConnection'=>(object)[
                    'hostname'=>$value->dataConnection->hostname,
                    'username'=>$value->dataConnection->username,
                    'password'=>$value->dataConnection->password,
                    'database'=>$value->dataConnection->database
                ],
                'structure' =>  (object)[
                    'tables'    =>[],
                    'views'     =>[],
                    'procedures'=>[],
                    'functions' =>[]
                ],
                'sqlInstall' => (object)[
                    'tables'    =>[],
                    'views'     =>[],
                    'procedures'=>[],
                    'functions' =>[]
                ],
            ];
            
            // Test de la conexión de la base de datos.

            try{

                $con = mysqli_connect(
                    $value->dataConnection->hostname,
                    $value->dataConnection->username,
                    $value->dataConnection->password,
                    $value->dataConnection->database
                );

                $tables = $con->query("SHOW FULL TABLES")->fetch_all(MYSQLI_NUM);

                foreach ($tables AS $table){
                    if ($table[1] == "VIEW"){
                        $dbInfo->structure->views[] = [ 
                            'name'=>$table[0]
                        ];
                    }else{
                        $dbInfo->structure->tables[] = [
                            'name'=>$table[0]
                        ];
                    }
                }

                $dbInfo->status = true;
            } catch (\Throwable $th) {
                $dbInfo->errorConnection = $th->getMessage();
            }
            

            // Definirmo el directorio de las estruture de la base de datos
            $dir = $_ENV['api-dir'] . "/databases/$key";
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
                $dbInfo->sqlInstall->tables = $tables;
                $dbInfo->sqlInstall->views = $views;
                $dbInfo->files = $structure_files;
            }

            $databases[] = $dbInfo;

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

                Response::message()->addContent("Error con los datos de conexión");
                Response::message()->addContent("Mensaje: " . $th->getMessage());
                return false;
            }


        }
    }

    /**
     * Actualiza la los datos de conexión de la base de datos.
     */
    function put(string $name){
        $data = $this->getBody(true);
        $config = Api::getConfig();
        
        if (isset($config->getConfigData()->databases->$name)){

            try {
                $d = mysqli_connect(
                    $data['hostname'],
                    $data['username'],
                    $data['password'],
                    $data['database']
                );

                $config->getConfigData()->databases->$name = [
                    'type'=>'mysql',
                    'dataConnection'=>$data
                ];
                $config->salve();
                return true;
            } catch (\Throwable $th) {

                Response::message()->addContent("Error con los datos de conexión");
                Response::message()->addContent("Mensaje: " . $th->getMessage());
                return false;
            }

        }else{
            Response::message()->addContent("No se encontro la base de datos a la cual aplicar los cambios.");
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
            Response::message()->addContent("No existe el nombre de la base de datos.");
            return false;
        }
    }
}