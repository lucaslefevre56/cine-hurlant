<?php
// app/Controllers/AdminController.php

namespace App\Controllers;

use App\Core\View;
use App\Core\ErrorHandler;
use App\Models\Utilisateur;
use App\Models\Article;
use App\Models\Oeuvre;
use App\Models\Commentaire;
use App\Helpers\AuthHelper;

class AdminController
{
    public function index(): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit : réservé aux administrateurs.");
        }

        View::render('admin/adminPanelView');
    }

    public function utilisateurs(): void
    {
        $utilisateurModel = new Utilisateur();
        $utilisateurs = $utilisateurModel->getAll();

        $this->rendreVue('admin/listeUtilisateursView', [
            'utilisateurs' => $utilisateurs
        ]);
    }

    public function supprimerUtilisateur($id_utilisateur): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit.");
        }

        if ($id_utilisateur == $_SESSION['user']['id']) {
            $this->utilisateursAvecMessage("Vous ne pouvez pas supprimer votre propre compte.");
            return;
        }

        $model = new Utilisateur();
        $ok = $model->desactiver($id_utilisateur);

        $message = $ok
            ? "Utilisateur désactivé avec succès. Ses contributions sont conservées."
            : "Échec de la désactivation du compte.";

        $this->utilisateursAvecMessage($message);
    }

    public function restaurerUtilisateur($id_utilisateur): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit.");
        }

        $model = new Utilisateur();
        $ok = $model->reactiver($id_utilisateur);

        $message = $ok
            ? "Utilisateur restauré avec succès."
            : "Échec de la restauration de l'utilisateur.";

        $this->utilisateursAvecMessage($message);
    }

    public function changerRole(): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit.");
        }

        if (!isset($_POST['id_utilisateur'], $_POST['role'])) {
            $this->utilisateursAvecMessage("Données manquantes.");
            return;
        }

        $id = (int) $_POST['id_utilisateur'];
        $role = $_POST['role'];

        if ($id === $_SESSION['user']['id']) {
            $this->utilisateursAvecMessage("Vous ne pouvez pas modifier votre propre rôle.");
            return;
        }

        $roles = ['utilisateur', 'redacteur', 'admin'];
        if (!in_array($role, $roles)) {
            $this->utilisateursAvecMessage("Rôle invalide.");
            return;
        }

        $model = new Utilisateur();
        $ok = $model->updateRole($id, $role);

        $message = $ok ? "Rôle mis à jour avec succès." : "Échec de la mise à jour.";
        $this->utilisateursAvecMessage($message);
    }

    public function articles(): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_article'])) {
            $ok = (new Article())->deleteById((int) $_POST['id_article']);
            $message = $ok ? "Article supprimé avec succès." : "Erreur lors de la suppression.";
            $this->articlesAvecMessage($message);
            return;
        }

        $this->articlesAvecMessage();
    }

    public function modifierArticle(int $id_article): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit.");
        }

        $model = new Article();
        $article = $model->getById($id_article);
        if (!$article) ErrorHandler::render404("Article introuvable.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $video_url = $_POST['video_url'] ?? '';
            $imageActuelle = $_POST['image_actuelle'] ?? '';
            $nouvelleImage = $imageActuelle;

            // ⚠️ Traitement de l’upload si un fichier est envoyé
            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
                $tmpName = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($ext, $extensionsAutorisees)) {
                    $nouveauNom = md5(time() . $fileName) . '.' . $ext;
                    $cheminUpload = ROOT . '/public/upload/' . $nouveauNom;

                    if (move_uploaded_file($tmpName, $cheminUpload)) {
                        $nouvelleImage = $nouveauNom;

                        // 🧹 Si ancienne image locale → suppression
                        if (!empty($imageActuelle) && !filter_var($imageActuelle, FILTER_VALIDATE_URL)) {
                            $ancienFichier = ROOT . '/public/upload/' . $imageActuelle;
                            if (file_exists($ancienFichier)) {
                                unlink($ancienFichier);
                            }
                        }
                    }
                }
            }

            // 🔄 Mise à jour finale
            $ok = $model->update($id_article, $titre, $contenu, $nouvelleImage, $video_url);

            $message = $ok
                ? "Article modifié avec succès."
                : "Erreur lors de la modification.";
            $this->articlesAvecMessage($message);
            return;
        }

        View::render('admin/modifierArticleView', ['article' => $article]);
    }

    public function oeuvres(): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_oeuvre'])) {
            $ok = (new Oeuvre())->deleteById((int) $_POST['id_oeuvre']);
            $message = $ok ? "Œuvre supprimée avec succès." : "Erreur lors de la suppression.";
            $this->oeuvresAvecMessage($message);
            return;
        }

        $this->oeuvresAvecMessage();
    }

    public function modifierOeuvre(int $id_oeuvre): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit.");
        }

        $model = new Oeuvre();
        $oeuvre = $model->getById($id_oeuvre);
        if (!$oeuvre) ErrorHandler::render404("Œuvre introuvable.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $auteur = $_POST['auteur'] ?? '';
            $annee = $_POST['annee'] ?? '';
            $video_url = $_POST['video_url'] ?? '';
            $analyse = $_POST['analyse'] ?? '';
            $id_type = (int) ($_POST['id_type'] ?? 0);
            $ancienneImage = $_POST['media_actuelle'] ?? '';
            $nouvelleImage = $ancienneImage;

            // Si une nouvelle image a été uploadée
            if (!empty($_FILES['media']['name']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['media']['tmp_name'];
                $originalName = $_FILES['media']['name'];
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($extension, $allowed)) {
                    $newName = md5(time() . $originalName) . '.' . $extension;
                    $dest = ROOT . '/public/upload/' . $newName;

                    if (move_uploaded_file($tmpName, $dest)) {
                        $nouvelleImage = $newName;

                        // Suppression de l'ancienne image si locale
                        if (!filter_var($ancienneImage, FILTER_VALIDATE_URL)) {
                            $cheminAncien = ROOT . '/public/upload/' . $ancienneImage;
                            if (file_exists($cheminAncien)) {
                                unlink($cheminAncien);
                            }
                        }
                    }
                }
            }

            // Mise à jour de l’œuvre
            $ok = $model->update(
                $id_oeuvre,
                $titre,
                $auteur,
                $annee,
                $nouvelleImage,
                $video_url,
                $analyse,
                $id_type
            );

            $message = $ok ? "Œuvre modifiée avec succès." : "Erreur lors de la modification.";
            $this->oeuvresAvecMessage($message);
            return;
        }

        View::render('admin/modifierOeuvreView', ['oeuvre' => $oeuvre]);
    }

    public function commentaires(): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_commentaire'])) {
            $ok = (new Commentaire())->deleteById((int) $_POST['id_commentaire']);
            $message = $ok ? "Commentaire supprimé avec succès." : "Erreur lors de la suppression.";
            $this->commentairesAvecMessage($message);
            return;
        }

        $this->commentairesAvecMessage();
    }

    // 🔁 Affichages factorisés avec vue partielle ou complète

    private function utilisateursAvecMessage(string $message = ''): void
    {
        $utilisateurs = (new Utilisateur())->getAll();
        $this->rendreVue('admin/listeUtilisateursView', [
            'utilisateurs' => $utilisateurs,
            'message' => $message
        ]);
    }

    private function articlesAvecMessage(string $message = ''): void
    {
        $articles = (new Article())->getAll();
        $this->rendreVue('admin/articlesView', [
            'articles' => $articles,
            'message' => $message
        ]);
    }

    private function oeuvresAvecMessage(string $message = ''): void
    {
        $oeuvres = (new Oeuvre())->getAll();
        $films = array_filter($oeuvres, fn($o) => strtolower($o['nom']) === 'film');
        $bds   = array_filter($oeuvres, fn($o) => strtolower($o['nom']) === 'bd');

        $this->rendreVue('admin/oeuvresView', [
            'films' => $films,
            'bds' => $bds,
            'message' => $message
        ]);
    }

    private function commentairesAvecMessage(string $message = ''): void
    {
        $commentaires = (new Commentaire())->getAll();
        $this->rendreVue('admin/commentairesView', [
            'commentaires' => $commentaires,
            'message' => $message
        ]);
    }

    // ✅ Inclusion propre d’une méthode AJAX check
    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // 🧩 Méthode centrale pour éviter les répétitions de render/renderPartial
    private function rendreVue(string $vue, array $data = []): void
    {
        if ($this->isAjaxRequest()) {
            View::renderPartial($vue, $data);
        } else {
            View::render('admin/adminPanelView', array_merge($data, ['contenu' => $vue]));
        }
    }
}
