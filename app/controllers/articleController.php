<?php
// app/Controllers/ArticleController.php

namespace App\Controllers;

use App\Models\Article;
use App\Models\Commentaire;
use App\Core\View;
use App\Core\ErrorHandler;

class ArticleController
{
    public function liste(): void
    {
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $parPage = 6;
        $offset = ($page - 1) * $parPage;

        $modele = new Article();
        $articles = $modele->getPaginated($parPage, $offset);
        $total = $modele->countAll();
        $totalPages = ceil($total / $parPage);

        View::render('articles/listeArticlesView', [
            'articles' => $articles,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function fiche(int $id): void
    {
        $articleModel = new Article();
        $article = $articleModel->getById($id);

        if (!$article) {
            ErrorHandler::render404("Article introuvable");
        }

        $oeuvres = $articleModel->getOeuvresByArticle($id);
        $video_url = $article['video_url'] ?? null;

        $commentaireModel = new Commentaire();
        $commentaires = $commentaireModel->getByArticle($id);

        View::render('articles/ficheArticleView', [
            'article' => $article,
            'oeuvres' => $oeuvres,
            'video_url' => $video_url,
            'commentaires' => $commentaires
        ]);
    }

    public function index(): void
    {
        $this->liste();
    }
}
