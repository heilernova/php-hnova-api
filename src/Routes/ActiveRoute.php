<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Routes;

use HNova\Api\ApiException;
use HNova\Api\ApiRoot;
use HNova\Api\Db\Database;

class ActiveRoute
{
    /**
     * Retorna URL base de la ruta
     */
    public function getBaseURL():string{
        return $_ENV['api-rest']->routes->routeActive->baseURL;
    }

    /** Retorna true si la ruta esta deshabilitada  */
    public function disabled():bool{
        return $_ENV['api-rest']->routes->routeActive->disable;
    }

    /**
     * Retorna la configuración de los CORS
     */
    public function getCORS():object{
        return $_ENV['api-rest']->routes->routeActive->cors;
    }

    /**
     * Carga la configuración de los CORS
     */
    public function loadCORS():void{
        try{
            $fun = function(array|string|null $data):?string{
                if ($data){
                    if (is_array($data)){
                        $text = "";
                        foreach ($data as $value){
                            $text .= ", $value";
                        }
            
                        return ltrim($text, ", ");
                    }else{
                        return $data;
                    }
                }else{
                    return null;
                }
            };

            $origin  = $fun($this->getCORS()->origin);
            $headers = $fun($this->getCORS()->headers);
            $methods = $fun($this->getCORS()->methods);
    
            if ($origin) header("Access-Control-Allow-Origin:  $origin");
            if ($headers) header("Access-Control-Allow-Headers: $headers");
            if ($methods) header("Access-Control-Allow-Methods: $methods");
    
            if (isset($_SERVER['HTTP_Origin'])) {
                header("Access-Control-Allow-Origin: {$_SERVER['HTTP_Origin']}");
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 86400');    // cache for 1 day
            }
    
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                    
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])){
                    if ($headers) header("Access-Control-Allow-Methods: $headers");
                }
                
                if ($headers){
                    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
                        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                    }
                }
                
                exit(0);
            }
        } catch(\Throwable $th){
            throw new ApiException(['Error al establecer los CORS de la RUTA'], $th);
        }
    }

    /**
     * Retorna la configuración.
     * @param string $table Nombre de la tabla por defecto para las consultas SQL
     */
    public function getDatabase(?string $table = null):Database{
        try {
            $database_name = $_ENV['api-rest']->routes->routeActive->database;
            $databases = ApiRoot::getConfig()->databases->get($database_name);

            if ($databases){
                return new Database($databases, $table);
            }else{
                throw new ApiException(['No se encontrol la condifguracioón de la base de datos']);
            }
        } catch (\Exception $th) {
            throw new ApiException(["Error al obtener la base de datos associada a la ruta [$database_name]"], $th);
        }
    }
}