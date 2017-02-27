<?php

namespace DAL;

class Connection extends \PDO
{
    private $stmt;
    public function __construct($dsn, $username, $passwd)
    {
        try{
            parent::__construct($dsn, $username, $passwd);
            $this->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
        }
        catch(\PDOException $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function executeQuery($query,array $parameters=[])
    {
        $this->stmt = parent::prepare($query);
        foreach ($parameters as $name => $value) {
            $this->stmt->bindValue($name, $value[0], $value[1]);
        }
        return $this->stmt->execute();
    }

    public function getResults()
    {
        return $this->stmt->fetchall();
    }
}