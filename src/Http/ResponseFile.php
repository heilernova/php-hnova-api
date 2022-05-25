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

class ResponseFile{

    function __construct(private string $path)
    {
        if (!file_exists($path)){
            throw new ApiException(["La rutal del archivo no existe: $path"], null, 'not found', 404);
        }
    }

    function echo(): void {

        echo file_get_contents($this->path);
    }
}