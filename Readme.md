# Exercice Defi Gestion des utilisateurs, Libellé + correction du cours

Défi<br>
Il est temps de mettre en commun tout ce qu'on a vu dans ce cours pour créer un formulaire d'inscription <br>
et de connexion.<br>

Question<br><br>
Un restaurant veut proposer un système de réservation en ligne à ses clients. Pour cela, il aimerait <br>
ajouter des fonctionnalités d'inscription et de connexion sur son site.<br>

Le restaurateur vous laisse gérer la structure de la table comme vous le souhaitez : les seules<br>
 informations qui l’intéressent sont l'e-mail, le nom, le prénom et le numéro de département de <br>
 leurs clients, mais vous pouvez rajouter autant de champs que nécessaires pour le système de <br>
 connexion.<br>

En revanche, il souhaiterait rester discret sur le nombre d'utilisateurs de sa plateforme :<br>
 il ne faut pas qu'un concurrent puisse déduire le nombre d'utilisateurs inscrits.<br>

Avec ces informations, créez une base de données d'utilisateurs, puis créez une classe PHP permettant<br>
 de les gérer.<br>

 Indice<br><br>
L'e-mail est un bon candidat pour le login, il est inutile d'en rajouter un.<br>

Attention : les numéros de département ne sont pas tous des nombres (2A et 2B pour la Corse), et ne sont<br>
pas tous sur deux caractères non plus (971 pour la Guadeloupe, par exemple).<br>

Contrairement aux auto-incréments, les UUID permettent de ne pas exposer le volume de données contenues<br>
dans une base.<br><br>

Solution<br>
Le schéma de base de données est le suivant :<br>


CREATE TABLE users ( <br>
  id CHAR(36) NOT NULL PRIMARY KEY,<br>
  email VARCHAR(254) UNIQUE NOT NULL,<br>
  password VARCHAR(60) NOT NULL,<br>
  firstName VARCHAR(100) NOT NULL,<br>
  lastName VARCHAR(100) NOT NULL,<br>
  department VARCHAR(3) NOT NULL<br>
);<br><br>

Fichier User.php :<br><br>


<?php<br>
class User<br>
{
    private string $id;<br>
    private string $email;<br>
    private string $password;<br>
    private string $firstName;<br>
    private string $lastName;<br>
    private string $department;<br>
}<br><br>

Question : <br>
Créez un UserManager qui va vous permettre de manipuler les utilisateurs en base de données.<br>
Cette classe devra comporter une méthode subscribe prenant en paramètres toutes les informations<br>
nécessaires à la création d'un utilisateur et qui retourne vrai si l'utilisateur a pu être inséré<br>
en base de données, faux sinon. Pour cela, une instance de PDO devra être fournie au manager par<br>
 injection de dépendances.<br>

Pour simplifier la question, la seule vérification à réaliser sur le mot de passe doit être<br>
sa taille, supérieure à 7 caractères. Il ne sera pas nécessaire de vérifier les autres données.<br> 
Le mot de passe devra être hashé en utilisant l'algorithme BCRYPT.<br>

Créez ensuite un utilisateur de votre choix dans le fichier index.php.<br>

Indice<br>
Pour le moment, il n'est pas utile d'utiliser la classe User.<br><br>

Solution: <br><br>
Fichier UserManager.php :<br>

<?php<br>
class UserManager<br>
{<br>
    private PDO $pdo;<br>
    public function __construct(PDO $pdo)<br>
    {<br>
        $this->pdo = $pdo;<br>
    }<br>
    public function subscribe(string $email, string $password, string $firstName, string $lastName, 
    string $department): bool <br>
    {<br>
        // Vérification de la taille du mot de passe<br>
        if (strlen($password) < 8) {<br>
            // Si le mot de passe n'est pas au bon format, on retourne false<br>
            return false;<br>
        }<br>
        // Préparation de la requête<br>
        $statement = $this->pdo->prepare('<br>
INSERT INTO users (id, email, password, firstName, lastName, department)<br>
VALUES (UUID(), :email, :password, :firstName, :lastName, :department)'<br>
        );<br>
        // Injection des valeurs des marqueurs<br>
        $statement->bindValue(':email', $email, PDO::PARAM_STR);<br>
        // Hashage du mot de passe<br>
        $statement->bindValue(':password', password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);<br>
        $statement->bindValue(':firstName', $firstName, PDO::PARAM_STR);<br>
        $statement->bindValue(':lastName', $lastName, PDO::PARAM_STR);<br>
        $statement->bindValue(':department', $department, PDO::PARAM_STR);<br>
        // On retourne le statut d'exécution de la requête<br>
        return $statement->execute();<br>
    }<br>
}<br><br>


Fichier index.php :<br>


<?php<br>

require_once 'UserManager.php';<br>

$pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '');<br>

// Injection de l'objet PDO dans le manager<br>

$manager = new UserManager($pdo);<br>

if ($manager->subscribe('john@doe.com', 'p4$$w0rd', 'John', 'Doe', '2A')) {<br>

    echo 'Inscription réussie';<br>
} else {<br>

   echo 'Echec lors de l\'inscription';<br>
}<br><br>

Question<br>
Ajoutez une méthode connect à votre UserManager. Cette méthode doit prendre en paramètres un login et<br>
un mot de passe et doit retourner le User correspondant. Si aucun utilisateur n'est trouvé,<br>
alors cette méthode doit lancer une exception.<br>

Connectez-vous ensuite en tant que l'utilisateur précédemment créé pour tester votre fonction :<br>
affichez Bonjour, suivi de son nom et de son prénom. Pour cela, créez une méthode sayHello dans<br> 
la classe User permettant de retourner la phrase à afficher.<br>

Indice<br>
N'oubliez pas d'inclure la classe User là où vous en avez besoin.<br><br>

Solution<br>
Fichier User.php :<br>

<?php<br>
class User<br>
{<br>
    private string $id;<br>
    private string $email;<br>
    private string $password;<br>
    private string $firstName;<br>
    private string $lastName;<br>
    private string $department;<br>
    public function getPassword(): string<br>
    {<br>
        return $this->password;<br>
    }
    public function sayHello(): string<br>
    {
        return 'Bonjour '.$this->firstName.' '.$this->lastName;<br>
    }<br>
}<br><br>


Question:<br>
Ajoutez une méthode connect à votre UserManager. Cette méthode doit prendre en paramètres un login<br>
et un mot de passe et doit retourner le User correspondant. Si aucun utilisateur n'est trouvé,<br>
alors cette méthode doit lancer une exception.<br>

Connectez-vous ensuite en tant que l'utilisateur précédemment créé pour tester votre fonction :<br> 
affichez Bonjour, suivi de son nom et de son prénom. Pour cela, créez une méthode sayHello dans<br>
la classe User permettant de retourner la phrase à afficher.<br>

Indice<br>
N'oubliez pas d'inclure la classe User là où vous en avez besoin.<br><br>

Solution<br><br>
Fichier User.php :<br>

<?php<br>
class User<br>
{<br>
    private string $id;<br>
    private string $email;<br>
    private string $password;<br>
    private string $firstName;<br>
    private string $lastName;<br>
    private string $department;<br>
    public function getPassword(): string<br>
    {<br>
        return $this->password;<br>
    }<br>
    public function sayHello(): string<br>
    {<br>
        return 'Bonjour '.$this->firstName.' '.$this->lastName;<br>
    }<br>
}<br><br>

Fichier UserManager.php :<br>

<?php<br>
class UserManager<br>
{<br>
    private PDO $pdo;<br>
    public function __construct(PDO $pdo)<br>
    {<br>
        $this->pdo = $pdo;<br>
    }<br>
    public function subscribe(string $email, string $password, string $firstName, string $lastName, string $department): bool<br>
    {<br>
        if (strlen($password) < 8) {<br>
            return false;<br>
        }<br>
        $statement = $this->pdo->prepare('<br>
INSERT INTO users (id, email, password, firstName, lastName, department)<br>
VALUES (UUID(), :email, :password, :firstName, :lastName, :department)'<br>
        );<br>
        $statement->bindValue(':email', $email, PDO::PARAM_STR);<br>
        $statement->bindValue(':password', password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);<br>
        $statement->bindValue(':firstName', $firstName, PDO::PARAM_STR);<br>
        $statement->bindValue(':lastName', $lastName, PDO::PARAM_STR);<br>
        $statement->bindValue(':department', $department, PDO::PARAM_STR);<br>

          return $statement->execute();<br>
    }<br>
    public function connect(string $email, string $password): User <br>
    {<br>
        require_once 'User.php';<br>
        // On cherche un utilisateur ayant l'adresse e-mail correspondante<br>
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');<br>
        $statement->setFetchMode(PDO::FETCH_CLASS, 'User');<br>
        $statement->bindValue(':email', $email);<br>
        if ($statement->execute()) {<br>
            // Il y a une contrainte d'unicité sur l'e-mail : il n'est donc pas utile de faire une boucle<br>
            $user = $statement->fetch();<br>
            // Il faut vérifier que fetch n'a pas retourné false avant de vérifier le mot de passe<br>
            if ($user !== false && password_verify($password, $user->getPassword())) {<br>
                return $user;<br>
            }<br>
        }<br>
        throw new Exception('Identifiants invalides');<br>
    }<br>
}<br><br>


Fichier index.php :<br>

<?php<br>
require_once 'UserManager.php';<br>
$pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '');<br>
$manager = new UserManager($pdo);<br>
$user = $manager->connect('john@doe.com', 'p4$$w0rd');<br>
echo $user->sayHello();<br><br>


Question:<br>
Le restaurant souhaite également proposer à ses clients un service de plats en livraison.<br>
Cependant, le restaurant n'est pas capable de livrer dans toute la France, donc cette fonctionnalité<br>
doit être limitée aux départements 75, 94, 92 et 93.<br>

Modifiez votre code de manière à ce que seuls les clients de ces départements aient le droit d'accéder<br> 
à la page du service de livraison.<br>

Indice<br>
Le but de l'exercice est de créer un rôle (ROLE_DELIVERABLE, par exemple) et de le déterminer<br> 
au moment de la connexion.<br>

Indice<br>
La fonction in_array peut également être utile pour gérer les départements.<br><br>

Solution<br>
Fichier User.php:<br>

<?php<br>
class User<br>
{
    private string $id;<br>
    private string $email;<br>
    private string $password;<br>
    private string $firstName;<br>
    private string $lastName;<br>
    private string $department;<br>
    private array $roles = [];<br>
    public function getPassword(): string<br>
    {<br>
        return $this->password;<br>
    }<br>
    public function getDepartment(): string<br>
    {<br>
        return $this->department;<br>
    }<br>
    public function sayHello(): string<br>
    {<br>
        return 'Bonjour '.$this->firstName.' '.$this->lastName;<br>
    }<br>
    public function addRole(string $role): void<br>
    {<br>
        $this->roles[] = $role;<br>
    }<br>
    public function getRoles(): array<br>
    {<br>
        return $this->roles;<br>
    }<br>
}<br><br>

Fichier UserManager.php :<br>

<?php<br>
class UserManager<br>
{<br>
    private PDO $pdo;<br>
    public function __construct(PDO $pdo)<br>
    {<br>
        $this->pdo = $pdo;<br>
    }<br>
    public function subscribe(string $email, string $password, string $firstName, string $lastName, string $department): bool<br>
    {<br>
        if (strlen($password) < 8) {<br>
            // Si le mot de passe n'est pas au bon format, on retourne false<br>
            return false;<br>
        }<br>
        $statement = $this->pdo->prepare('<br>
INSERT INTO users (id, email, password, firstName, lastName, department)<br>
VALUES (UUID(), :email, :password, :firstName, :lastName, :department)'<br>
        );<br>
        $statement->bindValue(':email', $email, PDO::PARAM_STR);<br>
        $statement->bindValue(':password', password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);<br>
        $statement->bindValue(':firstName', $firstName, PDO::PARAM_STR);<br>
        $statement->bindValue(':lastName', $lastName, PDO::PARAM_STR);<br>
        $statement->bindValue(':department', $department, PDO::PARAM_STR);<br>
        return $statement->execute();<br>
    }<br>
    public function connect(string $email, string $password): User<br>
    {<br>
        require_once 'User.php';<br>
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');<br>
        $statement->setFetchMode(PDO::FETCH_CLASS, 'User');<br>
        $statement->bindValue(':email', $email);<br>
        if ($statement->execute()) {<br>
            $user = $statement->fetch();<br>
            if ($user !== false && password_verify($password, $user->getPassword())) {<br>
                // On vérifie si le département de l'utilisateur est dans la liste des départements
                 autorisés<br>
                if (in_array($user->getDepartment(), ['75', '94', '92', '93'])) {<br>
                    $user->addRole('ROLE_DELIVERABLE');<br>
                }<br>
                return $user;<br>
            }<br>
        }<br>
        throw new Exception('Identifiants invalides');<br>
    }<br>
}<br><br>

Fichier index.php :<br>

<?php<br>
require_once 'UserManager.php';<br>
$pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '');<br>
$manager = new UserManager($pdo);<br>
$user = $manager->connect('john@doe.com', 'p4$$w0rd');<br>
if (!in_array('ROLE_DELIVERABLE', $user->getRoles())) {<br>
    throw new Exception('Accès interdit');<br>
}<br>