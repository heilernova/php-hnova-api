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
use HNova\Api\Scripts\Generate;
use HNova\Api\Settings\ApiConfig;

class Script
{
    /**
     * Almacena el envento de composer
     */
    private static Event $event;

    /**
     * Alamacen la información de los archivos ha crear.
     * @var object[]
     */
    private static array $files = [];


    /**
     * Alamacen la información de los archivos ha crear.
     * @var object[]
     */
    private static array $filesUpdate = [];

    private static array $args = [];

    private static ApiConfig $config;

    /**
     * retorna el evento de composer.
     */
    public static function getEvent():Event
    {
        return self::$event;
    }

    public static function getConfig():ApiConfig
    {
        return self::$config;
    }

    /**
     * Retorna el nombre del directorio htdost.
     */
    public static function getDirXammp():string
    {

        $v = Script::getEvent()->getComposer()->getConfig()->getConfigSource()->getName();
        $n =  strpos($v, 'htdocs');
        return dirname(substr($v, $n + 7));
    }

    /**
     * Rertona el primer argumento enviado por consola, en caso de retorna null es porqur no hay argumentos.
     */
    public static function getArgument():?string
    {
        $arg = array_shift(self::$args);
        return $arg ? strtolower($arg) : null;
    }

    /**
     * Ejecuta el script de [ composer nv ]
     */
    public static function execute(Event $event):void
    {
        self::$event = $event;
        try {
            // Cargamos el primer argumento
            self::$args = $event->getArguments();

            $arg = self::getArgument(); 

            switch ($arg) {
                case null:
                    console::error("Falta ingresar comandos en el script");
                    break;
                case "g":
                    self::$config = new ApiConfig(json_decode(file_get_contents("composer.json")));
                    Generate::execute();
                    break;
                    case "generate":
                    self::$config = new ApiConfig(json_decode(file_get_contents("composer.json")));
                    Generate::execute();
                    break;
                case "i":
                    self::$config = ApiConfig::init();
                    Install::run();
                    break;
                    case "install":
                    self::$config = ApiConfig::init();
                    Install::run();
                    break;
                case "test":
                    require __DIR__.'./test.php';
                    break;
                default:
                    console::error("Comando invalido");
                    break;
            }

        } catch (\Throwable $th) {
            console::error("");
            console::error("Error de ejecución");
            console::error("File:       " . $th->getFile());
            console::error("Line:       " . $th->getline());
            console::error("Message:    " . $th->getMessage());
            console::error("");
        }
    }

    /**
     * Agrega un la datos datos para crear un archivo.
     */
    public static function fileAdd(string $name, string $content):void
    {
        self::$files[] = (object)["name" => $name, "content" => $content ];
    }

    public static function fileUpdate(string $name, string $content):void{
        self::$filesUpdate[] = (object)["name" => $name, "content" => $content ];
    }

    /**
     * Crea los archivos armacenado.
     */
    public static function fileCreate():void
    {
        foreach (self::$files as $file){
            $name = $file->name;
            if (file_exists($name)){
                console::error("El archivo ya esta creado [ $name ]");
            }else{
                $dirmane = dirname($name);
                if (!file_exists($dirmane)){
                    mkdir($dirmane);
                }

                // Creamos el archivo.
                $f = fopen($file->name, 'a');
                fputs($f, $file->content);
                fclose($f);

                console::fileCreate($file->name);

            }
        }
        foreach (self::$filesUpdate as $file){
            $name = $file->name;
            if (!file_exists($name)){
                console::error("No se encotro el archivo para acutalizar [ $name ]");
            }else{
 
                // Abrimos el archivo.
                $f = fopen($file->name, 'w+');
                fputs($f, $file->content);
                fclose($f);

                console::fileUpdate($file->name);

            }
        }



    }




}