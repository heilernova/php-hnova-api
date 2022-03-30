<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Routes;

enum Methods:string
{
    case Get = 'get';
    case Post = 'post';
    case Put = 'put';
    case Delete = 'delete';
    case Patch = 'patch';
}