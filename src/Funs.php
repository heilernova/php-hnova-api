<?php
/**
 * esta clase contendra las funcionalidades que herederan los controladores de la API.
 */

namespace HNova\Api;

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

}