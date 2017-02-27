<?php

namespace modeles;

class Commentaire
{
    public $id;
    public $pseudo_auteur;
    public $date;
    public $contenu;
    public $id_article;

    public function __construct($id,$pseudo_auteur,$date,$contenu,$id_article)
    {
        if(isset($id)){
            $this->id = $id;
            $this->pseudo_auteur = $pseudo_auteur;
            $this->date=$date;
            $this->contenu=$contenu;
            $this->id_article=$id_article;
        }
    }
}