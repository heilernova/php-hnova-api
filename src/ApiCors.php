<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api;

class ApiCors
{
    public static function load(string $string_origin = null, string $string_headers = null, string $string_methods = null)
    {
        try {

            if ($string_origin) header("Access-Control-Allow-Origin:  $string_origin");
            if ($string_headers) header("Access-Control-Allow-Headers: $string_headers");
            if ($string_methods) header("Access-Control-Allow-Methods: $string_methods");
            // ------------------------ CORS

            if (isset($_SERVER['HTTP_Origin'])) {
                header("Access-Control-Allow-Origin: {$_SERVER['HTTP_Origin']}");
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 86400');    // cache for 1 day
            }

            // Access-Control headers are received during OPTIONS requests
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])){
                    if ($string_methods) header("Access-Control-Allow-Methods: $string_methods");
                }
                
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
                    header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                }
                
                exit(0);
            }
        } catch (\Throwable $th) {
            throw new ApiException(['Error al establecer los CORS'], $th);
        }
    }

    public static function loadApi(){
        $cors = Api::getApi()->getCors();

        $string_origin = null;
        $string_headers = null;
        $string_methods = null;

        if ($cors->origin){
            if (is_array($cors->origin)){
                $string_origin  = '';
                foreach ($cors->origin as $element) $string_origin .= ", $element";
                $string_origin = ltrim($string_origin, ', ');
            }else{
                $string_origin = $cors->origin;
            }
        }

        if ($cors->headers){
            if (is_array($cors->headers)){
                $string_headers = '';
                foreach ($cors->headers as $element) $string_headers .= ", $element";
                $string_headers = ltrim($string_headers, ', ');
            }else{
                $string_headers = $cors->headers;
            }
        }

        if ($cors->methods){
            if (is_array($cors->methods)){
                $string_methods = '';
                foreach ($cors->methods as $element) $string_methods .= ", $element";
                $string_methods = ltrim($string_methods, ', ');
            }else{
                $string_methods = $cors->methods;
            }
        }
        self::load($string_origin, $string_headers, $string_methods);
    }
}