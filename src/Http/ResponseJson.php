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

class ResponseJson{

    function __construct(private string $data)
    { }

    function echo(): void {
        echo json_encode($this->data);
    }
}