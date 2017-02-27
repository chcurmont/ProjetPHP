<?php

namespace modeles;

class Article
{
    public $id;
    public $id_auteur;
    public $titre;
    public $synopsis;
    public $texte;
    public $date_publication;
    public $image;
    public $date_modif;

    public function __construct($id,$id_auteur,$titre,$synopsis,$texte,$date_publication,$image,$date_modif)
    {
        $this->id=$id;
        $this->id_auteur=$id_auteur;
        $this->titre=$titre;
        $this->synopsis=$synopsis;
        $this->texte=$texte;
        $this->date_publication=$date_publication;
        $this->image=$image;
        $this->date_modif=$date_modif;
    }
}