<?php

namespace modeles;

class Compte
{
    public $id;
    public $login;
    public $mdp;

    public function __construct($id, $login, $mdp)
    {
        $this->id = $id;
        $this->login = $login;
        $this->mdp = $mdp;
    }
}