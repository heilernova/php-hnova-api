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

    public  function __construct(private $_result)
    {
        
    }
    
    public function echo():never
    {
        header('content-type: application/json');
        echo json_encode([
            'Applicaction'=>'Nombre de la app',
            'result'=>$this->_result
        ]);
        exit;
    }
}