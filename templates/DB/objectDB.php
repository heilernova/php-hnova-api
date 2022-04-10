<?php
namespace HNnamespace\DB;

use HNnamespace\BaseDB;

class Name extends BaseDB
{
    /**
     * Método contructor
     */
    public function __construct()
    {
        parent::__construct('table_name', 'name database');
    }

    public function get($id, int $limit = 1000)
    {
        $data = [
            "condition"=>['id=?', [$id]],
            'fields'=>'*'
        ];
        $this->_database->select($data);
    }

    public function insert($data, bool $auto_commit = false):bool
    {
        $ok = $this->_database->insert($data)->result;
        if ($ok){
            if ($auto_commit) $this->_database->commit();
            return true;
        }else{
            return false;
        }
    }

    public function update($data, $id, bool $auto_commit = false):bool
    {
        $ok = $this->_database->update($data, ['id=?', [$id]])->result;
        if ($ok){
            if ($auto_commit) $this->_database->commit();
            return true;
        }else{
            return false;
        }
    }

    public function delete($id, $auto_commit = false):bool
    {
        $ok = $this->_database->delete(['id=?', [$id]])->result;
        if ($ok){
            if ($auto_commit) $this->_database->commit();
            return true;
        }else{
            return false;
        }
    }
}