<?php
// app/Core/ErrorHandler.php

namespace App\Core;

class ErrorHandler
{
    // Si j’arrive sur une page qui n’existe pas ou une mauvaise URL,
    // j’envoie un code 404 + j’affiche une vue propre avec un message
    public static function render404(string $message = 'Page non trouvée')
    {
        http_response_code(404); // Je précise au navigateur (et à Google) que la page n’existe pas
        View::render('erreur/404', ['erreur' => $message]); // J’affiche une vraie vue HTML avec le message
        exit; // Je stoppe le script pour être sûr que rien ne s’exécute derrière
    }

    // Pour toutes les autres erreurs (ex: SQL, fichier manquant, bug PHP)
    public static function renderError(string $message = 'Une erreur est survenue')
    {
        http_response_code(500); // Erreur serveur
        View::render('erreur/error', ['erreur' => $message]); // J’affiche une vue d’erreur personnalisée
        exit;
    }
}
