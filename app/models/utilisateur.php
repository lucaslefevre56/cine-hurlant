<?php
// app/Models/Utilisateur.php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class Utilisateur extends BaseModel
{
    public function getById(int $id): array|false
    {
        $sql = "SELECT * FROM utilisateur WHERE id_utilisateur = ?";
        $stmt = $this->safeExecute($sql, [$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM utilisateur WHERE email = ?";
        $stmt = $this->safeExecute($sql, [$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add(string $nom, string $email, string $hashedPassword): bool
    {
        $sql = "INSERT INTO utilisateur (nom, email, password) VALUES (?, ?, ?)";
        return $this->safeExecute($sql, [$nom, $email, $hashedPassword]) !== false;
    }

    public function deleteById(int $id): bool
    {
        $sql = "DELETE FROM utilisateur WHERE id_utilisateur = ?";
        return $this->safeExecute($sql, [$id]) !== false;
    }

    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $sql = "UPDATE utilisateur SET password = ? WHERE id_utilisateur = ?";
        return $this->safeExecute($sql, [$hashedPassword, $id]) !== false;
    }

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

    public function updateRole(int $id, string $newRole): bool
    {
        $sql = "UPDATE utilisateur SET role = ? WHERE id_utilisateur = ?";
        return $this->safeExecute($sql, [$newRole, $id]) !== false;
    }

    public function getAll(): array
    {
        $sql = "SELECT id_utilisateur, nom, email, role, actif FROM utilisateur ORDER BY nom ASC";
        $stmt = $this->safeQuery($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function desactiver(int $id): bool
    {
        $sql = "UPDATE utilisateur SET actif = 0 WHERE id_utilisateur = ?";
        return $this->safeExecute($sql, [$id]) !== false;
    }

    public function reactiver(int $id): bool
    {
        $sql = "UPDATE utilisateur SET actif = 1 WHERE id_utilisateur = ?";
        return $this->safeExecute($sql, [$id]) !== false;
    }

    public function getCommentaires(int $id_utilisateur): array
    {
        $sql = "SELECT * FROM commentaire WHERE id_utilisateur = ?";
        $stmt = $this->safeExecute($sql, [$id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteCommentaire(int $id_commentaire): bool
    {
        $sql = "DELETE FROM commentaire WHERE id_commentaire = ?";
        return $this->safeExecute($sql, [$id_commentaire]) !== false;
    }

    public function getOeuvres(int $id_utilisateur): array
    {
        $sql = "SELECT * FROM oeuvre WHERE id_utilisateur = ?";
        $stmt = $this->safeExecute($sql, [$id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticles(int $id_utilisateur): array
    {
        $sql = "SELECT * FROM article WHERE id_utilisateur = ?";
        $stmt = $this->safeExecute($sql, [$id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
