<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 */
namespace HNova\Api\Date;

use DateInterval;
use DateTime;

class DateFunctions
{
    /**
     *@param $date Formato para asignar la fecha: yyyy-mm-dd hh:m:s, por defector es now = fecha actual.
     */
    public function __construct(private $date = "now"){

    }

    /**
     * Retorna un objeto DateTime
     */
    public function getDateTime():DateTime
    {
        return new DateTime($this->date);
    }

    /**
     * Retorna el objeto DateInterval
     */
    public function getDiff(string $date = 'now'):DateInterval
    {
        return $this->getDateTime()->diff(new DateTime($date));
    }

    /**
     * Retorna el tiempo tracurrido entre fecha en un string
     * @param string $date Fecha final.
     * @param string $format Formato de respuesta. [ 'date', 'datetime']
     */
    public function getDiffString(string $date = 'now',string $format = 'datetime'):string
    {
        $date_star = $this->getDateTime();
        $date_diff = $date_star->diff(new DateTime($date));
        $time = '';
        if ($format == 'date' && $format == 'datetime'){
            $time = '';
            if ($date_diff->y > 0) $time = $date_diff->y == 1 ? ' año, ' : ' años, ';
            if ($date_diff->m > 0) $time .= $date_diff->m == 1 ? ' mes, ' : ' meses, ';
            if ($date_diff->d > 0) $time .= $date_diff->d == 1 ? ' dia, ' : ' dias, ';
            if ($format == 'datetime'){
                if ($date_diff->h > 0) $time .= $date_diff->h == 1 ? ' hora, ' : ' horas, ';
                if ($date_diff->i > 0) $time .= $date_diff->i == 1 ? ' minuto, ' : ' minutos, ';
                // if ($date_diff->s > 0) $time .= $date_diff->s == 1 ? ' segundo, ' : ' segundos, ';
            }
            $time = rtrim($time, ', ');
        }
        return $time;
    }
}