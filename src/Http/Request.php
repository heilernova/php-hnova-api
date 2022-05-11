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
        $headers = apache_request_headers();
        return $headers ? ($headers[$name] ?? null) : null ;
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