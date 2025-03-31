<?php
// app/models/oeuvre.php

class Oeuvre
{
    private $conn;

    // Quand je crée un objet Oeuvre, je lui passe la connexion à la base de données
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Je crée une nouvelle œuvre dans la BDD, avec tous ses détails (titre, auteur, type, etc.)
     * et je lui associe un ou plusieurs genres sélectionnés dans le formulaire
     */
    public function add($titre, $auteur, $id_type, $annee, $analyse, $media, $id_utilisateur, $genres = [])
    {
        // Étape 1 : j’insère les infos principales dans la table "oeuvre"
        $sql = "INSERT INTO oeuvre (titre, auteur, annee, media, analyse, id_type, id_utilisateur)
                VALUES (:titre, :auteur, :annee, :media, :analyse, :id_type, :id_utilisateur)";

        // Je prépare la requête pour sécuriser les données
        $stmt = $this->conn->prepare($sql);

        // Je l’exécute avec les données du formulaire
        $success = $stmt->execute([
            ':titre' => htmlspecialchars($titre),
            ':auteur' => htmlspecialchars($auteur),
            ':annee' => (int)$annee,
            ':media' => htmlspecialchars($media),
            ':analyse' => htmlspecialchars($analyse),
            ':id_type' => (int)$id_type,
            ':id_utilisateur' => (int)$id_utilisateur
        ]);

        // Si l’insertion a bien fonctionné, je continue
        if ($success) {
            // Je récupère l’ID de l’œuvre qu’on vient d’insérer
            $id_oeuvre = $this->conn->lastInsertId();

            // Étape 2 : j’insère chaque genre sélectionné dans la table "appartenir"
            foreach ($genres as $id_genre) {
                $sqlGenre = "INSERT INTO appartenir (id_oeuvre, id_genre) VALUES (:id_oeuvre, :id_genre)";
                $stmtGenre = $this->conn->prepare($sqlGenre);

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
        $sql = "SELECT oeuvre.*, type.nom
                FROM oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                ORDER BY oeuvre.titre ASC";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Je récupère une seule œuvre précise grâce à son ID
     * → utilisé pour afficher la fiche d’une œuvre
     */
    public function getById($id)
    {
        $sql = "SELECT oeuvre.*, type.nom 
                FROM oeuvre 
                JOIN type ON oeuvre.id_type = type.id_type
                WHERE oeuvre.id_oeuvre = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Je récupère tous les genres liés à une œuvre (par son ID)
     * → permet d’afficher "Science-fiction, Western" dans la vue
     */
    public function getGenresByOeuvre($id_oeuvre)
    {
        $sql = "SELECT genre.nom 
                FROM appartenir 
                JOIN genre ON appartenir.id_genre = genre.id_genre 
                WHERE appartenir.id_oeuvre = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id_oeuvre]);

        // Je renvoie juste la liste des noms (pas un tableau complet)
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
