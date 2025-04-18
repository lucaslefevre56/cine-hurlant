<?php
// app/Controllers/AuthController.php

namespace App\Controllers;

use App\Models\Utilisateur;
use App\Core\View;
use App\Core\Database;
use App\Helpers\PasswordHelper;

class AuthController
{
    // Connexion d’un utilisateur existant
    public function login(): void
    {
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Je cherche un utilisateur correspondant à l’email
            $utilisateur = new Utilisateur();
            $user = $utilisateur->getByEmail($email);

            // Aucun utilisateur avec cet email
            if (!$user) {
                View::render('authentification/loginView', ['erreur' => "Email ou mot de passe incorrect."]);
                return;
            }

            // Si l’utilisateur existe mais que son compte est désactivé
            if ((int)$user['actif'] !== 1) {
                View::render('authentification/loginView', [
                    'erreur' => "Ce compte a été désactivé, contactez l'administrateur pour demander à le réactiver."
                ]);
                return;
            }

            // Si l’utilisateur est actif, je vérifie le mot de passe
            if (!password_verify($password, $user['password'])) {
                View::render('authentification/loginView', ['erreur' => "Email ou mot de passe incorrect."]);
                return;
            }

            // Tout est bon, je connecte l’utilisateur et je stocke ses infos en session
            $_SESSION['user'] = [
                'id' => $user['id_utilisateur'],
                'nom' => $user['nom'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            // Je redirige vers l’accueil, mais en appelant le contrôleur directement
            // pour avoir les variables attendues
            $accueil = new \App\Controllers\AccueilController();
            $accueil->index();
            exit;
        }

        // Si on arrive ici sans POST, on affiche simplement le formulaire de connexion vide
        View::render('authentification/loginView', ['erreur' => null]);
    }

    // Déconnexion complète de l’utilisateur
    public function logout(): void
    {
        // Je vérifie que la session est active, puis je la détruis
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }

        // Ensuite, je redirige vers l’accueil
        $accueil = new \App\Controllers\AccueilController();
        $accueil->index();
        exit;
    }

    // Inscription d’un nouvel utilisateur
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm'] ?? '';

            // Si les mots de passe ne correspondent pas, je bloque
            if ($password !== $confirm) {
                View::render('authentification/registerView', [
                    'erreur' => "Les mots de passe ne correspondent pas.",
                    'nom' => $nom,
                    'email' => $email
                ]);
                return;
            }

            // Je valide la sécurité du mot de passe
            $erreurs = PasswordHelper::isValid($password);
            if (!empty($erreurs)) {
                View::render('authentification/registerView', [
                    'erreur' => implode('<br>', $erreurs),
                    'nom' => $nom,
                    'email' => $email
                ]);
                return;
            }

            // Je vérifie si l'email est déjà utilisé
            $utilisateur = new Utilisateur();
            $existant = $utilisateur->getByEmail($email);

            if ($existant) {
                View::render('authentification/registerView', [
                    'erreur' => "Cet email est déjà utilisé.",
                    'nom' => $nom,
                    'email' => $email
                ]);
                return;
            }

            // Hash du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Ajout en base
            $ajout = $utilisateur->add($nom, $email, $hashedPassword);

            if ($ajout) {
                // Je récupère l’ID nouvellement inséré
                $id = Database::getInstance()->lastInsertId();

                // Connexion immédiate après inscription
                $_SESSION['user'] = [
                    'id' => $id,
                    'nom' => $nom,
                    'email' => $email,
                    'role' => 'utilisateur'
                ];

                // Affiche la page d’accueil directement
                View::render('accueil/indexView');
                exit;
            } else {
                // En cas d’échec à l’insertion
                View::render('authentification/registerView', [
                    'erreur' => "Erreur lors de l'inscription.",
                    'nom' => $nom,
                    'email' => $email
                ]);
                return;
            }
        }

        // Si la méthode est GET → j’affiche un formulaire d’inscription vide
        View::render('authentification/registerView', [
            'erreur' => null,
            'nom' => '',
            'email' => ''
        ]);
    }

    // Simple redirection vers la page de connexion
    public function index(): void
    {
        View::render('authentification/loginView', ['erreur' => null]);
        exit;
    }
}
