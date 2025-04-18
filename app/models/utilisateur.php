<?php
// app/Models/Utilisateur.php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class Utilisateur extends BaseModel
{
    // Je récupère un utilisateur complet à partir de son ID
    public function getById(int $id): array|false
    {
        $sql = "SELECT * FROM utilisateur WHERE id_utilisateur = ?";
        $stmt = $this->safeExecute($sql, [$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Même principe, mais avec l'email (utile pour la connexion)
    public function getByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM utilisateur WHERE email = ?";
        $stmt = $this->safeExecute($sql, [$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // J’ajoute un utilisateur dans la base, en recevant déjà le mot de passe hashé
    public function add(string $nom, string $email, string $hashedPassword): bool
    {
        $sql = "INSERT INTO utilisateur (nom, email, password) VALUES (?, ?, ?)";
        return $this->safeExecute($sql, [$nom, $email, $hashedPassword]) !== false;
    }

    // Suppression physique (utilisée uniquement par l’admin normalement)
    public function deleteById(int $id): bool
    {
        $sql = "DELETE FROM utilisateur WHERE id_utilisateur = ?";
        return $this->safeExecute($sql, [$id]) !== false;
    }

    // Mise à jour du mot de passe (déjà hashé à ce stade)
    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $sql = "UPDATE utilisateur SET password = ? WHERE id_utilisateur = ?";
        return $this->safeExecute($sql, [$hashedPassword, $id]) !== false;
    }

    // Je vérifie si un utilisateur a un rôle précis (admin, redacteur, utilisateur…)
    public function hasRole(int $id, string $role): bool
    {
        $user = $this->getById($id);
        return $user && strtolower($user['role']) === strtolower($role);
    }

    // Vérification rapide : est-ce un admin ?
    public function isAdmin(int $id): bool
    {
        return $this->hasRole($id, 'admin');
    }

    // Est-ce un rédacteur OU un admin ?
    public function isRedacteur(int $id): bool
    {
        $user = $this->getById($id);
        return $user && in_array($user['role'], ['redacteur', 'admin']);
    }

    // Est-ce un utilisateur simple ?
    public function isUtilisateur(int $id): bool
    {
        return $this->hasRole($id, 'utilisateur');
    }

    // Changement de rôle (par l'admin uniquement normalement)
    public function updateRole(int $id, string $newRole): bool
    {
        $sql = "UPDATE utilisateur SET role = ? WHERE id_utilisateur = ?";
        return $this->safeExecute($sql, [$newRole, $id]) !== false;
    }

    // Je récupère tous les utilisateurs (pour la page admin)
    public function getAll(): array
    {
        $sql = "SELECT id_utilisateur, nom, email, role, actif FROM utilisateur ORDER BY nom ASC";
        $stmt = $this->safeQuery($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Je désactive un compte (désactivation logique)
    public function desactiver(int $id): bool
    {
        $sql = "UPDATE utilisateur SET actif = 0 WHERE id_utilisateur = ?";
        return $this->safeExecute($sql, [$id]) !== false;
    }

    // Et je peux aussi le réactiver
    public function reactiver(int $id): bool
    {
        $sql = "UPDATE utilisateur SET actif = 1 WHERE id_utilisateur = ?";
        return $this->safeExecute($sql, [$id]) !== false;
    }

    // Je récupère tous les commentaires postés par un utilisateur
    public function getCommentaires(int $id_utilisateur): array
    {
        $sql = "SELECT * FROM commentaire WHERE id_utilisateur = ?";
        $stmt = $this->safeExecute($sql, [$id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Permet à l’utilisateur (ou à un admin) de supprimer un commentaire
    public function deleteCommentaire(int $id_commentaire): bool
    {
        $sql = "DELETE FROM commentaire WHERE id_commentaire = ?";
        return $this->safeExecute($sql, [$id_commentaire]) !== false;
    }

    // Récupère toutes les œuvres ajoutées par un utilisateur (pour son panel)
    public function getOeuvres(int $id_utilisateur): array
    {
        $sql = "SELECT * FROM oeuvre WHERE id_utilisateur = ?";
        $stmt = $this->safeExecute($sql, [$id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Idem avec les articles
    public function getArticles(int $id_utilisateur): array
    {
        $sql = "SELECT * FROM article WHERE id_utilisateur = ?";
        $stmt = $this->safeExecute($sql, [$id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
