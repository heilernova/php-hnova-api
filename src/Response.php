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
    private static ?object $_message = null;

    public  function __construct(private $_result)
    {
        
    }
    
    public function echo():never
    {
        header('content-type: application/json');
        $response = [];
        $response['Application'] = 'Nombre de la aplicacion';
        $response['time-response'] = time() - $_ENV['api-time-start'];
        if (self::$_message) $response['systemMessage'] = self::$_message;
        $response['response'] = $this->_result;

        echo json_encode($response);

        http_response_code(self::$_httpResponseCode);
        exit;
    }


    /**
     * ---- Clases estaticas
     */

    /**
     * Estable el código HTTP a responser por la API
     */
    public static function SetHttpResponseCode(int $code):void
    {
        self::$_httpResponseCode = $code;
    }

    public static function addMessage(string $content):void
    {
        if (!self::$_message) self::$_message = (object)[];

        self::$_message->content[] = $content;
    }
}