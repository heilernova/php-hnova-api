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
use HNova\Api\ApiRoot;
use HNova\Api\Db\Database;
use HNova\Api\Funs;
use HNova\Api\Panel\PanelBaseController;
use HNova\Api\Response;
use HNova\Api\Scripts\Files;
use mysqli;

class DbController
{
    /**
     * Retorna un array con la información de la base de datos. 
     * */
    function get(){
        return ApiRoot::getConfig()->databases->getAll();
    }

    function post(){
        $data = Api::request()->getData();
        $config = $_ENV['api-rest']->config;
        
        if (!array_key_exists($data->name, (array)$config->databases)){

            $name = $data->name;
            unset($data->name);
            $config->databases->$name = $data;
            ApiRoot::getConfig()->salve();
            return true;
        }else{
            Api::response()->httpResponseCode(400);
            Api::response()->message->addContent('El nombre ya esta en uso');
            return null;            
        }

    }

    function delete(string $name){
        $config = $_ENV['api-rest']->config;
        
        if (array_key_exists($name, (array)$config->databases)){
            unset($config->databases->$name);
            ApiRoot::getConfig()->salve();
            return true;
        }else{
            Api::response()->httpResponseCode(404);
            return null;            
        }
    }


    function put(string $name){
        $config = $_ENV['api-rest']->config;
        $dbs = (array)$_ENV['api-rest']->config->databases;
        $data = Api::request()->getData();

        if (array_key_exists($name, $dbs)){
            $db = $dbs[$name];

            $name_new = $data->name;

            if ($data->name != $name){
                $db = [];
                // Actaulimoa loa sROA
                foreach ($config->databases as $key => $value){
                    if ($key == $name){
                        $db[$name_new] = $value;
                    }else{
                        $db[$key] = $value;
                    }
                }

                $config->databases = (object)$db;

                // Actualizamos los datos
                foreach ($config->routes as $route){
                    if ($route->database == $name){
                        $route->database = $name_new;
                    }
                }
            }

            unset($data->name);
            $config->databases->$name_new = $data;

            ApiRoot::getConfig()->salve();
            return true;
        }else{
            Api::response()->httpResponseCode(404);
            return null;
        }
    }

    function test(){
        $data = Api::request()->getData();

        $db = new Database($data);
        return true;
    }

}