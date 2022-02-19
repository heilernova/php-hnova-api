<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Config\Apis;

use Phpnv\Api\Api;
use Phpnv\Api\Config\Databases\DatabaseInfo;
use Phpnv\Api\Data\Database;
use Stringable;

class ApiInfo
{
    /**
     * @param string $name nombre del proyecto este nombre debe ser unico.
     * @param string $namespace
     * @param string $dir Directorio relativo donde se encuentran alojados los componentes de la api.
     * @param string $resourcesDir Nombre del direcctorio donde se encuentran alojados los recursos multimedio y documentos.
     * @param string $defaultDatabase  Nombre de la base de datos 
     * @param object $objecto con la información de los cors
     */
    public function __construct(
        private string $name,
        private string $namespace,
        private string $resourcesDir,
        private string $defaultDatabase,
        private object $cors
    ){}

    public function getObject():object
    {
        return (object)[
            "namespace"=>$this->namespace,
            "resourcesDir"=>$this->resourcesDir,
            "defaultDatabase"=>$this->defaultDatabase,
            "cors"=>$this->cors
        ];
    }

    /**
     * Retrona el nombre de la api.
     */
    public function getName():string
    {
        return $this->name;
    }

    /**
     * Retorna el namespace de la api
     */
    public function getNamespace():string
    {
        return 'Api' . (!empty($this->namespace) ?  ("\\" . $this->namespace ) : '');;
    }

    /**
     * Retorna el directorio donde esta alojada api
     */
    public function getDir():string
    {
        return Api::getDir() . (!empty($this->namespace) ?  ("/" . $this->namespace) : '');
    }

    /**
     * Carga la rutas de la api.
     */
    public function getLoadRoutes():void
    {
        // echo $this->getDir(); exit;
        require $this->getDir() . "/" . $this->name . "-routes.php";
    }

    /**
     * Retorna el directorio donde esta alojada los recursos
     */
    public function getResourcesDir():string
    {
        return Api::getDir() . "/$this->resourcesDir";
    }

    /**
     * Retorna un objete para interactura con la base de datos
     */
    public function getDefaultDatabase():Database
    {
        // echo json_encode(Api::getConfig()->getDatabases()->)
        $data = Api::getConfig()->getDatabases()->find($this->defaultDatabase);
        return new Database($data->type, $data->dataConnection);
    }

    public function getCors():object
    {
        return $this->cors;
    }
}