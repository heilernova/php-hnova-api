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

class ApiLog
{
    public static function addRequest():void{
        $dir = Api::getDir() . "/Bin/request.log";
        $file = fopen($dir, 'a');

        $date = date('Y-m-d H:i:s P', time());
        $ip =  str_pad(req::ip(), 15) ;
        $info_connec =  str_pad('"' . req::platform() . " " . req::device() . '"', 15) ;
        $info_http = '"' . req::method() . " ./" .   $_ENV['api-rest']->request->url . " HTTP/1.1 " . $_ENV['api-rest']->response->code . '"';

        $json = json_encode([
            'date'=>$date,
            'ip' => req::ip(),
            'platform' => req::platform(),
            'devive' => req::device(),
            'method' => req::method(),
            'url' => $_ENV['api-rest']->request->url,
            'status' =>$_ENV['api-rest']->response->code
        ]);

        $log = "[$date]  $ip  $info_connec $info_http  $json\n";

        fputs($file, $log);
        fclose($file);
    }

    public static function addError(ApiException $exe):void{
        $dir = Api::getDir() . "/Bin/errors.log";
        $file = fopen($dir, 'a');

        $date = date('Y-m-d H:i:s P', time());

        $json = json_encode([
            'data' => date('Y-m-d H:i:s P', time()),
            'ip'=> req::ip(),
            'platform' => req::platform(),
            'device' => req::device(),
            'messageDeveloper' => $exe->getMessageDeveloper(),
            'info' => [
                'code' => $exe->getCode(),
                'message' => $exe->getMessage(),
                'file' => $exe->getFile(),
                'line' => $exe->getLine(),
                'trace' => $exe->getTrace(),
            ]
        ]);

        fputs($file, "$json\n");
        fclose($file);
    }

    public static function getRequest():array{
        $dir = Api::getDir() . "/Bin/request.log";
        $content = file_get_contents($dir);

        $logs = [];

        if ($content){
            $lines = explode("\n", $content);
            foreach ($lines as $line){
                $part = explode('{', $line);
                if (isset($part[1])){
                    $logs[] = json_decode( '{' .$part[1]);
                }
            }
        }
        return $logs;
    }

    public static function getErrors():array{
        $dir = Api::getDir() . "/Bin/request.log";
        $content = file_get_contents($dir);

        $logs = [];
        if ($content){
            $lines = explode("\n", $content);
            foreach ($lines as $line){
                $logs[] = json_decode($line);
            }
        }
        return $logs;
    }

    public static function clearRequest():void{
        if (file_exists(Api::getDir() . "/Bin/request.log")){
            unlink(Api::getDir() . "/Bin/request.log");
        }
    }

    public static function clearErrors():void {
        if (file_exists(Api::getDir() . "/Bin/errors.log")){
            unlink(Api::getDir() . "/Bin/errors.log");
        }
    }
}