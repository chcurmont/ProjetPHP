<?php

namespace DAL;

class ArticleGateway
{
    private $con;
    public function __construct($dbHost,$dbName,$user,$pass)
    {
        $this->con = new Connection('mysql:host='.$dbHost.';dbname='.$dbName,$user,$pass);
    }

    public function selectArticleId($id){
        try{
            $query = 'SELECT * from article where id=:id';
            $this->con->executeQuery($query, array(':id' => array($id, \PDO::PARAM_INT)));
            $res=$this->con->getResults();
            if($res==[])
                return NULL;
            return new \modeles\Article($res[0]['id'],
                                        $res[0]['id_auteur'],
                                        $res[0]['titre'],
                                        $res[0]['synopsis'],
                                        $res[0]['texte'],
                                        $res[0]['date_publication'],
                                        $res[0]['image'],
                                        $res[0]['date_modif']);
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function selectArticleIdAuteur($id_auteur){
        try{
            $query = 'SELECT * from article where id_auteur=:id_auteur order by date_publication desc';
            $this->con->executeQuery($query, array(':id_auteur' => array($id_auteur, \PDO::PARAM_INT)));
            $res=$this->con->getResults();
            $articles=[];
            foreach ($res as $row){
                $articles[]=new \modeles\Article(   $row['id'],
                    $row['id_auteur'],
                    $row['titre'],
                    $row['synopsis'],
                    $row['texte'],
                    $row['date_publication'],
                    $row['image'],
                    $row['date_modif']);
            }
            return $articles;
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function selectArticlesFromNb($from,$nb){
        try{
            $query = 'SELECT * from article order by date_publication desc limit :nb offset :from';
            $this->con->executeQuery($query, array(
                ':nb' => array($nb, \PDO::PARAM_INT),
                ':from' => array($from, \PDO::PARAM_INT)
            ));
            $res=$this->con->getResults();
            $articles=[];
            foreach ($res as $row){
                $articles[]=new \modeles\Article(   $row['id'],
                    $row['id_auteur'],
                    $row['titre'],
                    $row['synopsis'],
                    $row['texte'],
                    $row['date_publication'],
                    $row['image'],
                    $row['date_modif']);
            }
            return $articles;
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function nbArticles(){
        try{
            $query = 'SELECT count(1) from article';
            $this->con->executeQuery($query);
            $res=$this->con->getResults()[0]['count(1)'];
            return $res;
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function deleteArticleId($id){
        try{
            $query = 'DELETE from commentaire where id_article=:id; DELETE from article where id=:id';
            return $this->con->executeQuery($query, array(':id' => array($id, \PDO::PARAM_INT)));
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function insertArticle($id_auteur,$titre,$synopsis,$texte,$image){
        try{
            $query = 'INSERT into article values(NULL,:id_auteur,:titre,:synopsis,:texte,now(),:image,NULL)';
            $this->con->executeQuery($query, array(
                ':id_auteur' => array($id_auteur, \PDO::PARAM_INT),
                ':titre' => array($titre, \PDO::PARAM_STR),
                ':synopsis' => array($synopsis, \PDO::PARAM_STR),
                ':texte' => array($texte, \PDO::PARAM_STR),
                ':image'=>array($image, \PDO::PARAM_STR)
            ));
            return $this->con->lastInsertId();
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }

    public function updateArticleId($id,$titre,$synopsis,$texte,$image){
        try{
            $query = 'UPDATE article set titre=:titre, synopsis=:synopsis, texte=:texte, image=:image, date_modif=now() where id=:id';
            return $this->con->executeQuery($query, array(
                ':id' => array($id, \PDO::PARAM_INT),
                ':titre' => array($titre, \PDO::PARAM_STR),
                ':synopsis' => array($synopsis, \PDO::PARAM_STR),
                ':texte' => array($texte, \PDO::PARAM_STR),
                ':image' => array($image, \PDO::PARAM_STR)
            ));
        }
        catch(\PDOException $e){
            throw new \Exception("DatabaseError : " . $e->getMessage());
        }
    }
}