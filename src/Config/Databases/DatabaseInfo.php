<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Phpnv\Api\Config\Databases;

class DatabaseInfo
{
    public function __construct(private $name, public string $type, public array $dataConnection)
    {
        
    }
    public static function load(string $name, object $data):DatabaseInfo
    {
        return new DatabaseInfo($name, $data->type, (array)$data->dataConnection);
    }
}