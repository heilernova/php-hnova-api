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

use HNova\Api\Scripts\console;

class ConfigDevelopers
{

    public function __construct(private $cofig)
    {
        
    }

    /**
     * 
     */
    public function getAll():array
    {
        return $this->cofig;
    }

    public function add($name, $email, $homepage){
        $this->cofig->developers[] = (object)[
            "name" => $name,
            "email" => $email,
            "homepage" => $homepage
        ];
        // echo json_encode($this->cofig, 128); exit;
    }
}