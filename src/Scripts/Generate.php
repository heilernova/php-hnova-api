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

use HNova\Api\Scripts\console;
use HNova\Api\Scripts\Script;
use HNova\Api\Settings\AppConfig;

class Generate
{
    public static function execute():void
    {
        $arg = Script::getArgument();
        if ($arg){
            switch ($arg){
                case "class":
                    // Crea una clase.
                    
                    break;
                case ("c" || "controller"):
                    // crea un controlador
                    echo "Controller";
                    break;
                case ("m" || "model");
                    // Crea un modelo
                    break;
                case "api":
                    break;
                default:
                    console::error("Comando invalido");
            }
        }else{
            console::error("Falta ingresar comandos");
        }
    }

    /**
     * Crea un clase en clase ruta ingresada.
     */
    private static function class():void
    {

    }

    public static function app(AppConfig $app){

        $namespace = $app->getNamespace();

        // Routes
        $content = file_get_contents( __DIR__.'./../../template/example/example-routes.php' );
        $content = str_replace("Example", $namespace, $content);
        Script::fileAdd("app/$namespace/$namespace-routes.php", $content);
        
        //  Base controller
        $content = file_get_contents(__DIR__.'./../../template/example/ExampleBaseController.php');
        $content = str_replace("Example", $namespace, $content);
        Script::fileAdd("app/$namespace/$namespace" . "BaseController.php", $content);

        //  Base model
        $content = file_get_contents(__DIR__.'./../../template/example/ExampleBaseModel.php');
        $content = str_replace("Example", $namespace, $content);
        Script::fileAdd("app/$namespace/$namespace" . "BaseModel.php", $content);
        
        // Guards
        $content = file_get_contents(__DIR__.'./../../template/example/ExampleGuards.php');
        $content = str_replace("Example", $namespace, $content);
        Script::fileAdd("app/$namespace/$namespace" . "Guards.php", $content);

        // Script::getEvent()->getComposer()
        $composer_json = json_decode(file_get_contents("composer.json"), true);

        $composer_json['autoload']['psr-4']["$namespace\\"] = "app/$namespace/";

        Script::fileUpdate("composer.json", str_replace('\/', '/',json_encode($composer_json, 128)));
        // echo json_encode($composer_json, 128); exit;

        

    }
}