<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Settings\HTTP;

class Cors
{
    public function __construct(private object $_cors)
    {
        
    }

    function getOrigin():CorsEdit
    {
        return new CorsEdit($this->_cors, 'origin');
    }

    function getHeaders():CorsEdit
    {
        return new CorsEdit($this->_cors, 'origin');
    }

    function getMetods():CorsEdit
    {
        return new CorsEdit($this->_cors, 'origin');
    }
}