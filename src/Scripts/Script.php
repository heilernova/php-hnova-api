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

    /**
     * retorna el evento de composer.
     */
    public static function getEvent():Event
    {
        return self::$event;
    }

    /**
     * Rertona el primer argumento enviado por consola, en caso de retorna null es porqur no hay argumentos.
     */
    public static function getArgument():?string
    {
        $args = self::$event->getArguments();
        return array_shift($args);
    }

    /**
     * Ejecuta el script de [ composer nv ]
     */
    public static function execute(Event $event):void
    {
        self::$event = $event;
        try {
            // Cargamos el primer argumento
            $arg = self::getArgument();

            switch ($arg) {
                case null:
                    console::error("Falta ingresar comandos en el script");
                    break;
                case "g":
                    Generate::execute();
                    break;
                case "generate":
                    Generate::execute();
                    break;
                case "i":
                    Install::run();
                    break;
                case "install":
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