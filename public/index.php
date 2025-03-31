<?php

// ----------------------------------------------------
// POINT D'ENTRÉE PRINCIPAL DU SITE CINE-HURLANT
// ----------------------------------------------------
//
// Peu importe ce que l'utilisateur tape dans l'URL, 
// c’est TOUJOURS ce fichier qui est exécuté en premier.
//
// Pourquoi ? Parce que le fichier .htaccess dans /public redirige
// toutes les requêtes ici (c’est le comportement classique en MVC).
//
// Mon but ici : 
// - Préparer l’environnement de travail
// - Et déléguer à un routeur la responsabilité de 
//   comprendre quelle page afficher.
//
// C’est le fichier chef d’orchestre du projet.
// ----------------------------------------------------

// 1. J’active les sessions PHP
// Obligatoire si je veux utiliser $_SESSION (pour stocker l’utilisateur connecté, etc.)
session_start(); 

// 2. Je définis une constante ROOT
// Elle contient le chemin absolu du dossier principal du projet (pas celui de /public !)
// Très pratique pour inclure des fichiers propres depuis n’importe où
define('ROOT', dirname(__DIR__));

// Chargement de l'autoloader Composer
require_once ROOT . '/vendor/autoload.php';

// 3. Connexion à la base de données + chargement du fichier .env
// Le fichier connexion.php va : 
// - charger les variables d’environnement (host, utilisateur, mot de passe...)
// - établir une connexion PDO et la stocker dans $conn
require_once ROOT . '/config/connexion.php';


// ------------------------------------------------------------------
// 4. INIT AUTOMATIQUE DE DONNÉES ESSENTIELLES EN BASE (types + genres)
// ------------------------------------------------------------------
// Objectif : éviter que l’application plante si on a oublié d’insérer 
// les types (film, bd) ou les genres (science-fiction, western, etc.)
// Ce code s’exécute UNE seule fois (au chargement du site) 
// uniquement si les tables sont vides.
try {
    // Vérifie si la table 'type' est vide (0 ligne ?)
    $stmt = $conn->query("SELECT COUNT(*) FROM type");
    if ($stmt->fetchColumn() == 0) {
        // Si vide, je l’initialise avec 2 entrées indispensables
        $conn->exec("INSERT INTO type (id_type, nom) VALUES 
            (1, 'film'), 
            (2, 'bd')");
    }

    // Même chose pour la table 'genre'
    $stmt = $conn->query("SELECT COUNT(*) FROM genre");
    if ($stmt->fetchColumn() == 0) {
        // Insertion des 4 genres principaux
        $conn->exec("INSERT INTO genre (id_genre, nom) VALUES 
            (1, 'Science-fiction'), 
            (2, 'Fantastique'), 
            (3, 'Western'), 
            (4, 'Cyberpunk')");
    }

} catch (PDOException $e) {
    // Si une erreur survient pendant l’insertion, j’affiche le message
    echo "Erreur lors de l’initialisation des types/genres : " . $e->getMessage();
    exit; // Je stoppe tout le programme pour éviter d’aller plus loin avec une base instable
}


// ----------------------------------------------------
// 5. ROUTAGE
// ----------------------------------------------------
//
// Maintenant que tout est prêt, je passe le relais au routeur personnalisé.
// Son rôle : 
// - Lire l’URL (ex: /redacteur/ajouterOeuvre)
// - Trouver quel contrôleur il faut charger
// - Appeler la bonne méthode
//
// Par exemple, l’URL /public/redacteur/ajouterOeuvre va appeler :
// -> RedacteurController.php
// -> méthode ajouterOeuvre()
// ----------------------------------------------------
use App\Core\Router;

$router = new Router();        // J’instancie mon routeur
$router->handleRequest();      // Et je lui demande de traiter la requête

?>
