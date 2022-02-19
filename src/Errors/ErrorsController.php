<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Errors;

use Phpnv\Api\Api;
use Phpnv\Api\Response;

class ErrorsController
{
    function getErrors():Response
    {
        $dir = Api::getDir() . "/nv-panel/errors/list.txt";

        $list = ltrim(file_get_contents($dir));
        $list = json_decode("[$list]");
        return new Response($list);
    }
}