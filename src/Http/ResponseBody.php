<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Phpnv\Api\Http;

class ResponseBody
{
    /**
     * Estado de resultado de la cosulta. true para una consulta exitosa
     * @var bool por default es false
     */
    public bool $status = false;

    /**
     * Código de respuesta
     */
    public int $statusCode = 0;

    /**
     * Información a enviar al frontend el valor se codificara a formato JSON,
     * por default es null
     */
    public mixed $data = null;

    public MessageBody $message;

    /**
     * Método contructor.
     */
    public function __construct(){
        $this->message = new MessageBody();
    }
}