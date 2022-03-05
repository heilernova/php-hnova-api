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
            exit;
        }

        $dir = Script::getSRC();

        // Validamos si la carpeta existe y tiene contenido
        if (file_exists($dir)){
            
            if (filesize($dir) > 0){
                // En caso de tener contenido detenemos la ejecución de la instalación
                console::error("El directorio [ src ] ya se encuetra el uso.");
                exit;
            }
        }

        // Creamo el objeto de api json

        $io = Script::getEvent()->getIO();
        $api_name = $io->ask("¿Nombre de tu primera API (nombre por default: [ app ] ) ?: ", 'app');

        Script::getConfig()->getUser()->setUsername("admin");
        Script::getConfig()->getUser()->setPassword("admin");

        Script::getConfig()->getDevelopers()->add("Name developer", "email@email", null);

        $api_namespace = ucfirst($api_name);
        Script::getConfig()->getApps()->add($api_name, $api_namespace);
        Script::getConfig()->getDatabases()->add("test", "mysql", ["hostname"=>"localhost", "username"=>"root", "password"=>"", "database"=>"test" ]);
        Script::getConfig()->getApps()->get($api_name)->setDatabase("test");

        // Creamos los directorios.
        Script::fileAdd("$dir/api-index.php", file_get_contents(__DIR__.'./../../template/api-index.php'));
        
        Generate::app(Script::getConfig()->getApps()->get($api_name), false);

        // // Cramos las archivos de la carpeta www
        Script::fileAdd("www/.htaccess","RewriteEngine On\nRewriteRule ^(.*) index.php?url=$1 [L,QSA]");
        Script::fileAdd("www/index.php","<?php\nrequire __DIR__.'./../$dir/api-index.php';");
        Script::fileAdd("api.json", str_replace('\/', '/', json_encode(Script::getConfig()->getObject(), 128)));
        Script::fileCreate();

        $dir = Script::getDirXammp();
        console::alert("\n  Enlace de acceso: ** http://localhost/$dir/www/  **\n" );
        console::log("  Importante actualizar el autoload de composer [ composer dump-autoload ]\n");
    }
}