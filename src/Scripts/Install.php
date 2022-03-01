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

use HNova\Api\Classes\ApiJsonClass;
use HNova\Api\Classes\AppInfoClass;
use HNova\Api\Settings\ApiConfig;

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
        $api_config = ApiConfig::init();

        $io = Script::getEvent()->getIO();
        $api_name = $io->ask("¿Nombre de la api (app) ?:", 'app');

        $api_config->getUser()->setUsername("admin");
        $api_config->getUser()->setPassword("admin");
        $api_config->getApps()->add("app", "App");
        $api_config->getDatabases()->add("test", "mysql", ["hostname"=>"localhost", "username"=>"root", "password"=>"", "database"=>"test" ]);


        // Creamos los directorios.
        // Script::fileAdd("app/app-index.php", file_get_contents(__DIR__.'./../../template/app-index.php'));

        // // Cramos las archivos de la carpeta www
        // Script::fileAdd("www/.htaccess","RewriteEngine On\nRewriteRule ^(.*) index.php?url=$1 [L,QSA]");
        // Script::fileAdd("www/index.php","<?php\nrequire __DIR__.'./../app/app-index.php'");
        // Script::fileAdd("api.json", str_replace('\/', '/', json_encode($api_config->salve(), 128)));
        // Script::fileCreate();

        echo json_encode($api_config->salve(), 128);
    }
}