<?php
// app/Models/Utilisateur.php

namespace App\Models;

use PDO;
use App\Core\Database;

class Utilisateur
{
    /**
     * Récupère un utilisateur par son ID
     */
    public function getById(int $id): array|false
    {
        $db = Database::getInstance();

        $sql = "SELECT * FROM Utilisateur WHERE id_utilisateur = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un utilisateur actif par son email
     * → utilisé pour la connexion
     */
    public function getByEmail(string $email): array|false
    {
        $db = Database::getInstance();

        $sql = "SELECT * FROM utilisateur WHERE email = ? AND actif = 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute un nouvel utilisateur en base (mot de passe déjà hashé)
     */
    public function add(string $nom, string $email, string $hashedPassword): bool
    {
        $db = Database::getInstance();

        $sql = "INSERT INTO Utilisateur (nom, email, password) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);

        return $stmt->execute([$nom, $email, $hashedPassword]);
    }

    public function deleteById(int $id): bool
    {
        $db = Database::getInstance();

        $sql = "DELETE FROM Utilisateur WHERE id_utilisateur = ?";
        $stmt = $db->prepare($sql);

        return $stmt->execute([$id]);
    }

    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $db = Database::getInstance();

        $sql = "UPDATE Utilisateur SET password = ? WHERE id_utilisateur = ?";
        $stmt = $db->prepare($sql);

        return $stmt->execute([$hashedPassword, $id]);
    }


    // ----------------------------------------
    // RÔLES & DROITS
    // ----------------------------------------

    /**
     * Vérifie si un utilisateur a un rôle donné
     */
    public function hasRole(int $id, string $role): bool
    {
        $user = $this->getById($id);
        return $user && strtolower($user['role']) === strtolower($role);
    }

    public function isAdmin(int $id): bool
    {
        return $this->hasRole($id, 'admin');
    }

    public function isRedacteur(int $id): bool
    {
        $user = $this->getById($id);
        return $user && in_array($user['role'], ['redacteur', 'admin']);
    }

    public function isUtilisateur(int $id): bool
    {
        return $this->hasRole($id, 'utilisateur');
    }

    /**
     * Change le rôle d’un utilisateur
     */
    public function updateRole(int $id, string $newRole): bool
    {
        $db = Database::getInstance();

        $sql = "UPDATE Utilisateur SET role = ? WHERE id_utilisateur = ?";
        $stmt = $db->prepare($sql);

        return $stmt->execute([$newRole, $id]);
    }

    /**
     * Récupère tous les utilisateurs (id, nom, email, rôle)
     */
    public function getAll(): array
    {
        $db = Database::getInstance();

        $sql = "SELECT id_utilisateur, nom, email, role, actif FROM utilisateur ORDER BY nom ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function desactiver(int $id): bool
    {
        $db = Database::getInstance();

        $sql = "UPDATE utilisateur SET actif = 0 WHERE id_utilisateur = :id";

        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function reactiver(int $id): bool
    {
        $db = Database::getInstance();

        $sql = "UPDATE utilisateur SET actif = 1 WHERE id_utilisateur = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ----------------------------------------
    // COMMENTAIRES
    // ----------------------------------------

    public function getCommentaires(int $id_utilisateur): array
    {
        $db = Database::getInstance();

        $sql = "SELECT * FROM Commentaire WHERE id_utilisateur = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_utilisateur]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteCommentaire(int $id_commentaire): bool
    {
        $db = Database::getInstance();

        $sql = "DELETE FROM Commentaire WHERE id_commentaire = ?";
        $stmt = $db->prepare($sql);

        return $stmt->execute([$id_commentaire]);
    }

    // ----------------------------------------
    // PUBLICATIONS
    // ----------------------------------------

    public function getOeuvres(int $id_utilisateur): array
    {
        $db = Database::getInstance();

        $sql = "SELECT * FROM Oeuvre WHERE id_utilisateur = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_utilisateur]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticles(int $id_utilisateur): array
    {
        $db = Database::getInstance();

        $sql = "SELECT * FROM Article WHERE id_utilisateur = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_utilisateur]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
