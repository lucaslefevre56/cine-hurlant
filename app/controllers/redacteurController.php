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
                    $uploadDir = ROOT . '/public/upload/';
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
                    $uploadDir = ROOT . '/public/upload/';
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
        if (!AuthHelper::isUserRedacteur()) {
            View::render('accueil/indexView');
            return;
        }

        View::render('redacteur/redacteurPanelView');
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

        $this->mesOeuvresAvecMessage($message);
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

        $this->mesArticlesAvecMessage($message);
    }

    public function modifierOeuvre(int $id_oeuvre): void
    {
        $model = new Oeuvre();
        $oeuvre = $model->getById($id_oeuvre);

        if (!$oeuvre || $oeuvre['id_utilisateur'] != $_SESSION['user']['id']) {
            $this->mesOeuvresAvecMessage("Vous ne pouvez pas modifier cette œuvre.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $media = $_POST['media_actuelle']; // Par défaut on garde l’ancienne image

            // Si une nouvelle image est uploadée
            if (!empty($_FILES['media']['name'])) {
                $fileTmpPath = $_FILES['media']['tmp_name'];
                $fileName = $_FILES['media']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadDir = ROOT . '/public/upload/';
                    $destPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        // Supprimer l’ancienne image si c’est un fichier local
                        if (!empty($media) && !filter_var($media, FILTER_VALIDATE_URL)) {
                            $ancienneImage = $uploadDir . $media;
                            if (file_exists($ancienneImage)) {
                                unlink($ancienneImage);
                            }
                        }

                        // On remplace par la nouvelle
                        $media = $newFileName;
                    }
                }
            }

            $ok = $model->update(
                $id_oeuvre,
                $_POST['titre'],
                $_POST['auteur'],
                (int) $_POST['annee'],
                $media,
                $_POST['video_url'],
                $_POST['analyse'],
                (int) $_POST['id_type']
            );

            $message = $ok ? "Œuvre modifiée." : "Erreur lors de la modification.";
            $this->mesOeuvresAvecMessage($message);
            return;
        }

        View::render('redacteur/modifierOeuvreView', ['oeuvre' => $oeuvre]);
    }

    public function modifierArticle(int $id_article): void
    {
        $model = new Article();
        $article = $model->getById($id_article);

        if (!$article || $article['id_utilisateur'] != $_SESSION['user']['id']) {
            $this->mesArticlesAvecMessage("Vous ne pouvez pas modifier cet article.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $video_url = $_POST['video_url'] ?? '';
            $ancienneImage = $_POST['media_actuelle'] ?? '';
            $nouvelleImage = $ancienneImage; // par défaut, on garde l’ancienne

            // Traitement de l'image si un fichier a été uploadé
            if (!empty($_FILES['media']['name']) && $_FILES['media']['error'] === 0) {
                $fileTmpPath = $_FILES['media']['tmp_name'];
                $fileName = $_FILES['media']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    $uploadDir = ROOT . '/public/upload/';
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        // suppression de l'ancienne image si elle existe et est locale
                        if (!empty($ancienneImage) && !filter_var($ancienneImage, FILTER_VALIDATE_URL)) {
                            $ancienChemin = $uploadDir . $ancienneImage;
                            if (file_exists($ancienChemin)) {
                                unlink($ancienChemin);
                            }
                        }
                        $nouvelleImage = $newFileName;
                    }
                }
            }

            $ok = $model->update($id_article, $titre, $contenu, $nouvelleImage, $video_url);

            $message = $ok ? "Article modifié." : "Erreur lors de la modification.";
            $this->mesArticlesAvecMessage($message);
            return;
        }

        View::render('redacteur/modifierArticleView', ['article' => $article]);
    }

    public function mesOeuvres(): void
    {
        $this->mesOeuvresAvecMessage();
    }

    public function mesArticles(): void
    {
        $this->mesArticlesAvecMessage();
    }

    private function mesArticlesAvecMessage(string $message = ''): void
    {
        $id_utilisateur = $_SESSION['user']['id'] ?? null;
        $articles = (new Article())->getByAuteur($id_utilisateur);

        $this->rendreVue('redacteur/mesArticlesView', [
            'articles' => $articles,
            'message' => $message
        ]);
    }

    private function mesOeuvresAvecMessage(string $message = ''): void
    {
        $id_utilisateur = $_SESSION['user']['id'] ?? null;
        $oeuvres = (new Oeuvre())->getByAuteur($id_utilisateur);
        $films = array_filter($oeuvres, fn($o) => strtolower($o['nom']) === 'film');
        $bds   = array_filter($oeuvres, fn($o) => strtolower($o['nom']) === 'bd');

        $this->rendreVue('redacteur/mesOeuvresView', [
            'films' => $films,
            'bds' => $bds,
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
