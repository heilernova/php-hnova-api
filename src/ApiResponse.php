<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api;

use Composer\IO\NullIO;
use HNova\Api\Http\HttpFuns;
use HNova\Api\Http\Response;
use HNova\Api\Http\ResponseFile;
use HNova\Api\Http\ResponseJson;
use HNova\Api\Http\ResponseText;
use HNova\Api\Http\ResponseView;
use SplFileInfo;

class ApiResponse
{
    public function __construct(private mixed $value)
    {

        // Regitramos la solicitud
        $dir = Api::getDir();
        if (!file_exists($dir . "/bin")) mkdir($dir . "/bin");
        if (!file_exists($dir . "/bin/error.log")) fopen("$dir/bin/error.log", 'a');
        if (!file_exists($dir . "/bin/request.log")) fopen("$dir/bin/request.log", 'a');
        if ($value instanceof ApiException){
            $this->value = res::send($value->getTextBody())->status($value->getHttpResponseCode());
        }
    }

    /**
     * Imprimire las respuesta de la API y finaliza la ejecución
     */
    public function echo():never{

        // $type         = $_ENV['api-rest']->response->contentType;
        // $file         = $_ENV['api-rest']->response->file;
        // $body         = $_ENV['api-rest']->response->body;
        // $code         = $_ENV['api-rest']->response->code;
        // $headers      = $_ENV['api-rest']->response->headers;
        // $content_type = "application/json; charset=UTF-8";

        $res = $this->value;

        if ($res instanceof Response){
            $res->echo();
        }else{
            //  Por default respondemos JSO
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode($res);
        }

        exit();
    }
}