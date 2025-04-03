<?php
// app/Models/Article.php

// Nécessaire pour l'autoload PSR-4 avec Composer
namespace App\Models;

// J’importe la classe Database pour pouvoir l’utiliser dans mes méthodes
use App\Core\Database;

class Article
{
    /**
     * J’ajoute un nouvel article en base, avec un lien facultatif vers une ou plusieurs œuvres
     * et un champ pour l'upload d'image et d'URL vidéo
     */
    public function add($titre, $contenu, $image, $video_url, $id_utilisateur, $oeuvres = [])
    {
        // Je sécurise les champs facultatifs
        $image = empty($image) ? null : $image;
        $video_url = empty($video_url) ? null : $video_url;

        // Connexion BDD
        $db = Database::getInstance();

        // Étape 1 : insertion dans la table article
        $sql = "INSERT INTO article (titre, contenu, image, video_url, id_utilisateur)
            VALUES (:titre, :contenu, :image, :video_url, :id_utilisateur)";
        $stmt = $db->prepare($sql);

        $success = $stmt->execute([
            ':titre' => $titre,
            ':contenu' => $contenu,
            ':image' => $image,
            ':video_url' => $video_url,
            ':id_utilisateur' => (int) $id_utilisateur
        ]);

        if ($success) {
            // Récupération de l'ID de l'article
            $id_article = $db->lastInsertId();

            // Étape 2 : lier les œuvres dans la table "analyser"
            foreach ($oeuvres as $id_oeuvre) {
                $sqlOeuvre = "INSERT INTO analyser (id_article, id_oeuvre) VALUES (:id_article, :id_oeuvre)";
                $stmtOeuvre = $db->prepare($sqlOeuvre);
                $ok = $stmtOeuvre->execute([
                    ':id_article' => $id_article,
                    ':id_oeuvre' => (int) $id_oeuvre
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
     * Je récupère tous les articles avec leur auteur
     * → utilisé pour afficher la liste
     */
    public function getAll()
    {
        $db = Database::getInstance();

        $sql = "SELECT article.*, utilisateur.nom AS auteur
                FROM article
                JOIN utilisateur ON article.id_utilisateur = utilisateur.id_utilisateur
                ORDER BY article.date_redaction DESC";

        $stmt = $db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Je récupère un article par son ID avec son auteur
     * → utilisé pour afficher la fiche
     */
    public function getById($id)
    {
        $db = Database::getInstance();

        $sql = "SELECT article.*, utilisateur.nom AS auteur
                FROM article
                JOIN utilisateur ON article.id_utilisateur = utilisateur.id_utilisateur
                WHERE article.id_article = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Je récupère tous les articles publiés par un utilisateur
     */
    public function getByAuteur($id_utilisateur)
    {
        $db = Database::getInstance();

        $sql = "SELECT article.*, utilisateur.nom AS auteur
                FROM article
                JOIN utilisateur ON article.id_utilisateur = utilisateur.id_utilisateur
                WHERE article.id_utilisateur = :id_utilisateur
                ORDER BY article.date_redaction DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Je récupère toutes les œuvres liées à un article donné
     * → utilisé dans la fiche article pour afficher les œuvres analysées
     */
    public function getOeuvresByArticle($id_article)
    {
        $db = Database::getInstance();

        $sql = "SELECT oeuvre.id_oeuvre, oeuvre.titre, type.nom AS type
                FROM analyser
                JOIN oeuvre ON analyser.id_oeuvre = oeuvre.id_oeuvre
                JOIN type ON oeuvre.id_type = type.id_type
                WHERE analyser.id_article = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id_article]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

?>
