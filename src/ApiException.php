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
use Throwable;

class ApiException extends Exception
{
    /**
     * Almacena los mensaje de desarrollador
     * @var string[]
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
    public function __construct(array $messages_developer, ?Throwable $th = null, $text_body = 'Error - server', private $responeCode = 500)
    {
        try {
            
            $this->messageDeveloper = $messages_developer;
            $this->textBody = $text_body;
    
            parent::__construct('', 0, $th);
            if ($th){
                $this->message = $th->getMessage();
                $this->code = $th->getCode();
                $this->line = $th->getLine();
                $this->file = $th->getFile();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
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
        return $this->textBody;
    }

    /**
     * Imprime el error en el body
     */
    public function echo():void
    {
        // Carmaso la informacion del errro a un array asosiativo
        $content = [
            "api"=>"",
            "date"=> date('Y-m-d H:i:s',time()) . "z",
            "messageDeveloper"=>$this->messageDeveloper,
            "mensajeError"=>$this->message,
            "code"=>$this->getCode(),
            "file"=>$this->getFile(),
            "line"=>$this->getLine(),
            "trace"=>$this->getTrace()
        ];

        // Códigicamos al formato JSON
        $content = json_encode($content);

        $file_path = Api::getDir() . "/nv-panel/errors/list.txt";
        $dir_name = dirname($file_path);
        
        if (!file_exists(dirname($dir_name))) mkdir(dirname($dir_name));
        if (!file_exists($dir_name)) mkdir($dir_name);

        $file = fopen($file_path, 'a+');
        fputs($file, "$content,\n");
        fclose($file);

        header('content-type: text, charset=utf-8');
        if (Api::getConfig()->debug()){
            echo "Mensaje del desarrollador:\n";
            foreach($this->getMessageDeveloper() as $item){
                if (is_string($item)){
                    echo "$item\n";
                }else{
                    foreach($item as $sub_item){
                        echo "\t" . (is_string($sub_item) ? $sub_item : json_encode($sub_item, 128)) . "\n";
                    }
                }
            }
            echo "\n\nMessaje error: " . $this->getMessage() . "\n";
            echo "-----------------------------------------------------\n";
            echo "Código:    " . $this->getCode() . "\n";
            echo "Archivo:    " . $this->getFile() . "\n";
            echo "Linea:    " . $this->getLine() . "\n";
            echo "\n-----------------------------------------------------\n";
            echo "Rastro:";
            echo json_encode($this->getTrace(), 128);
        }else{
            echo $this->textBody;
        }
        http_response_code($this->responeCode);
        exit;
    }
}