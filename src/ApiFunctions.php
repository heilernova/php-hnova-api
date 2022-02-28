<?php
/*
 * This file is part of Phpnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Phpnv\Api;

use Phpnv\Api\Date\DateFunctions;

class ApiFunctions
{
    /**
     * Genera un string aleatorio ustilizado el random_bytes y bin2hex
     * @param int $long número de caracteres que tendra el string generado, el número debe ser mayor o igual 4,
     * en caso de ser menor a 4, por defecto usara el 4
     */
    public static function generateToken(int $long = 4):string
    {
        if ($long < 4) $long = 4;
        return bin2hex(random_bytes(($long - ($long % 2) /2)));
    }

    /**
     * Retorna un objeto que funcionalidades para el manejo de los intervalos entre dos fechas
     * @param string $date Formato para asignar la fecha: yyyy-mm-dd hh:m:s
     */
    public static function date(string $date = 'now'):DateFunctions
    {
        return new DateFunctions($date);
    }
}