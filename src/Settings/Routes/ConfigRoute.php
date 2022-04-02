<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Settings\Routes;

use HNova\Api\ApiException;
use HNova\Api\Settings\HTTP\Cors;

class ConfigRoute
{
    
    public function __construct(private object $_data, private ?Cors $_cors = null)
    {

        $this->_cors = new Cors($_data->cors);
        // echo json_encode($this->_cors, 128); exit;
    }

    public function getCORS():Cors
    {
        return $this->_cors;    
    }

    public function disabled():bool
    {
        return $this->_cors->disable ?? false;
    }

    /**
     * Carga los CORS de la ruta
     */
    public function loadCORS():void
    {
        try {
            $string_origin = $this->getCORS()->getOrigin()->getValueString();
            $string_headers = $this->getCORS()->getHeaders()->getValueString();
            $string_methods = $this->getCORS()->getMetods()->getValueString();

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
                
                if ($string_headers){
                    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
                        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                    }
                }
                
                exit(0);
            }
        } catch (\Throwable $th) {
            throw new ApiException(['Error al establecer los CORS'], $th);
        }
    }
}