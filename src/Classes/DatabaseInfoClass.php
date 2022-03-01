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
namespace HNova\Api\Classes;

class DatabaseInfoClass
{
    public string $type = "";
    public object|array $dataConnection;

    public function __construct(string $type, array $dataConnection)
    {
        $this->type = $type;
        $this->dataConnection = $dataConnection;
    }
}