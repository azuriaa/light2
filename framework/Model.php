<?php

namespace Light2;

use \PDO;
use Light2\Libraries\FluentPDO\Query;

abstract class Model
{
    public string $table = '';
    public string $primaryKey = '';
    protected $pdo = null;
    protected $fluent = null;

    public function __construct()
    {
        $this->pdo = new PDO(
            $_ENV['database']['dsn'],
            $_ENV['database']['username'],
            $_ENV['database']['password']
        );
        $this->fluent = new Query($this->pdo);
    }

    public function __destruct()
    {
        $this->fluent->close();
        $this->pdo = null;
    }

    public function findAll()
    {
        return $this->fluent->from($this->table)->fetchAll();
    }

    public function find($id)
    {
        return $this
            ->fluent
            ->from($this->table)
            ->where($this->primaryKey, $id)
            ->fetch();
    }

    public function insert(array $data)
    {
        return $this
            ->fluent
            ->insertInto($this->table, $data)
            ->execute();
    }

    public function update($data, $id)
    {
        return $this
            ->fluent
            ->update($this->table)
            ->set($data)
            ->where($this->primaryKey, $id)
            ->execute();
    }

    public function delete($id)
    {
        return $this
            ->fluent
            ->deleteFrom($this->table)
            ->where($this->primaryKey, $id)
            ->execute();
    }
}