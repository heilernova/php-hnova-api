<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Panel\Controllers;

use Phpnv\Api\Api;
use Phpnv\Api\Response;

class AppsController
{
    function get():Response
    {
        $apps = Api::getConfig()->getApis()->getAll();

        return new Response($apps);
    }

    function disable(string $name):Response
    {
        return new Response(false);
    }
    
    function eneable(string $name):Response
    {
        return new Response(false);
    }
}