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

use Exception;
use HNova\Api\ApiException;
use HNova\Api\Settings\Classes\ApiConfigData;

class ConfigRoutes
{
    public function __construct(private ApiConfigData $_dataConfig)
    {
        
    }

    public function getCount():int
    {
        return count((array)$this->_dataConfig->routes);
    }

    public function get(string $name = 'default'):ConfigRoute
    {
        if (isset($this->_dataConfig->routes->$name)){

            return new ConfigRoute($name, (object)$this->_dataConfig->routes->$name);
        }else{
            throw new ApiException(["No se enctro la configuración para la ruta $name"]);
        }
    }
}