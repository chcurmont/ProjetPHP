<?php

namespace controller;

class AdminController{
    public function head(){
        $this->checkAdmin();
        global $dir,$vues;
        $v=new \config\Validation();

        $liensHead=[];
        if(isset($_SESSION['login'])) {
            $_SESSION['login']=$v->nettoyerString($_SESSION['login']);
            $liensHead['Profile [' . $_SESSION['login'] . ']'] = 'index.php?action=profil';
        }
        else
            $liensHead['Profile']='index.php?action=profil';
        $liensHead['Add article']='index.php?action=addArticle';
        $liensHead['Disconnect']='index.php?action=disconnect';

        require_once ($dir.$vues['header']);
    }

    public function checkAdmin(){
        try{
            if(isset($_SESSION)){
                if($_SESSION['role']=='admin'){
                    return;
                }
            }
            //verifier login dans bdd?
            global $dir,$vues;
            $liensHead=[];
            $liensHead['Connect']='index.php?action=connect';
            //mémorisation de l'action en cours
            if(isset($_REQUEST['action'])) {
                if ($_REQUEST['action'] != 'connect') {
                    $liensHead['Connect'] = $liensHead['Connect'] . '&nextAction=' . $_REQUEST['action'];
                    foreach ($_REQUEST as $key => $val) {
                        if ($key != 'action') {
                            $liensHead['Connect'] = $liensHead['Connect'] . '&' . $key . '=' . $val;
                        }
                    }
                } else {
                    if (isset($_REQUEST['nextAction'])) {
                        foreach ($_REQUEST as $key => $val) {
                            if ($key != 'action') {
                                $liensHead['Connect'] = $liensHead['Connect'] . '&' . $key . '=' . $val;
                            }
                        }
                    } else {
                        $liensHead['Connect'] = $liensHead['Connect'] . '&nextAction=home';
                    }
                }
            }
            else {
                $liensHead['Connect'] = $liensHead['Connect'] . '&nextAction=home';
            }
            require_once ($dir.$vues['header']);
            $err='You don\'t have the rights to perform this action';
            require ($dir.$vues['error']);
            exit(1);
        }
        catch (\Exception $e){
            $this->erreur('Exception: ' . $e->getMessage());
            exit(1);
        }
        catch (\Error $e){
            $this->erreur('Error: ' . $e->getMessage());
            exit(1);
        }
    }

    public function home(){
        try{
            $this->checkAdmin();
            global $dir,$vues,$db_host,$db_login,$db_name,$db_password,$nbArticlesParPage;
            $g=new \DAL\ArticleGateway($db_host,$db_name,$db_login,$db_password);
            $cg=new \DAL\CompteGateway($db_host,$db_name,$db_login,$db_password);
            $v=new \config\Validation();
            if(!isset($_REQUEST['numPage']))
                $_REQUEST['numPage']=1;
            if(!$v->validateEntierIntervalleInclus($_REQUEST['numPage'],1,ceil($g->nbArticles()/$nbArticlesParPage))){
                $this->erreur('Numéro de page inconnu');
                return;
            }
            $articles=$g->selectArticlesFromNb($nbArticlesParPage*$_REQUEST['numPage']-$nbArticlesParPage,$nbArticlesParPage);
            $prev=$_REQUEST['numPage']-1;
            if($g->nbArticles()>$_REQUEST['numPage']*$nbArticlesParPage)
                $next=$_REQUEST['numPage']+1;
            else
                $next=0;
            $admin=true;
            $nbArtTotal=$g->nbArticles();
            $comptes=$cg->selectAllComptes();
            $this->head();
            require ($dir.$vues['home']);
        }
        catch (\Exception $e){
            $this->erreur('Exception: ' . $e->getMessage());
            exit(1);
        }
        catch (\Error $e){
            $this->erreur('Error: ' . $e->getMessage());
            exit(1);
        }
    }

    public function profil(){
        //changer mdp
        //créer user admin
        try{
            $this->checkAdmin();
            global $dir,$vues,$db_host,$db_login,$db_name,$db_password;
            $g=new \DAL\CompteGateway($db_host,$db_name,$db_login,$db_password);
            $v=new \config\Validation();
            if(!isset($_SESSION['login'])){
                $this->erreur('Incorrect parameter');
                return;
            }

            $compte=$g->selectCompteLogin($_SESSION['login']);
            $nbArt=$g->selectNbArticlesId($compte->id);
            $nbComms=$g->selectNbCommsPseudo($compte->login);
            $this->head();
            require ($dir.$vues['profil']);
        }
        catch (\Exception $e){
            $this->erreur('Exception: ' . $e->getMessage());
            exit(1);
        }
        catch (\Error $e){
            $this->erreur('Error: ' . $e->getMessage());
            exit(1);
        }
    }

    public function article(){
        try{
            $this->checkAdmin();
            global $dir,$vues,$db_host,$db_login,$db_name,$db_password;
            $g=new \DAL\ArticleGateway($db_host,$db_name,$db_login,$db_password);
            $cg=new \DAL\CompteGateway($db_host,$db_name,$db_login,$db_password);
            $comg=new \DAL\CommentaireGateway($db_host,$db_name,$db_login,$db_password);
            $v=new \config\Validation();
            if(!isset($_REQUEST['id'])){
                $this->erreur('Id incorrect');
                return;
            }
            if(!$v->validateInt($_REQUEST['id'])) {
                $this->erreur('Id incorrect');
                return;
            }
            $article=$g->selectArticleId($_REQUEST['id']);
            if($article==NULL){
                $this->erreur('404, article introuvable');
                return;
            }
            $admin=true;
            $coms=$comg->selectCommentairesArticle($article->id);
            $auteur=$cg->selectCompteId($article->id_auteur);
            $comptes=$cg->selectAllLogins();
            $this->head();
            require ($dir.$vues['article']);
        }
        catch (\Exception $e){
            $this->erreur('Exception: ' . $e->getMessage());
            exit(1);
        }
        catch (\Error $e){
            $this->erreur('Error: ' . $e->getMessage());
            exit(1);
        }
    }

    public function addComm(){
        try{
            $this->checkAdmin();
            global $db_host,$db_login,$db_name,$db_password;
            $g=new \DAL\CommentaireGateway($db_host,$db_name,$db_login,$db_password);
            $ag=new \DAL\ArticleGateway($db_host,$db_name,$db_login,$db_password);
            $v=new \config\Validation();

            if(!isset($_REQUEST['articleId'])||!isset($_REQUEST['comm'])){
                $this->erreur('Incorrect parameter');
                return;
            }
            if(!$v->validateInt($_REQUEST['articleId'])) {
                $this->erreur('Id incorrect');
                return;
            }
            $_SESSION['login']=$v->nettoyerString($_SESSION['login']);
            $_REQUEST['comm']=$v->nettoyerString($_REQUEST['comm']);
            if($_REQUEST['comm']==""){
                $this->erreur('Empty parameter');
                return;
            }
            $article=$ag->selectArticleId($_REQUEST['articleId']);
            if($article==NULL){
                //revenir sur formulaire avec sauvegarde des données et message d'erreur
                $this->erreur('404, article introuvable');
                return;
            }
            $lastInsert=$g->insertCommentaire($_SESSION['login'],$_REQUEST['comm'],$_REQUEST['articleId']);
            header('Location: index.php?action=article&id=' . $_REQUEST['articleId'] . '#comm' . $lastInsert);
        }
        catch (\Exception $e){
            $this->erreur('Exception: ' . $e->getMessage());
            exit(1);
        }
        catch (\Error $e){
            $this->erreur('Error: ' . $e->getMessage());
            exit(1);
        }
    }

    public function addArticle(){
        try{
            $this->checkAdmin();
            global $dir,$vues,$db_host,$db_login,$db_name,$db_password;
            $g=new \DAL\ArticleGateway($db_host,$db_name,$db_login,$db_password);
            $cg=new \DAL\CompteGateway($db_host,$db_name,$db_login,$db_password);
            $v=new \config\Validation();

            if(!isset($_SESSION['login'])){
                $this->erreur('Incorrect parameter');
                return;
            }

            if(isset($_REQUEST['titre']) && isset($_REQUEST['synopsis']) && isset($_REQUEST['text'])){
                $_REQUEST['titre']=$v->nettoyerString($_REQUEST['titre']);
                $_REQUEST['synopsis']=$v->nettoyerString($_REQUEST['synopsis']);
                $_REQUEST['text']=$v->nettoyerString($_REQUEST['text']);

                if($_REQUEST['titre']=="" && $_REQUEST['synopsis']=="" && $_REQUEST['text']==""){
                    $this->erreur('Empty parameter');
                    return;
                }

                $_SESSION['login']=$v->nettoyerString($_SESSION['login']);
                $compte=$cg->selectCompteLogin($_SESSION['login']);

                $lastInsert=$g->insertArticle($compte->id,$_REQUEST['titre'],$_REQUEST['synopsis'],$_REQUEST['text'],"views/images/900x300.png");

                header('Location: index.php?action=article&id=' . $lastInsert);
                return;
            }

            $this->head();
            require ($dir.$vues['manageArticle']);
        }
        catch (\Exception $e){
            $this->erreur('Exception: ' . $e->getMessage());
            exit(1);
        }
        catch (\Error $e){
            $this->erreur('Error: ' . $e->getMessage());
            exit(1);
        }
    }

    public function editArticle(){
        try{
            $this->checkAdmin();

            global $dir,$vues,$db_host,$db_login,$db_name,$db_password;
            $g=new \DAL\ArticleGateway($db_host,$db_name,$db_login,$db_password);
            $v=new \config\Validation();


            if(!isset($_REQUEST['id'])){
                $this->erreur('Incorrect parameter');
                return;
            }
            if(!$v->validateInt($_REQUEST['id'])) {
                $this->erreur('Id incorrect');
                return;
            }
            $article=$g->selectArticleId($_REQUEST['id']);
            if($article==NULL){
                $this->erreur('Commentaire introuvable');
                return;
            }

            if(isset($_REQUEST['titre']) && isset($_REQUEST['synopsis']) && isset($_REQUEST['text'])){
                $_REQUEST['titre']=$v->nettoyerString($_REQUEST['titre']);
                $_REQUEST['synopsis']=$v->nettoyerString($_REQUEST['synopsis']);
                $_REQUEST['text']=$v->nettoyerString($_REQUEST['text']);

                if($_REQUEST['titre']=="" && $_REQUEST['synopsis']=="" && $_REQUEST['text']==""){
                    $this->erreur('Empty parameter');
                    return;
                }

                $g->updateArticleId($_REQUEST['id'],$_REQUEST['titre'],$_REQUEST['synopsis'],$_REQUEST['text'],$article->image);
                header('Location: index.php?action=article&id=' . $_REQUEST['id']);
                return;
            }

            $this->head();
            require ($dir.$vues['manageArticle']);
        }
        catch (\Exception $e){
            $this->erreur('Exception: ' . $e->getMessage());
            exit(1);
        }
        catch (\Error $e){
            $this->erreur('Error: ' . $e->getMessage());
            exit(1);
        }
    }

    public function deleteArticle(){
        try{
            $this->checkAdmin();
            global $db_host,$db_login,$db_name,$db_password;
            $g=new \DAL\ArticleGateway($db_host,$db_name,$db_login,$db_password);
            $v=new \config\Validation();

            if(!isset($_REQUEST['id'])){
                $this->erreur('Incorrect parameter');
                return;
            }
            if(!$v->validateInt($_REQUEST['id'])) {
                $this->erreur('Id incorrect');
                return;
            }
            $art=$g->selectArticleId($_REQUEST['id']);
            if($art==NULL){
                $this->erreur('Article introuvable');
                return;
            }

            $g->deleteArticleId($_REQUEST['id']);
            header('Location: index.php');
        }
        catch (\Exception $e){
            $this->erreur('Exception: ' . $e->getMessage());
            exit(1);
        }
        catch (\Error $e){
            $this->erreur('Error: ' . $e->getMessage());
            exit(1);
        }
    }

    public function editComm(){
        try{
            $this->checkAdmin();
            global $dir,$vues,$db_host,$db_login,$db_name,$db_password;
            $g=new \DAL\CommentaireGateway($db_host,$db_name,$db_login,$db_password);
            $cg=new \DAL\CompteGateway($db_host,$db_name,$db_login,$db_password);
            $v=new \config\Validation();

            if(!isset($_REQUEST['id'])){
                $this->erreur('Incorrect parameter');
                return;
            }
            if(!$v->validateInt($_REQUEST['id'])) {
                $this->erreur('Id incorrect');
                return;
            }
            $comm=$g->selectCommentaireId($_REQUEST['id']);
            if($comm==NULL){
                $this->erreur('Commentaire introuvable');
                return;
            }

            if(isset($_REQUEST['text'])){
                $_REQUEST['text']=$v->nettoyerString($_REQUEST['text']);
                if($_REQUEST['text']==""){
                    $this->erreur('Empty parameter');
                    return;
                }

                $g->updateCommentaireId($_REQUEST['id'],$_REQUEST['text']);
                header('Location: index.php?action=article&id=' . $comm->id_article . '#comm' . $comm->id);
                return;
            }

            $comptes=$cg->selectAllLogins();
            $this->head();
            require ($dir.$vues['editComm']);
        }
        catch (\Exception $e){
            $this->erreur('Exception: ' . $e->getMessage());
            exit(1);
        }
        catch (\Error $e){
            $this->erreur('Error: ' . $e->getMessage());
            exit(1);
        }
    }

    public function deleteComm(){
        try{
            $this->checkAdmin();
            global $db_host,$db_login,$db_name,$db_password;
            $g=new \DAL\CommentaireGateway($db_host,$db_name,$db_login,$db_password);
            $v=new \config\Validation();

            if(!isset($_REQUEST['id'])){
                $this->erreur('Incorrect parameter');
                return;
            }
            if(!$v->validateInt($_REQUEST['id'])) {
                $this->erreur('Id incorrect');
                return;
            }
            $comm=$g->selectCommentaireId($_REQUEST['id']);
            if($comm==NULL){
                $this->erreur('Commentaire introuvable');
                return;
            }

            $next='Location: index.php?action=article&id=' . $comm->id_article . '#comms';
            $g->deleteCommentaireId($_REQUEST['id']);
            header($next);
        }
        catch (\Exception $e){
            $this->erreur('Exception: ' . $e->getMessage());
            exit(1);
        }
        catch (\Error $e){
            $this->erreur('Error: ' . $e->getMessage());
            exit(1);
        }
    }

    public function disconnect(){
        try{
            $this->checkAdmin();
            session_unset();
            session_destroy();
            //vider cookies
            //setcookie('nomCookie', NULL, -1);
            header('Location: index.php');
        }
        catch (\Exception $e){
            $this->erreur('Exception: ' . $e->getMessage());
            exit(1);
        }
        catch (\Error $e){
            $this->erreur('Error: ' . $e->getMessage());
            exit(1);
        }
    }

    public function erreur($err){
        try{
            $this->checkAdmin();
            global $dir,$vues;
            $this->head();
            require ($dir.$vues['error']);
        }
        catch (\Exception $e){
            echo 'Fatal error: '.$e->getMessage();
            exit(1);
        }
        catch (\Error $e){
            echo 'Fatal error: '.$e->getMessage();
            exit(1);
        }
    }
}