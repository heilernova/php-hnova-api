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

namespace HNova\Api\Scripts;

class Install
{
    /**
     * Inicia la instalación de la app
     */
    public static function run():void
    {
        // Validamos que la instalación no se halla ejecutado.
        if (file_exists("api.json")){
            console::error("El install ya se ejecuto.");
        }


        // Creamo el objeto de api json
        $api_json = (object)[
            "name" => "Application name",
            "user" => (object)[
                "username" => "admin",
                "password" => ""
            ],
            "developers" => [
                (object)[ "name" => "developer 1", "email" => "email@email" ]
            ],
            "debug" => true,
            "databases"=> (object)[
                "test" => (object)[
                    "type" => "mysql",
                    "dataConnection" => (object)[
                        "hostname"  => "localhost",
                        "username"  => "root",
                        "password"  => "",
                        "database"  => "test"
                    ]
                ]
            ],
            "apps" => (object)[]
        ];

        $io = Script::getEvent()->getIO();
        $api_name = $io->ask("¿Nombre de la api (app) ?:", 'app');

        $api_json->apps->$api_name  = (object)[
            "namespace" => ucfirst($api_name),
            "disable"   => false,
            "dirResources" => "../public",
            "database" => "test"
        ];


        // Creamos los directorios.
        Script::fileAdd("app/app-index.php", file_get_contents(__DIR__.'./../../template/app-index.php'));

        // Cramos las archivos de la carpeta www
        Script::fileAdd("www/.htaccess","");
        Script::fileAdd("www/index.php","<?php\nrequire __DIR__.'./../app/app-index.php'");
        Script::fileAdd("api.json", str_replace('\/', '/', json_encode($api_json, 128)));
        Script::fileCreate();
    }
}