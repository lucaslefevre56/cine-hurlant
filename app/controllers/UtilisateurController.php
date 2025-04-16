<?php
// app/Controllers/UtilisateurController.php

namespace App\Controllers;

use App\Core\View;
use App\Models\Utilisateur;
use App\Helpers\AuthHelper;
use App\Helpers\PasswordHelper;


class UtilisateurController
{
    public function profil(): void
    {
        if (!AuthHelper::isLoggedIn()) {
            View::render('auth/loginView', [
                'erreur' => 'Vous devez être connecté pour accéder à votre profil.'
            ]);
            return;
        }

        $id = $_SESSION['user']['id'];
        $utilisateurModel = new Utilisateur();
        $utilisateur = $utilisateurModel->getById($id);

        if (!$utilisateur) {
            View::render('accueil/indexView', [
                'erreur' => 'Utilisateur introuvable.'
            ]);
            return;
        }

        View::render('utilisateur/profilView', compact('utilisateur'));
    }

    public function supprimer(): void
    {
        if (!AuthHelper::isLoggedIn()) {
            View::render('auth/loginView', ['erreur' => 'Connexion requise']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $id = $_SESSION['user']['id'];
            $utilisateurModel = new Utilisateur();
            View::render('utilisateur/profilView', [
                'utilisateur' => $utilisateurModel->getById($id),
                'erreur' => "Accès non autorisé. Cette action doit être envoyée par un formulaire sécurisé."
            ]);
            return;
        }

        $id = $_SESSION['user']['id'];
        $role = $_SESSION['user']['role'];
        $utilisateurModel = new Utilisateur();

        if ($role === 'admin') {
            View::render('utilisateur/profilView', [
                'utilisateur' => $utilisateurModel->getById($id),
                'erreur' => "Un compte administrateur ne peut pas se désactiver lui-même pour éviter de bloquer l'accès à l'administration."
            ]);
            return;
        }

        // ❌ Ancienne suppression physique (à éviter)
        // $utilisateurModel->deleteById($id);

        // ✅ Nouvelle désactivation logique
        $ok = $utilisateurModel->desactiver($id);

        $message = $ok
            ? "Votre compte a été désactivé avec succès. Vous pouvez le réactiver en contactant l’administrateur."
            : "Une erreur est survenue lors de la désactivation du compte.";

        // Nettoyage complet de la session
        $_SESSION = [];
        session_destroy();

        // Redémarrage de session pour message flash
        session_start();
        $_SESSION['message_suppression'] = $message;

        header("Location: " . BASE_URL . "/accueil/index");
        exit;
    }

    public function modifierMotDePasse(): void
    {
        if (!AuthHelper::isLoggedIn()) {
            View::render('auth/loginView', ['erreur' => 'Connexion requise']);
            return;
        }

        $erreur = null;
        $confirmation = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $motDePasseActuel = $_POST['ancien_password'] ?? '';
            $nouveauPassword = $_POST['nouveau_password'] ?? '';
            $confirmationPassword = $_POST['confirmation_password'] ?? '';

            if (empty($motDePasseActuel) || empty($nouveauPassword) || empty($confirmationPassword)) {
                $erreur = "Tous les champs sont obligatoires.";
            } elseif ($nouveauPassword !== $confirmationPassword) {
                $erreur = "Les mots de passe ne correspondent pas.";
            } else {
                $erreurs = PasswordHelper::isValid($nouveauPassword);

                if (!empty($erreurs)) {
                    $erreur = implode('<br>', $erreurs);
                } else {
                    $utilisateurModel = new Utilisateur();
                    $utilisateur = $utilisateurModel->getById($_SESSION['user']['id']);

                    // Vérification de l'ancien mot de passe
                    if (!password_verify($motDePasseActuel, $utilisateur['password'])) {
                        $erreur = "L'ancien mot de passe est incorrect.";
                    } else {
                        // Hash du nouveau mot de passe
                        $hash = password_hash($nouveauPassword, PASSWORD_DEFAULT);

                        // Mise à jour
                        $utilisateurModel->updatePassword($utilisateur['id_utilisateur'], $hash);
                        $confirmation = "Mot de passe mis à jour avec succès.";
                    }
                }
            }
        }

        View::render('utilisateur/modifierMotDePasseView', compact('erreur', 'confirmation'));
    }
}
