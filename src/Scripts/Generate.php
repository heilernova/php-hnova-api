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
use HNova\Api\Settings\ApiConfig;
use HNova\Api\Settings\AppConfig;

class Generate
{
    public static function execute():void
    {
        $arg = Script::getArgument();

        if ($arg){
            // echo $arg;
            switch ($arg){
                case "class":
                    // Crea una clase.
                    echo "Class";
                    break;
                case "c":
                    // crea un controlador
                    self::controller();
                    break;
                case "controller":
                    // Creamo un controlador
                    self::controller();
                    break;
                case "m";
                    // Creación de un modelo
                    self::model();
                    break;
                case "model";
                    // Creación de un modelo
                    self::model();
                    break;
                case "api":
                    break;
                default:
                    console::error("Comando generate invalido");
                    break;
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

    public static function controller(){

        $name = Script::getArgument();
        if ($name){
            $name = ucfirst($name);
            if (Script::getConfig()->getAppsCount() > 1){
                // Se debe espeficiara a cual app se le creara el controlador.
                echo "Espeficiacar el controlador.";
            }else{
                $app = Script::getConfig()->getApps()->get();
            }

            $dir = "app/" . $app->getNamespace() . "/Controllers/$name" . "Controller.php";

            if (file_exists($dir)){
                console::error("El nombre del controlador ya esta en uso."); exit;
            }

            $content = file_get_contents(__DIR__.'./../../template/example/Controllers/Controller.php');
            $content = str_replace("Example", $app->getNamespace(), $content);
            $content = str_replace("NameController", $name, $content);

            Script::fileAdd($dir, $content);
            Script::fileCreate();
        }else{
            console::error("Falta ingresar el nombre de comtrolador [ composer nv g c <name> ]");
        }
    }

    public static function model()
    {
        $name = Script::getArgument();

        if ($name){
            $name = ucfirst($name);
            if (Script::getConfig()->getAppsCount() > 1){
                // Se debe espeficiara a cual app se le creara el controlador.
                $app = Script::getArgument();
                if ($app){
                    $app = Script::getConfig()->getApps()->get($app);
                }else{
                    console::error("Espeficiacar el controlador."); exit;
                }
            }else{
                $app = Script::getConfig()->getApps()->get();
            }

            $dir = "app/" . $app->getNamespace() . "/Models/$name" . "Model.php";

            if (file_exists($dir)){
                console::error("El nombre del modelo ya esta en uso."); exit;
            }

            
            $content = file_get_contents(__DIR__.'./../../template/example/Models/Model.php');
            $content = str_replace("Example", $app->getNamespace(), $content);
            $content = str_replace("Name", $name, $content);

            Script::fileAdd($dir, $content);
            Script::fileCreate();



        }else{
            console::error("Falta ingresar el nombre del modelo [ composer nv g m <name> ]");           
        }
    }

    public static function app(AppConfig $app, bool $auto_sale = true){

        $namespace = $app->getNamespace();

        // Routes
        $content = file_get_contents( __DIR__.'./../../template/example/example-routes.php' );
        $content = str_replace("Example", $namespace, $content);
        Script::fileAdd("src/$namespace/$namespace-routes.php", $content);
        
        //  Base controller
        $content = file_get_contents(__DIR__.'./../../template/example/ExampleBaseController.php');
        $content = str_replace("Example", $namespace, $content);
        Script::fileAdd("src/$namespace/$namespace" . "BaseController.php", $content);

        //  Base model
        $content = file_get_contents(__DIR__.'./../../template/example/ExampleBaseModel.php');
        $content = str_replace("Example", $namespace, $content);
        Script::fileAdd("src/$namespace/$namespace" . "BaseModel.php", $content);
        
        // Guards
        $content = file_get_contents(__DIR__.'./../../template/example/ExampleGuards.php');
        $content = str_replace("Example", $namespace, $content);
        Script::fileAdd("src/$namespace/$namespace" . "Guards.php", $content);

        // Script::getEvent()->getComposer()
        $composer_json = json_decode(file_get_contents("composer.json"), true);

        $composer_json['autoload']['psr-4']["$namespace\\"] = "src/$namespace/";

        Script::fileUpdate("composer.json", str_replace('\/', '/',json_encode($composer_json, 128)));

        if ($auto_sale) Script::fileCreate();
    }
}