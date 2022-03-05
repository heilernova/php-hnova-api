<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 */
namespace HNova\Api\Settings;


class ConfigApis
{
    public function __construct(private object $apis)
    {
        
    }

    /**
     * Retorna un array de AppConfig de los 
     * @return ApiConfig[]
     */
    public function getAll():array
    {
       $list = [];
        foreach ($this->apis as $key => $element ){

            $apis = new ApiConfig($key, $element);
            $list[$key] = $apis;
        }
       return $list;
    }

    /**
     * Retorna la configuración de un app, retorna null en caso de que no se encuentre la app
     * @param string|null $name En caso de ser null retorna la primera app en la lista.
     */
    public function get(string $name = null):?ApiConfig
    {
        // echo json_encode($this->apps, 128); exit;
        if ($name){
            if (isset($this->apis->$name)){
                $api = new ApiConfig($name, $this->apis->$name);
                return $api;
            }else{
                return null;
            }
        }else{
            $api = null;

            foreach ($this->apis as $key => $value){
                $app = new ApiConfig($key, $value);
                break;
            }

            return $app;
        }
    }

    /**
     * 
     */
    public function add($name, $namespace):void
    {
        $namespace = ucfirst($namespace);
        $this->apis->$name = (object)[
            "namespace"=>$namespace,
            "disable"=>false,
            "dirResources"=>"",
            "database"=> null,
            "cors"=> (object)[
                "origin"  => null,
                "headers" => null,
                "methods" => null
            ]
        ];
    }
}