<?php
// app/Helpers/AuthHelper.php

// J'indique le namespace App\Helpers pour que Composer sache où le trouver
namespace App\Helpers;

// J'importe la classe Utilisateur (modèle) pour vérifier les rôles en base
use App\Models\Utilisateur;

// --------------------------------------------------------------
// Classe AuthHelper – contient des méthodes pour gérer les rôles
// --------------------------------------------------------------
// Je l’utilise dans tout le site pour vérifier si un utilisateur
// est connecté, s’il est admin ou rédacteur.
// Toutes les méthodes sont statiques, donc je n’ai pas besoin
// d’instancier cette classe pour les utiliser.
// --------------------------------------------------------------

class AuthHelper
{
    // Je vérifie si quelqu’un est connecté en regardant
    // si la clé 'user' existe dans la session
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    // Je vérifie si l’utilisateur connecté est un administrateur
    // Si personne n’est connecté, je renvoie false
    // Sinon, je regarde son rôle en base via le modèle
    public static function isUserAdmin(): bool
    {
        // Si personne n’est connecté → accès interdit
        if (!self::isLoggedIn()) {
            return false;
        }

        // Je récupère l’ID de l’utilisateur stocké en session
        $id = $_SESSION['user']['id'];

        // Je crée une instance du modèle pour interroger la base
        $utilisateur = new Utilisateur($GLOBALS['conn']);

        // Et je retourne true ou false selon son rôle réel
        return $utilisateur->isAdmin($id);
    }

    // Je vérifie si l’utilisateur est un rédacteur
    // Même principe que pour admin, mais avec un autre rôle
    public static function isUserRedacteur(): bool
    {
        // Si l'utilisateur n'est pas connecté → refus
        if (!self::isLoggedIn()) {
            return false;
        }

        // Récupération de l’ID depuis la session
        $id = $_SESSION['user']['id'];

        // Vérification en base avec le modèle
        $utilisateur = new Utilisateur($GLOBALS['conn']);
        return $utilisateur->isRedacteur($id);
    }
}
