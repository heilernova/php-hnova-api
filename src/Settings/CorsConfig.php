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
namespace HNova\Api\Settings;

class CorsConfig
{
    public function __construct(private $cors)
    {
        
    }

    public function origin():CorsEdit
    {
        return new CorsEdit("origin", $this->cors);
    }

    public function headers():CorsEdit
    {
        return new CorsEdit("headers", $this->cors);
    }

    public function methods():CorsEdit
    {
        return new CorsEdit("methods", $this->cors);
    }
}