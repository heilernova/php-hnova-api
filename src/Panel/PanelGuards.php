<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Panel;

use HNova\Api\Api;
use HNova\Api\Response;

class PanelGuards
{
    public static function authenticate():callable
    {
        return function(){
            $headers = apache_request_headers();

            if (array_key_exists("nv-panel-token", $headers)){
                $path = Api::getDir() . "/nv-panel/.access-token.txt";
                if (file_exists($path)){
                    $token = file_get_contents($path);
                    if ($token == $headers["nv-panel-token"]){
                        return null;
                    }else{
                        return new Response("Not - access token", 401);
                    }
                }else{  
                    return new Response("Not - access token", 401);
                }
            }else{
                return new Response("Not - access token", 401);
            }
        };
    }
}