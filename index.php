<?php
require_once 'userManager.php';

$pdo= new PDO('mysql:host=localhost:3308;dbname=restaurant', 'root', '');

// Injection de l'objet PDO dans le Manager

$manager = new UserManager($pdo);
if($manager->subscribe('john@doe.com', 'p4$$w0rd', 'John', 'Doe', '2A')){
    echo "L'inscription a reussie";
}else{
      echo "Echec lors de l'inscription";
}

$user= $manager->connect('john@doe.com', 'p4$$w0rd');
echo $user->sayHello();

if(!in_array('ROLE_DELIVERABLE',$user->getRoles())){
    throw new Exception('Accès Interdit');
}