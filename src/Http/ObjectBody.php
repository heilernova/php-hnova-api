<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Phpnv\Api\ApiException;

enum Status
{
    case Draft;
    case Published;
    case Archived;
}

class ObjectBody
{
    public function __construct(array|null|object $body)
    {
        if ($body){
            $body = (array)$body;
            $errors = [];
            foreach ($this as $key=>$value){
                if (array_key_exists($key, $body)){
                    try {
                        $this->$key = $body[$key];
                    } catch (\Throwable $th) {
                        $errors[] = "$key : Tipo de dato invalido" . Status::Archived;
                    }
                }else{
                    $errors[] = "$key : No encotrado";
                }
            }
            if ($errors){
                throw new ApiException([
                    'Faltan parametros en el body'
                ]);
            }
        }else{
            throw new ApiException(['No hay datos que leer en el body.']);
        }
    }
}