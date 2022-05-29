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

use HNova\Api\Http\Response;
use SplFileInfo;

class ApiResponse
{
    public function __construct(private mixed $value)
    {
        // Regitramos la solicitud
        $dir = Api::getDir();
        if (!file_exists($dir . "/bin")) mkdir($dir . "/bin");

        if ($value instanceof ApiException){
            $this->value = res::send($value->getTextBody())->status($value->getHttpResponseCode());
        }
    }

    /**
     * Imprimire las respuesta de la API y finaliza la ejecución
     */
    public function echo():never{
        // Registamos la soliciutad.
        $res = $this->value;

        if ($res instanceof Response){
            $res->echo();
        }else{
            //  Por default respondemos JSON
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode($res);
        }
        ApiLog::request();
        exit();
    }
}