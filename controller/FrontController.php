<?php

namespace controller;

class FrontController{
    public function __construct($root)
    {
        try{
            global $actions,$actionsAdmin;
            //session_start();
            $v=new \config\Validation();

            if(!isset($_SESSION['role'])){
                $_SESSION['role']='user';
            }
            $_SESSION['role']=$v->nettoyerString($_SESSION['role']);
            if($_SESSION['role']=='admin'){
                $c=new \controller\AdminController();
                if(!isset($_REQUEST['action'])){
                    $c->home();
                    return;
                }
                $_REQUEST['action']=$v->nettoyerString($_REQUEST['action']);
                if(!in_array($_REQUEST['action'],$actions)&&!in_array($_REQUEST['action'],$actionsAdmin)){
                    $c->erreur('Unknown action: '.$_REQUEST['action']);
                    return;
                }
                //actions admin
                switch($_REQUEST['action']){
                    case null:
                        $c->erreur('Unknown action: '.$_REQUEST['action']);
                        break;
                    case 'home':
                        $c->home();
                        break;
                    case 'article':
                        $c->article();
                        break;
                    case 'connect':
                        $c->profil();
                        break;
                    case 'profil':
                        $c->profil();
                        break;

                    case 'addArticle':
                        $c->addArticle();
                        break;
                    case 'editArticle':
                        $c->editArticle();
                        break;
                    case 'deleteArticle':
                        $c->deleteArticle();
                        break;

                    case 'addComm':
                        $c->addComm();
                        break;
                    case 'editComm':
                        $c->editComm();
                        break;
                    case 'deleteComm':
                        $c->deleteComm();
                        break;

                    case 'disconnect':
                        $c->disconnect();
                        break;
                    default:
                        $c->erreur('Not implemented action: '.$_REQUEST['action']);
                }

            }
            else{
                $c=new \controller\UserController();
                if(!isset($_REQUEST['action'])){
                    $c->home();
                    return;
                }
                $_REQUEST['action']=$v->nettoyerString($_REQUEST['action']);
                if(!in_array($_REQUEST['action'],$actions)){
                    if(in_array($_REQUEST['action'],$actionsAdmin)){
                        $c->erreur('You must be logged in to perform this action: '.$_REQUEST['action']);
                        //$c->connect();
                        return;
                    }
                    else{
                        $c->erreur('Unknown action: '.$_REQUEST['action']);
                        return;
                    }
                }
                //actions user
                switch($_REQUEST['action']){
                    case null:
                        $c->erreur('Unknown action: '.$_REQUEST['action']);
                        break;
                    case 'home':
                        $c->home();
                        break;
                    case 'article':
                        $c->article();
                        break;
                    case 'connect':
                        $c->connect();
                        break;
                    case 'addComm':
                        $c->addComm();
                        break;
                    default:
                        $c->erreur('Not implemented action: '.$_REQUEST['action']);
                }

            }
        }
        catch (\Exception $e){
            $err='Exception: '.$e->getMessage();
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
            require_once ($dir.$vues['error']);
            exit(1);
        }
        catch (\Error $e){
            echo 'Fatal error: '.$e->getMessage();
            exit(1);
        }
    }
}