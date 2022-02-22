<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Phpnv\Api\Scripts;

use Phpnv\Api\ApiConfig;
use Phpnv\Api\Config\Config;
use Phpnv\Api\Config\ConfigInfo;

try {
    $io = Script::getEvent()->getIO();

    $dir_src = "src";
    
    if (file_exists($dir_src)){
        if (file_exists("$dir_src/api.json")){
            return null;
        }else{
            Console::log("El direcorio [ src ] ya se enceuntra en uso");
            $name = $io->ask("Escriba el nombre un directorio por defecto (api): ", "api");
            $dir_src = $name;
        }
    }else{
        $name = 'src';
    }

    // Creamos los archivos
    $api_config = new ApiConfig(null, $name);
    $api_config->getDatabases()->add('default', 'mysql', ['hostname'=>'localhost', 'username'=>'root', 'password'=>'', 'database'=>'test']);
    Script::setApiConfig($api_config);

    Console::log('Configuración inicial');

    $multi_api = null;

    while ($multi_api != 's' && $multi_api != 'n'){
        $multi_api = strtolower($io->ask("¿El projecto usar multi apis? ( s/n ): ", ''));
    }

    Script::getApiConfig()->setMultiApi($multi_api == 's');

    $api_config->getDevelopers()->add("Name developer", "developer@email.com");
    Script::addFile("$dir_src/index.php", "<?php\nrequire __DIR__ .'/../vendor/autoload.php';\n\nuse Phpnv\Api\Api;\n\n" . 'Api::run($_GET["url"],__DIR__);');

    // Llamamos el escript para la cración de una api.
    require __DIR__.'/create-api.php';

    Script::addFile("$dir_src/api.json", str_replace('\/', '/', json_encode($api_config->getObjectConfig(), 128)));
    
    // Creamos el directorio www

    if (file_exists("www/.htaccess")) unlink("www/.htaccess");
    if (file_exists("www/index.php")) unlink("www/index.php");

    Script::addFile("www/.htaccess", "RewriteEngine On\nRewriteRule ^(.*) index.php?url=$1 [L,QSA]");
    Script::addFile("www/index.php", "<?php\nrequire '../$dir_src/index.php';");
    
    Script::createFiles();
    $host = basename(dirname(Script::getEvent()->getComposer()->getConfig()->get('vendor-dir')));
    Console::log("  Ruta de acceso api: *** http://localhost/$host/www/ ***",);

    $composer_json = json_decode(file_get_contents("composer.json"), true);
    $composer_json['autoload']['psr-4']['Api\\'] = "$dir_src/";
    $composer_json['scripts']['nv'] = 'Phpnv\\Api\\Scripts\\Script::execute';
    
    $file = fopen('composer.json', 'w+');
    fputs($file, str_replace('\/', '/', json_encode($composer_json, 128)));
    fclose($file);

} catch (\Throwable $th) {
    throw $th;
}