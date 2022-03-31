<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Settings;

use HNova\Api\Settings\Classes\ApiConfigData;
use HNova\Api\Settings\Routes\ConfigRoutes;

class ApiConfig
{
    private ApiConfigData $_dataConfig;

    /**
     *@param object JSON de la configuración
     */
    public function __construct(object $data_json = null)
    {
        if ($data_json){
            if ($data_json::class == ApiConfigData::class){
                $this->_dataConfig = $data_json;
            }else{
                $this->_dataConfig = new ApiConfigData($data_json);
            }
        }else{
            $dir = $_ENV['api-dir'];

            $this->_dataConfig = new ApiConfigData(json_decode(file_get_contents("$dir/api.json")));
        }
    }

    public static function initInstall():ApiConfig
    {
        $api_cofig_data = new ApiConfigData(null);
        return new ApiConfig($api_cofig_data);
    }

    /**
     * Retrona el directorio don se encutra el codigo de APIREST
     */
    public function getDir():string
    {
        return $_ENV['api-dir-src'];
    }

    /**
     * Retrona el objecto que que representa el api.json
     */
    public function getConfigData():ApiConfigData{
        return $this->_dataConfig;
    }


    public function getRoutes():ConfigRoutes{
        return new ConfigRoutes($this->_dataConfig);
    }
}