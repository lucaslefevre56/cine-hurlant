<?php
// app/controllers/RedacteurController.php

namespace App\Controllers;

use App\Models\Oeuvre;   // J'importe le modèle Oeuvre
use App\Models\Article;  // J'importe le modèle Article
use App\Helpers\AuthHelper;

class RedacteurController
{
    // ------------------------------------------------------------
    // MÉTHODE : AJOUTER UNE ŒUVRE
    // ------------------------------------------------------------
    public function ajouterOeuvre()
    {
        // Vérifie que l’utilisateur est bien un rédacteur
        if (!AuthHelper::isUserRedacteur()) {
            header('Location: /cine-hurlant/public/');
            exit;
        }

        // Initialisation des variables pour pré-remplir le formulaire en cas d’erreur
        $titre = '';
        $auteur = '';
        $annee = '';
        $analyse = '';
        $video_url = ''; // URL vidéo
        $id_type = null;
        $genres = [];

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Je récupère les données saisies
            $titre = $_POST['titre'] ?? '';
            $auteur = $_POST['auteur'] ?? '';
            $annee = $_POST['annee'] ?? '';
            $analyse = $_POST['analyse'] ?? '';
            $genres = $_POST['genres'] ?? [];
            $video_url = $_POST['video_url'] ?? '';  // URL de la vidéo (facultatif)
            $type = $_POST['type'] ?? '';
            $imageName = ''; // Nom final du fichier image (vide par défaut)

            // Je transforme le texte "film" ou "bd" en ID (1 ou 2)
            $types = ['film' => 1, 'bd' => 2];
            $id_type = $types[$type] ?? null;

            if (isset($_FILES['media']) && $_FILES['media']['error'] === 0) {
    $fileTmpPath = $_FILES['media']['tmp_name'];
    $fileName = $_FILES['media']['name'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];  // Extensions autorisées

    if (in_array($fileExtension, $allowedExtensions)) {
        // Répertoire de destination
        $uploadDir = ROOT . '/public/images/';
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $imageName = $newFileName;  // On garde le nom de fichier pour l'enregistrement
        } else {
            $erreur = "Erreur lors de l'upload de l'image."; //ICI !!
        }
    } else {
        $erreur = "Le fichier doit être une image valide (JPG, PNG, GIF, WEBP)."; //ICI !!
    }
}

            // Je vérifie que tous les champs obligatoires sont bien remplis
            if (
                $titre === '' || $auteur === '' || $id_type === null || $annee === '' ||
                $analyse === '' || empty($genres) || empty($imageName)
            ) {
                $erreur = "Tous les champs sont obligatoires, y compris l'image."; //ICI !!
                require_once ROOT . '/app/views/redacteur/ajouterOeuvreView.php';
                return;
            }

            // Je crée une instance du modèle Oeuvre (sans passer la connexion grâce au singleton)
            $oeuvre = new Oeuvre();

            // Je récupère l’ID de l’utilisateur connecté (remplacer 1 par la gestion de session réelle)
            $id_utilisateur = $_SESSION['user_id'] ?? 1;

            // J’essaie d’ajouter l’œuvre
            $ajout = $oeuvre->add($titre, $auteur, $id_type, $annee, $analyse, $imageName, $video_url, $id_utilisateur, $genres);

            // Si l’ajout a fonctionné → je redirige vers la confirmation
            if ($ajout === true) {
                $_SESSION['confirmation'] = "L’œuvre a bien été ajoutée !";
                header('Location: /cine-hurlant/public/redacteur/ajoutReussiOeuvre');
                exit;
            } else {
                // Sinon, message d’erreur
                $erreur = "Erreur lors de l'ajout de l'œuvre."; //ICI !!
            }
        }
       
        // Si on est ici, c’est soit une première visite, soit un retour après une erreur
        require_once ROOT . '/app/views/redacteur/ajouterOeuvreView.php';
    }

    // ------------------------------------------------------------
    // MÉTHODE : AJOUTER UN ARTICLE
    // ------------------------------------------------------------
    public function ajouterArticle()
    {
        // Vérifie que l’utilisateur est bien un rédacteur
        if (!AuthHelper::isUserRedacteur()) {
            header('Location: /cine-hurlant/public/');
            exit;
        }

        // Initialisation des variables pour pré-remplir le formulaire en cas d’erreur
        $titre = '';
        $contenu = '';
        $image = '';  // Nom de l'image
        $video_url = $_POST['video_url'] ?? '';
        $oeuvres = [];

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Je récupère les données saisies
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $oeuvres = $_POST['oeuvres'] ?? []; // tableau d’ID d’œuvres sélectionnées

            // Gestion de l'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileSize = $_FILES['image']['size']; //ICI !!
                $fileType = $_FILES['image']['type']; //ICI !!
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];  // Ajout du format webp

                if (in_array($fileExtension, $allowedExtensions)) {
                    // Chemin où on veut stocker le fichier
                    $uploadDir = ROOT . '/public/images/';
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $image = $newFileName;  // On stocke le nom du fichier
                    } else {
                        $erreur = "Erreur lors de l'upload de l'image."; //ICI !!
                    }
                } else {
                    $erreur = "Le fichier doit être une image valide (JPG, PNG, GIF, WEBP)."; //ICI !!
                }
            } else {
                $image = ''; // Si aucune image n'est téléchargée
            }

            // Je vérifie que les champs obligatoires sont bien remplis
            if ($titre === '' || $contenu === '') {
                $erreur = "Le titre et le contenu sont obligatoires."; //ICI !!
                $oeuvreModel = new Oeuvre();
                $oeuvresListe = $oeuvreModel->getAll(); //ICI !!
                require_once ROOT . '/app/views/redacteur/ajouterArticleView.php';
                return;
            }

            // Je crée une instance du modèle Article (grâce au singleton)
            $article = new Article();

            // Je récupère l’ID de l’utilisateur connecté
            $id_utilisateur = $_SESSION['user_id'] ?? 1;

            // J’essaie d’ajouter l’article + ses œuvres liées
            $ajout = $article->add($titre, $contenu, $image, $video_url, $id_utilisateur, $oeuvres);

            // Si l’ajout fonctionne → je redirige vers la confirmation
            if ($ajout === true) {
                $_SESSION['confirmation'] = "L’article a bien été ajouté !";
                header('Location: /cine-hurlant/public/redacteur/ajoutReussiArticle');
                exit;
            } else {
                // Sinon → message d’erreur
                $erreur = "Erreur lors de l'ajout de l'article."; //ICI !!
            }
        }

        // Si on est ici (GET ou retour avec erreur), je charge la liste des œuvres
        $oeuvreModel = new Oeuvre();
        $oeuvresListe = $oeuvreModel->getAll(); //ICI !!

        require_once ROOT . '/app/views/redacteur/ajouterArticleView.php';
    }

    // ------------------------------------------------------------
    // MÉTHODE : CONFIRMATION AJOUT ŒUVRE
    // ------------------------------------------------------------
    public function ajoutReussiOeuvre()
    {
        $message = $_SESSION['confirmation'] ?? ''; //ICI !!
        unset($_SESSION['confirmation']);

        require_once ROOT . '/app/views/redacteur/confirmationOeuvreView.php';
    }

    // ------------------------------------------------------------
    // MÉTHODE : CONFIRMATION AJOUT ARTICLE
    // ------------------------------------------------------------
    public function ajoutReussiArticle()
    {
        $message = $_SESSION['confirmation'] ?? ''; //ICI !!
        unset($_SESSION['confirmation']);

        require_once ROOT . '/app/views/redacteur/confirmationArticleView.php';
    }

    // Page par défaut du contrôleur → redirection vers ajout œuvre
    public function index()
    {
        header('Location: /cine-hurlant/public/redacteur/ajouterOeuvre');
        exit;
    }
}
