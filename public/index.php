<?php 
// index.php est le point d'entrée de mon site
// Peu importe l'URL tapée, tout passera par ici (grâce au .htaccess)

// Je définis une constante ROOT qui pointe vers la racine du projet
define('ROOT', dirname(__DIR__));

// 1. Connexion à la BDD (et chargement du .env)
// Je vais chercher le fichier connexion.php qui s'occupe de charger Dotenv
// et de créer ma connexion PDO avec les variables du .env
require_once ROOT . '/config/connexion.php';

// 2. Lancer le routeur
// Je vais chercher la classe Router et je lui demande de gérer la requête
require_once ROOT . '/app/core/Router.php';

$router = new Router();
$router->handleRequest();

// Ce fichier index.php, c’est mon chef d’orchestre.
// Il lance la connexion à la base, puis il laisse le Router s’occuper de tout : 
// comprendre l’URL, appeler le bon contrôleur, la bonne méthode, avec les bons paramètres.
