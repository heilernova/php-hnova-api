<?php
/**
 * En esta clase puede agregar las restrcicioines de aceceso a las rutas del sistema
 * mediante el uso del los guard almacenso el calse estatica.
 */

namespace Example;

use HNova\Api\Response;

class ExampleGuards
{
    public static function authenticate():callable
    {
        /**
         * el guard es un función que retunr null para dar acceso al sistema.
         * en caso de retrona un Response no se podra accesder al controlador de la ruta.
         *
         * en este caso todas la ruta que implemento el el guard authenticate dependerar de que el
         * randim_int sea igual a 1 para acceder al controladroa asosiado a la ruta. 
         */
        return function(){
            if (random_int(1,2) == 1){
                return null;
            }else{
                return new Response('No access',  401);
            }
        };
    }
}