<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\ErrorHandler;
use App\Models\Utilisateur;
use App\Helpers\AuthHelper;

class AdminController
{
    public function changerRole(): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit.");
        }

        if (!isset($_POST['id_utilisateur'], $_POST['role'])) {
            $_SESSION['message'] = "Données manquantes.";
            $this->utilisateurs();
            return;
        }

        $id = (int) $_POST['id_utilisateur'];
        $nouveauRole = $_POST['role'];

        if ($id === $_SESSION['user']['id']) {
            $_SESSION['message'] = "Vous ne pouvez pas modifier votre propre rôle. What a shame...";
            $this->utilisateurs();
            return;
        }

        $rolesAutorises = ['utilisateur', 'redacteur', 'admin'];
        if (!in_array($nouveauRole, $rolesAutorises)) {
            $_SESSION['message'] = "Rôle invalide.";
            $this->utilisateurs();
            return;
        }

        $utilisateurModel = new Utilisateur();
        $success = $utilisateurModel->updateRole($id, $nouveauRole);

        $_SESSION['message'] = $success
            ? "Rôle mis à jour avec succès."
            : "Échec de la mise à jour.";

        $this->utilisateurs();
    }

    public function utilisateurs(): void
    {
        if (!AuthHelper::isUserAdmin()) {
            ErrorHandler::render404("Accès interdit : réservé aux administrateurs.");
        }

        $utilisateurModel = new Utilisateur();
        $utilisateurs = $utilisateurModel->getAll();

        View::render('admin/listeUtilisateursView', [
            'utilisateurs' => $utilisateurs
        ]);
    }
}
