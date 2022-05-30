<?php
/**
 * In the class you can add access restrictions to sytem paths
 * by using the guards store inthe static class
 */

namespace HNnamespace\Guards;

use HNova\Api\{Api, req, res};

class NameGuard
{
    public static function authenticate():callable
    {
        /**
         * Guard is a function that returns null to give access to the system.
         * in case of returning a HNova\Api\Response, it will not be possible access the acction of the route
         *
         * en este caso todas la ruta que implemento el el guard authenticate dependerar de que el
         * randim_int sea igual a 1 para acceder al controladroa asosiado a la ruta. 
         */
        return function(){
            if (random_int(1,2) == 1){

                // We return null to give access
                return null;
            }else{

                // We return a HNova\Api\Response to deny access
                return res::send('access denied')->status(401);
            }
        };
    }
}