<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Http;

use HNova\Api\ApiException;

class ResponseView
{
    function __construct(private string $path_view)
    {
        if (!file_exists($path_view)){
            throw new ApiException(["La rutal de la vista no existe: $path_view"]);
        }
    }

    function getContentType():string{
        return "text/html; charset=UTF-8";
    }

    function echo(): void {
        require $this->path_view;
    }
}