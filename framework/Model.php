<?php

namespace Light2;

use Light2\Libraries\FluentPDO\Query;

abstract class Model
{
    public string $table;
    public string $primaryKey;
    protected Query $db;
    protected string $dsn;
    protected string $username = '';
    protected string $password = '';

    public function connect(): Query
    {
        if (!isset($this->db) && !isset($this->dsn)) {
            $this->db = db_connect();
        } elseif (!isset($this->db) && isset($this->dsn)) {
            $this->db = db_connect($this->dsn, $this->username, $this->password);
        }

        return $this->db;
    }

    public function findAll()
    {
        return $this->connect()->from($this->table)->fetchAll();
    }

    public function find($id)
    {
        return $this
            ->connect()
            ->from($this->table)
            ->where($this->primaryKey, $id)
            ->fetch();
    }

    public function insert(array $data)
    {
        return $this
            ->connect()
            ->insertInto($this->table, $data)
            ->execute();
    }

    public function update(array $data, $id)
    {

        return $this
            ->connect()
            ->update($this->table)
            ->set($data)
            ->where($this->primaryKey, $id)
            ->execute();
    }

    public function delete($id)
    {
        return $this
            ->connect()
            ->deleteFrom($this->table)
            ->where($this->primaryKey, $id)
            ->execute();
    }
}