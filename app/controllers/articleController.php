<?php
// app/Controllers/ArticleController.php

namespace App\Controllers;

use App\Models\Article;
use App\Models\Commentaire;
use App\Core\View;
use App\Core\ErrorHandler;

class ArticleController
{
    // Cette méthode affiche la liste paginée des articles
    public function liste(): void
    {
        // Je récupère la page actuelle depuis l'URL, en forçant à 1 minimum
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        // Nombre d'articles par page
        $parPage = 6;
        $offset = ($page - 1) * $parPage;

        $modele = new Article();
        $articles = $modele->getPaginated($parPage, $offset);
        $total = $modele->countAll();
        $totalPages = ceil($total / $parPage);

        // J'affiche la vue avec les articles et la pagination
        View::render('articles/listeArticlesView', [
            'articles' => $articles,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    // Cette méthode affiche la fiche détaillée d'un article
    public function fiche(int $id): void
    {
        $articleModel = new Article();
        $article = $articleModel->getById($id);

        // Si l'article est introuvable, j'affiche une 404 personnalisée
        if (!$article) {
            ErrorHandler::render404("Article introuvable");
        }

        // Je récupère les œuvres liées à cet article
        $oeuvres = $articleModel->getOeuvresByArticle($id);

        // Je récupère l'URL de la vidéo si elle existe
        $video_url = $article['video_url'] ?? null;

        // Je récupère les commentaires associés à cet article
        $commentaireModel = new Commentaire();
        $commentaires = $commentaireModel->getByArticle($id);

        // J'affiche la vue avec toutes les données de la fiche
        View::render('articles/ficheArticleView', [
            'article' => $article,
            'oeuvres' => $oeuvres,
            'video_url' => $video_url,
            'commentaires' => $commentaires
        ]);
    }

    // Cette méthode redirige vers la liste des articles
    public function index(): void
    {
        // Elle me permet de garder une route propre /article
        $this->liste();
    }
}
