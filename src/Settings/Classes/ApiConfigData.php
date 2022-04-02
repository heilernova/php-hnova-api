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
            $this->user = $data->user;
            $this->developers = $data->developers;
            $this->debug = $data->debug;
            $this->databases = $data->databases;
            $this->routes = $data->routes;

        }else{

            $this->developers = [];
            $this->user = (object)[
                'username'=>'$2y$04$A8dGLVylvwo/0eLebRIam.jJ6xLqfrnMKay2m1xB7cmptEYAyGp9.',
                'password'=>'$2y$04$A8dGLVylvwo/0eLebRIam.jJ6xLqfrnMKay2m1xB7cmptEYAyGp9.',
                'email'=>null
            ];
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
                    'namespace'=>'',
                    'disable'=>false,
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