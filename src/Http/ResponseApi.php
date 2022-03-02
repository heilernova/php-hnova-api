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
namespace HNova\Api\Http;

class ResponseApi
{
    public bool $status = false;
    public int $statusCode = 0;
    public mixed $data  = null;
    public Message $message;

    public function __construct()
    {
        $this->message = new Message();
    }

    /**
     * Genera el objeto ResponseApi a partir de los parametros ingresador.
     */
    public static function generates(bool $status, mixed $data = null, array $message = null, int $status_code = 0):ResponseApi
    {
        $m = new ResponseApi();
        $m->status = $status;
        $m->data = $data;
        // $m->message = $message;
        $m->statusCode = $status_code;

        if ($message)
        {
            $m->message->content = $message[0]; 
            $m->message->title = $message[1] ?? $m->message->title;
            $m->message->type = $message[2] ?? 0;
        }

        return $m;
    }
}