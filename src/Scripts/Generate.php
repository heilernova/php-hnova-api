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
}