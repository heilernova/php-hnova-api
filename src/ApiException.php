<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 */
namespace HNova\Api;

use Exception;
use HNova\Api\Error\ErrorRegister;
use HNova\Api\Routes\Router;
use Throwable;

class ApiException extends Exception
{
    /**
     * Almacena los mensaje de desarrollador
     * @var (string|string[])[]
     */
    private array $messageDeveloper = [];

    /**
     * alacena el texto a responer en el body
     */
    private string $textBody = '';

    /**
     * @param (string|string[])[] $messages_developer
     * @param Throwable $th exception del cath
     */
    public function __construct(array $messages_developer, ?Throwable $th = null, string $text_body = null, public int $responeCode = 500)
    {
        try {
            
            $this->messageDeveloper = $messages_developer;
            $this->textBody = $text_body ? $text_body : '[ERROR SERVER]';

            parent::__construct('', 0, $th);
            if ($th){
                $this->message = $th->getMessage();
                $this->code = $th->getCode();
                $this->line = $th->getLine();
                $this->file = $th->getFile();
            }
        } catch (\Throwable $th) {
            throw $th;
            throw new Exception("Errro a inilizalizar la clase: " . ApiException::class . "\n Message: " . $th->getMessage());
        }
    }

    /**
     * @return int Rotorna el códido de estado de la petición HTTP
     */
    public function getHttpResponseCode():int
    {
        return $this->responeCode;
    }

    /**
     * Retorna un array de los mensaje
     * @return (string|string[])[]
     */
    public function getMessageDeveloper():array
    {
        return $this->messageDeveloper;
    }

    /**
     * Obtene el código http status.
     */
    public function getResponseCode():int
    {
        return $this->responeCode;
    }

    /**
     * Obtiene el texto que se retornara el error en la petición HTTP encaso de que 
     * el debug de API Config este el false
     */
    public function getTextBody():string
    {
        $text = "<<< [ RUNTIME ERROR ] >>>\n";
        $text .= "\nMessage developer:\n";
        foreach ($this->messageDeveloper as $value){
            if (is_string($value)){
                $text .= "  $value\n";
            }else{
                foreach ($value as $subs){
                    $subs = str_replace("\n", "\n    ", $subs);
                    $text .= "    $subs\n";
                }
            }
        }

        $text .= "\nMessage error:\n";
        $text .= "  " . $this->message . "\n\n";
        $text .= "Code: " . $this->code . "\n";
        $text .= "File: " . $this->file . "\n";
        $text .= "Line: " . $this->line . "\n";
        return $text;
    }
}