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
        return str_replace('ruote', "$rotue/index.api.php", file_get_contents(self::$_dir . 'www/index.php'));
    }

    public static function getWWWHtaccess():string
    {
        return file_get_contents(self::$_dir . 'www/.htaccess');
    }

    public static function getBaseController(string $namespace = "ApiRest"):string
    {
        return str_replace('HNnamespace', $namespace, file_get_contents(self::$_dir . 'BaseController.php'));
    }
    public static function getBaseModel(string $namespace = "ApiRest"):string
    {
        return str_replace('HNnamespace', $namespace, file_get_contents(self::$_dir . 'BaseModel.php'));
    }
    public static function getBaseDB(string $namespace = "ApiRest"):string
    {
        return str_replace('HNnamespace', $namespace, file_get_contents(self::$_dir . 'BaseDB.php'));
    }

    public static function getController($name, $namespace)
    {
        $searh = ['HNnamespace', 'Name'];
        $replace = [$namespace, $name];
        return str_replace($searh, $replace, file_get_contents(self::$_dir . 'Controllers/Controller.php'));
    }

    public static function getGuard($name = '', $namespace = "ApiRest"):string
    {
        $searh = ['HNnamespace', 'Name'];
        $replace = [$namespace, $name];
        return str_replace($searh, $replace, file_get_contents(self::$_dir . 'Guard.php'));
    }

    //
    public static function getObjectDB($name, $namespace):string{
        $searh = ['HNnamespace', 'Name'];
        $replace = [$namespace, $name];
        return str_replace($searh, $replace, file_get_contents(self::$_dir . 'DB/ObjectDB.php'));
    }

    public static function getModel($name, $namespace ,$namespace_long):string
    {
        $searh =["HNnamespace", "Name", 'Long'];
        $replace = [$namespace, $name, $namespace_long];
        return str_replace($searh, $replace, file_get_contents(self::$_dir . 'Models/Model.php'));
    }

}