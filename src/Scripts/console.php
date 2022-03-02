<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * https://codigonaranja.com/como-cambiar-el-color-del-texto-en-aplicaciones-de-consola-de-php
 */
namespace HNova\Api\Scripts;

class console
{

    public static function log(string $text):void
    {
        echo "$text\n";
    }

    public static function error(string $text):void
    {
        echo "\e[1;31m$text\e[0m\n";
    }

    public static function alert(string $text):void
    {
        echo "\e[1;33m$text\e[0m\n";
    }

    public static function fileCreate(string $name):void
    {
        if (file_exists($name)){
            $zise = filesize($name);
        }else{
            $zise = 0;
        }
        echo "\e[1;32mCREATE:\e[0m $name ( $zise bytes )\n";
    }
    public static function fileUpdate(string $name):void
    {
        if (file_exists($name)){
            $zise = filesize($name);
        }else{
            $zise = 0;
        }
        echo "\e[1;36mUPDATE:\e[0m $name ( $zise bytes )\n";
    }
}