<?php
// app/Models/Commentaire.php

namespace App\Models;

use PDO;
use App\Core\BaseModel;

class Commentaire extends BaseModel
{
    // J’ajoute un commentaire lié à un article et un utilisateur
    // Si tout se passe bien, je retourne l'ID du commentaire inséré
    public function add(string $contenu, int $id_article, int $id_utilisateur): int|false
    {
        $sql = "INSERT INTO commentaire (contenu, id_article, id_utilisateur)
                VALUES (:contenu, :id_article, :id_utilisateur)";
        
        $stmt = $this->safeExecute($sql, [
            ':contenu' => $contenu,
            ':id_article' => $id_article,
            ':id_utilisateur' => $id_utilisateur
        ]);

        return $stmt ? (int) $this->db->lastInsertId() : false;
    }

    // Je récupère tous les commentaires validés pour un article donné
    public function getByArticle(int $id_article): array
    {
        $sql = "SELECT commentaire.*, utilisateur.nom AS auteur
                FROM commentaire
                JOIN utilisateur ON commentaire.id_utilisateur = utilisateur.id_utilisateur
                WHERE commentaire.id_article = :id_article AND valider = 1
                ORDER BY date_redaction ASC";

        $stmt = $this->safeExecute($sql, [':id_article' => $id_article]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // Je récupère un commentaire précis, s’il est validé
    public function getById(int $id_commentaire): array|false
    {
        $sql = "SELECT commentaire.*, utilisateur.nom AS auteur
                FROM commentaire
                JOIN utilisateur ON commentaire.id_utilisateur = utilisateur.id_utilisateur
                WHERE commentaire.id_commentaire = :id_commentaire AND valider = 1";

        $stmt = $this->safeExecute($sql, [':id_commentaire' => $id_commentaire]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    // Je récupère tous les commentaires du site (pour l’admin par exemple)
    public function getAll(): array
    {
        $sql = "SELECT commentaire.*, utilisateur.nom AS auteur, article.titre AS article
                FROM commentaire
                JOIN utilisateur ON commentaire.id_utilisateur = utilisateur.id_utilisateur
                JOIN article ON commentaire.id_article = article.id_article
                WHERE valider = 1
                ORDER BY date_redaction DESC";

        $stmt = $this->safeQuery($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // Je supprime un commentaire en base (accessible si droits suffisants côté contrôleur)
    public function deleteById(int $id_commentaire): bool
    {
        $sql = "DELETE FROM commentaire WHERE id_commentaire = :id";
        $stmt = $this->safeExecute($sql, [':id' => $id_commentaire]);
        return (bool) $stmt;
    }

    // Je modifie un commentaire (mise à jour + date actualisée automatiquement)
    public function updateContenu(int $id_commentaire, string $nouveau_contenu): bool
    {
        $sql = "UPDATE commentaire
                SET contenu = :contenu, date_redaction = NOW()
                WHERE id_commentaire = :id";

        $stmt = $this->safeExecute($sql, [
            ':contenu' => $nouveau_contenu,
            ':id' => $id_commentaire
        ]);

        return (bool) $stmt;
    }
}
