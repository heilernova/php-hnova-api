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
use Phpnv\Api\ApiFunctions;
use Phpnv\Api\Http\ResponseBody;
use Phpnv\Api\Response;

class UserController
{
    public function __construct()
    {

    }
    function getUsername():Response
    {
        return new Response(Api::getConfig()->getUser()->username);
    }

    function changeUsername():Response
    {
        $username = json_decode(file_get_contents('php://input'));
        Api::getConfig()->setUser($username, Api::getConfig()->getUser()->password);
        Api::getConfig()->salve();
        return new Response(true);
    }

    /**
     * Elimina todos los registro de errrores
     */
    function clear():Response
    {
        unlink($this->errorsDir);
        return new Response(true);
    }
}