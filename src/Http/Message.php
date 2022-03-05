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

class Message
{
    /** Titulo del mensaje */
    public string $title = "Mensaje del sistema";

    /** Tipo de mensaje 0:mensaje, 1:arlerta, 2:error, 3:error de servidor */
    public int $type = 0;
    /**
     * Contenido del mensaje donde cada item de tipo string es un parrafo, y los items de tipo array son una lista
     * @var (string|string[])[]
     */
    public array $content = [];
}