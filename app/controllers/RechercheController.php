<?php
// app/Controllers/RechercheController.php

namespace App\Controllers;

use App\Models\Article;
use App\Models\Oeuvre;
use App\Core\View;

class RechercheController
{
    public function index(): void
    {
        $query = trim($_GET['q'] ?? '');
        $type = trim($_GET['type'] ?? '');

        $oeuvres = [];
        $articles = [];

        if ($query !== '') {
            if ($type === 'oeuvre') {
                $oeuvres = Oeuvre::searchByTitleOrAuthor($query);
            } elseif ($type === 'article') {
                $articles = Article::searchByTitleOrAuthor($query);
            } else {
                $oeuvres = Oeuvre::searchByTitleOrAuthor($query);
                $articles = Article::searchByTitleOrAuthor($query);
            }
        }

        View::render('recherche/resultatsView', [
            'q' => $query,
            'oeuvres' => $oeuvres,
            'articles' => $articles
        ]);
    }
}
