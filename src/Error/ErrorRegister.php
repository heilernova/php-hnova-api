<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Error;

use HNova\Api\ApiException;
use HNova\Api\Funs;
use HNova\Api\HTTP\ClientInfo;

class ErrorRegister
{
    public static function __load(ApiException $exec){
        return new ErrorRegister(
            time() . '-' . Funs::generateToken(5),
            date('Y-m-d H:i:s',time()) . "z",
            (object)[
                'url'       => $_ENV['api-http-request-url'],
                'method'    => $_SERVER['REQUEST_METHOD'],
                'ip'        => ClientInfo::getIp(),
                'device'    => ClientInfo::getDevice(),
                'platform'  => ClientInfo::getPlatform()
            ],
            "Error de ejecución",
            $exec->getMessageDeveloper(),
            (object)[
                'code'  => $exec->getCode(),
                'file'  => $exec->getFile(),
                'line'  => $exec->getLine(),
                'trace' => $exec->getTrace()
            ]
        );

    }

    public function __construct(
        public string $id = "",
        public string $date = '',
        public object $httpRquest = new \stdClass(),
        public string $description = "",
        public array $messageDeveloper = [],
        public object $error = new \stdClass()
    )
    {
        
    }
    
}