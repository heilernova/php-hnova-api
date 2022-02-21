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

class MessageBody
{
    /**
     * Título del encabezado del mensaje. por default es null
     */
    public string|null $title = null;

    /**
     * Tipo de mensaje enviado al fronted, por default es 0
     * @var int 0 message, 1 alerta, 2 error, 3 error del servidor.
     */
    public int $type = 0;

    /** 
     * Cada item coresponderia a un linea o parrafo, en caso de ser un array coresponderia una lista.
     * por defector es un array vacio.
     * @var (string|string[])[] */
    public array $content = [];
}