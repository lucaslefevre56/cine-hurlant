<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\ErrorHandler;
use App\Models\Utilisateur;
use App\Helpers\AuthHelper;
use App\Models\Oeuvre;
use App\Models\Article;
use App\Models\Commentaire;

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
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit : réservé aux administrateurs.");
        }

        $utilisateurModel = new Utilisateur();
        $utilisateurs = $utilisateurModel->getAll();

        View::renderPartial('admin/listeUtilisateursView', [
            'utilisateurs' => $utilisateurs
        ]);
    }

    public function articles(): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit : réservé aux administrateurs.");
        }

        // Gestion suppression article
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_article'])) {
            $model = new Article();
            $ok = $model->deleteById((int) $_POST['id_article']);

            $_SESSION['message'] = $ok ? "Article supprimé avec succès." : "Erreur lors de la suppression de l'article.";
            $_SESSION['onglet_actif'] = 'articles';
            header("Location: " . BASE_URL . "/admin");
            exit;
        }

        $articleModel = new Article();
        $articles = $articleModel->getAll();

        View::renderPartial('admin/articlesView', [
            'articles' => $articles
        ]);
    }

    public function modifierArticle(int $id_article): void
    {
        // Vérification si l'utilisateur est admin
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit : réservé aux administrateurs.");
        }

        // Récupérer l'article à modifier
        $articleModel = new Article();
        $article = $articleModel->getById($id_article);

        // Si l'article n'existe pas, renvoyer une erreur 404
        if (!$article) {
            ErrorHandler::render404("Article non trouvé.");
        }

        // Traitement du formulaire de modification
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les valeurs soumises dans le formulaire
            $titre = $_POST['titre'];
            $contenu = $_POST['contenu'];
            $image = $_POST['image'] ?? ''; // Valeur par défaut si pas d'image
            $video_url = $_POST['video_url'] ?? ''; // Valeur par défaut si pas de vidéo

            // Mise à jour de l'article
            $ok = $articleModel->update($id_article, $titre, $contenu, $image, $video_url);

            // Message de confirmation
            $_SESSION['message'] = $ok ? "Article modifié avec succès." : "Erreur lors de la modification de l'article.";

            // Redirection vers l'admin panel après modification
            $_SESSION['onglet_actif'] = 'articles';
            header("Location: " . BASE_URL . "/admin");
            exit;
        }

        // Affichage de la vue pour modifier l'article
        View::render('admin/modifierArticleView', [
            'article' => $article
        ]);
    }

    public function oeuvres(): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit : réservé aux administrateurs.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_oeuvre'])) {
            $model = new Oeuvre();
            $ok = $model->deleteById((int) $_POST['id_oeuvre']);

            $_SESSION['message'] = $ok ? "Œuvre supprimée avec succès." : "Erreur lors de la suppression.";
            $_SESSION['onglet_actif'] = 'oeuvres';
            header("Location: " . BASE_URL . "/admin");
            exit;
        }

        $oeuvreModel = new Oeuvre();
        $oeuvres = (new Oeuvre())->getAll();

        $films = array_filter($oeuvres, fn($o) => strtolower($o['nom']) === 'film');
        $bds   = array_filter($oeuvres, fn($o) => strtolower($o['nom']) === 'bd');

        View::renderPartial('admin/oeuvresView', [
            'films' => $films,
            'bds' => $bds
        ]);
    }

    public function modifierOeuvre(int $id_oeuvre): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit : réservé aux administrateurs.");
        }

        $oeuvreModel = new Oeuvre();
        $oeuvre = $oeuvreModel->getById($id_oeuvre); // Récupérer l'œuvre à modifier

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement du formulaire de modification
            $titre = $_POST['titre'];
            $auteur = $_POST['auteur'];
            $annee = $_POST['annee'];
            $media = $_POST['media'];
            $video_url = $_POST['video_url'];
            $analyse = $_POST['analyse'];
            $id_type = $_POST['id_type'];

            $ok = $oeuvreModel->update($id_oeuvre, $titre, $auteur, $annee, $media, $video_url, $analyse, $id_type); // Update oeuvre

            $_SESSION['message'] = $ok ? "Œuvre modifiée avec succès." : "Erreur lors de la modification de l'œuvre.";

            // Redirection après modification
            $_SESSION['onglet_actif'] = 'oeuvres';
            header("Location: " . BASE_URL . "/admin");
            exit;
        }

        View::render('admin/modifierOeuvreView', [
            'oeuvre' => $oeuvre
        ]);
    }

    public function commentaires(): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit : réservé aux administrateurs.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_commentaire'])) {
            $model = new Commentaire();
            $ok = $model->deleteById((int) $_POST['id_commentaire']);

            $_SESSION['message'] = $ok ? "Commentaire supprimé avec succès." : "Erreur lors de la suppression.";
            $_SESSION['onglet_actif'] = 'commentaires';

            header("Location: " . BASE_URL . "/admin");
            exit;
        }

        $model = new Commentaire();
        $commentaires = $model->getAll();

        View::renderPartial('admin/commentairesView', [
            'commentaires' => $commentaires
        ]);
    }

    public function changerRole(): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit.");
        }

        if (!isset($_POST['id_utilisateur'], $_POST['role'])) {
            $_SESSION['message'] = "Données manquantes.";
            $_SESSION['onglet_actif'] = 'utilisateurs';
            header("Location: " . BASE_URL . "/admin");
            exit;
        }

        $id = (int) $_POST['id_utilisateur'];
        $nouveauRole = $_POST['role'];

        if ($id === $_SESSION['user']['id']) {
            $_SESSION['message'] = "Vous ne pouvez pas modifier votre propre rôle. What a shame...";
            $_SESSION['onglet_actif'] = 'utilisateurs';
            header("Location: " . BASE_URL . "/admin");
            exit;
        }

        $rolesAutorises = ['utilisateur', 'redacteur', 'admin'];
        if (!in_array($nouveauRole, $rolesAutorises)) {
            $_SESSION['message'] = "Rôle invalide.";
            $_SESSION['onglet_actif'] = 'utilisateurs';
            header("Location: " . BASE_URL . "/admin");
            exit;
        }

        $utilisateurModel = new Utilisateur();
        $success = $utilisateurModel->updateRole($id, $nouveauRole);

        $_SESSION['message'] = $success
            ? "Rôle mis à jour avec succès."
            : "Échec de la mise à jour.";
        $_SESSION['onglet_actif'] = 'utilisateurs';

        header("Location: " . BASE_URL . "/admin");
        exit;
    }

    public function supprimerUtilisateur($id_utilisateur): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit.");
        }

        if ($id_utilisateur == $_SESSION['user']['id']) {
            $_SESSION['message'] = "Vous ne pouvez pas supprimer votre propre compte.";
            $_SESSION['onglet_actif'] = 'utilisateurs';
            header("Location: " . BASE_URL . "/admin");
            exit;
        }

        $utilisateurModel = new Utilisateur();
        $success = $utilisateurModel->deleteById($id_utilisateur);

        $_SESSION['message'] = $success
            ? "Utilisateur supprimé avec succès."
            : "Échec de la suppression de l'utilisateur.";
        $_SESSION['onglet_actif'] = 'utilisateurs';

        header("Location: " . BASE_URL . "/admin");
        exit;
    }
}
