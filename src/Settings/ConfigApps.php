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


class ConfigApps
{
    public function __construct(private object $apps)
    {
        
    }

    /**
     * Retorna un array de AppConfig de los 
     * @return AppConfig[]
     */
    public function getAll():array
    {
       $list = [];
        foreach ($this->apps as $key => $element ){
            $app = new AppConfig($key, $element->namespace);
            $app->disable = $element->disable;
            $app->dirResources = $element->dirResources;
            $app->cors = new CorsConfig($element->cors->origin, $element->cors->headers, $element->cors->methods);

            $list[$key] = $app;
        }
       return $list;
    }

    /**
     * Retorna la configuración de un app, retorna null en caso de que no se encuentre la app
     * @param string|null $name En caso de ser null retorna la primera app en la lista.
     */
    public function get(string $name = null):?AppConfig
    {
        if ($name){
            if (isset($this->apps->$name)){
                $app = new AppConfig($name, $this->apps->$name);
                return $app;
            }else{
                return null;
            }
        }else{
            $app = null;

            foreach ($this->apps as $key=>$value){
                $app = new AppConfig($key, $value);
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
        $this->apps->$name = (object)[
            "namespace"=>$namespace,
            "disable"=>false,
            "dirResource"=>"",
            "database"=> null,
            "cors"=> (object)[
                "origin"  => null,
                "headers" => null,
                "methods" => null
            ]
        ];
    }
}