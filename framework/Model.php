<?php

namespace Light2;

abstract class Model
{
    public string $table;
    public string $primaryKey;
    public $db = null;

    public function connect(): Model
    {
        if (is_null($this->db)) {
            $this->db = db_connect();
        }

        return $this;
    }

    public function disconnect(): bool
    {
        if (!is_null($this->db)) {
            $this->db->close();
            $this->db = null;
            return true;
        } else {
            return false;
        }
    }

    public function findAll()
    {
        $result = $this->connect()->db->from($this->table)->fetchAll();
        $this->disconnect();

        return $result;
    }

    public function find($id)
    {
        $result = $this
            ->connect()
            ->db
            ->from($this->table)
            ->where($this->primaryKey, $id)
            ->fetch();
        $this->disconnect();

        return $result;
    }

    public function insert(array $data)
    {
        $result = $this
            ->connect()
            ->db
            ->insertInto($this->table, $data)
            ->execute();
        $this->disconnect();

        return $result;
    }

    public function update($data, $id)
    {

        $result = $this
            ->connect()
            ->db
            ->update($this->table)
            ->set($data)
            ->where($this->primaryKey, $id)
            ->execute();
        $this->disconnect();

        return $result;
    }

    public function delete($id)
    {
        $result = $this
            ->connect()
            ->db
            ->deleteFrom($this->table)
            ->where($this->primaryKey, $id)
            ->execute();
        $this->disconnect();

        return $result;
    }
}