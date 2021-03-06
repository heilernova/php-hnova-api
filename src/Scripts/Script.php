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

use Composer\Script\Event;
use HNova\Api\Settings\ApiConfig;

/**
 * Case de les escripst que ejecutara composer
 */
class Script
{
    private static string $_mainDir = "";
    private static string $_srcDir = "src";
    private static Event $_event;
    /** @var string[] */
    private static array $_args = [];
    private static ApiConfig $_apiConfig;

    /**
     * Este escript solo es para testear en desarrollo.
     */
    public static function test(Event $event)
    {
        self::$_srcDir = "api";
    }

    public static function getEvent():Event{
        return self::$_event;
    }

    /**
     * Retorna el directorio principal donde se encuetra alojada la aplicación
     */
    public static function getMainDir():string
    {
        return self::$_mainDir;
    }

    public static function getAppDir():string
    {
        return self::$_srcDir;
    }

    /**
     * Retorna el argumente
     */
    public static function getArgment():?string
    {
        $arg = array_shift(self::$_args);
        return $arg ? strtolower($arg) : null;
    }

    public static function getConfig():ApiConfig
    {
        return self::$_apiConfig;
    }
    /**
     * Ejecuta los scrips definidos en la aplicación
     */
    public static function execute(Event $event)
    {
        try {
    
            self::$_event = $event;
            self::$_args = $event->getArguments();
            self::$_mainDir = dirname(self::$_event->getComposer()->getConfig()->getConfigSource()->getName());

            $arg = self::getArgment();

            if ($arg){
                
                if ($arg == 'i' || $arg == 'install'){

                    Install::run();

                }else if ($arg == 'g' || $arg == 'generate'){

                    $composer = json_decode(file_get_contents('composer.json'));
                    $dir_src = trim($composer->autoload->{'psr-4'}->{'App\\'}, '/');
                    
                    $cmd = self::getArgment();
                    if (file_exists("$dir_src/app.json")){

                        $_ENV['api-rest'] = (object)[
                            'dir' => $dir_src,
                            'config' => json_decode(file_get_contents("$dir_src/app.json"))
                        ];

                        self::$_apiConfig  = new ApiConfig();
    
                        switch ($cmd) {
                            case 'c':
                                # code...
                                Generate::controller();
                                break;
                            case 'm':
                                Generate::model();
                                break;
                            case 'route':
                                Generate::route();
                                break;
                            case 'r':
                                Generate::route();
                                break;
                            default:
                                # code...
                                break;
                        }
                    }else{
                        Console::error("Se debe instalar la aplicación");
                    }
                }else{
                    Console::error("nv: '$arg' is not a nv command.");
                }
            }else{ 
                Console::log(" -- Comandos validos");
                Console::log("g ");
                Console::log("i ");
            }
        } catch (\Throwable $th) {
            Console::error("");
            Console::error("Error de ejecución");
            Console::error("File:       " . $th->getFile());
            Console::error("Line:       " . $th->getline());
            Console::error("Message:    " . $th->getMessage());
            Console::error("");
        }
    }

}