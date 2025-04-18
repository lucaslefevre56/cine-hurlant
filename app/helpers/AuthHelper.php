<?php
// app/Helpers/AuthHelper.php

namespace App\Helpers;

class AuthHelper
{
    // Je vérifie si un utilisateur est connecté (présent en session avec des infos de base)
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user'])                         // La clé 'user' existe en session
            && is_array($_SESSION['user'])                      // C’est bien un tableau (protection supplémentaire)
            && !empty($_SESSION['user']['id'])                  // Il a un ID valide
            && !empty($_SESSION['user']['nom'])                 // Son nom est renseigné
            && !empty($_SESSION['user']['email']);              // Son email aussi (utile pour certaines vérifs)
    }

    // Je vérifie si l’utilisateur est un administrateur
    public static function isUserAdmin(): bool
    {
        return self::hasRole('admin');
    }

    // Je vérifie si l’utilisateur est un rédacteur (ou admin, qui a les mêmes droits ici)
    public static function isUserRedacteur(): bool
    {
        return self::hasAnyRole(['redacteur', 'admin']);
    }

    // Je vérifie si l’utilisateur est un simple visiteur enregistré (ni rédacteur ni admin)
    public static function isUser(): bool
    {
        return self::hasRole('utilisateur');
    }

    // Méthode générique : je vérifie si le rôle en session fait partie d’une liste autorisée
    public static function hasAnyRole(array $roles): bool
    {
        return isset($_SESSION['user']['role']) && in_array($_SESSION['user']['role'], $roles);
    }

    // Méthode privée : je vérifie si l’utilisateur a exactement un rôle donné
    private static function hasRole(string $role): bool
    {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
    }
}
