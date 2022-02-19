<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Http;

use Phpnv\Api\Api;
use Phpnv\Api\Data\Database;
use Phpnv\Api\Main;
use Phpnv\Api\Package;

class Controller
{
    public Database $database;
    public function __construct()
    {
        $this->database = Api::getApi()->getDefaultDatabase();
    }

    /**
     * Retorna el JSON decodificado enviado en el body.
     * @param bool $associative En caso de que se true retornara un array asociativo del objeto json enviado
     * @return object|array|null Retornara null en caso de que no se pueda decoficar el contenido.
     */
    public function getBody(?bool $associative = false):object|array|null
    {
        return json_decode(file_get_contents('php://input'), $associative);
    }
}