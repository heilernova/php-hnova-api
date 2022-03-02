<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 */
namespace HNova\Api\Http;

class Message
{
    public string $title = "Mensaje del sistema";
    public int $type = 0;
    /**
     * @var (string|string[])[]
     */
    public array $content = [];
}