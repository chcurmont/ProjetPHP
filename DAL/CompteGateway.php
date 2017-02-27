<?php

namespace DAL;

class CompteGateway {

    private $con;
    public function __construct($dbHost,$dbName,$user,$pass)
    {
        $this->con = new Connection('mysql:host='.$dbHost.';dbname='.$dbName,$user,$pass);
    }

    public function selectCompteId($id){
        try{
            $query = 'SELECT * from compte where id=:id';
            $this->con->executeQuery($query, array(':id' => array($id, \PDO::PARAM_INT)));
            $res=$this->con->getResults();
            if($res==[])
                return NULL;
            return new \modeles\Compte($res[0]['id'],$res[0]['login'],$res[0]['mdp']);
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function selectCompteLogin($login){
        try{
            $query = 'SELECT * from compte where login=:login';
            $this->con->executeQuery($query, array(':login' => array($login, \PDO::PARAM_STR)));
            $res=$this->con->getResults();
            if($res==[])
                return NULL;
            return new \modeles\Compte($res[0]['id'],$res[0]['login'],$res[0]['mdp']);
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function insertCompte($login,$mdp){
        try{
            $query = 'INSERT into compte values(:login,:mdp)';
            $this->con->executeQuery($query, array(
                ':login' => array($login, \PDO::PARAM_STR),
                ':mdp' => array($mdp,\PDO::PARAM_STR)
            ));
            return $this->con->lastInsertId();
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function deleteCompte($id){
        try{
            $query = 'DELETE from compte where id=:id';
            return $this->con->executeQuery($query, array(':id' => array($id, \PDO::PARAM_INT)));
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function updateCompteMdp($id,$mdp){
        try{
            $query = 'UPDATE article set mdp=:mdp where id=:id';
            return $this->con->executeQuery($query, array(
                ':id' => array($id, \PDO::PARAM_INT),
                ':mdp' => array($mdp, \PDO::PARAM_STR)
            ));
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function selectAllComptes(){
        try{
            $query = 'SELECT * from compte';
            $this->con->executeQuery($query);
            $res=$this->con->getResults();
            $comptes=[];
            foreach ($res as $row){
                $comptes[$row['id']]=new \modeles\Compte($row['id'],$row['login'],$row['mdp']);
            }
            return $comptes;
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function selectAllLogins(){
        try{
            $query = 'SELECT login from compte';
            $this->con->executeQuery($query);
            $res=$this->con->getResults();
            $comptes=[];
            foreach ($res as $row){
                $comptes[]=$row['login'];
            }
            return $comptes;
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function isCompteOk($login,$mdp){
        try{
            $query = 'SELECT count(1) from compte where login=:login and mdp=:mdp';
            $this->con->executeQuery($query, array(
                ':login' => array($login, \PDO::PARAM_STR),
                ':mdp' => array($mdp,\PDO::PARAM_STR)
            ));
            $res=$this->con->getResults()[0]['count(1)'];
            return $res;
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function selectNbArticlesId($id){
        try{
            $query = 'SELECT count(1) from article where id_auteur=:id';
            $this->con->executeQuery($query, array(':id' => array($id, \PDO::PARAM_INT)));
            $res=$this->con->getResults()[0]['count(1)'];
            return $res;
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function selectNbCommsPseudo($pseudo){
        try{
            $query = 'SELECT count(1) from commentaire where pseudo_auteur=:pseudo';
            $this->con->executeQuery($query, array(':pseudo' => array($pseudo, \PDO::PARAM_STR)));
            $res=$this->con->getResults()[0]['count(1)'];
            return $res;
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }
}