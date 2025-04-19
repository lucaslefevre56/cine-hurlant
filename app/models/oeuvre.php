<?php
// app/Models/Oeuvre.php

namespace App\Models;

use PDO;
use App\Core\BaseModel;

class Oeuvre extends BaseModel
{
    // J’ajoute une œuvre et je lie les genres sélectionnés
    public function add(
        string $titre,
        string $auteur,
        int $id_type,
        int $annee,
        string $analyse,
        string $media,
        string $video_url,
        int $id_utilisateur,
        array $genres = []
    ): bool {
        $sql = "INSERT INTO oeuvre (titre, auteur, annee, media, video_url, analyse, id_type, id_utilisateur)
                VALUES (:titre, :auteur, :annee, :media, :video_url, :analyse, :id_type, :id_utilisateur)";

        $stmt = $this->safeExecute($sql, [
            ':titre' => $titre,
            ':auteur' => $auteur,
            ':annee' => $annee,
            ':media' => $media,
            ':video_url' => $video_url,
            ':analyse' => $analyse,
            ':id_type' => $id_type,
            ':id_utilisateur' => $id_utilisateur
        ]);

        if ($stmt) {
            // Je récupère l’ID de l’œuvre pour lier les genres
            $id_oeuvre = $this->db->lastInsertId();

            // J’insère chaque genre dans la table de liaison
            foreach ($genres as $id_genre) {
                $sqlGenre = "INSERT INTO appartenir (id_oeuvre, id_genre) VALUES (:id_oeuvre, :id_genre)";
                $ok = $this->safeExecute($sqlGenre, [
                    ':id_oeuvre' => $id_oeuvre,
                    ':id_genre' => (int)$id_genre
                ]);

                if (!$ok) return false;
            }

            return true;
        }

        return false;
    }

    // Récupère toutes les œuvres, triées par titre, avec leur type
    public function getAll(): array
    {
        $sql = "SELECT oeuvre.*, type.nom
                FROM oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                ORDER BY oeuvre.titre ASC";

        $stmt = $this->safeQuery($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère une œuvre spécifique par son ID
    public function getById(int $id): array|false
    {
        $sql = "SELECT oeuvre.*, type.nom 
                FROM oeuvre 
                JOIN type ON oeuvre.id_type = type.id_type
                WHERE oeuvre.id_oeuvre = :id";

        $stmt = $this->safeExecute($sql, [':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Je récupère les genres associés à une œuvre (pour affichage ou modification)
    public function getGenresByOeuvre(int $id_oeuvre): array
    {
        $sql = "SELECT genre.nom 
                FROM appartenir 
                JOIN genre ON appartenir.id_genre = genre.id_genre 
                WHERE appartenir.id_oeuvre = :id";

        $stmt = $this->safeExecute($sql, [':id' => $id_oeuvre]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Pagination simple des œuvres avec leur type
    public function getPaginated(int $limit, int $offset): array
    {
        $sql = "SELECT oeuvre.*, type.nom
                FROM oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                ORDER BY oeuvre.titre ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Je récupère les œuvres paginées selon leur type (film ou bd)
    public function getPaginatedByType(string $type, int $limit, int $offset): array
    {
        $sql = "SELECT oeuvre.*, type.nom
                FROM oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                WHERE LOWER(type.nom) = :type
                ORDER BY oeuvre.titre ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':type', strtolower($type), PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Je compte le nombre total d’œuvres d’un type donné (film ou bd)
    public function countByType(string $type): int
    {
        $sql = "SELECT COUNT(*) 
                FROM oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                WHERE LOWER(type.nom) = :type";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':type' => strtolower($type)]);
        return (int) $stmt->fetchColumn();
    }

    // Nombre total d’œuvres pour la pagination
    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) FROM oeuvre";
        $stmt = $this->safeQuery($sql);
        return (int) $stmt->fetchColumn();
    }

    // Suppression d'une œuvre + suppression du fichier image s’il existe
    public function deleteById($id): bool
    {
        $stmt = $this->safeExecute("SELECT media FROM oeuvre WHERE id_oeuvre = :id", [':id' => $id]);
        $oeuvre = $stmt->fetch(PDO::FETCH_ASSOC);

        // Je supprime l’image locale uniquement si ce n’est pas une URL
        if ($oeuvre && !empty($oeuvre['media']) && !filter_var($oeuvre['media'], FILTER_VALIDATE_URL)) {
            $chemin = ROOT . '/public/upload/' . $oeuvre['media'];
            if (file_exists($chemin)) unlink($chemin);
        }

        // Puis je supprime l’œuvre de la base
        return $this->safeExecute("DELETE FROM oeuvre WHERE id_oeuvre = :id", [':id' => $id]) !== false;
    }

    // Mise à jour des infos d’une œuvre (hors genres, gérés ailleurs)
    public function update(
        int $id_oeuvre,
        string $titre,
        string $auteur,
        int $annee,
        string $media,
        string $video_url,
        string $analyse,
        int $id_type
    ): bool {
        $sql = "UPDATE oeuvre SET titre = ?, auteur = ?, annee = ?, media = ?, video_url = ?, analyse = ?, id_type = ? WHERE id_oeuvre = ?";
        return $this->safeExecute($sql, [$titre, $auteur, $annee, $media, $video_url, $analyse, $id_type, $id_oeuvre]) !== false;
    }

    // Je récupère toutes les œuvres créées par un utilisateur
    public function getByAuteur(int $id_utilisateur): array
    {
        $sql = "SELECT oeuvre.*, type.nom
                FROM oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                WHERE oeuvre.id_utilisateur = :id_utilisateur
                ORDER BY oeuvre.titre ASC";

        $stmt = $this->safeExecute($sql, [':id_utilisateur' => $id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recherche dans les titres ou auteurs (moteur de recherche)
    public static function searchByTitleOrAuthor(string $query): array
    {
        $db = \App\Core\Database::getInstance();
        $sql = "SELECT oeuvre.*, type.nom AS type
                FROM oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                WHERE oeuvre.titre LIKE :query OR oeuvre.auteur LIKE :query
                ORDER BY titre ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':query' => '%' . $query . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Suggestions aléatoires pour la page d’accueil
    public function getRandom(int $nb): array
    {
        $sql = "SELECT oeuvre.*, type.nom AS type
            FROM oeuvre
            JOIN type ON oeuvre.id_type = type.id_type
            ORDER BY RAND() LIMIT :nb";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nb', $nb, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
