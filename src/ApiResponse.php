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
use SplFileInfo;
use Throwable;

class ApiResponse
{
    public function __construct(mixed $value)
    {

        // Regitramos la solicitud
        $dir = Api::getDir();
        if (!file_exists($dir . "/bin")) mkdir($dir . "/bin");
        if (!file_exists($dir . "/bin/error.log")) fopen("$dir/bin/error.log", 'a');
        if (!file_exists($dir . "/bin/request.log")) fopen("$dir/bin/request.log", 'a');

        // Registramos la solicitud

        $request = $_ENV['api-rest']->request;
        
        if ($value  instanceof Throwable){
            $_ENV['api-rest']->response->body = $_ENV['api-rest-exception']->getTextBody();
            $_ENV['api-rest']->response->code = $_ENV['api-rest-exception']->getHttpResponseCode();

            // Registramos error el
            $text = "[" . date('Y-m-d h:i:s', time()) . "]";
            $text .= "   " . str_pad("IP:[$request->ip]", 21, ' ');
            $text .= "   " . "resources: " . $request->url;
            $text .= "   JSON: " . str_replace('\/', '/', json_encode($request));

            $file_error =  fopen("$dir/bin/error.log", 'a');
            fwrite($file_error, "$text\n");
            fclose($file_error);

        }else{
            $_ENV['api-rest']->response->body = $value;
        }
        $file_request = fopen("$dir/bin/request.log", 'a');

        $text = '['. $request->date . ']';
        $text .= "   " . str_pad("IP:[$request->ip]", 21, ' ');
        $text .= "   " . str_pad("Method:[$request->method]", 6, ' ');
        $text .= "   " . str_pad("Status:[" . $_ENV['api-rest']->response->code ."]", 6, ' ');
        $text .= "   " . str_pad("Platform:[$request->platform]", 6, ' ');
        $text .= "   JSON: " . str_replace('\/', '/', json_encode($request));
                
        fwrite($file_request, "$text\n");
        fclose($file_request);
    }

    /**
     * Imprimire las respuesta de la API y finaliza la ejecución
     */
    public function echo():never{

        // $type         = $_ENV['api-rest']->response->contentType;
        $file         = $_ENV['api-rest']->response->file;
        $body         = $_ENV['api-rest']->response->body;
        $code         = $_ENV['api-rest']->response->code;
        $headers      = $_ENV['api-rest']->response->headers;
        $content_type = "application/json, charset=UTF-8";

        if ($file){
            $file = new SplFileInfo($file);
            $content_type = HttpFuns::getContentType($file->getExtension());

            $content_file = file_get_contents($file['path']);

            $body = $content_file ? $content_file : '';

            // Delete the file
            if ($file['autoDelete']){

                unlink($file['path']);
            }
        }else{

            if ($code >= 200 && $code < 300){
                // encode the JSON format
                $json = json_encode($body);
                $body = $json ? $json : '';
            }else{
                $content_type = "text; charset=UTF-8";
                // $content_type = null;
                $body = is_string($body) ? $body : '';
            }
        }

        if ($content_type) header("Content-Type: $content_type");

        // We list the headers to exposed to clients
        $expose_headers = '';
        foreach ($headers as $key => $value){
            header("$key: $value");
            $expose_headers .= ", $value";
        }

        $expose_headers = ltrim($value, ', ');
        header("Access-Control-Expose-Headers: $expose_headers");

        http_response_code($code);

        echo $body;
        exit();
    }
}