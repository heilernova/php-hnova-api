<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Panel;

use Phpnv\Api\Api;
use Phpnv\Api\Response;

class PanelGuard
{
    public static function authenticate():callable
    {
        return function(){
            $headers = apache_request_headers();
            if (array_key_exists('nv-panel-access', $headers)){
                // Api::getDir();
                $path = Api::getDir() . "/nv-panel/.token-access.txt";
                if (file_exists($path)){
                    $token = file_get_contents($path);
                    if ($headers['nv-panel-access'] == $token){
                        return null;
                    }else{
                        return new Response('No access', 401);
                    }
                }else{
                    return new Response('No access', 401);
                }
            }else{
                return new Response('No access', 401);
            }
        };
    }
}