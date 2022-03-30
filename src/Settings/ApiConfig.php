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

class ApiConfig
{
    private ApiConfigData $_dataConfig;

    /**
     *@param object JSON de la configuración
     */
    public function __construct(object $data_json)
    {
        if ($data_json::class == ApiConfigData::class){
            $this->_dataConfig = $data_json;
        }else{
            $this->_dataConfig = new ApiConfigData($data_json);
        }
    }

    public static function initInstall():ApiConfig
    {
        $api_cofig_data = new ApiConfigData(null);
        return new ApiConfig($api_cofig_data);
    }

    public function getConfigData():ApiConfigData{
        return $this->_dataConfig;
    }
}