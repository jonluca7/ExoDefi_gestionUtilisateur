<?php

/*
Question

Créez un UserManager qui va vous permettre de manipuler les utilisateurs en base de données. 
Cette classe devra comporter une méthode subscribe prenant en paramètres toutes les informations 
nécessaires à la création d'un utilisateur et qui retourne vrai si l'utilisateur a pu être inséré 
en base de données, faux sinon. Pour cela, une instance de PDO devra être fournie au manager par 
injection de dépendances.

Pour simplifier la question, la seule vérification à réaliser sur le mot de passe doit être sa taille, 
supérieure à 7 caractères. Il ne sera pas nécessaire de vérifier les autres données. 
Le mot de passe devra être hashé en utilisant l'algorithme BCRYPT.

Créez ensuite un utilisateur de votre choix dans le fichier index.php.*/

require_once 'user.php';

class UserManager{

    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function subscribe(string $email, string $password, string  $firstName, string  $lastName, string $department): bool
    {
        // Vérification de la longueur du mot de passe
        if(strlen($password) < 8 ){
        
       // Si le mot de passe n'est pas au bon format, on retourne false
            return false;
        }

        
    // Preparation de la requête 
    $statement= $this->pdo->prepare('
    INSERT INTO users(id, email, password,  firstName, lastName, department)
    VALUES(UUID(), :email, :password, :firstName, :lastName, :department) 
    ');
    
    // Injection des valeurs des marqueurs

    $statement->bindValue(':email', $email, PDO::PARAM_STR );

    // Hachage du mot de passe

    $statement->bindValue(':password', password_hash($password, PASSWORD_BCRYPT),PDO::PARAM_STR );
    $statement->bindValue(':firstName', $firstName, PDO::PARAM_STR);
    $statement->bindValue(':lastName', $lastName, PDO::PARAM_STR);
    $statement->bindValue(':department', $department, PDO::PARAM_STR);

     // On retourne le statut d'execution de la requête
    return false;
    }

    public function connect(string $email, string $password): user
    {
        
        
        // On recherche un utilisateur ayant l'adresse Email correspondante
        $statement= $this->pdo->prepare('SELECT * FROM users WHERE email= :email');
        $statement-> setFetchMode(PDO::FETCH_CLASS, 'User');
        $statement->bindValue(':email', $email);

        if($statement->execute()){
        // Il y a une contrainte d'unicité sur l'email: Il n'est donc pas besoin de faire une boucle

        $user= $statement->fetch();

        // Il faut vérifier que Fetch n'a pas retourné False avant de vérifier le mot de passe

        if($user !== false && password_verify($password, $user->getPassword())){

            // On verifie si le departement de l'utilisateur est dans la liste des départements autorisées
        
            if(in_array($user->getDepartment(), ['75', '94', '92', '93'])){
                $user->addRole('ROLE_DELIVERABLE');

            }
            return $user;
        }

       }
         throw new Exception('Identifiants Invalids');
    }
}
