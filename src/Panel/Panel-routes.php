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
    $res->message->title = "Credenciales incorrectas";

    if (Api::getAppConfig()->getUser()->getUsername() == $data->username){
        if (Api::getAppConfig()->getUser()->passwordVerify($data->password)){

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
# ----------------- Manejo de las base de datos
# -------------------------------------------------------------------

// Retorna la lista de las bases de datos.
Routes::get("databases", function(){
    $d = Api::getAppConfig()->getObject()->databases;
    $map = [];
    foreach($d as $key => $item){
        $item->name = $key;
        $map[] = $item;
    }
    return new Response($map);
});

// Actualiza la información de la base de datos.
Routes::put("databases/{name}", function(string $name){
    return new Response(true);
});

// Elimina una base de datos.
Routes::delete("database/{name}", function(string $name){
    return new Response(true);
});

// Verifica el estado de conexión de la base de datos
Routes::get("database/{name}/test", function(string $name){
    return new Response("");
});

// Verifica si los datos son validos para una conexión MySql
Routes::post("database/test-connection", function(){
    $data = json_decode(file_get_contents("php://input"));

    $res = new ResponseApi();
    try {
        $co = mysqli_connect($data->hostname, $data->username, $data->password, $data->database);
        $res->status = true;
    } catch (\Throwable $th) {

        $res->message->title = "Datos de conexión incorrectos";
        $res->message->content = $th->getMessage();

    }

    return new Response([]);
});

# -------------------------------------------------------------------
# ----------------- Manejo de las api
# -------------------------------------------------------------------

// Retornamos la lista de apis
Routes::get("apis", function(){

    $obj = Api::getAppConfig()->getObject()->apis;
    $list = [];

    foreach ($obj as $key => $value){
        $value->name = $key;
        $list[] = $value;
    }

    return new Response($list);
});

// Retronamos la información de la api.
Routes::get("apis/{name}", function($name){
    $info = Api::getAppConfig()->getApps()->get($name);
    
    return new Response($info ? $info->getInfo() : null);
});

// Deshabilita el acceso a una api
Routes::patch("apis/{name}/disable", function(string $name){
    $re = Api::getAppConfig()->getApps()->get($name);
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