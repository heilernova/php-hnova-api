<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DataSystem;

class Message
{
    private object $_message;

    public function __construct()
    {
        $this->_message = (object)['content'=>[], 'type'=>0];
    }

    public function setTitle(string $title):void
    {
        $this->_message->title = $title;
    }
    
    /** 0: Messaje, 1:Alerta, 2:Error */
    public function setType(int $type):void
    {
        $this->_message->type = $type;
    }

    /**
     * @param string[]|string $content
     */
    public function addContent(array|string $content):void
    {
        $this->_message->content[] = $content;
    }


    public function getObject():Object
    {
        return $this->_message;
    }
}