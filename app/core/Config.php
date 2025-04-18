<?php
// app/Core/Config.php

namespace App\Core; // J'organise ma classe dans le namespace App\Core, comme tous mes fichiers "noyau"

class Config
{
    public static function getBaseUrl(): string
    {
        // Je détecte le protocole utilisé (http ou https) pour construire une URL correcte
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

        // Je retourne l'URL de base de mon site (en dur ici pour que ça fonctionne sur mon hébergement)
        // Exemple : https://stagiaires-kercode9.greta-bretagne-sud.org/lucas-lefevre/cine-hurlant/
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . '/cine-hurlant';
    }

    public static function getRoot(): string
    {
        // Je retourne le chemin absolu vers la racine de mon projet sur le serveur
        // Très utile pour inclure des fichiers sans galérer avec des chemins relatifs
        // Je remonte depuis /app/Core jusqu’à la racine du projet
        return __DIR__ . '/../..'; 
    }
}
