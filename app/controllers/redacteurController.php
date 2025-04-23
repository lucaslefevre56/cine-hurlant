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
        // Sécurité : je bloque l’accès si l’utilisateur n’est pas rédacteur
        if (!AuthHelper::isUserRedacteur()) {
            View::render('accueil/indexView');
            return;
        }

        // Je prépare mes variables par défaut (pratique pour pré-remplir le formulaire en cas d’erreur)
        $erreur = null;
        $titre = '';
        $auteur = '';
        $annee = '';
        $analyse = '';
        $video_url = '';
        $id_type = null;
        $genres = [];

        // Si on a soumis le formulaire (méthode POST), je traite les données
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Je récupère les champs du formulaire (avec fallback vide ou array)
            //  // L'opérateur "?? '' " permet d'éviter une erreur si la clé n'existe pas
            $titre = $_POST['titre'] ?? '';
            $auteur = $_POST['auteur'] ?? '';
            $annee = $_POST['annee'] ?? '';
            $analyse = $_POST['analyse'] ?? '';
            $genres = $_POST['genres'] ?? [];
            $video_url = $_POST['video_url'] ?? '';
            $type = $_POST['type'] ?? '';
            $imageName = ''; // Nom final de l'image uploadée

            // Je mappe le type texte (film/bd) vers son id numérique attendu en base
            $types = ['film' => 1, 'bd' => 2];
            $id_type = $types[$type] ?? null;

            // Je gère l'upload d’image s’il y en a une
            if (isset($_FILES['media']) && $_FILES['media']['error'] === 0) {

                $fileTmpPath = $_FILES['media']['tmp_name'];
                $fileName = $_FILES['media']['name'];
                $fileNameCmps = explode(".", $fileName); // explode() coupe une chaîne en tableau
                $fileExtension = strtolower(end($fileNameCmps)); // Je récupère l'extension en minuscule
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                // Je vérifie l'extension de l'image (filtrage par liste blanche)
                if (in_array($fileExtension, $allowedExtensions)) {
                    $uploadDir = ROOT . '/public/upload/';
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension; // nom aléatoire sécurisé
                    $destPath = $uploadDir . $newFileName;

                    // Je déplace le fichier depuis le dossier temporaire vers notre dossier final
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $imageName = $newFileName; // Je garde le nouveau nom en mémoire
                    } else {
                        $erreur = "Erreur lors de l'upload de l'image.";
                    }
                } else {
                    $erreur = "Le fichier doit être une image valide (JPG, PNG, GIF, WEBP).";
                }
            }

            // Je vérifie que tous les champs obligatoires sont remplis
            if (
                $titre === '' || $auteur === '' || $id_type === null || $annee === '' ||
                $analyse === '' || empty($genres) || empty($imageName)
            ) {
                $erreur = "Tous les champs sont obligatoires, y compris l'image.";

                // Je recharge la vue avec les anciennes valeurs pour ne pas faire perdre ce que l’utilisateur a tapé
                // J’utilise compact() pour transformer ces variables en tableau associatif automatiquement
                // Cela permet de passer facilement plusieurs variables à la vue sans écrire le tableau à la main
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

            // Si tout est OK, j’envoie à la BDD via le modèle Oeuvre
            $oeuvre = new Oeuvre();
            $id_utilisateur = $_SESSION['user']['id'] ?? null; // Je récupère l’id du rédacteur connecté
            $ajout = $oeuvre->add($titre, $auteur, $id_type, $annee, $analyse, $imageName, $video_url, $id_utilisateur, $genres);

            // Si l’ajout a réussi, je redirige vers une vue de confirmation
            if ($ajout === true) {
                View::render('redacteur/confirmationOeuvreView', [
                    'message' => "L’œuvre a bien été ajoutée !"
                ]);
                return;
            } else {
                // Si ça a échoué côté BDD, je signale une erreur
                $erreur = "Erreur lors de l'ajout de l'œuvre.";
            }
        }

        // Si on arrive ici (aucune soumission ou erreur), je charge la vue de formulaire avec les éventuels messages
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
        // Je vérifie que l’utilisateur est bien un rédacteur avant d’autoriser l’accès à ce formulaire
        if (!AuthHelper::isUserRedacteur()) {
            View::render('accueil/indexView');
            return;
        }

        // Je prépare toutes mes variables avec des valeurs par défaut
        $erreur = null;
        $titre = '';
        $contenu = '';
        $image = '';
        $video_url = $_POST['video_url'] ?? '';
        $oeuvres = [];

        // Si on a bien soumis le formulaire en POST, je traite les données
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Je récupère les champs texte du formulaire
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $oeuvres = $_POST['oeuvres'] ?? [];

            // Je gère l’upload d’image s’il y en a une
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                // Je vérifie que l’image a une extension autorisée
                if (in_array($fileExtension, $allowedExtensions)) {
                    $uploadDir = ROOT . '/public/upload/';
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;

                    // Je tente de déplacer le fichier dans le dossier final
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $image = $newFileName;
                    } else {
                        $erreur = "Erreur lors de l'upload de l'image.";
                    }
                } else {
                    $erreur = "Le fichier doit être une image valide (JPG, PNG, GIF, WEBP).";
                }
            }

            // Je vérifie que les champs obligatoires sont bien remplis
            if ($titre === '' || $contenu === '') {
                $erreur = "Le titre et le contenu sont obligatoires.";

                // Je recharge la liste des œuvres pour la vue (champ sélection multiple)
                $oeuvreModel = new Oeuvre();
                $oeuvresListe = $oeuvreModel->getAll();

                // Je recharge la vue avec les données saisies + l’erreur
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

            // Si tous les champs sont OK, je passe à l’insertion en base
            $article = new Article();
            $id_utilisateur = $_SESSION['user']['id'] ?? null;
            $ajout = $article->add($titre, $contenu, $image, $video_url, $id_utilisateur, $oeuvres);

            // En cas de succès, je redirige vers une page de confirmation
            if ($ajout === true) {
                View::render('redacteur/confirmationArticleView', [
                    'message' => "L’article a bien été ajouté !"
                ]);
                return;
            } else {
                $erreur = "Erreur lors de l'ajout de l'article.";
            }
        }

        // Je recharge la liste des œuvres pour l'affichage initial (ou après une erreur sans POST)
        $oeuvreModel = new Oeuvre();
        $oeuvresListe = $oeuvreModel->getAll();

        // J’affiche la vue avec toutes les données nécessaires, y compris l’éventuel message d’erreur
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
        // Méthode appelée après l’ajout réussi d’une œuvre (affiche un message de confirmation simple)
        View::render('redacteur/confirmationOeuvreView', [
            'message' => "L’œuvre a bien été ajoutée !"
        ]);
    }

    public function ajoutReussiArticle(): void
    {
        // Même logique que pour l’œuvre, mais pour les articles
        View::render('redacteur/confirmationArticleView', [
            'message' => "L’article a bien été ajouté !"
        ]);
    }

    public function index(): void
    {
        // Je sécurise l’accès au panneau des rédacteurs
        if (!AuthHelper::isUserRedacteur()) {
            View::render('accueil/indexView');
            return;
        }

        // Si tout est bon, je charge la vue principale du panel
        View::render('redacteur/redacteurPanelView');
    }

    public function supprimerOeuvre(int $id_oeuvre): void
    {
        $model = new Oeuvre();
        $oeuvre = $model->getById($id_oeuvre);

        // Je vérifie que l’œuvre existe ET qu’elle appartient bien à l’utilisateur connecté
        if ($oeuvre && $oeuvre['id_utilisateur'] == $_SESSION['user']['id']) {
            $model->deleteById($id_oeuvre);
            $message = "Œuvre supprimée.";
        } else {
            // Sinon, je bloque la suppression
            $message = "Accès refusé ou œuvre introuvable.";
        }

        // Je redirige vers la liste perso avec message contextuel
        $this->mesOeuvresAvecMessage($message);
    }

    public function supprimerArticle(int $id_article): void
    {
        $model = new Article();
        $article = $model->getById($id_article);

        // Je sécurise la suppression comme pour les œuvres : seul l’auteur peut supprimer
        if ($article && $article['id_utilisateur'] == $_SESSION['user']['id']) {
            $model->deleteById($id_article);
            $message = "Article supprimé.";
        } else {
            $message = "Accès refusé ou article introuvable.";
        }

        // Je recharge la vue avec message adapté
        $this->mesArticlesAvecMessage($message);
    }

    public function modifierOeuvre(int $id_oeuvre): void
    {
        $model = new Oeuvre();
        $oeuvre = $model->getById($id_oeuvre);

        // Si l’œuvre n’existe pas ou qu’elle n’appartient pas à l’utilisateur, je bloque
        if (!$oeuvre || $oeuvre['id_utilisateur'] != $_SESSION['user']['id']) {
            $this->mesOeuvresAvecMessage("Vous ne pouvez pas modifier cette œuvre.");
            return;
        }

        // Si c’est une soumission de formulaire (modification en POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $media = $_POST['media_actuelle']; // Par défaut, je garde l’image existante

            // Si l’utilisateur a uploadé une nouvelle image
            if (!empty($_FILES['media']['name'])) {
                $fileTmpPath = $_FILES['media']['tmp_name'];
                $fileName = $_FILES['media']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                // Je vérifie que le nouveau fichier est bien une image valide
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadDir = ROOT . '/public/upload/';
                    $destPath = $uploadDir . $newFileName;

                    // Je tente d’uploader la nouvelle image
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        // Si l’image précédente était locale (et non une URL), je la supprime
                        if (!empty($media) && !filter_var($media, FILTER_VALIDATE_URL)) {
                            $ancienneImage = $uploadDir . $media;
                            if (file_exists($ancienneImage)) {
                                unlink($ancienneImage);
                            }
                        }

                        // Je remplace par la nouvelle
                        $media = $newFileName;
                    }
                }
            }

            // Je lance la mise à jour avec toutes les infos
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

            // Je prépare le message de retour en fonction du résultat
            $message = $ok ? "Œuvre modifiée." : "Erreur lors de la modification.";
            $this->mesOeuvresAvecMessage($message);
            return;
        }

        // Si on est en GET, j’affiche simplement le formulaire de modification pré-rempli
        View::render('redacteur/modifierOeuvreView', ['oeuvre' => $oeuvre]);
    }


    public function modifierArticle(int $id_article): void
    {
        $model = new Article();
        $article = $model->getById($id_article);

        // Je vérifie que l’article existe ET qu’il appartient bien au rédacteur connecté
        if (!$article || $article['id_utilisateur'] != $_SESSION['user']['id']) {
            $this->mesArticlesAvecMessage("Vous ne pouvez pas modifier cet article.");
            return;
        }

        // Si on a envoyé le formulaire en POST, je traite la modification
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $video_url = $_POST['video_url'] ?? '';
            $ancienneImage = $_POST['media_actuelle'] ?? '';
            $nouvelleImage = $ancienneImage; // Par défaut, je garde l’ancienne image

            // Si une nouvelle image a été uploadée, je la traite
            if (!empty($_FILES['media']['name']) && $_FILES['media']['error'] === 0) {
                $fileTmpPath = $_FILES['media']['tmp_name'];
                $fileName = $_FILES['media']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                // Je valide l’extension du fichier
                if (in_array($fileExtension, $allowedExtensions)) {
                    $uploadDir = ROOT . '/public/upload/';
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;

                    // Je tente de déplacer l’image dans le dossier upload
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        // Si l’ancienne image est locale, je la supprime du serveur
                        if (!empty($ancienneImage) && !filter_var($ancienneImage, FILTER_VALIDATE_URL)) {
                            $ancienChemin = $uploadDir . $ancienneImage;
                            if (file_exists($ancienChemin)) {
                                unlink($ancienChemin);
                            }
                        }
                        $nouvelleImage = $newFileName; // Et je mets à jour avec la nouvelle
                    }
                }
            }

            // Je lance la mise à jour avec les nouvelles infos
            $ok = $model->update($id_article, $titre, $contenu, $nouvelleImage, $video_url);

            // Message de confirmation ou d’échec
            $message = $ok ? "Article modifié." : "Erreur lors de la modification.";
            $this->mesArticlesAvecMessage($message);
            return;
        }

        // Si c’est une requête GET, j’affiche juste le formulaire prérempli
        View::render('redacteur/modifierArticleView', ['article' => $article]);
    }

    public function mesOeuvres(): void
    {
        // Méthode raccourcie : j’utilise une méthode centrale pour afficher avec ou sans message
        $this->mesOeuvresAvecMessage();
    }

    public function mesArticles(): void
    {
        // Même logique, côté articles
        $this->mesArticlesAvecMessage();
    }

    private function mesArticlesAvecMessage(string $message = ''): void
    {
        // Je récupère l’id du rédacteur connecté
        $id_utilisateur = $_SESSION['user']['id'] ?? null;

        // Je récupère ses articles
        $articles = (new Article())->getByAuteur($id_utilisateur);

        // J’affiche la vue des articles avec le message éventuel
        $this->rendreVue('redacteur/mesArticlesView', [
            'articles' => $articles,
            'message' => $message
        ]);
    }

    private function mesOeuvresAvecMessage(string $message = ''): void
    {
        // Je récupère les œuvres de l’utilisateur connecté
        $id_utilisateur = $_SESSION['user']['id'] ?? null;
        $oeuvres = (new Oeuvre())->getByAuteur($id_utilisateur);

        // Je trie les œuvres en deux blocs (films / BD) pour les sous-onglets
        $films = array_filter($oeuvres, fn($o) => strtolower($o['nom']) === 'film');
        $bds   = array_filter($oeuvres, fn($o) => strtolower($o['nom']) === 'bd');

        // Je charge la vue avec les deux listes + un éventuel message
        $this->rendreVue('redacteur/mesOeuvresView', [
            'films' => $films,
            'bds' => $bds,
            'message' => $message
        ]);
    }

    private function isAjaxRequest(): bool
    {
        // Je détecte si la requête a été envoyée via AJAX (fetch côté JS)
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function rendreVue(string $vue, array $data = []): void
    {
        // Si c’est une requête AJAX, je charge uniquement la vue partielle
        if ($this->isAjaxRequest()) {
            View::renderPartial($vue, $data);
        } else {
            // Sinon je rends la vue complète du panneau avec l’onglet demandé
            View::render('redacteur/redacteurPanelView', array_merge($data, ['contenu' => $vue]));
        }
    }
}
