<?php
// app/Core/View.php

namespace App\Core;

class View
{
    public static function render(string $template, array $data = []): void
    {
        extract($data);

        // Utilisation de Config::getRoot() pour obtenir la racine du projet
        $viewPath = \App\Core\Config::getRoot() . '/app/views/' . $template . '.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Délégation propre à ErrorHandler pour gérer l'erreur
            ErrorHandler::renderError("La vue demandée ($template) est introuvable.");
        }
    }

    public static function renderPartial(string $template, array $data = []): void
    {
        extract($data);

        // Même logique que render() pour obtenir le chemin complet
        $viewPath = \App\Core\Config::getRoot() . '/app/views/' . $template . '.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Même gestion d’erreur élégante
            ErrorHandler::renderError("La vue partielle demandée ($template) est introuvable.");
        }
    }
}
