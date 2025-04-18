<?php
// app/Controllers/UtilisateurController.php

namespace App\Controllers;

use App\Core\View;
use App\Models\Utilisateur;
use App\Helpers\AuthHelper;
use App\Helpers\PasswordHelper;

class UtilisateurController
{
    // Affichage de la page "Mon profil"
    public function profil(): void
    {
        // Je vérifie si l’utilisateur est connecté
        if (!AuthHelper::isLoggedIn()) {
            // S’il ne l’est pas, je le renvoie à la page de connexion avec un message
            View::render('auth/loginView', [
                'erreur' => 'Vous devez être connecté pour accéder à votre profil.'
            ]);
            return;
        }

        // Je récupère les infos de l’utilisateur connecté via la session
        $id = $_SESSION['user']['id'];
        $utilisateurModel = new Utilisateur();
        $utilisateur = $utilisateurModel->getById($id);

        // Si pour une raison quelconque il n’existe pas (bizarre, mais je gère le cas)
        if (!$utilisateur) {
            View::render('accueil/indexView', [
                'erreur' => 'Utilisateur introuvable.'
            ]);
            return;
        }

        // Sinon j’affiche la vue du profil avec ses infos
        View::render('utilisateur/profilView', compact('utilisateur'));
    }

    // Traitement de la suppression du compte utilisateur (désactivation logique)
    public function supprimer(): void
    {
        // Je vérifie d’abord si l’utilisateur est bien connecté
        if (!AuthHelper::isLoggedIn()) {
            View::render('auth/loginView', ['erreur' => 'Connexion requise']);
            return;
        }

        // Je bloque toute tentative si ce n’est pas une requête POST
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

        // Par sécurité, j’empêche la désactivation d’un compte admin
        if ($role === 'admin') {
            View::render('utilisateur/profilView', [
                'utilisateur' => $utilisateurModel->getById($id),
                'erreur' => "Un compte administrateur ne peut pas se désactiver lui-même pour éviter de bloquer l'accès à l'administration."
            ]);
            return;
        }

        // Ancien fonctionnement : suppression physique du compte (désactivé volontairement)
        // $utilisateurModel->deleteById($id);

        // Nouveau fonctionnement : désactivation logique (champ "actif" à 0)
        $ok = $utilisateurModel->desactiver($id);

        // Je prépare le message à afficher après redirection
        $message = $ok
            ? "Votre compte a été désactivé avec succès. Vous pouvez le réactiver en contactant l’administrateur."
            : "Une erreur est survenue lors de la désactivation du compte.";

        // Je nettoie la session pour forcer la déconnexion
        $_SESSION = [];
        session_destroy();

        // Je redémarre une session pour stocker le message
        session_start();
        $_SESSION['message_suppression'] = $message;

        // Redirection vers la page d’accueil
        header("Location: " . BASE_URL . "/accueil/index");
        exit;
    }

    // Modification du mot de passe utilisateur
    public function modifierMotDePasse(): void
    {
        // Je vérifie que l’utilisateur est bien connecté
        if (!AuthHelper::isLoggedIn()) {
            View::render('auth/loginView', ['erreur' => 'Connexion requise']);
            return;
        }

        $erreur = null;
        $confirmation = null;

        // Je traite uniquement les requêtes POST (soumission du formulaire)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $motDePasseActuel = $_POST['ancien_password'] ?? '';
            $nouveauPassword = $_POST['nouveau_password'] ?? '';
            $confirmationPassword = $_POST['confirmation_password'] ?? '';

            // Je vérifie que tous les champs sont remplis
            if (empty($motDePasseActuel) || empty($nouveauPassword) || empty($confirmationPassword)) {
                $erreur = "Tous les champs sont obligatoires.";
            } elseif ($nouveauPassword !== $confirmationPassword) {
                $erreur = "Les mots de passe ne correspondent pas.";
            } else {
                // Je valide la complexité du nouveau mot de passe
                $erreurs = PasswordHelper::isValid($nouveauPassword);

                if (!empty($erreurs)) {
                    $erreur = implode('<br>', $erreurs);
                } else {
                    $utilisateurModel = new Utilisateur();
                    $utilisateur = $utilisateurModel->getById($_SESSION['user']['id']);

                    // Je vérifie que l’ancien mot de passe est bien correct
                    if (!password_verify($motDePasseActuel, $utilisateur['password'])) {
                        $erreur = "L'ancien mot de passe est incorrect.";
                    } else {
                        // Tout est bon → je hash le nouveau mot de passe
                        $hash = password_hash($nouveauPassword, PASSWORD_DEFAULT);

                        // Et je le mets à jour en base
                        $utilisateurModel->updatePassword($utilisateur['id_utilisateur'], $hash);
                        $confirmation = "Mot de passe mis à jour avec succès.";
                    }
                }
            }
        }

        // Je renvoie la vue avec soit un message d'erreur, soit une confirmation
        View::render('utilisateur/modifierMotDePasseView', compact('erreur', 'confirmation'));
    }
}
