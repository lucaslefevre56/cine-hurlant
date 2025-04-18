<?php
// app/Controllers/RechercheController.php

namespace App\Controllers;

use App\Models\Article;
use App\Models\Oeuvre;
use App\Core\View;

class RechercheController
{
    // Méthode principale appelée quand on effectue une recherche via la barre de recherche
    public function index(): void
    {
        // Je récupère le mot-clé recherché (trim enlève les espaces en trop)
        $query = trim($_GET['q'] ?? '');

        // Je récupère le type de recherche (article, œuvre ou les deux si vide)
        $type = trim($_GET['type'] ?? '');

        // Je prépare mes tableaux de résultats
        $oeuvres = [];
        $articles = [];

        // Si la requête n’est pas vide, je lance la recherche
        if ($query !== '') {
            if ($type === 'oeuvre') {
                // Si l’utilisateur a précisé qu’il cherche une œuvre uniquement
                $oeuvres = Oeuvre::searchByTitleOrAuthor($query);
            } elseif ($type === 'article') {
                // Si la recherche concerne uniquement les articles
                $articles = Article::searchByTitleOrAuthor($query);
            } else {
                // Si aucun type n’est précisé, je cherche dans les deux
                $oeuvres = Oeuvre::searchByTitleOrAuthor($query);
                $articles = Article::searchByTitleOrAuthor($query);
            }
        }

        // Je passe les résultats à la vue pour affichage
        View::render('recherche/resultatsView', [
            'q' => $query,             // Le mot-clé recherché (pour l’afficher dans la vue)
            'oeuvres' => $oeuvres,     // Les œuvres trouvées (si applicable)
            'articles' => $articles    // Les articles trouvés (si applicable)
        ]);
    }
}
