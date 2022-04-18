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

    public static function loadFiles($echo = true):void
    {
        foreach (self::$_files as $item){

            $path = $item->path;

            $edit = file_exists($path);

            $dir_name = dirname($path);
            if (!file_exists($dir_name)){
                $dir_names = explode('/', $dir_name);
                $dir = ".";
                foreach ($dir_names as $name){
                    $dir .= "/$name";
                    if (!file_exists($dir)) mkdir($dir);
                }
            }
            
            $file = fopen($path, $edit ? 'w' : 'a');
            fputs($file, $item->content);
            fclose($file);
            if ($echo){
                if ($edit){
                    Console::fileUpdate($item->path);
                }else{
                    Console::fileCreate($item->path);
                }
            }
        }
    }
}