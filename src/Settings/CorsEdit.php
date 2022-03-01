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

class CorsEdit
{

    public function __construct(private $edit, private $data)
    {
        
    }

    public function fullAccess():void
    {
        $this->data = "*";
    }

    /**
     * @return string[]|string|null
     */
    public function get():array|string|null
    {
        return $this->data;
    }

    public function add(string $value):void
    {
        $edit = $this->edit;
        if (!is_array($this->data->$edit)){
            $this->data->$edit = [];
        }
        $this->data->$edit[] = $value;
    }

    public function getValueString():?string
    {
        $edit = $this->edit;
        $values = $this->data->$edit;
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

    public function clear():void
    {
        $this->data = null;
    }
}