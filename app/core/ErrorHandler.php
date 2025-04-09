<?php
// app/Core/ErrorHandler.php
namespace App\Core;

class ErrorHandler
{
    public static function render404(string $message = 'Page non trouvée')
    {
        http_response_code(404);
        View::render('erreur/404', ['erreur' => $message]);
        exit;
    }

    public static function renderError(string $message = 'Une erreur est survenue')
    {
        http_response_code(500);
        View::render('erreur/error', ['erreur' => $message]);
        exit;
    }
}
