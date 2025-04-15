<?php
// app/Controllers/AccueilController.php

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;
use App\Models\Oeuvre;

class AccueilController
{
    public function index(): void
    {
        $articleModel = new Article();
        $oeuvreModel = new Oeuvre();

        // Récupération des 3 derniers articles pour le slider
        $articlesRecents = $articleModel->getPaginated(3, 0);

        // Suggestions aléatoires : soit 3 œuvres, soit 3 articles
        $choix = rand(0, 1);
        if ($choix === 0) {
            $suggestions = $oeuvreModel->getRandom(3);
            $typeSuggestions = 'oeuvre';
        } else {
            $suggestions = $articleModel->getRandom(3);
            $typeSuggestions = 'article';
        }

        View::render('accueil/indexView', [
            'articlesRecents' => $articlesRecents,
            'suggestions' => $suggestions,
            'typeSuggestions' => $typeSuggestions
        ]);
    }
}
