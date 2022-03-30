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
        $this->developers = [];
        $this->databases = (object)[
            'test'=>[
                'type'=>'mysql',
                'dataConnection'=>[
                    "hostname"=> "localhost",
                    "username"=> "root",
                    "password"=> "",
                    "database"=> "ssm"
                ]
            ]
        ];
        $this->routes = (object)[];

        if ($data){
            $this->developers = $this->developers;
        }
    }
}