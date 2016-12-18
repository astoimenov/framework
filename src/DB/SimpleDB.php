<?php

namespace LittleNinja\DB;

use LittleNinja\App;

class SimpleDB
{
    private $db = null;
    protected $connection = 'default';
    private $stmt = null;
    private $params = [];
    private $sql;

    public function __construct($connection = null)
    {
        if ($connection instanceof \mysqli) {
            $this->db = $connection;
        } elseif ($connection !== null) {
            $this->db = App::getInstance()->getDBConnection($connection);
            $this->connection = $connection;
        } else {
            $this->db = App::getInstance()->getDBConnection($this->connection);
        }
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param array  $pdoOptions
     *
     * @return \GF\DB\SimpleDB
     */
    public function prepare($sql, array $params = [], array $pdoOptions = [])
    {
        $this->stmt = $this->db->prepare($sql, $pdoOptions);
        $this->params = $params;
        $this->sql = $sql;

        return $this;
    }

    /**
     * @param array $params
     *
     * @return \LittleNinja\DB\SimpleDB
     */
    public function execute(array $params = [])
    {
        if ($params) {
            $this->params = $params;
        }

        $this->stmt->execute($this->params);

        return $this;
    }

    public function fetchAllAssoc()
    {
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchRowAssoc()
    {
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAllNum()
    {
        return $this->stmt->fetchAll(\PDO::FETCH_NUM);
    }

    public function fetchRowNum()
    {
        return $this->stmt->fetch(\PDO::FETCH_NUM);
    }

    public function fetchAllObj()
    {
        return $this->stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function fetchRowObj()
    {
        return $this->stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function fetchAllColumn($column)
    {
        return $this->stmt->fetchAll(\PDO::FETCH_COLUMN, $column);
    }

    public function fetchRowColumn($column)
    {
        return $this->stmt->fetch(\PDO::FETCH_BOUND, $column);
    }

    public function fetchAllClass($class)
    {
        return $this->stmt->fetchAll(\PDO::FETCH_CLASS, $class);
    }

    public function fetchRowClass($class)
    {
        return $this->stmt->fetch(\PDO::FETCH_BOUND, $class);
    }

    public function getLastInsertId()
    {
        return $this->db->lastInsertId();
    }

    public function getAffectedRows()
    {
        return $this->stmt->rowCount();
    }

    public function getSTMT()
    {
        return $this->stmt;
    }
}
