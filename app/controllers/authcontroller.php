<?php
// app/Controllers/AuthController.php

namespace App\Controllers;

use App\Models\Utilisateur;
use App\Core\View;
use App\Core\Database;
use App\Helpers\PasswordHelper;


class AuthController
{
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Cherche l'utilisateur par email
            $utilisateur = new Utilisateur();
            $user = $utilisateur->getByEmail($email);  // Il n'est trouvé que si actif = 1

            // Si l'utilisateur n'existe pas ou mot de passe incorrect
            if (!$user || !password_verify($password, $user['password'])) {
                View::render('authentification/loginView', ['erreur' => "Email ou mot de passe incorrect."]);
                return;
            }

            // Si l'utilisateur est trouvé et actif, création de la session
            $_SESSION['user'] = [
                'id' => $user['id_utilisateur'],
                'nom' => $user['nom'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            // Redirection vers la page d'accueil
            View::render('accueil/indexView');
            exit;
        }

        // Si la requête est GET, on affiche le formulaire de connexion sans erreur
        View::render('authentification/loginView', ['erreur' => null]);
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_unset();
        session_destroy();

        View::render('accueil/indexView');
        exit;
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm'] ?? '';

            // Vérif confirmation
            if ($password !== $confirm) {
                View::render('authentification/registerView', [
                    'erreur' => "Les mots de passe ne correspondent pas.",
                    'nom' => $nom,
                    'email' => $email
                ]);
                return;
            }

            // Vérif sécurité du mot de passe
            $erreurs = PasswordHelper::isValid($password);
            if (!empty($erreurs)) {
                View::render('authentification/registerView', [
                    'erreur' => implode('<br>', $erreurs),
                    'nom' => $nom,
                    'email' => $email
                ]);
                return;
            }

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

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $ajout = $utilisateur->add($nom, $email, $hashedPassword);

            if ($ajout) {
                $id = Database::getInstance()->lastInsertId();

                $_SESSION['user'] = [
                    'id' => $id,
                    'nom' => $nom,
                    'email' => $email,
                    'role' => 'utilisateur'
                ];

                View::render('accueil/indexView');
                exit;
            } else {
                View::render('authentification/registerView', [
                    'erreur' => "Erreur lors de l'inscription.",
                    'nom' => $nom,
                    'email' => $email
                ]);
                return;
            }
        }

        View::render('authentification/registerView', [
            'erreur' => null,
            'nom' => '',
            'email' => ''
        ]);
    }

    public function index(): void
    {
        View::render('authentification/loginView', ['erreur' => null]);
        exit;
    }
}
