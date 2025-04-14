<?php
// app/Models/Article.php

namespace App\Models;

use PDO;
use App\Core\BaseModel;

class Article extends BaseModel
{
    /**
     * J’ajoute un nouvel article en base, avec un lien facultatif vers une ou plusieurs œuvres
     * et un champ pour l'upload d'image et d'URL vidéo
     */
    public function add(string $titre, string $contenu, ?string $image, ?string $video_url, int $id_utilisateur, array $oeuvres = []): bool
    {
        $sql = "INSERT INTO article (titre, contenu, image, video_url, id_utilisateur)
                VALUES (:titre, :contenu, :image, :video_url, :id_utilisateur)";

        $stmt = $this->safeExecute($sql, [
            ':titre' => $titre,
            ':contenu' => $contenu,
            ':image' => $image ?: null,
            ':video_url' => $video_url ?: null,
            ':id_utilisateur' => $id_utilisateur
        ]);

        if ($stmt) {
            $id_article = $this->db->lastInsertId();

            foreach ($oeuvres as $id_oeuvre) {
                $ok = $this->safeExecute(
                    "INSERT INTO analyser (id_article, id_oeuvre) VALUES (:id_article, :id_oeuvre)",
                    [':id_article' => $id_article, ':id_oeuvre' => (int)$id_oeuvre]
                );

                if (!$ok) return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Je récupère tous les articles avec leur auteur
     * → utilisé pour afficher la liste
     */
    public function getAll(): array
    {
        $sql = "SELECT article.*, utilisateur.nom AS auteur
                FROM article
                JOIN utilisateur ON article.id_utilisateur = utilisateur.id_utilisateur
                ORDER BY article.date_redaction DESC";

        $stmt = $this->safeQuery($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Je récupère un article par son ID avec son auteur
     * → utilisé pour afficher la fiche
     */
    public function getById(int $id): array|false
    {
        $sql = "SELECT article.*, utilisateur.nom AS auteur
                FROM article
                JOIN utilisateur ON article.id_utilisateur = utilisateur.id_utilisateur
                WHERE article.id_article = :id";

        $stmt = $this->safeExecute($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Je récupère tous les articles publiés par un utilisateur
     */
    public function getByAuteur(int $id_utilisateur): array
    {
        $sql = "SELECT article.*, utilisateur.nom AS auteur
                FROM article
                JOIN utilisateur ON article.id_utilisateur = utilisateur.id_utilisateur
                WHERE article.id_utilisateur = :id_utilisateur
                ORDER BY article.date_redaction DESC";

        $stmt = $this->safeExecute($sql, [':id_utilisateur' => $id_utilisateur]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Je récupère toutes les œuvres liées à un article donné
     * → utilisé dans la fiche article pour afficher les œuvres analysées
     */
    public function getOeuvresByArticle(int $id_article): array
    {
        $sql = "SELECT oeuvre.id_oeuvre, oeuvre.titre, type.nom AS type
                FROM analyser
                JOIN oeuvre ON analyser.id_oeuvre = oeuvre.id_oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                WHERE analyser.id_article = :id";

        $stmt = $this->safeExecute($sql, [':id' => $id_article]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Je récupère une portion paginée des articles
     * → utilisée pour afficher la liste avec pagination
     */
    public function getPaginated(int $limit, int $offset): array
    {
        $sql = "SELECT article.*, utilisateur.nom AS auteur
                FROM article
                JOIN utilisateur ON article.id_utilisateur = utilisateur.id_utilisateur
                ORDER BY date_redaction DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Je compte combien d’articles existent en base
     * → utilisé pour calculer le nombre total de pages
     */
    public function countAll(): int
    {
        $stmt = $this->safeQuery("SELECT COUNT(*) FROM article");
        return (int) ($stmt ? $stmt->fetchColumn() : 0);
    }

    /**
     * Je supprime un article et son image locale
     */
    public function deleteById(int $id): bool
    {
        $stmt = $this->safeExecute("SELECT image FROM article WHERE id_article = :id", [':id' => $id]);
        $image = $stmt ? $stmt->fetchColumn() : null;

        $this->safeExecute("DELETE FROM commentaire WHERE id_article = :id", [':id' => $id]);
        $success = $this->safeExecute("DELETE FROM article WHERE id_article = :id", [':id' => $id]);

        if ($success && $image && !filter_var($image, FILTER_VALIDATE_URL)) {
            $chemin = ROOT . '/public/upload/' . $image;
            if (file_exists($chemin)) unlink($chemin);
        }

        return (bool) $success;
    }

    /**
     * Je mets à jour un article existant
     */
    public function update(int $id_article, string $titre, string $contenu, ?string $image, ?string $video_url): bool
    {
        $sql = "UPDATE article SET titre = ?, contenu = ?, image = ?, video_url = ? WHERE id_article = ?";
        $stmt = $this->safeExecute($sql, [$titre, $contenu, $image, $video_url, $id_article]);

        return (bool) $stmt;
    }

    /**
     * Recherche un article par titre ou nom d’auteur
     */
    public static function searchByTitleOrAuthor(string $query): array
    {
        $db = \App\Core\Database::getInstance();

        $sql = "SELECT article.*, utilisateur.nom AS auteur
                FROM article
                JOIN utilisateur ON article.id_utilisateur = utilisateur.id_utilisateur
                WHERE article.titre LIKE :query OR utilisateur.nom LIKE :query
                ORDER BY date_redaction DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':query' => '%' . $query . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
