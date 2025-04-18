<?php
// app/Core/Database.php

namespace App\Core;

use PDO;
use Exception;
use Dotenv\Dotenv;

class Database
{
    // Je stocke l'instance unique de PDO ici (c'est le principe du Singleton)
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        // Si l’instance n’existe pas encore, je la crée
        if (self::$instance === null) {

            // Je vais chercher les infos dans mon fichier .env (via Dotenv)
            $root = dirname(__DIR__, 2); // Je remonte à la racine du projet
            $dotenv = Dotenv::createImmutable($root);
            $dotenv->load(); // Les variables sont maintenant accessibles via $_ENV

            try {
                // Je construis la connexion PDO avec les infos du .env
                self::$instance = new PDO(
                    'mysql:host=' . $_ENV['DB_HOST'] .
                    ';dbname=' . $_ENV['DB_NAME'] .
                    ';charset=utf8',
                    $_ENV['DB_USER'],
                    $_ENV['DB_PASS']
                );

                // Je configure PDO pour qu'il me renvoie une vraie exception en cas d’erreur SQL
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (Exception $e) {
                // Si jamais la connexion échoue, je n'affiche rien de technique : je redirige vers mon gestionnaire d’erreurs
                ErrorHandler::renderError("Erreur de connexion à la base de données.");
            }
        }

        // Et je retourne toujours la même instance
        return self::$instance;
    }

    // Je bloque toute tentative de création manuelle de l’objet (Singleton = 1 seule instance autorisée)
    private function __construct() {}
    private function __clone() {}
}
