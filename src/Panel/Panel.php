<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Panel;

use HNova\Api\Api;
use HNova\Api\Utils\Funs;

class Panel
{
    public static function generateToken():string
    {
        $token = Funs::generateToken(50);
        $dir = Api::getDir() . "/Bin";

        $file = fopen("$dir/.access-token.txt", file_exists("$dir/.access-token.txt") ? 'w': 'a');

        fputs($file, $token);
        fclose($file);

        return $token;
    }
}