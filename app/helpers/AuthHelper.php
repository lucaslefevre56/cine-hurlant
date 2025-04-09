<?php
// app/Helpers/AuthHelper.php

namespace App\Helpers;

class AuthHelper
{
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function isUserAdmin(): bool
    {
        return self::hasRole('admin');
    }

    public static function isUserRedacteur(): bool
    {
        return self::hasAnyRole(['redacteur', 'admin']);
    }

    public static function isUser(): bool
    {
        return self::hasRole('utilisateur');
    }

    public static function hasAnyRole(array $roles): bool
    {
        return isset($_SESSION['user']['role']) && in_array($_SESSION['user']['role'], $roles);
    }

    private static function hasRole(string $role): bool
    {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
    }
}
