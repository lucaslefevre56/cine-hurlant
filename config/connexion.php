<?php
// config/connexion.php

// Si la constante ROOT n’a pas encore été définie (ex: si on exécute ce fichier seul), je la définis ici
if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__)); // Je pointe vers la racine du projet
}

// J’indique que je vais utiliser la classe Dotenv (qui est dans le namespace Dotenv)
use Dotenv\Dotenv;

// Je charge l’autoload de Composer pour pouvoir utiliser Dotenv et tout le reste
require_once ROOT . '/vendor/autoload.php';

// Je crée une instance de Dotenv, en lui disant d’aller chercher le fichier .env à la racine du projet
$dotenv = Dotenv::createImmutable(ROOT);
$dotenv->load();

try {
    // Je crée une connexion PDO à MySQL, avec les infos récupérées dans le .env
    $conn = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8',
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );

    // Je dis à PDO d’afficher les erreurs sous forme d’exceptions (pratique pour debugger)
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    // S’il y a une erreur de connexion, je l’affiche proprement et j’arrête tout
    die('Erreur : ' . $e->getMessage());
}

// Ce fichier me permet d’avoir une connexion à la base sécurisée, avec les infos planquées dans .env.
// Je peux inclure ce fichier n’importe où dans mon projet, et j’ai direct $conn.
// Grâce à ça, mon projet est propre, modulaire et prêt pour du pro
