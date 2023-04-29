<?php

namespace Light2;

abstract class Model
{
    public string $table = '';
    public string $primaryKey = '';
    protected $db = null;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function __destruct()
    {
        $this->db->close();
        $this->db = null;
    }

    public function findAll()
    {
        return $this->db->from($this->table)->fetchAll();
    }

    public function find($id)
    {
        return $this
            ->db
            ->from($this->table)
            ->where($this->primaryKey, $id)
            ->fetch();
    }

    public function insert(array $data)
    {
        return $this
            ->db
            ->insertInto($this->table, $data)
            ->execute();
    }

    public function update($data, $id)
    {
        return $this
            ->db
            ->update($this->table)
            ->set($data)
            ->where($this->primaryKey, $id)
            ->execute();
    }

    public function delete($id)
    {
        return $this
            ->db
            ->deleteFrom($this->table)
            ->where($this->primaryKey, $id)
            ->execute();
    }
}