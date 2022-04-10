<?php
/**
 * esta clase contendra las funcionalidades que herederan los controladores de la API.
 */

namespace HNova\Api;

use HNova\Api\Date\DateFunctions;

class Funs
{
    /**
     * Genera un string aleatorio ustilizado el random_bytes y bin2hex
     * @param int $long número de caracteres que tendra el string generado, el número debe ser mayor o igual 4,
     * en caso de ser menor a 4, por defecto usara el 4
     */
    public static function generateToken(int $long = 4):string
    {
        if ($long < 4) $long = 4;
        return bin2hex(random_bytes(($long - ($long % 2)) /2));
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