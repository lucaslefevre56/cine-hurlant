<?php
// app/controllers/AuthController.php

// Je rends dispo le modèle Utilisateur dans tout ce contrôleur (register, login, etc.)
require_once ROOT . '/app/models/utilisateur.php';

// Ce contrôleur gère tout ce qui touche à l’authentification : inscription, connexion, déconnexion

class AuthController
{

    // -----------------------------
    // Connexion de l'utilisateur
    // -----------------------------
    public function login()
    {
        // Cette méthode va gérer toute la logique de connexion d’un utilisateur

        // Je vérifie si on est en POST → ça veut dire que le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Je récupère les champs tapés dans le formulaire de connexion
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';

            // Je crée une instance du modèle Utilisateur pour accéder à la BDD
            $utilisateur = new Utilisateur($GLOBALS['conn']);
            $user = $utilisateur->getByEmail($email);

            // Si aucun utilisateur trouvé, ou mauvais mot de passe → erreur générique
            if (!$user || !password_verify($password, $user['password'])) {
                $erreur = "Email ou mot de passe incorrect.";
                require ROOT . '/app/views/authentification/loginView.php';
                return;
            }

            // Tout est OK → je crée la session pour connecter l'utilisateur
            $_SESSION['user'] = [
                'id' => $user['id_utilisateur'],
                'nom' => $user['nom'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            // Je le redirige vers la page d'accueil
            header('Location: /cine-hurlant/public/');
            exit;
        } else {
            // Si c'est un GET → j'affiche la vue avec le formulaire de connexion
            $erreur = null;
            require_once ROOT . '/app/views/authentification/loginView.php';
        }
    }


    // -----------------------------
    // Déconnexion de l'utilisateur
    // -----------------------------
    public function logout()
    {
        // Cette méthode gère la déconnexion de l'utilisateur

        // Je vide toutes les données de session manuellement (au cas où)
        $_SESSION = [];

        // Je détruis la session côté PHP
        session_unset();    // Je vide les variables de session
        session_destroy();  // Je détruis complètement la session sur le serveur

        // Une fois déconnecté, je renvoie l'utilisateur vers la page d'accueil
        header('Location: /cine-hurlant/public/');
        exit;
    }


    // -----------------------------
    // Inscription d'un nouvel utilisateur
    // -----------------------------
    public function register()
    {
        // Si je suis en POST, ça veut dire que le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Je récupère les infos tapées dans le formulaire
            $nom = $_POST['nom'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm'] ?? '';

            // Si les mots de passe ne sont pas identiques, j'arrête tout
            if ($password !== $confirm) {
                $erreur = "Les mots de passe ne correspondent pas.";
                require ROOT . '/app/views/authentification/registerView.php';
                return;
            }

            // J’inclus le modèle Utilisateur pour bosser avec la BDD
            $utilisateur = new Utilisateur($GLOBALS['conn']);

            // Je vérifie si un compte existe déjà avec cet email
            $existant = $utilisateur->getByEmail($email);

            if ($existant) {
                $erreur = "Cet email est déjà utilisé.";
                require ROOT . '/app/views/authentification/registerView.php';
                return;
            }

            // Je hash le mot de passe avant de le stocker en base
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // J'appelle la méthode add() du modèle pour enregistrer le nouvel utilisateur
            $ajout = $utilisateur->add($nom, $email, $hashedPassword);

            // Si l’ajout a fonctionné :
            if ($ajout) {
                // Je connecte directement l’utilisateur en créant sa session
                $_SESSION['user'] = [
                    'id' => $GLOBALS['conn']->lastInsertId(),
                    'nom' => $nom,
                    'email' => $email,
                    'role' => 'utilisateur' // Par défaut, il a ce rôle-là
                ];

                // Je le redirige vers la page d’accueil ou j'affiche une erreur et redirige vers 
                // le formulaire
                header('Location: /cine-hurlant/public/');
                exit;
            } else {
                $erreur = "Erreur lors de l'inscription.";
                require_once ROOT . '/app/views/authentification/registerView.php';
                return;
            }
        } else {
            // Si je suis en GET (donc pas encore envoyé le formulaire), j’affiche la vue d'inscription
            $erreur = null;
            require_once ROOT . '/app/views/authentification/registerView.php';
        }
    }
}


// --------------------------------------------------------------
// CONCLUSION DE MA MÉTHODE register()
// --------------------------------------------------------------
//
// Cette méthode gère toute la logique d’inscription d’un utilisateur.
//
// - Si l'utilisateur arrive sur la page sans rien envoyer (GET),
//   → je lui affiche juste le formulaire pour qu’il puisse s’inscrire.
//
// - Si le formulaire est envoyé (POST), je récupère les infos :
//   nom, email, mot de passe, confirmation.
//
// - Je fais mes vérifs :
//   1. Est-ce que les deux mots de passe sont bien identiques ?
//   2. Est-ce que l’email est déjà utilisé ?
//
// - Si tout est bon, je hash le mot de passe (avec password_hash),
//   puis j’appelle la méthode add() de ma classe Utilisateur pour créer le compte en base.
//
// - Si ça marche, je connecte directement l’utilisateur en créant la session,
//   et je le redirige vers l’accueil.
//
// - Si ça échoue quelque part, j’affiche une erreur claire.
//
// Cette méthode prépare le terrain pour tout le système d’authentification du site : login, sessions, rôles, etc.
