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

use Composer\Script\Event;
use Directory;
use Phpnv\Api\Api;
use Phpnv\Api\ApiConfig;

class Script
{
    private static Event $event;
    private static $config;
    private static array $files = [];
    private static $apiConfig;


    public static function getApiConfig():ApiConfig
    {
        return self::$apiConfig;
    }

    public static function setApiConfig(ApiConfig $config)
    {
        self::$apiConfig = $config;
    }

    public static function getEvent():Event
    {
        return self::$event;
    }

    public static function addFile($path, $content)
    {
        self::$files[] = (object)['path'=>$path, 'content'=>$content];
    }

    public static function createFiles()
    {
        foreach (self::$files as $file){
            $dir = dirname($file->path);
            if (!file_exists($dir)){
                mkdir($dir);
            }
            $f = fopen($file->path, 'a+');
            fputs($f, $file->content);
            
            Console::create($file->path);
        }
    }

    public static function getFiles()
    {
        return self::$files;
    }

    public static function execute(Event $event)
    {
        try {
            self::$event = $event;
            $autolaod = $event->getComposer()->getPackage()->getAutoload();
            if (isset($autolaod['psr-4']['Api\\'])){            
                
                if (file_exists($autolaod['psr-4']['Api\\'] . "api.json")){
                    $config = new ApiConfig($autolaod['psr-4']['Api\\'] . "api.json", $autolaod['psr-4']['Api\\']);
                    self::$apiConfig = $config;
                    Api::setDir(trim($config->getDir(),'/'));
                    Console::log(":: Inicio del script - PHPnv", CONSOLE_COLOR_TEXT_CYAN);

                    require __DIR__.'/functions/script-execute.php';
                }else{

                    $arguments =$event->getArguments();

                    if (array_shift($arguments) == 'install'){
                        require __DIR__.'/functions/script-install.php';
                    }else{
                        
                        Console::log('No se encontro el file: ' . $autolaod['psr-4']['Api\\'] . "api.json");
                        Console::log('  en commando [ composer nv install ]  para crear los ficheros');
                    }

                }
                
    
            }else{
                $arguments =$event->getArguments();

                if (array_shift($arguments) == 'install'){
                    require __DIR__.'/functions/script-install.php';
                }else{
                    Console::log('Error no se encotrol el autoload de Api\\');
                }
            }
        } catch (\Throwable $th) {
            Console::log("Error del ejecución", CONSOLE_COLOR_TEXT_ROJO);
            Console::log("Mensaje: ". $th->getMessage());
            Console::log("File: ". $th->getFile());
            Console::log("Line: ". $th->getLine());
        }
    }
}