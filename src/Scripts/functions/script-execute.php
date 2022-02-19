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

use Phpnv\Api\Config\Config;

try {
    $arguments = Script::getEvent()->getArguments();
    
    $command = array_shift($arguments);
    
    switch ($command){
        case 'install':
            Console::log('No se puedo volver a ejcutar el install');
            break;
        case 'g':
            $command_generator = array_shift($arguments);
    
            if ($command_generator){
    
                switch ($command_generator){
                    case 'c':
                        require __DIR__ . '/create-controller.php';
                        break;
                    case 'm':
                        require __DIR__ . '/create-model.php';
                        break;
                    case 'o':
                        require __DIR__ . '/create-object.php';
                        break;
                }
    
            }else{
                Console::log("Error: falta argumentos.", CONSOLE_COLOR_TEXT_ROJO);
            }
    
            break;
        case 'c':
            $command_generator = array_shift($arguments);
            if ($command_generator == 'api'){
                require __DIR__.'/create-api.php';
                Script::createFiles();
                Script::getApiConfig()->salve();
            }else{
                Console::log("Error: falta argumentos.", CONSOLE_COLOR_TEXT_ROJO);
            }
            break;
        default:
            echo "Comando [ nv $command ] invalido";
    }
    
} catch (\Throwable $th) {
    throw $th;
}