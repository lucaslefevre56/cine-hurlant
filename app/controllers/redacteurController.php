<?php
// app/controllers/redacteurController.php

// J’inclus mes fonctions de sécurité pour vérifier le rôle de l’utilisateur connecté
require_once ROOT . '/app/helpers/authHelpers.php';

// Ce contrôleur est dédié aux rédacteurs (ou aux admins)
// Il permet d’ajouter des œuvres et d’accéder à des vues spécifiques
class RedacteurController
{
    // Cette méthode gère à la fois l’affichage et le traitement du formulaire d’ajout d’œuvre
    public function ajouterOeuvre()
    {
        // Avant toute chose, je vérifie que l’utilisateur est bien un rédacteur
        // Sinon → je le renvoie vers la page d’accueil
        if (!isUserRedacteur()) {
            header('Location: /cine-hurlant/public/');
            exit;
        }

        // Je prépare toutes mes variables pour éviter des erreurs dans la vue
        $titre = '';
        $auteur = '';
        $annee = '';
        $analyse = '';
        $media = '';
        $id_type = null;
        $genres = [];

        // Si le formulaire a été soumis (POST), je récupère les données
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $auteur = $_POST['auteur'] ?? '';
            $annee = $_POST['annee'] ?? '';
            $analyse = $_POST['analyse'] ?? '';
            $media = $_POST['media'] ?? '';
            $genres = $_POST['genres'] ?? [];
            $type = $_POST['type'] ?? '';

            // Je traduis le texte du type (film ou bd) en ID numérique
            $types = ['film' => 1, 'bd' => 2];
            $id_type = $types[$type] ?? null;

            // Je vérifie que tous les champs ont bien été remplis
            if (
                $titre === '' || $auteur === '' || $id_type === null || $annee === '' ||
                $analyse === '' || $media === '' || empty($genres)
            ) {
                // S’il manque un champ → je renvoie vers le formulaire avec un message d’erreur
                $erreur = "Tous les champs sont obligatoires, y compris au moins un genre.";
                require_once ROOT . '/app/views/redacteur/ajouterOeuvreView.php';
                return;
            }

            // Je charge le modèle Oeuvre pour accéder à la méthode d’ajout
            require_once ROOT . '/app/models/oeuvre.php';
            $oeuvre = new Oeuvre($GLOBALS['conn']);

            // Je récupère l’ID de l’utilisateur connecté pour le lier à l’œuvre ajoutée
            $id_utilisateur = $_SESSION['user_id'] ?? 1; // À adapter si besoin

            // J’essaie d’ajouter l’œuvre en base de données
            $ajout = $oeuvre->add($titre, $auteur, $id_type, $annee, $analyse, $media, $id_utilisateur, $genres);

            // Si tout s’est bien passé → je redirige vers la page de confirmation
            if ($ajout === true) {
                $_SESSION['confirmation'] = "L’œuvre a bien été ajoutée !";
                header('Location: /cine-hurlant/public/redacteur/ajoutReussi');
                exit;
            } else {
                // Sinon → message d’erreur dans la vue
                $erreur = "Erreur lors de l'ajout de l'œuvre.";
            }
        }

        // Si je suis ici : soit c’est un GET, soit il y a une erreur → j’affiche le formulaire
        require_once ROOT . '/app/views/redacteur/ajouterOeuvreView.php';
    }

    // Cette méthode affiche la page de confirmation après l’ajout d’une œuvre
    public function ajoutReussi()
    {
        // Je récupère le message stocké dans la session (puis je le supprime)
        $message = $_SESSION['confirmation'] ?? '';
        unset($_SESSION['confirmation']);

        // Et j’affiche la vue de confirmation
        require_once ROOT . '/app/views/redacteur/confirmationOeuvreView.php';
    }
}

// Ce contrôleur est accessible uniquement aux utilisateurs ayant un rôle "rédacteur" ou "admin"
// Il permet l’ajout d’une nouvelle œuvre via un formulaire.
// En cas de succès : redirection vers une vue de confirmation.
// En cas d’échec : message d’erreur + rechargement du formulaire.
