<?php
// app/Core/BaseModel.php

namespace App\Core;

use PDO;
use PDOStatement;
use Exception;

// Tous mes modèles vont hériter de cette classe de base
// Je centralise ici la connexion et la sécurité des requêtes SQL
abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        // Je récupère une seule fois l’instance PDO (partagée dans tout le projet)
        $this->db = Database::getInstance();
    }

    // J’utilise cette méthode pour exécuter une requête préparée avec des paramètres
    // Elle me protège contre les injections SQL et les erreurs
    protected function safeExecute(string $sql, array $params = []): PDOStatement|false
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (Exception $e) {
            // En cas d’erreur, je délègue à mon gestionnaire pour afficher une erreur propre
            ErrorHandler::renderError("Erreur lors de l'exécution SQL : " . $e->getMessage());
            return false;
        }
    }

    // Variante plus simple : j’utilise ça quand j’ai une requête toute bête sans paramètres
    protected function safeQuery(string $sql): PDOStatement|false
    {
        try {
            return $this->db->query($sql);
        } catch (Exception $e) {
            ErrorHandler::renderError("Erreur lors de la requête : " . $e->getMessage());
            return false;
        }
    }
}
