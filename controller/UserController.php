<?php

namespace controller;

class UserController{

    //ajouter try catch

    public function head(){
        global $dir,$vues;

        $liensHead=[];
        $liensHead['Connect']='index.php?action=connect';
        //mémorisation de l'action en cours
        if(isset($_REQUEST['action'])) {
            if ($_REQUEST['action'] != 'connect') {
                $liensHead['Connect'] = $liensHead['Connect'] . '&nextAction=' . $_REQUEST['action'];
                foreach ($_REQUEST as $key => $val) {
                    if ($key != 'action' && $key != "nbComm") {
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
    }

    public function home(){
        try{
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

    public function connect(){
        //ajouter cryptage
        //mettre valeurs à transmettre dans cookie ou post ou session au lieu de l'url
        try{
            global $dir,$vues,$db_host,$db_login,$db_name,$db_password;
            $g=new \DAL\CompteGateway($db_host,$db_name,$db_login,$db_password);
            $v=new \config\Validation();

            if(!isset($_REQUEST['nextAction']))
                $_REQUEST['nextAction']='home';

            if(isset($_REQUEST['err']))
                if($v->nettoyerString($_REQUEST['err']))
                    $err=$_REQUEST['err'];

            if(isset($_REQUEST['login']) && isset($_REQUEST['password'])){
                if($v->validatePrintableSansEspaces($_REQUEST['login']) && $v->validatePrintableSansEspaces($_REQUEST['password'])){
                    $_REQUEST['login']=$v->nettoyerString($_REQUEST['login']);
                    $_REQUEST['password']=$v->nettoyerString($_REQUEST['password']);
                    if($g->isCompteOk($_REQUEST['login'],$_REQUEST['password'])){
                        $_SESSION['role']='admin';
                        $_SESSION['login']=$_REQUEST['login'];
                        $next='Location: index.php?action=' . $_REQUEST['nextAction'];
                        foreach ($_REQUEST as $key => $val) {
                            if ($key != 'action' && $key != 'nextAction' && $key != 'login' && $key != 'password' && $key != 'err') {
                                $next = $next . '&' . $key . '=' . $val;
                            }
                        }
                        if($_REQUEST['nextAction']=='article'&&isset($_REQUEST['comm']))
                            $next=$next . '#writeComm';
                        setcookie("nbComm","",time()-3600);
                        header($next);
                    }else{
                        $err='Login/mot de passe incorrect';
                    }
                }
                else{
                    $this->erreur('Login/password contains illegal characters!');
                    return;
                }
            }

            $this->head();
            require ($dir.$vues['connect']);
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
            global $db_host,$db_login,$db_name,$db_password;
            $g=new \DAL\CommentaireGateway($db_host,$db_name,$db_login,$db_password);
            $ag=new \DAL\ArticleGateway($db_host,$db_name,$db_login,$db_password);
            $cg=new \DAL\CompteGateway($db_host,$db_name,$db_login,$db_password);
            $v=new \config\Validation();

            if(!isset($_REQUEST['login'])||!isset($_REQUEST['articleId'])||!isset($_REQUEST['comm'])){
                $this->erreur('Incorrect parameter');
                return;
            }
            if(!$v->validateInt($_REQUEST['articleId'])) {
                $this->erreur('Id incorrect');
                return;
            }
            $article=$ag->selectArticleId($_REQUEST['articleId']);
            if($article==NULL){
                $this->erreur('404, article not found');
                return;
            }
            $_REQUEST['login']=$v->nettoyerString($_REQUEST['login']);
            $_REQUEST['comm']=$v->nettoyerString($_REQUEST['comm']);
            if(!$v->validatePrintableSansEspaces($_REQUEST['login'])){
                $this->erreur('Login contains illegal characters!');
                return;
            }
            if($_REQUEST['login']=="" && $_REQUEST['comm']==""){
                $this->erreur('Empty parameter');
                return;
            }

            $listeLogins=$cg->selectAllLogins();
            if($v->validateStringDansTab($_REQUEST['login'],$listeLogins)){
                $next='Location: index.php?action=connect&nextAction=article&id=' . $_REQUEST['articleId'] . '&comm=' . $_REQUEST['comm'] . '&login=' . $_REQUEST['login'] . '&err=You must be logged in to post with this login';
                header($next);
                return;
            }
            $_SESSION['login']=$_REQUEST['login'];

            $lastInsert=$g->insertCommentaire($_SESSION['login'],$_REQUEST['comm'],$_REQUEST['articleId']);
            //le cookie serait normalement remplacé par un appel bdd...
            if(isset($_COOKIE['nbComm'])){
                if(!$v->validateInt($_COOKIE['nbComm']))
                    $_COOKIE['nbComm']=0;
                setcookie("nbComm",$_COOKIE['nbComm']+1);
            }else
                setcookie("nbComm",1);
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

    public function erreur($err){
        try{
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