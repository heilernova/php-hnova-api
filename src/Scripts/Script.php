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

/**
 * Case de les escripst que ejecutara composer
 */
class Script
{
    private static string $_mainDir = "";
    private static string $_srcDir = "";
    private static Event $_event;
    /** @var string[] */
    private static array $_args = [];
    

    /**
     * Este escript solo es para testear en desarrollo.
     */
    public static function test(Event $event)
    {
        self::$_srcDir = "test";
    }

    /**
     * Retorna el directorio principal donde se encuetra alojada la aplicación
     */
    public static function getMainDir():string
    {
        // self::$_event
        // $v = self::$_event->getComposer()->getConfig()->getConfigSource()->getName();
        // $n =  strpos($v, 'htdocs');
        // return dirname(substr($v, $n + 7));
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

                    $cmd = self::getArgment();

                    switch ($cmd) {
                        case 'c':
                            # code...
                            break;
                        
                        default:
                            # code...
                            break;
                    }

                    Console::error("gene $arg");
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