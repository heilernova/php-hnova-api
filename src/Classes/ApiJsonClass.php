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
namespace HNova\Api\Classes;

class ApiJsonClass
{
    public string $name = "";

    /** @var UserClass */
    public object $user;

    /**
     * Array de los desarrolladores
     * @param DeveloperClass[]
     */
    public array $developers = [];
    
    public bool $debug = true;
    
    /**
     * Array asociativo de los bases de datos
     * @var DatabaseInfoClass[]
     */
    public array $databases;

    /**
     * Array asociativo de la app
     * @var AppInfoClass[]
     */
    public array $apps;


    public function __construct(object $data = null)
    {
        if (is_null($data)){
            $this->user = new UserClass("admin", "");
            $this->developers[] = new DeveloperClass("heiler", "heiler@gmail.com");
            $this->databases["test"] = new DatabaseInfoClass("msyql", ["hostname"=>"localhost", "username"=>"root", "password"=>"", "database"=>"test"]);
            $this->apps["app"] = new AppInfoClass("app", "App");
        }else{
            $this->name = $data->name;
            $this->user = $data->user;
            $this->debug = $data->debug;
            $this->developers = $data->developers;
            $this->databases = (array)$data->databases;
            $this->apps = (array)$data->apps;
        }
    }
}