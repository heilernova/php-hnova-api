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
use HNova\Api\Http\ResponseJson;
use HNova\Api\Http\ResponseView;
use SplFileInfo;

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
        
        if ($value  instanceof ApiException){
            // En caso de error lo registamos
            $_ENV['api-rest']->response->body = $value;
            $_ENV['api-rest']->response->code = $value->getHttpResponseCode();

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
        $content_type = "application/json; charset=UTF-8";

        $nv_data = [
            'API' => 'test'
        ];

        // Agremago el mensaje si lo hay
        if ($_ENV['api-rest']->response->message->content){
            $nv_data['message'] = $_ENV['api-rest']->response->message;
        }

        // Agregamos la información del header nv-data
        $headers['nv-data'] = json_encode($nv_data);

        // We list the headers to exposed to clients
        $expose_headers = '';
        foreach ($headers as $key => $value){
            header("$key: $value");
            $expose_headers .= ", $key";
        }

        $expose_headers = ltrim($expose_headers, ', ');
        header("Access-Control-Expose-Headers: $expose_headers");

        http_response_code($code);

        // Definimos el contenido del body
        if ($body instanceof ResponseView){
            
            $content_type = $body->getContentType();
            $body->echo();

        }elseif($body instanceof ApiException){
            
            echo $body->getTextBody();

        }else{
            echo json_encode($body);
        }

        if ($content_type) header("Content-Type: $content_type");
        exit();
    }
}