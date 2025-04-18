<?php
// app/Core/View.php

namespace App\Core;

class View
{
    // Méthode pour afficher une vue complète (avec header, footer, etc.)
    public static function render(string $template, array $data = []): void
    {
        extract($data); // Je récupère mes variables passées depuis le contrôleur (ex: $titre, $oeuvre…)

        // Je construis le chemin complet du fichier vue à charger (depuis la racine du projet)
        $viewPath = \App\Core\Config::getRoot() . '/app/views/' . $template . '.php';

        // Si le fichier existe, je l'inclus
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Sinon je déclenche une erreur propre
            ErrorHandler::renderError("La vue demandée ($template) est introuvable.");
        }
    }

    // Variante pour les vues partielles (incluses via AJAX ou dans des blocs dynamiques)
    public static function renderPartial(string $template, array $data = []): void
    {
        extract($data); // Pareil, j’importe les variables en local

        // Même construction de chemin
        $viewPath = \App\Core\Config::getRoot() . '/app/views/' . $template . '.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Je déclenche une erreur personnalisée si la vue n’existe pas
            ErrorHandler::renderError("La vue partielle demandée ($template) est introuvable.");
        }
    }
}
