<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Http;

use HNova\Api\Api;

class HttpBody{

   /**
    * Carga los datos del body recivido de la petición HTTP
    */
   public static function loadData(): void {

      $content_type = Api::request()->getContentType();

      $content_type = explode(';', $content_type,2);

      switch ($content_type[0]) {
         case 'multipart/form-data':
            if ($_SERVER['REQUEST_METHOD'] != 'POST'){
               $data = require __DIR__ . '/Form-data.php';
            }else{
               $data = $_POST;
            }
            break;
         case 'application/json':
            
            $data = json_decode(file_get_contents('php://input'));

            break;
         default:
            $data = file_get_contents('php://input');
            break;
      }
      $_ENV['api-rest']->request->body = $data;
   }

}