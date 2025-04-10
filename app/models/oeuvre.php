<?php
// app/Models/Oeuvre.php

namespace App\Models;

use PDO;
use App\Core\Database;

class Oeuvre
{
    /**
     * Ajoute une nouvelle œuvre en base avec ses genres associés
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
        $db = Database::getInstance();

        $sql = "INSERT INTO oeuvre (titre, auteur, annee, media, video_url, analyse, id_type, id_utilisateur)
                VALUES (:titre, :auteur, :annee, :media, :video_url, :analyse, :id_type, :id_utilisateur)";

        $stmt = $db->prepare($sql);
        $success = $stmt->execute([
            ':titre' => $titre,
            ':auteur' => $auteur,
            ':annee' => $annee,
            ':media' => $media,
            ':video_url' => $video_url,
            ':analyse' => $analyse,
            ':id_type' => $id_type,
            ':id_utilisateur' => $id_utilisateur
        ]);

        if ($success) {
            $id_oeuvre = $db->lastInsertId();

            foreach ($genres as $id_genre) {
                $sqlGenre = "INSERT INTO appartenir (id_oeuvre, id_genre) VALUES (:id_oeuvre, :id_genre)";
                $stmtGenre = $db->prepare($sqlGenre);

                $ok = $stmtGenre->execute([
                    ':id_oeuvre' => $id_oeuvre,
                    ':id_genre' => (int)$id_genre
                ]);

                if (!$ok) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Récupère toutes les œuvres avec leur type
     */
    public function getAll(): array
    {
        $db = Database::getInstance();

        $sql = "SELECT oeuvre.*, type.nom
                FROM oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                ORDER BY oeuvre.titre ASC";

        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une œuvre par son ID
     */
    public function getById(int $id): array|false
    {
        $db = Database::getInstance();

        $sql = "SELECT oeuvre.*, type.nom 
                FROM oeuvre 
                JOIN type ON oeuvre.id_type = type.id_type
                WHERE oeuvre.id_oeuvre = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les genres associés à une œuvre
     */
    public function getGenresByOeuvre(int $id_oeuvre): array
    {
        $db = Database::getInstance();

        $sql = "SELECT genre.nom 
                FROM appartenir 
                JOIN genre ON appartenir.id_genre = genre.id_genre 
                WHERE appartenir.id_oeuvre = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id_oeuvre]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Récupère les œuvres avec pagination
     */
    public function getPaginated(int $limit, int $offset): array
    {
        $db = Database::getInstance();

        $sql = "SELECT oeuvre.*, type.nom
                FROM oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                ORDER BY oeuvre.titre ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre total d'œuvres
     */
    public function countAll(): int
    {
        $db = Database::getInstance();

        $sql = "SELECT COUNT(*) FROM oeuvre";
        return (int) $db->query($sql)->fetchColumn();
    }

    public function deleteById($id)
    {
        $db = \App\Core\Database::getInstance();

        $sql = "DELETE FROM oeuvre WHERE id_oeuvre = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function update(int $id_oeuvre, string $titre, string $auteur, int $annee, string $media, string $video_url, string $analyse, int $id_type): bool
    {
        $db = Database::getInstance();

        // Préparer la requête de mise à jour
        $sql = "UPDATE oeuvre SET titre = ?, auteur = ?, annee = ?, media = ?, video_url = ?, analyse = ?, id_type = ? WHERE id_oeuvre = ?";
        $stmt = $db->prepare($sql);

        // Exécuter la requête avec les valeurs passées
        return $stmt->execute([$titre, $auteur, $annee, $media, $video_url, $analyse, $id_type, $id_oeuvre]);
    }
}
