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

class CorsEdit
{
    public function __construct(private object $_cors, private string $_cors_name)
    {
        
    }

    public function getValueString():?string
    {
        // $edit = $this->_cors->{$this->_cors_name};
        $values = $this->_cors->{$this->_cors_name};
        if ($values){
            if (is_array($values)){
                $text = "";
                foreach ($values as $item){
                    $text .= ", $item";
                }
                return ltrim($text, ", ");
            }else{
                return $values;
            }
        }else{
            return null;
        }
    }
}