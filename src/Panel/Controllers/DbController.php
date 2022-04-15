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
            $value->src = $_ENV['api-dir'] . "/databases/$key/tables";
            $value->structure = (object)[
                'tables'=>[],
                'views'=>[]
            ];

            $path = $_ENV['api-dir'] . "/databases/$key/tables/";

            $dir = opendir($_ENV['api-dir'] . "/databases/$key/tables");
            while ($elemento = readdir($dir)){
                // Tratamos los elementos . y .. que tienen todas las carpetas
                if( $elemento != "." && $elemento != ".."){

                    if( !is_dir($path.$elemento) ){
                        $value->structure->tables[] = [
                            'name' => basename($elemento, '.sql'),
                            'creationCode' =>file_get_contents($path.$elemento)
                        ];

                    }
                }
            }
            $path = $_ENV['api-dir'] . "/databases/$key/views/";

            $dir = opendir($_ENV['api-dir'] . "/databases/$key/views");
            while ($elemento = readdir($dir)){
                // Tratamos los elementos . y .. que tienen todas las carpetas
                if( $elemento != "." && $elemento != ".."){

                    if( !is_dir($path.$elemento) ){
                        $value->structure->views[] = [
                            'name' => basename($elemento, '.sql'),
                            'creationCode' =>file_get_contents($path.$elemento)
                        ];

                    }
                }
            }

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