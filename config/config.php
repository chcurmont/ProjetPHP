<?php

//attention mettre bdd en utf8_bin

//rajouter isset dans views
//rechercher news par date


$authors='CURMONT LAURENT';

$blog_name="Projet blog " . $authors;
$footer_text="Copyright &copy; 2016 ".$blog_name." groupe 4";

$db_host="localhost";
$db_name="projetphp";
$db_login="root";
$db_password="password";

$dir=__DIR__.'/../';

$nbArticlesParPage=10;


$vues['header']='views/header.php';
$vues['footer']='views/footer.php';

$vues['article']='views/article.php';
$vues['home']='views/home.php';
$vues['profil']='views/profil.php';

$vues['error']='views/error.php';

$vues['connect']='views/connect.php';
$vues['editComm']='views/editComm.php';
$vues['manageArticle']='views/manageArticle.php';


$actions=[];
$actions[]='home';
$actions[]='article';
$actions[]='connect';
$actions[]='addComm';

$actionsAdmin=[];
$actionsAdmin[]='profil';
$actionsAdmin[]='addArticle';
$actionsAdmin[]='editArticle';
$actionsAdmin[]='deleteArticle';
$actionsAdmin[]='editComm';
$actionsAdmin[]='deleteComm';
$actionsAdmin[]='disconnect';
//changer password compte
//créer compte
