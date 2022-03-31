<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Settings\Classes;

class ApiConfigData
{
    public string $name = "";
    public string $timezone = "UTC";
    public object $user;
    public array $developers;
    public bool $debug = true;
    public object $databases;
    public object $routes;
     
    public function __construct(object $data = null)
    {
        if ($data){

            $this->name = $data->name;
            $this->timezone = $data->timezone;
            $this->developers = $data->developers;
            $this->debug = $data->debug;
            $this->databases = $data->databases;
            $this->routes = $data->routes;

        }else{

            $this->developers = [];
            $this->databases = (object)[
                'test'=>[
                    'type'=>'mysql',
                    'dataConnection'=>[
                        "hostname"=> "localhost",
                        "username"=> "root",
                        "password"=> "",
                        "database"=> "test"
                    ]
                ]
            ];
            $this->routes = (object)[
                'default'=>[
                    'cors'=>[
                        'origin'=>null,
                        'headers'=> null,
                        'metods' => null
                    ]
                ]
            ];
        }

    }
}