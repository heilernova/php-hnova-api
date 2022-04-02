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

class PanelGuard 
{
    static function auth():callable{
        return function(){
            $headers = apache_request_headers();
            $ok = false;
            if (array_key_exists('nv-panel-token', $headers)){
                $path = Api::getConfig()->getDir() . "./../nv-panel/.access-token";
                if (file_exists($path)){

                    $token = file_get_contents($path);
                    $ok = ($token == $headers['nv-panel-token']);
                }
            }

            if ($ok){
                return null;
            }else{
                Response::SetHttpResponseCode(401);
                return "not authenticated";
            }
        };
    }
}