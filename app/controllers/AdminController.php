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
            $ok = $model->update(
                $id_article,
                $_POST['titre'],
                $_POST['contenu'],
                $_POST['image'] ?? '',
                $_POST['video_url'] ?? ''
            );

            $message = $ok ? "Article modifié avec succès." : "Erreur lors de la modification.";
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
            $ok = $model->update(
                $id_oeuvre,
                $_POST['titre'],
                $_POST['auteur'],
                $_POST['annee'],
                $_POST['media'],
                $_POST['video_url'],
                $_POST['analyse'],
                $_POST['id_type']
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
