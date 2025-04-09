<?php
// app/Core/Database.php
namespace App\Core;

use PDO;
use Exception;
use Dotenv\Dotenv;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $root = dirname(__DIR__, 2);
            $dotenv = Dotenv::createImmutable($root);
            $dotenv->load();

            try {
                self::$instance = new PDO(
                    'mysql:host=' . $_ENV['DB_HOST'] .
                    ';dbname=' . $_ENV['DB_NAME'] .
                    ';charset=utf8',
                    $_ENV['DB_USER'],
                    $_ENV['DB_PASS']
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (Exception $e) {
                // Ici on appelle la classe ErrorHandler
                ErrorHandler::renderError("Erreur de connexion à la base de données.");
            }
        }

        return self::$instance;
    }

    // On empêche l’instanciation directe et le clonage
    private function __construct() {}
    private function __clone() {}
}
