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

        header('nv-data: ' . json_encode($api_info));
        
        if (self::$_httpResponseType == "blob"){
            
            if (file_exists($this->_result)){

                // Agregamos el content-type al header
                
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
}