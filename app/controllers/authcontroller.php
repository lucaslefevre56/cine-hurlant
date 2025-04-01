<?php
// app/controllers/AuthController.php

namespace App\Controllers;

// Chargement automatique du modèle Utilisateur
use App\Models\Utilisateur;

// Ce contrôleur gère tout ce qui touche à l’authentification : inscription, connexion, déconnexion

class AuthController
{
    // -----------------------------
    // Connexion de l'utilisateur
    // -----------------------------
    public function login()
    {
        // Si le formulaire de connexion est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';

            // Je crée une instance du modèle (plus besoin de connexion à passer)
            $utilisateur = new Utilisateur();
            $user = $utilisateur->getByEmail($email);

            // Vérifie l’existence de l’utilisateur et la validité du mot de passe
            if (!$user || !password_verify($password, $user['password'])) {
                $erreur = "Email ou mot de passe incorrect.";
                require ROOT . '/app/views/authentification/loginView.php';
                return;
            }

            // Si tout est OK → je crée la session
            $_SESSION['user'] = [
                'id' => $user['id_utilisateur'],
                'nom' => $user['nom'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            // Redirection vers la page d’accueil
            header('Location: /cine-hurlant/public/');
            exit;
        } else {
            // Si c’est un GET → j’affiche le formulaire
            $erreur = null;
            require_once ROOT . '/app/views/authentification/loginView.php';
        }
    }

    // -----------------------------
    // Déconnexion de l'utilisateur
    // -----------------------------
    public function logout()
    {
        // Je vide la session manuellement
        $_SESSION = [];

        // Puis je la détruis proprement
        session_unset();
        session_destroy();

        // Je renvoie vers la page d’accueil
        header('Location: /cine-hurlant/public/');
        exit;
    }

    // -----------------------------
    // Inscription d'un nouvel utilisateur
    // -----------------------------
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nom = $_POST['nom'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm'] ?? '';

            // Vérification des mots de passe
            if ($password !== $confirm) {
                $erreur = "Les mots de passe ne correspondent pas.";
                require ROOT . '/app/views/authentification/registerView.php';
                return;
            }

            // Création d’une instance du modèle
            $utilisateur = new Utilisateur();

            // Vérifie si l’email est déjà utilisé
            $existant = $utilisateur->getByEmail($email);

            if ($existant) {
                $erreur = "Cet email est déjà utilisé.";
                require ROOT . '/app/views/authentification/registerView.php';
                return;
            }

            // Hashage du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Ajout du nouvel utilisateur
            $ajout = $utilisateur->add($nom, $email, $hashedPassword);

            if ($ajout) {
                // Je récupère l’ID du nouvel utilisateur via Database directement
                $db = \App\Core\Database::getInstance();
                $id = $db->lastInsertId();

                // Création de la session utilisateur
                $_SESSION['user'] = [
                    'id' => $id,
                    'nom' => $nom,
                    'email' => $email,
                    'role' => 'utilisateur'
                ];

                header('Location: /cine-hurlant/public/');
                exit;
            } else {
                $erreur = "Erreur lors de l'inscription.";
                require_once ROOT . '/app/views/authentification/registerView.php';
                return;
            }
        } else {
            // Affiche simplement le formulaire en GET
            $erreur = null;
            require_once ROOT . '/app/views/authentification/registerView.php';
        }
    }

    public function index()
{
    // Redirection vers le formulaire de connexion
    header('Location: /cine-hurlant/public/auth/login');
    exit;
}

}
