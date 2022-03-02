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
namespace HNova\Api;

class Response
{
    /**
     * @param mixed $body valor que desea responser en la api, el valor ingresado de codificara a formato JSON.
     * @param int $responseCode
     */
    public function __construct(
        public mixed $body,
        public int $reponseCode = 200,
        public $type = 'json'
    ){}


    /**
     * Imprime la respuesta.
     */
    public function echo()
    {
        switch ($this->type){
            case "json":
                header('content-type: application/json');
                echo str_replace('\/', '/', json_encode($this->body));
                break;
            case "file":
                break;
            default:
                echo "no";
        }
        http_response_code($this->reponseCode);
    }
}