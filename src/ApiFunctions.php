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

use Phpnv\Api\Date\DateFunctions;

class ApiFunctions
{
    /**
     * Genera un string aleatorio ustilizado el random_bytes y bin2hex
     * @param int $long número de caracteres que tendra el string generado, el número debe ser mayor o igual 4s
     */
    public static function generateToken(int $long = 4)
    {
        if ($long < 4) $long = 4;
        return bin2hex(random_bytes(($long - ($long % 2) /2)));
    }

    /**
     * @param string $date Formatro yyyy-mm-dd hh:m:s
     */
    public static function date(string $date = 'now'):DateFunctions
    {
        return new DateFunctions($date);
    }
}