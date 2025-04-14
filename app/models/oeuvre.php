<?php
// app/Models/Oeuvre.php

namespace App\Models;

use PDO;
use App\Core\BaseModel;

class Oeuvre extends BaseModel
{
    /**
     * J’ajoute une œuvre avec ses genres associés
     */
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
            $id_oeuvre = $this->db->lastInsertId();

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

    /**
     * Je récupère toutes les œuvres avec leur type
     */
    public function getAll(): array
    {
        $sql = "SELECT oeuvre.*, type.nom
                FROM oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                ORDER BY oeuvre.titre ASC";

        $stmt = $this->safeQuery($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|false
    {
        $sql = "SELECT oeuvre.*, type.nom 
                FROM oeuvre 
                JOIN type ON oeuvre.id_type = type.id_type
                WHERE oeuvre.id_oeuvre = :id";

        $stmt = $this->safeExecute($sql, [':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getGenresByOeuvre(int $id_oeuvre): array
    {
        $sql = "SELECT genre.nom 
                FROM appartenir 
                JOIN genre ON appartenir.id_genre = genre.id_genre 
                WHERE appartenir.id_oeuvre = :id";

        $stmt = $this->safeExecute($sql, [':id' => $id_oeuvre]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

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

    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) FROM oeuvre";
        $stmt = $this->safeQuery($sql);
        return (int) $stmt->fetchColumn();
    }

    public function deleteById($id): bool
    {
        // Je récupère le nom de l’image pour la supprimer du disque
        $stmt = $this->safeExecute("SELECT media FROM oeuvre WHERE id_oeuvre = :id", [':id' => $id]);
        $oeuvre = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($oeuvre && !empty($oeuvre['media']) && !filter_var($oeuvre['media'], FILTER_VALIDATE_URL)) {
            $chemin = ROOT . '/public/upload/' . $oeuvre['media'];
            if (file_exists($chemin)) unlink($chemin);
        }

        // Je supprime ensuite l’œuvre
        return $this->safeExecute("DELETE FROM oeuvre WHERE id_oeuvre = :id", [':id' => $id]) !== false;
    }

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
}
