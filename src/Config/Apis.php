<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Config;

use Phpnv\Api\Config\Apis\ApiInfo;
use Phpnv\Api\Config\ConfigInfo;

class Apis
{
    public function __construct(private $data, private $dir = ''){}

    /**
     * Buscar en api.json la api que corresponde al nombre ingresado y retorna un objeto con la información
     * @param string $name nombre de la api
     * @param ApiInfo|null Retorna null en caso de no encontrase la api.
     */
    public function find(string $name):ApiInfo|null
    {
        // echo json_encode($this->data->apis, 128);
        $listApis = (array)$this->data->apis;

        if (array_key_exists($name, $listApis)){
            $d = $listApis[$name];
            return new ApiInfo($name, $d->namespace, $d->resourcesDir, $d->defaultDatabase, $d->cors);
        }else{
            return null;
        }
    }

    /**
     * Agrega una api al objecto api.json, importante darle salve apiconfig para guardar los cambios.
     * @param string $name Nombre de la api.
     * @param string $namespace,
     * @param string $rosourcesDir
     * @param string $default_database Nombre de la base de datos por defecto.
     * @param object $cors Objecto con los cors
     */
    public function add($name, $namespace, $resourcesDir, $default_database, $cors)
    {
        $this->data->apis->$name = (new ApiInfo($name, $namespace, $resourcesDir, $default_database, $cors))->getObject();
    }

    public function count():int
    {
        return count((array)$this->data->apis);
    }

}