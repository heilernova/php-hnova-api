<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api;

use HNova\Api\Data\Database;

class ApiController
{
    protected Database $database;
    public function __construct()
    {
        $db = Api::getConfig()->getDatabase();
        if ($db) $this->database = $db;
    }

    /**
     * Obtiene el contenido del body decodificado
     * El contenido del body debe estar en formato json.
     * @param bool $assoc Si es true retornara un array asositivo en caso controlario un objeto
     */
    public function getBody(bool $assoc = false):object|array
    {
        return json_decode(file_get_contents("php://input"), $assoc);
    }
}