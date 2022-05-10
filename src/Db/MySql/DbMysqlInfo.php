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
namespace HNova\Api\Db\MySql;

class DbMysqlInfo
{
    /** Tipo de la base de datos */
    public string $type = "";
    /** Datos de conexión */
    public object $dataConnection;

    public function __construct($type, $data_conncetion)
    {
        $this->type = $type;
        $this->dataConnection = $data_conncetion;
    }
}