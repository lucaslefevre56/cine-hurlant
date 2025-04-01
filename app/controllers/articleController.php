<?php
// app/controllers/ArticleController.php

namespace App\Controllers;

// J’importe le modèle Article pour interagir avec la BDD
use App\Models\Article; 

// J’importe la fonction qui affiche une erreur 404 personnalisée
use function App\Helpers\render404; 

// -----------------------------------------------------------
// CONTRÔLEUR ARTICLE – GÈRE LA CONSULTATION DES ARTICLES
// -----------------------------------------------------------
// Il fait le lien entre :
//  → le modèle Article (app/models/Article.php)
//  → les vues (listeArticlesView.php, ficheArticleView.php)
// Ce contrôleur est accessible à tous (visiteurs, utilisateurs)
// Objectifs :
// - Afficher la liste des articles disponibles
// - Afficher une fiche article complète à partir de son ID
// -----------------------------------------------------------

class ArticleController {

    // Méthode appelée quand l’URL est /articles/liste
    public function liste()
    {
        // 1. Je crée une instance du modèle Article
        $article = new Article();

        // 2. Je récupère tous les articles de la base (titre, auteur, extrait, œuvre liée…)
        $articles = $article->getAll();

        // 3. J’inclus la vue correspondante pour afficher les articles
        require_once ROOT . '/app/views/articles/listeArticlesView.php';
    }

    // Méthode appelée quand l’URL est /articles/fiche/:id
    public function fiche($id)
    {
        // 1. Je crée une instance du modèle Article
        $articleModel = new Article();

        // 2. Je récupère les infos complètes de l’article demandé
        $article = $articleModel->getById($id);

        // 3. Si aucun article ne correspond à l’ID, je déclenche une erreur 404 personnalisée
        if (!$article) {
            render404("Article introuvable");
            return;
        }

        // 4. Je récupère les œuvres liées à cet article
        $oeuvres = $articleModel->getOeuvresByArticle($id);

        // 5. Je récupère l'URL de la vidéo si elle existe
        $video_url = $article['video_url'] ?? null;  // On récupère l'URL de la vidéo associée, si présente

        // 6. Si tout est OK, j’affiche la fiche détaillée de l’article
        require_once ROOT . '/app/views/articles/ficheArticleView.php';
    }

    public function index()
    {
        // Redirection vers la liste des articles
        header('Location: /cine-hurlant/public/article/liste');
        exit;
    }
}
