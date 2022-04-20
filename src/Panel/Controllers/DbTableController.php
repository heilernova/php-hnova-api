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
use HNova\Api\Funs;
use HNova\Api\Panel\PanelBaseController;
use HNova\Api\Response;
use HNova\Api\Scripts\Files;
use mysqli;

class DbTableController extends PanelBaseController
{
    /**
     * Retorna un array con la información de la base de datos. 
     * */
    function get(string $db, string $name){

        $db = Api::getDatabase($db);
        $data = (object)[];
        $data->numRows = $db->execute("SELECT COUNT(*) FROM $name")->fetchArray()[0];
        $data->status = $db->execute("SHOW TABLE STATUS WHERE `Name`='$name'")->fetchAssoc();

        $columns = [];
        foreach ($db->execute("SHOW COLUMNS FROM $name")->fetchAll() as $column) {
            
            $column_tempo = [];
            foreach ($column  as $key=>$value){
                $column_tempo[strtolower($key)] =$value;
            }
            $columns[] = $column_tempo;
        }
        $data->columns =  $columns;
        $data->create = $db->execute("SHOW CREATE TABLE $name")->fetchAssoc()['Create Table'];
        return $data;
    }
}