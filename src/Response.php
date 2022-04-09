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

use SplFileInfo;

class Response
{

    private static int $_httpResponseCode = 200;
    private static string $_httpResponseType = "json";
    private static ?object $_message = null;

    public  function __construct(private $_result)
    {
        
    }
    
    public function echo():never
    {
        $api_info = [];
        $api_info['application'] = Api::getConfig()->getConfigData()->name;
        $api_info['time-response'] = time() - $_ENV['api-time-start'];
        
        if (self::$_message) $api_info['systemMessage'] = self::$_message;

        header('Access-Control-Expose-Headers: nv-data');
        header('nv-data: ' . json_encode($api_info));
        
        if (self::$_httpResponseType == "blob"){
            
            if (file_exists($this->_result)){

                $file = new SplFileInfo($this->_result);
                $type_content = $this->getContentType($file->getExtension());
                
                // Agregamos el content-type al header
                if ($type_content) header("content-type: $type_content");

                require $this->_result;
            }else{
                self::$_httpResponseCode = 404;
            }
        }else{
            
            header('content-type: application/json charset=UTF-8');
            echo json_encode($this->_result);
        }
        http_response_code(self::$_httpResponseCode);
        exit;
    }

    public static function setResponsetype(string $type):void
    {
        self::$_httpResponseType = $type;
    }

    /**
     * ---- Métodos estaticas
     */

    /**
     * Estable el código HTTP a responser por la API
     */
    public static function SetHttpResponseCode(int $code):void
    {
        self::$_httpResponseCode = $code;
    }

    /**
     * @param string|string[] $content
     */
    public static function addMessage(string|array $content):void
    {
        if (!self::$_message) self::$_message = (object)[];

        self::$_message->content[] = $content;
    }
    
    /**
     * @param string|string[] $content
     */
    public static function setMenssage(array $content):void
    {
        if (!self::$_message) self::$_message = (object)[];
        self::$_message->content = $content;
    }

    /**
     * Retorna el headers de content-type segun la extención del archivo.;
     */
    private function getContentType(string $extension):?string
    {
        switch ($extension) {
            case 'png': return "image/$extension";
            case 'jpg': return "image/$extension";
            case 'jpeg': return "image/$extension";
            case 'git': return "image/$extension";

            case 'pdf': return 'application/pdf';

            case 'doc': return "application/msword";
            case 'dot': return "application/msword";

            case 'docx': return "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
            case 'dotx': return "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
            case 'docm': return "application/vnd.ms-word.document.macroEnabled.12";
            case 'dotm': return "application/vnd.ms-word.document.macroEnabled.12";

            case 'xls': return "application/vnd.ms-excel";
            case 'xlt': return "application/vnd.ms-excel";
            case 'xla': return "application/vnd.ms-excel";

            case 'xlsx': return "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            case 'xltx': return "application/vnd.openxmlformats-officedocument.spreadsheetml.template";

            case 'xlsm': return "aapplication/vnd.ms-excel.sheet.macroEnabled.12";
            case 'xltm': return "application/vnd.ms-excel.template.macroEnabled.12";

            case 'xlam': return "application/vnd.ms-excel.addin.macroEnabled.12";
            case 'xlsb': return "pplication/vnd.ms-excel.sheet.binary.macroEnabled.12";

            case 'ppt': return "application/vnd.ms-powerpoint";
            case 'pot': return "application/vnd.ms-powerpoint";
            case 'pps': return "application/vnd.ms-powerpoint";
            case 'ppa': return "application/vnd.ms-powerpoint";

            case 'pptx': return "application/vnd.openxmlformats-officedocument.presentationml.presentation";
            case 'potx': return "application/vnd.openxmlformats-officedocument.presentationml.template";
            case 'ppsx': return "application/vnd.openxmlformats-officedocument.presentationml.slideshow";
            case 'ppam': return "application/vnd.ms-powerpoint.addin.macroEnabled.12";
            case 'pptm': return "application/vnd.ms-powerpoint.presentation.macroEnabled.12";
            case 'potm': return "application/vnd.ms-powerpoint.template.macroEnabled.12";
            case 'ppsm': return "application/vnd.ms-powerpoint.slideshow.macroEnabled.12";
            
            case 'mdb': return "application/vnd.ms-access";

            default: null;
        }
    }
}