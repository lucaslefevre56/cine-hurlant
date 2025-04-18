<?php
// app/Helpers/PasswordHelper.php

namespace App\Helpers;

class PasswordHelper
{
    // Méthode appelée pour vérifier si un mot de passe respecte nos critères de sécurité
    // Elle renvoie un tableau d’erreurs (vide si le mot de passe est valide)
    public static function isValid(string $password): array
    {
        // Je prépare un tableau pour stocker les messages d’erreur éventuels
        $erreurs = [];

        // Première règle : longueur minimale
        // Je vérifie que le mot de passe contient au moins 8 caractères
        if (strlen($password) < 8) {
            $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }

        // Deuxième règle : au moins une lettre majuscule (A-Z)
        if (!preg_match('/[A-Z]/', $password)) {
            $erreurs[] = "Le mot de passe doit contenir au moins une majuscule.";
        }

        // Troisième règle : au moins une lettre minuscule (a-z)
        if (!preg_match('/[a-z]/', $password)) {
            $erreurs[] = "Le mot de passe doit contenir au moins une minuscule.";
        }

        // Quatrième règle : au moins un chiffre (0-9)
        if (!preg_match('/[0-9]/', $password)) {
            $erreurs[] = "Le mot de passe doit contenir au moins un chiffre.";
        }

        // Cinquième règle : au moins un caractère spécial (n’importe quel symbole non alphanumérique)
        // La classe \W capte tous les caractères qui ne sont ni lettre ni chiffre (avec _ inclus)
        if (!preg_match('/[\W_]/', $password)) {
            $erreurs[] = "Le mot de passe doit contenir au moins un caractère spécial.";
        }

        // Je retourne le tableau des erreurs (s’il est vide, le mot de passe est valide)
        return $erreurs;
    }
}
