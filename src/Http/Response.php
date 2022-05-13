<?php
 /* This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Http;

class Response
{

    public function __construct(
        public Message $message = new Message()
    ){}

    /**
     * Establece el código HTTP de estado
     */
    public function httpResponseCode(int $code):void{
        $_ENV['api-rest']->response->code = $code;
    }

    /**
     * Agrega un header a la respuesta.
     */
    public function addHeader(string $key, string $value): void{

        $_ENV['api-rest']->headers[$key] = $value;
    }

    /**
     * In case of responding with file inter the path
     * @param string $path File path
     * @param bool $auto_delete Set in the file is delete after submission, default is true
     * @return bool Returns false if the file path is wrong
     */
    public function sendFile(string $path, bool $auto_delete = true):bool{
        if (file_exists($path)){
            $_ENV['api-rest']->response->file = ['path'=>$path, 'autoDelete'=>$auto_delete];
            return true;
        }else{
            return false;
        }
    }
}