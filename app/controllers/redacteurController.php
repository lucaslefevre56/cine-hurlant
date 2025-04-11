<?php
// app/Controllers/RedacteurController.php

namespace App\Controllers;

use App\Models\Oeuvre;
use App\Models\Article;
use App\Core\View;
use App\Helpers\AuthHelper;

class RedacteurController
{
    public function ajouterOeuvre(): void
    {
        if (!AuthHelper::isUserRedacteur()) {
            View::render('accueil/indexView');
            return;
        }

        $erreur = null;
        $titre = '';
        $auteur = '';
        $annee = '';
        $analyse = '';
        $video_url = '';
        $id_type = null;
        $genres = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $auteur = $_POST['auteur'] ?? '';
            $annee = $_POST['annee'] ?? '';
            $analyse = $_POST['analyse'] ?? '';
            $genres = $_POST['genres'] ?? [];
            $video_url = $_POST['video_url'] ?? '';
            $type = $_POST['type'] ?? '';
            $imageName = '';

            $types = ['film' => 1, 'bd' => 2];
            $id_type = $types[$type] ?? null;

            if (isset($_FILES['media']) && $_FILES['media']['error'] === 0) {
                $fileTmpPath = $_FILES['media']['tmp_name'];
                $fileName = $_FILES['media']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    $uploadDir = ROOT . '/public/images/';
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $imageName = $newFileName;
                    } else {
                        $erreur = "Erreur lors de l'upload de l'image.";
                    }
                } else {
                    $erreur = "Le fichier doit être une image valide (JPG, PNG, GIF, WEBP).";
                }
            }

            if (
                $titre === '' || $auteur === '' || $id_type === null || $annee === '' ||
                $analyse === '' || empty($genres) || empty($imageName)
            ) {
                $erreur = "Tous les champs sont obligatoires, y compris l'image.";

                View::render('redacteur/ajouterOeuvreView', compact(
                    'titre',
                    'auteur',
                    'annee',
                    'analyse',
                    'video_url',
                    'id_type',
                    'genres',
                    'erreur'
                ));
                return;
            }

            $oeuvre = new Oeuvre();
            $id_utilisateur = $_SESSION['user']['id'] ?? null;
            $ajout = $oeuvre->add($titre, $auteur, $id_type, $annee, $analyse, $imageName, $video_url, $id_utilisateur, $genres);

            if ($ajout === true) {
                View::render('redacteur/confirmationOeuvreView', [
                    'message' => "L’œuvre a bien été ajoutée !"
                ]);
                return;
            } else {
                $erreur = "Erreur lors de l'ajout de l'œuvre.";
            }
        }

        View::render('redacteur/ajouterOeuvreView', compact(
            'titre',
            'auteur',
            'annee',
            'analyse',
            'video_url',
            'id_type',
            'genres',
            'erreur'
        ));
    }

    public function ajouterArticle(): void
    {
        if (!AuthHelper::isUserRedacteur()) {
            View::render('accueil/indexView');
            return;
        }

        $erreur = null;
        $titre = '';
        $contenu = '';
        $image = '';
        $video_url = $_POST['video_url'] ?? '';
        $oeuvres = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $oeuvres = $_POST['oeuvres'] ?? [];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    $uploadDir = ROOT . '/public/images/';
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $image = $newFileName;
                    } else {
                        $erreur = "Erreur lors de l'upload de l'image.";
                    }
                } else {
                    $erreur = "Le fichier doit être une image valide (JPG, PNG, GIF, WEBP).";
                }
            }

            if ($titre === '' || $contenu === '') {
                $erreur = "Le titre et le contenu sont obligatoires.";

                $oeuvreModel = new Oeuvre();
                $oeuvresListe = $oeuvreModel->getAll();

                View::render('redacteur/ajouterArticleView', compact(
                    'titre',
                    'contenu',
                    'video_url',
                    'oeuvresListe',
                    'oeuvres',
                    'image',
                    'erreur'
                ));
                return;
            }

            $article = new Article();
            $id_utilisateur = $_SESSION['user']['id'] ?? null;
            $ajout = $article->add($titre, $contenu, $image, $video_url, $id_utilisateur, $oeuvres);

            if ($ajout === true) {
                View::render('redacteur/confirmationArticleView', [
                    'message' => "L’article a bien été ajouté !"
                ]);
                return;
            } else {
                $erreur = "Erreur lors de l'ajout de l'article.";
            }
        }

        $oeuvreModel = new Oeuvre();
        $oeuvresListe = $oeuvreModel->getAll();

        View::render('redacteur/ajouterArticleView', compact(
            'titre',
            'contenu',
            'video_url',
            'oeuvresListe',
            'oeuvres',
            'image',
            'erreur'
        ));
    }

    public function ajoutReussiOeuvre(): void
    {
        View::render('redacteur/confirmationOeuvreView', [
            'message' => "L’œuvre a bien été ajoutée !"
        ]);
    }

    public function ajoutReussiArticle(): void
    {
        View::render('redacteur/confirmationArticleView', [
            'message' => "L’article a bien été ajouté !"
        ]);
    }

    public function index(): void
    {
        $this->panel();
    }

    public function panel(): void
    {
        if (!AuthHelper::isUserRedacteur()) {
            View::render('accueil/indexView');
            return;
        }

        $this->contenusAvecMessage();
    }

    public function supprimerOeuvre(int $id_oeuvre): void
    {
        $model = new Oeuvre();
        $oeuvre = $model->getById($id_oeuvre);

        if ($oeuvre && $oeuvre['id_utilisateur'] == $_SESSION['user']['id']) {
            $model->deleteById($id_oeuvre);
            $message = "Œuvre supprimée.";
        } else {
            $message = "Accès refusé ou œuvre introuvable.";
        }

        $this->contenusAvecMessage($message);
    }

    public function supprimerArticle(int $id_article): void
    {
        $model = new Article();
        $article = $model->getById($id_article);

        if ($article && $article['id_utilisateur'] == $_SESSION['user']['id']) {
            $model->deleteById($id_article);
            $message = "Article supprimé.";
        } else {
            $message = "Accès refusé ou article introuvable.";
        }

        $this->contenusAvecMessage($message);
    }

    public function modifierOeuvre(int $id_oeuvre): void
    {
        $model = new Oeuvre();
        $oeuvre = $model->getById($id_oeuvre);

        if (!$oeuvre || $oeuvre['id_utilisateur'] != $_SESSION['user']['id']) {
            $this->contenusAvecMessage("Vous ne pouvez pas modifier cette œuvre.");
            return;
        }

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

            $message = $ok ? "Œuvre modifiée." : "Erreur lors de la modification.";
            $this->contenusAvecMessage($message);
            return;
        }

        View::render('admin/modifierOeuvreView', ['oeuvre' => $oeuvre]);
    }

    public function modifierArticle(int $id_article): void
    {
        $model = new Article();
        $article = $model->getById($id_article);

        if (!$article || $article['id_utilisateur'] != $_SESSION['user']['id']) {
            $this->contenusAvecMessage("Vous ne pouvez pas modifier cet article.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = $model->update(
                $id_article,
                $_POST['titre'],
                $_POST['contenu'],
                $_POST['image'] ?? '',
                $_POST['video_url'] ?? ''
            );

            $message = $ok ? "Article modifié." : "Erreur lors de la modification.";
            $this->contenusAvecMessage($message);
            return;
        }

        View::render('admin/modifierArticleView', ['article' => $article]);
    }

    private function contenusAvecMessage(string $message = ''): void
    {
        $id_utilisateur = $_SESSION['user']['id'] ?? null;
        $oeuvres = (new Oeuvre())->getByAuteur($id_utilisateur);
        $articles = (new Article())->getByAuteur($id_utilisateur);

        $this->rendreVue('redacteur/redacteurPanelView', [
            'oeuvres' => $oeuvres,
            'articles' => $articles,
            'message' => $message
        ]);
    }

    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function rendreVue(string $vue, array $data = []): void
    {
        if ($this->isAjaxRequest()) {
            View::renderPartial($vue, $data);
        } else {
            View::render('redacteur/redacteurPanelView', array_merge($data, ['contenu' => $vue]));
        }
    }
}
