<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Http;

class ResponseApi
{
    /**
     * Estado de la cosulta true para petición exitosa por defecto es false
     */
    public bool $status = false;

    /** En caso de darle un código ingresalo aquí */
    public int $statusCode = 0;

    /** Carge aqui los datos a reponser */
    public mixed $data  = null;

    /**
     * Objeto que representa los datos del mensaje a enviar.
     */
    public Message $message;

    public function __construct()
    {
        $this->message = new Message();
    }

    /**
     * Genera el objeto ResponseApi a partir de los parametros ingresador.
     * @param bool $status
     * @param mixed $data
     * @param array $message array que contiene la información del mensaje el primer item es el contenido el segundo el titulo
     * el tercero es el tipo.
     */
    public static function generates(bool $status, mixed $data = null, array $message = null, int $status_code = 0):ResponseApi
    {
        $m = new ResponseApi();
        $m->status = $status;
        $m->data = $data;
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