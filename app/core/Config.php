<?php
// app/Core/Config.php
namespace App\Core;

class Config
{
    public static function getBaseUrl(): string
    {
        // On définit le protocole selon HTTPS ou HTTP
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        
        // Retourne l'URL complète de base
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . '/cine-hurlant';
    }

    public static function getRoot(): string
    {
        // Retourne le répertoire racine du projet
        return __DIR__ . '/../..';  // Calcul du répertoire racine
    }
}
