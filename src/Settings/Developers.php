<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Settings;
class Developers
{
    public function getList():array{
        return (array)$_ENV['api-rest']->config->developers;
    }

    public function remove(int $index):bool{
        return true;
    }

    public function add($name, $email, $homepage): bool{
        return true;
    }
}