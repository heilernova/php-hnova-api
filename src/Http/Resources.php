<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Http;

use Phpnv\Api\Response;

class Resources
{
    public static function public($path):Response
    {

        return new Response( 'resources/public/' .  $path, 200, 'file');
    }

    public static function private($path)
    {

    }
}