<?php
// app/Helpers/PasswordHelper.php

namespace App\Helpers;

class PasswordHelper
{
    public static function isValid(string $password): array
    {
        $erreurs = [];

        if (strlen($password) < 8) {
            $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $erreurs[] = "Le mot de passe doit contenir au moins une majuscule.";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $erreurs[] = "Le mot de passe doit contenir au moins une minuscule.";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $erreurs[] = "Le mot de passe doit contenir au moins un chiffre.";
        }

        if (!preg_match('/[\W_]/', $password)) {
            $erreurs[] = "Le mot de passe doit contenir au moins un caractère spécial.";
        }

        return $erreurs;
    }
}
