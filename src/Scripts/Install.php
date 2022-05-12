<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Scripts;

use HNova\Api\Settings\ApiConfig;

class Install
{
    public static function run()
    {
        Console::log("Instalando paquetes");
        $dir = Script::getAppDir();
        

        // Validamos que la instalación no se halla ejecutado
        if ($dir != "api"){
            if (file_exists("api.json")){
                Console::error("El install ya se ejecuta");
                exit;
            }
        }

        if (!file_exists($dir)) mkdir($dir);

        // Creamos la carta prinfipal
        if ($dir != "api"){
            if (file_exists("$dir")){
                if (filesize("$dir")){
                    Console::error("El directorio [src] ya se encuetra en suo.");
                    exit;
                }
            }
        }

    
        $api_config = ApiConfig::initConfig();

        // Archivo públicos
        Files::addFile('www/.htaccess', Templates::getWWWHtaccess());
        Files::addFile('www/index.php', Templates::getWWWIndex($dir));

        Files::addFile("$dir/app.json", str_replace('\/','/', json_encode($api_config, 128)));
        Files::addFile("$dir/app.php", Templates::getIndex());
        Files::addFile("$dir/Routes/.routes.php", "");
        Files::addFile("$dir/Guards/AppGuard.php", "");

        if (!file_exists("$dir/Controllers")) mkdir("$dir/Controllers");
        if (!file_exists("$dir/Bin")) mkdir("$dir/Bin");
        if (!file_exists("$dir/Models")) mkdir("$dir/Models");
        if (!file_exists("$dir/Guards")) mkdir("$dir/Guards");

        // Actualizamos el composer.json
        $composer = json_decode(file_get_contents('composer.json'));
        $composer->autoload->{'psr-4'}->{'App\\'} = "$dir/";
        Files::addFile('composer.json', str_replace('\/','/', json_encode($composer, 128)));
        Files::loadFiles();

        Console::log("Fin ... ");
    }
}