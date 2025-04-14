<?php
// app/Core/BaseModel.php

namespace App\Core;

use PDO;
use PDOStatement;
use Exception;

abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function safeExecute(string $sql, array $params = []): PDOStatement|false
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (Exception $e) {
            ErrorHandler::renderError("Erreur lors de l'exécution SQL : " . $e->getMessage());
            return false;
        }
    }

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
