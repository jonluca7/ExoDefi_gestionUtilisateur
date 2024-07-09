<?php

/*Défi

Il est temps de mettre en commun tout ce qu'on a vu dans ce cours pour créer un formulaire d'inscription et 
de connexion.

Question
Un restaurant veut proposer un système de réservation en ligne à ses clients. Pour cela, il aimerait 
ajouter des fonctionnalités d'inscription et de connexion sur son site.

Le restaurateur vous laisse gérer la structure de la table comme vous le souhaitez : les seules informations
 qui l’intéressent sont l'e-mail, le nom, le prénom et le numéro de département de leurs clients, 
 mais vous pouvez rajouter autant de champs que nécessaires pour le système de connexion.

En revanche, il souhaiterait rester discret sur le nombre d'utilisateurs de sa plateforme : il ne faut pas 
qu'un concurrent puisse déduire le nombre d'utilisateurs inscrits.

Avec ces informations, créez une base de données d'utilisateurs, puis créez une classe PHP permettant 
de les gérer.

Indice
L'e-mail est un bon candidat pour le login, il est inutile d'en rajouter un.

Indice
Attention : les numéros de département ne sont pas tous des nombres (2A et 2B pour la Corse), et ne sont 
pas tous sur deux caractères non plus (971 pour la Guadeloupe, par exemple).

Indice
Contrairement aux auto-incréments, les UUID permettent de ne pas exposer le volume de données contenues
dans une base.*/

require_once 'UserManager.php';

class User {
    private string $id;
    private string $email;
    private string $password;
    private string $firstName;
    private string $lastName;
    private string $department;
    private array $roles = [];

    public function getPassword():string
    {
        return $this->password;
    }

    public function sayHello(){
    echo 'Bonjour'.' '.$this->firstName.' '.$this->lastName;
    }

    public function addRole(string $role) : void
    {
        $this->roles[]= $role;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}
