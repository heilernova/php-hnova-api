<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Panel\Controllers;

use Phpnv\Api\Api;
use Phpnv\Api\ApiFunctions;
use Phpnv\Api\Http\Controller;
use Phpnv\Api\Http\ResponseBody;
use Phpnv\Api\Response;

class AuthController
{
    function post():Response{
        $data = json_decode(file_get_contents('php://input'));
        $res = new ResponseBody();
        
        if (Api::getConfig()->getUser()->username == strtolower($data->username)){
            if (password_verify($data->password, Api::getConfig()->getUser()->password)){
                $token = ApiFunctions::generateToken(50);
                $token_file_path = Api::getDir() . "/nv-panel/.token-access.txt";
                if (!file_exists(dirname($token_file_path))) mkdir(dirname($token_file_path));
                $file = fopen($token_file_path, 'a');
                fputs($file, $token);
                fclose($file);
                
                $res->status = true;
                $res->data = $token;
            }else{
                $res->message->content[] = "Contraseña incorrecta.";
            }
        }else{
            $res->message->content[] = "Usuario incorrecto.";
        }
        $dir = Api::getDir() . "/nv-panel/token-access.txt";
        
        return new Response($res);
    }
}