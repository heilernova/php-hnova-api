<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Scripts;

class Templates
{
    private static $_dir = __DIR__.'./../../templates/';

    public static function getIndex():string
    {
        return file_get_contents(self::$_dir . 'index.api.php');
    }

    public static function getRoutes():string
    {
        return file_get_contents(self::$_dir . 'routes.php');
    }

    public static function getWWWIndex(string $rotue):string
    {
        return str_replace('ruote', "$rotue/app.php", file_get_contents(self::$_dir . 'www/index.php'));
    }

    public static function getWWWHtaccess():string
    {
        return file_get_contents(self::$_dir . 'www/.htaccess');
    }

    public static function getController($name, $namespace, $long = "")
    {
        $searh = ['HNnamespace', 'Name', 'Long'];
        $replace = [$namespace, $name, $long];
        return str_replace($searh, $replace, file_get_contents(self::$_dir . 'Controllers/Controller.php'));
    }

    public static function getGuard($name = 'App', $namespace = "App"):string
    {
        $searh = ['HNnamespace', 'Name'];
        $replace = [$namespace, $name];
        return str_replace($searh, $replace, file_get_contents(self::$_dir . 'Guard.php'));
    }

    public static function getModel($name, $namespace ,$namespace_long):string
    {
        $searh =["HNnamespace", "Name", 'Long'];
        $replace = [$namespace, $name, $namespace_long];
        return str_replace($searh, $replace, file_get_contents(self::$_dir . 'Models/Model.php'));
    }

    public static function getRoute():string{
        return file_get_contents(self::$_dir . "Routes/.routes.php");
    }
}