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

class ResponseBody
{
    public function __construct(
        public bool $status = false,
        public int $statusCode = 0,
        public array $messages = [],
        public mixed $data = null
    ){}
}