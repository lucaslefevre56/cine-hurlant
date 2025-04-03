<?php
// app/Models/Oeuvre.php

// Je définis le namespace pour l’autoload PSR-4
namespace App\Models;

// J’importe la classe PDO (pour fetchAll, etc.) et ma classe Database (singleton)
use PDO;
use App\Core\Database;

class Oeuvre
{
    /**
     * Je crée une nouvelle œuvre dans la BDD, avec tous ses détails (titre, auteur, type, etc.)
     * et je lui associe un ou plusieurs genres sélectionnés dans le formulaire
     */
    public function add($titre, $auteur, $id_type, $annee, $analyse, $media, $video_url, $id_utilisateur, $genres = [])
    {
        // Je récupère la connexion PDO via le singleton
        $db = Database::getInstance();

        // Étape 1 : j’insère les infos principales dans la table "oeuvre"
        $sql = "INSERT INTO oeuvre (titre, auteur, annee, media, video_url, analyse, id_type, id_utilisateur)
                VALUES (:titre, :auteur, :annee, :media, :video_url, :analyse, :id_type, :id_utilisateur)";

        // Je prépare la requête pour sécuriser les données
        $stmt = $db->prepare($sql);

        // Je l’exécute avec les données du formulaire
        $success = $stmt->execute([
            ':titre' => $titre,
            ':auteur' => $auteur,
            ':annee' => (int)$annee,
            ':media' => $media,
            ':video_url' => $video_url,  // Ajout de l'URL vidéo
            ':analyse' => $analyse,
            ':id_type' => (int)$id_type,
            ':id_utilisateur' => (int)$id_utilisateur
        ]);

        // Si l’insertion a bien fonctionné, je continue
        if ($success) {
            // Je récupère l’ID de l’œuvre qu’on vient d’insérer
            $id_oeuvre = $db->lastInsertId();

            // Étape 2 : j’insère chaque genre sélectionné dans la table "appartenir"
            foreach ($genres as $id_genre) {
                $sqlGenre = "INSERT INTO appartenir (id_oeuvre, id_genre) VALUES (:id_oeuvre, :id_genre)";
                $stmtGenre = $db->prepare($sqlGenre);

                $ok = $stmtGenre->execute([
                    ':id_oeuvre' => $id_oeuvre,
                    ':id_genre' => (int)$id_genre
                ]);

                // Si un des liens échoue, je retourne false immédiatement
                if (!$ok) {
                    return false;
                }
            }

            // Si tout est bon, je retourne true
            return true;
        }

        // Si l’insertion de base a échoué, je retourne false
        return false;
    }

    /**
     * Je récupère toutes les œuvres de la base avec leur type associé (film ou BD)
     * → utilisé pour afficher la liste complète des œuvres
     */
    public function getAll()
    {
        // Je récupère la connexion
        $db = Database::getInstance();

        // Requête SQL avec jointure vers "type" (nom du type)
        $sql = "SELECT oeuvre.*, type.nom
                FROM oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                ORDER BY oeuvre.titre ASC";

        // J’exécute la requête et je renvoie le tableau associatif des résultats
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Je récupère une seule œuvre précise grâce à son ID
     * → utilisé pour afficher la fiche d’une œuvre
     */
    public function getById($id)
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
     * Je récupère tous les genres liés à une œuvre (par son ID)
     * → permet d’afficher "Science-fiction, Western" dans la vue
     */
    public function getGenresByOeuvre($id_oeuvre)
    {
        $db = Database::getInstance();

        $sql = "SELECT genre.nom 
                FROM appartenir 
                JOIN genre ON appartenir.id_genre = genre.id_genre 
                WHERE appartenir.id_oeuvre = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id_oeuvre]);

        // Je renvoie juste la liste des noms (pas un tableau complet)
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
