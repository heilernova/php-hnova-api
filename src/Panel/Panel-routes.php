<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Panel\Controllers;

use HNova\Api\Api;
use HNova\Api\ApiFunctions;
use HNova\Api\Http\ResponseApi;
use HNova\Api\Response;
use HNova\Api\Routes;

Routes::get("test", function(){
    return new Response(date_default_timezone_get());
});


# -------------------------------------------------------------------
# ----------------- Autenticación de usuario.
# -------------------------------------------------------------------

Routes::post("auth", function(){
    $data = json_decode(file_get_contents("php://input"));

    $res = new ResponseApi();
    $res->message->title = "Acceso no permitido";

    if (Api::getConfig()->getUser()->getUsername() == $data->username){
        if (Api::getConfig()->getUser()->passwordVerify($data->password)){

            $token = ApiFunctions::generateToken(50);

            $path_token = Api::getDir() . "/nv-panel/.access-token.txt";

            if (file_exists(Api::getDir() . "/nv-panel")) mkdir(Api::getDir() . "/nv-panel");

            $file = fopen($path_token, "a");
            fputs($file, $token);

            $res->data = $token;
        }else{
            $res->message->content[] = "Contraseña incorrecta.";
        }
    }else{
        $res->message->content[] = "Usuario incorrecto.";
    }

    return new Response($res);
});

# -------------------------------------------------------------------
# ----------------- Manejor de las api
# -------------------------------------------------------------------

// Retornamos la lista de apis
Routes::get("apis", function(){
    return new Response([]);
});

Routes::get("apis/{name}", function($name){
    $info = Api::getConfig()->getApps()->get($name);
    
    return new Response($info ? $info->getInfo() : null);
});

// Desabiluita el acceso a una api
Routes::patch("apis/{name}/disable", function(string $name){
    $re = Api::getConfig()->getApps()->get($name);
    return new Response($re);
});

// Habilita el acceso a una api
Routes::patch("api/{name}/enable", function(string $name){
    return new Response(true);
});

# -------------------------------------------------------------------
# ------------------- Manejo de errores
# -------------------------------------------------------------------

// Retrona la lista de error registrador la API
Routes::get("errors", function(){

    $errors = [];
    $dir = Api::getDir() . "/nv-panel/errors/list.txt";
    if (file_exists($dir)){
        
        $content = rtrim(file_get_contents($dir),",\n");
        $errors = json_decode("[$content]");
    }

    return new Response($errors);
});

// Elimina todos los registros de errores.
Routes::delete("errors", function(){
    $dir = Api::getDir() . "/nv-panel/errors/list.txt";
    unlink($dir);
});