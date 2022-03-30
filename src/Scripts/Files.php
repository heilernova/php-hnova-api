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

class Files
{
    /** @var object[] */
    private static array $_files = [];


    public static function addFile(string $path, string $content):void
    {
        self::$_files[] = (object)["path"=>$path, 'content'=>$content];
    }

    public static function loadFiles():void
    {
        foreach (self::$_files as $item){
            Console::fileCreate($item->path);
        }
    }
}