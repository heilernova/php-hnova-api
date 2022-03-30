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

        $api_config = ApiConfig::initInstall();

        Files::addFile("api.json", str_replace('\/','/', json_encode($api_config->getConfigData(), 128)));

        Files::addFile('www/.htaccess', Templates::getWWWHtaccess());
        Files::addFile('www/index.php', Templates::getWWWIndex($dir));

        Files::addFile("$dir/index.api.php", Templates::getIndex());
        Files::addFile("$dir/routes.php", Templates::getRoutes());

        Files::addFile("$dir/BaseController.php", Templates::getBaseController());
        Files::addFile("$dir/BaseModel.php", Templates::getBaseModel());
        Files::addFile("$dir/BaseDB.php", Templates::getBaseDB());

        $composer = json_decode(file_get_contents('composer.json'));

        $composer->autoload->{'psr-4'}->{'ApiRest\\'} = "$dir/";

        Files::addFile('composer.json', str_replace('\/','/', json_encode($composer, 128)));
        // $e = Script::getEvent()->getComposer()->getAutoloadGenerator();
        // $e->
        // echo json_encode($e, 128);
        // // Script::getEvent()->getComposer()->setAutoloadGenerator($e);
        Files::loadFiles();
    }
}