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

class Convert
{
    /**
     * Convierto un array a un object
     */
    public static function arrayToObject(array $array, $class_name):object
    {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($class_name),
            $class_name,
            strstr(serialize($array), ':')
        ));
    }

    public static function objectToObject($instance, $class_name):object
    {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($class_name),
            $class_name,
            strstr(strstr(serialize($instance), '"'), ':')
        ));
    }
}