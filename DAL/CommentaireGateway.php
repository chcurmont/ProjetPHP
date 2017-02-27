<?php

namespace DAL;

class CommentaireGateway
{
    private $con;
    public function __construct($dbHost,$dbName,$user,$pass)
    {
        $this->con = new Connection('mysql:host='.$dbHost.';dbname='.$dbName,$user,$pass);
    }

    public function selectCommentaireId($id){
        try{
            $query = 'SELECT * from commentaire where id=:id';
            $this->con->executeQuery($query, array(':id' => array($id, \PDO::PARAM_INT)));
            $res=$this->con->getResults();
            if($res==[])
                return NULL;
            return new \modeles\Commentaire($res[0]['id'],
                $res[0]['pseudo_auteur'],
                $res[0]['date'],
                $res[0]['contenu'],
                $res[0]['id_article']);
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function selectCommentairesArticle($id_article){
        try{
            $query = 'SELECT * from commentaire where id_article=:id_article order by date';
            $this->con->executeQuery($query, array(':id_article' => array($id_article, \PDO::PARAM_INT)));
            $res=$this->con->getResults();
            $commentaires=[];
            foreach ($res as $row){
                $commentaires[]=new \modeles\Commentaire(   $row['id'],
                    $row['pseudo_auteur'],
                    $row['date'],
                    $row['contenu'],
                    $row['id_article']);
            }
            return $commentaires;
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function insertCommentaire($pseudo_auteur,$contenu,$id_article){
        try{
            $query = 'INSERT into commentaire values(NULL,:pseudo_auteur,now(),:contenu,:id_article)';
            $this->con->executeQuery($query, array(
                ':pseudo_auteur' => array($pseudo_auteur, \PDO::PARAM_STR),
                ':contenu' => array($contenu, \PDO::PARAM_STR),
                ':id_article' => array($id_article, \PDO::PARAM_INT)
            ));
            return $this->con->lastInsertId();
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function deleteCommentaireId($id){
        try{
            $query = 'DELETE from commentaire where id=:id';
            return $this->con->executeQuery($query, array(':id' => array($id, \PDO::PARAM_INT)));
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function updateCommentaireId($id,$contenu){
        try{
            $query='UPDATE commentaire set contenu=:contenu where id=:id';
            return $this->con->executeQuery($query,array(
                ':contenu'=>array($contenu, \PDO::PARAM_STR),
                ':id'=>array($id, \PDO::PARAM_INT)
            ));
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }
}