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
            return new ApiInfo($name, $d->namespace, $d->resourcesDir, $d->defaultDatabase, $d->cors, $d->disable);
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

    /**
     * Retrona un array de las apis de la aplicación
     * @return object[]
     */
    public function getAll():array{
        return (array)$this->data->apis;
    }

    /**
     * Desactiva la api, esto negaria el acceso a todas peticiones realizadas.
     */
    public function disableApi(string $name):bool{
        $listApis = (array)$this->data->apis;

        if (array_key_exists($name, $listApis)){
            $this->data->$name->disable = true;
            return true;
        }else{
            return false;
        }
    }

    /**
     * Activa el accesoa  todas las peticion realizadas.
     */
    public function eneableApi(string $name):bool{
        $listApis = (array)$this->data->apis;

        if (array_key_exists($name, $listApis)){
            $this->data->$name->disable = true;
            return true;
        }else{
            return false;
        }
    }
}