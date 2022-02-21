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
use Phpnv\Api\Http\ResponseBody;
use Phpnv\Api\Response;

class UserController
{
    public function __construct()
    {

    }
    function getUsername():Response
    {
        return new Response(Api::getConfig()->getUser()->username);
    }

    function changeUsername():Response
    {
        $username =  json_decode(file_get_contents('php://input'));
        Api::getConfig()->setUser($username, Api::getConfig()->getUser()->password);
        Api::getConfig()->salve();
        return new Response(true);
    }

    function changePassword():Response
    {
        $data = json_decode(file_get_contents('php://input'));
        $password = $data->password;
        $new_password = $data->newPassword;

        $res = new ResponseBody();

        if (password_verify($password, Api::getConfig()->getUser()->password)){
            $passwrod_hash = password_hash($new_password, PASSWORD_DEFAULT, ['cost'=>3]);
            Api::getConfig()->setUser(Api::getConfig()->getUser()->username, $passwrod_hash);
            Api::getConfig()->salve();
        }else{
            $res->message->content[] = "Contraseña incorrecta.";
        }

        return new Response($res);
    }
}