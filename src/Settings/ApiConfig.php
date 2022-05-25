<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Settings;

class ApiConfig
{
    public function __construct(
        public Databases $databases = new Databases(),
        public Developers $developers = new Developers()
    ){}
    

    public static function initConfig():Object{
        return (object)[
            'name' => "Nombre del projecto",
            'timezone' => 'UTC',
            'user' => (object)[
                'username'  => '$2y$04$A8dGLVylvwo/0eLebRIam.jJ6xLqfrnMKay2m1xB7cmptEYAyGp9.',
                'password'  => '$2y$04$A8dGLVylvwo/0eLebRIam.jJ6xLqfrnMKay2m1xB7cmptEYAyGp9.',
                'email'     => null
            ],
            'developers' => [
                (object)['name'=>'Nombre del desarollador', 'email'=>'correo electrónico']
            ],
            'debug' => true,
            'databases' => (object)[
                'test'  => (object)[
                    'type' => 'mysql',
                    'dataConnection'=> (object)[
                        'hostname' => "localhost",
                        'username' => 'root',
                        'password' => '',
                        'database' => 'test'
                    ]
                ]
            ],
            'routes' => (object)[
                './' => (object)[
                    'database' => 'test',
                    'disable' => false,
                    'cors' => (object)[
                        'origin'  => null,
                        'headers' => null,
                        'methods' => null
                    ]
                ]
            ]
        ];
    }

    public function authenticate(string $username, string $password): bool{
        return password_verify($username,  $_ENV['api-rest']->config->user->username) && password_verify($password,  $_ENV['api-rest']->config->user->password);
    }

    public function getDir():string{
        return $_ENV['api-rest']->dir;
    }

    public function getName():string{
        return $_ENV['api-rest']->config->name;
    }
    
    public function setName(string $name):void{
        $_ENV['api-rest']->config->name = $name;
    }

    /**
     * Obtiene le tipo de zona horaria por la API
     */
    public function getTimezone():string{
        return $_ENV['api-rest']->config->timezone;
    }

    /**
     * Establecle la zona horía
     */
    public function setTimezone(string $timezone):void{
        $_ENV['api-rest']->config->timezone = $timezone;
    }

    /**
     * Si es true la API respondera la información en caso de error
     */
    public function isDebug():bool{
        return $_ENV['api-rest']->config->debug;
    }
}