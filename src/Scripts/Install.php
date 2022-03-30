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
        if (file_exists("$dir/api.json")){
            Console::error("El install ya se ejecuta");
            exit;
        }

        if (!file_exists($dir)) mkdir($dir);

        // Creamos la carta prinfipal
        if (file_exists("$dir/src")){
            if (filesize("$dir/src")){
                Console::error("El directorio [src] ya se encuetra en suo.");
                exit;
            }
        }

        $api_config = ApiConfig::initInstall();

        Files::addFile("$dir/api.json", json_encode($api_config->getConfigData()));

        Files::addFile("$dir/src/index.php", "");
        Files::loadFiles();
    }
}