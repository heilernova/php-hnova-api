<?php
 /* This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Http;

class Request
{
    /**
     * busca le header
     * @return ?string Retorna el valor o null en caso de no encotrarse el header con el nombre
     */
    public function getHeader(string $name):?string{
        return $_ENV['api-rest']->request->headers[$name] ??  null ;
    }

    /**
     * Retorna un array de los headers de la petición HTTP
     * @return string[]
     */
    public function getHeaderAll():array{
        return $_ENV['api-rest']->request->headers;
    }

    /**
     * Retorna le tipo de contenido de la solicitud HTTP
     */
    public function getContentType():string{
        return $_ENV['api-rest']->request->headers['Content-Type'] ?? '';
    }

    /**
     * Retorna los datos del body deacuerdo con su formato de envio.
     */
    public function getData():array|object|string|int|float|null|bool{
        return $_ENV['api-rest']->request->body;
    }

    public function getFiles():array{
        return $_FILES;
    }

    /**
     * Retorna el contenido del body decodificado el formato JSON
     */
    public function getContentBody(bool $assoc = false):null|object|array|int|float|string{
        return json_decode(file_get_contents('php://input'), $assoc);
    }

    /**
     * Retorna el contenido del body de la solicitud HTTP
     */
    public function getContentBodyToString():string{
        return file_get_contents('php://input');
    }

    /**
     * Obtiene la IP desde donde se raliza la solicitud HTTP
     */
    public function getIP():string{
        return $_ENV['api-rest']->request->ip;
    }

    /**
     * Obtiene el sistema operativo desde donde se realiza la petición HTTP
     */
    public function getPlatform():string{
        return $_ENV['api-rest']->request->platform;
    }

    /**
     * Obtiene el tipo de dispositivo desde se realiza la petición HTTP
     */
    public function getDevice():int{
        return $_ENV['api-rest']->request->device;
    }
}