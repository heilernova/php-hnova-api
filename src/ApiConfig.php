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

use Phpnv\Api\Config\Apis;
use Phpnv\Api\Config\Databases;
use Phpnv\Api\Config\Developers;

class ApiConfig
{
    private object $dataConfig;
    /**
     * Caraga la configuraciones de la api.
     */
    public function __construct(string|object|null $data, private string $dir  = '')
    {
        if (is_string($data)){
            $this->dataConfig = json_decode(file_get_contents($data));
        }else if (is_object($data)){
            $this->dataConfig = $data;
        }else{
            $this->dataConfig = (object)[
                "name"          => "api name",
                "description"   => "",
                "developers"    => [],
                "user"          => (object)['username'=>'admin', 'password'=>'$2y$10$fTIvJOYB8bMluMfX.qMUrO78AeTHX3pTAL0LMaZyIDkQMH6Y8XPqi'],
                "debug"         => true,
                "databases"     => (object)[],
                "multiApi"      => false,
                "apis"          => (object)[]
            ];
        }
    }

    public function getDir():string
    {
        return $this->dir;
    }

    public function getName():string
    {
        return $this->dataConfig->name;
    }
    public function setName(string $name):void
    {
        $this->dataConfig->name = $name;
    }

    public function getDescription():string
    {
        return $this->dataConfig->description;
    }
    public function setDescription(string $description):void
    {
        $this->dataConfig->description = $description;
    }


    public function getUser():object
    {
        return $this->dataConfig->user;
    }
    public function setUser(string $username, $password):void
    {
        $this->dataConfig->user = (object)['username'=>$username, 'password'=>$password];
    }

    public function getDevelopers():Developers
    {
        return new Developers($this->dataConfig);
    }

    public function isDebug():bool
    {
        return $this->dataConfig->debug;
    }
    public function setDebug(bool $debug):void
    {
        $this->dataConfig->debug = $debug;
    }

    public function isMultiApi():bool
    {
        return $this->dataConfig->multiApi;
    }

    public function setMultiApi(bool $multi_api):void
    {
        $this->dataConfig->multiApi = $multi_api;
    }

    public function getDatabases():Databases
    {
        return new Databases($this->dataConfig);
    }

    public function getApis():Apis
    {
        return new Apis($this->dataConfig, $this->dir);
    }

    public function salve():void
    {
        $file = fopen($this->dir . "/api.json", 'w+');
        fputs($file, str_replace('\/', '/', json_encode($this->dataConfig, 128)));
        fclose($file);
    }

    public function getObjectConfig():object
    {
        return $this->dataConfig;
    }

}