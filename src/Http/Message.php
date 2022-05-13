<?php
 /* This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Http;

class Message
{

    public function setTitle(string $title):void{
        $_ENV['api-rest']->response->message->title = $title;
    }

    public function addContent(string $message):void{
        $_ENV['api-rest']->response->message->content[] = $message;
    }
}