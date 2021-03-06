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

use HNova\Api\Http\Response;

class res{

    public static function send(string $value):Response{
        return new Response($value, ['type'=>'text']);
    }

    /**
     * Para retornar un objeto JSON
     */
    public static function json(mixed $value):Response{
        return new Response($value);
    }

    public static function file(string $path, bool $auto_delete =  false):Response{
        if (!file_exists($path)) throw new ApiException(['No es econtro el archivo para retornar por la API'], null, 'not found', 404);
        return new Response($path, ['type'=>'file', 'auto-delete'=>$auto_delete]);
    }

    public static function html(string $html_string):Response{
        return new Response($html_string,['type'=>'html']);
    }

    public static function php(string $path):Response{
        return new Response($path, ['type'=>'php']);
    }
}