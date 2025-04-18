<?php
// app/Controllers/AccueilController.php

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;
use App\Models\Oeuvre;

class AccueilController
{
    // Méthode principale appelée quand on accède à la racine du site
    public function index(): void
    {
        // Je crée mes modèles pour aller chercher des articles et des œuvres
        $articleModel = new Article();
        $oeuvreModel = new Oeuvre();

        // Je récupère les 3 derniers articles pour les mettre en avant dans un slider (carrousel)
        // Ici je prends la page 1 (offset 0) avec 3 articles
        $articlesRecents = $articleModel->getPaginated(3, 0);

        // Pour la partie "Suggestions", je tire au sort si je propose des œuvres ou des articles
        // Ça ajoute de la variété à l’accueil
        $choix = rand(0, 1); // 0 = œuvres, 1 = articles

        if ($choix === 0) {
            // Si le tirage donne 0 → je propose 3 œuvres aléatoires
            $suggestions = $oeuvreModel->getRandom(3);
            $typeSuggestions = 'oeuvre';
        } else {
            // Sinon → je propose 3 articles aléatoires
            $suggestions = $articleModel->getRandom(3);
            $typeSuggestions = 'article';
        }

        // Je passe toutes les données à la vue d'accueil
        View::render('accueil/indexView', [
            'articlesRecents' => $articlesRecents,      // Les articles du slider
            'suggestions' => $suggestions,              // Les suggestions du bloc de droite
            'typeSuggestions' => $typeSuggestions       // Pour que la vue sache ce qu’elle affiche
        ]);
    }
}
