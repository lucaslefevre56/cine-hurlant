<?php
// app/core/Database.php

// Déclaration du namespace pour l'autoload (PSR-4 via Composer)
namespace App\Core;

// J'importe les classes nécessaires
use PDO;
use Dotenv\Dotenv;
use Exception;

class Database
{
    // Stocke l'instance unique de la classe Database (singleton)
    private static $instance = null;

    // Contiendra la connexion PDO une fois qu'elle est créée
    private $connection;

    // Constructeur privé = empêche qu'on puisse faire "new Database()" depuis l'extérieur
    // Ce pattern permet de garantir qu'on ne crée qu'une seule connexion PDO dans tout le projet
    private function __construct()
    {
        // On remonte à la racine du projet pour charger le fichier .env
        // __DIR__ = app/core, donc dirname(__DIR__, 2) = racine du projet
        $root = dirname(__DIR__, 2);

        // On crée une instance de Dotenv et on charge les variables du .env (host, nom BDD, identifiants...)
        $dotenv = Dotenv::createImmutable($root);
        $dotenv->load();

        try {
            // Je crée une connexion PDO avec les infos extraites du .env
            $this->connection = new PDO(
                'mysql:host=' . $_ENV['DB_HOST'] . 
                ';dbname=' . $_ENV['DB_NAME'] . 
                ';charset=utf8',
                $_ENV['DB_USER'],
                $_ENV['DB_PASS']
            );

            // J'active l'affichage des erreurs sous forme d'exceptions (très utile pour le debug)
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (Exception $e) {
            // Si la connexion échoue, j'affiche une erreur claire et j'arrête le script
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }

    // Méthode publique et statique : c'est elle qu'on appelle dans tout le projet
    // Elle nous retourne la connexion PDO, sans qu'on ait à la recréer à chaque fois
    public static function getInstance()
    {
        // Si aucune instance n’a encore été créée, on en crée une
        if (self::$instance === null) {
            self::$instance = new self(); // appelle le constructeur privé
        }

        // On retourne la connexion PDO stockée dans l'instance
        return self::$instance->connection;
    }
}
