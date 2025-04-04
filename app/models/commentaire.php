<?php
// app/Models/Commentaire.php

// Je définis le namespace pour l’autoload PSR-4
namespace App\Models;

// J’importe la classe PDO et ma classe Database (singleton)
use PDO;
use App\Core\Database;

class Commentaire
{
    /**
     * J’ajoute un nouveau commentaire lié à un article
     * → utilisé lorsqu’un utilisateur commente un article via formulaire AJAX
     */
    public function add($contenu, $id_article, $id_utilisateur)
    {
        // Je récupère la connexion à la base de données
        $db = Database::getInstance();

        // Requête SQL d’insertion du commentaire
        $sql = "INSERT INTO commentaire (contenu, id_article, id_utilisateur)
                VALUES (:contenu, :id_article, :id_utilisateur)";

        // Je prépare la requête pour éviter les injections SQL
        $stmt = $db->prepare($sql);

        // J’exécute la requête avec les valeurs sécurisées
        $success = $stmt->execute([
            ':contenu' => $contenu,
            ':id_article' => (int) $id_article,
            ':id_utilisateur' => (int) $id_utilisateur
        ]);

        // Si tout s’est bien passé, je retourne l’ID du nouveau commentaire
        if ($success) {
            $id_commentaire = $db->lastInsertId();
            return $id_commentaire;
        } else {
            // Sinon je retourne false
            return false;
        }
    }

    /**
     * Je récupère tous les commentaires liés à un article
     * → utilisé pour les afficher sous un article
     */
    public function getByArticle($id_article)
    {
        $db = Database::getInstance();

        // Je récupère les commentaires avec le nom de l’auteur
        $sql = "SELECT commentaire.*, utilisateur.nom AS auteur
                FROM commentaire
                JOIN utilisateur ON commentaire.id_utilisateur = utilisateur.id_utilisateur
                WHERE commentaire.id_article = :id_article
                AND valider = 1
                ORDER BY date_redaction ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id_article' => $id_article]);

        // Je retourne tous les commentaires validés, triés par ancienneté
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Je récupère un commentaire précis grâce à son ID
     * → utilisé pour vérifier les droits avant suppression
     */
    public function getById($id_commentaire)
    {
        $db = Database::getInstance();

        // Je récupère le commentaire avec le nom de l’auteur
        $sql = "SELECT commentaire.*, utilisateur.nom AS auteur
                FROM commentaire
                JOIN utilisateur ON commentaire.id_utilisateur = utilisateur.id_utilisateur
                WHERE commentaire.id_commentaire = :id_commentaire
                AND valider = 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id_commentaire' => $id_commentaire]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Je récupère tous les commentaires du site, avec le titre de l’article associé
     * → utilisé pour une éventuelle interface admin
     */
    public function getAll()
    {
        $db = Database::getInstance();

        // Jointure avec utilisateur (auteur) et article (titre)
        $sql = "SELECT commentaire.*, utilisateur.nom AS auteur, article.titre AS article
                FROM commentaire
                JOIN utilisateur ON commentaire.id_utilisateur = utilisateur.id_utilisateur
                JOIN article ON commentaire.id_article = article.id_article
                WHERE valider = 1
                ORDER BY date_redaction DESC";

        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Je supprime un commentaire en base par son ID
     * → utilisé après vérification des droits dans l’API ou le contrôleur
     */
    public function deleteById($id_commentaire)
    {
        $db = Database::getInstance();

        $sql = "DELETE FROM commentaire WHERE id_commentaire = :id";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([':id' => $id_commentaire]);

        return $ok;
    }

      /**
     * Je mets à jour le contenu d’un commentaire existant
     * → uniquement utilisé si l’utilisateur est l’auteur du commentaire
     * → met aussi à jour la date de rédaction avec NOW()
     */
    public function updateContenu($id_commentaire, $nouveau_contenu)
    {
        $db = Database::getInstance();

        $sql = "UPDATE commentaire
                SET contenu = :contenu, date_redaction = NOW()
                WHERE id_commentaire = :id";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':contenu' => $nouveau_contenu,
            ':id' => $id_commentaire
        ]);
    }
}
