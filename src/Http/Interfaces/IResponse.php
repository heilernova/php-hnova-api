<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Http\Interfaces;

interface IResponse{

    function code(int $code);

    function addHeader(string $name, string $value);

    function echo(): void;
}