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

class ErrorsController
{
    private string $errorsDir = '';
    public function __construct()
    {
        $this->errorsDir = Api::getDir() . "/nv-panel/errors/list.txt";

    }
    function get():Response
    {
        $list = rtrim(file_get_contents($this->errorsDir), ",\n");
        $list = "[$list]";
        return new Response($list);
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