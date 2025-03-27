<?php
// app/controllers/AuthController.php

// Ce contrôleur gère tout ce qui touche à l’authentification : inscription, connexion, déconnexion

class AuthController
{
    // Je crée une méthode register() qui va gérer l’inscription
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
                echo "Les mots de passe ne correspondent pas.";
                return;
            }

            // J’inclus le modèle Utilisateur pour bosser avec la BDD
            require_once ROOT . '/app/models/utilisateur.php';
            $utilisateur = new Utilisateur($GLOBALS['conn']);

            // Je vérifie si un compte existe déjà avec cet email
            $existant = $utilisateur->getByEmail($email);

            if ($existant) {
                echo "Cet email est déjà utilisé.";
                return;
            }

            // Tout est bon → je hash le mot de passe (sécurité max)
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

                // Je le redirige vers la page d’accueil
                header('Location: /');
                exit;
            } else {
                // Si jamais la requête SQL foire pour une raison ou une autre
                echo "Erreur lors de l'inscription.";
            }

        } else {
            // Si je suis en GET (donc pas encore envoyé le formulaire), j’affiche la vue d'inscription
            require_once ROOT . '/app/views/auth/registerView.php';
        }
    }
}
