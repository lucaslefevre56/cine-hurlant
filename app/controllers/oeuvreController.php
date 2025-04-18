<?php
// app/Controllers/OeuvreController.php

namespace App\Controllers;

use App\Models\Oeuvre;
use App\Core\View;
use App\Core\ErrorHandler;

class OeuvreController
{
    // Cette méthode permet d'afficher la fiche détaillée d'une œuvre
    public function fiche(int $id): void
    {
        $oeuvreModel = new Oeuvre();
        $oeuvre = $oeuvreModel->getById($id);

        // Si l'œuvre n'existe pas, j'affiche une erreur 404 personnalisée
        if (!$oeuvre) {
            ErrorHandler::render404("Œuvre introuvable");
        }

        // Je récupère les genres associés à cette œuvre
        $genres = $oeuvreModel->getGenresByOeuvre($id);

        // Je récupère aussi la vidéo si elle est présente
        $video_url = $oeuvre['video_url'] ?? null;

        // Et j'affiche la vue avec toutes ces infos
        View::render('oeuvres/ficheOeuvreView', [
            'oeuvre' => $oeuvre,
            'genres' => $genres,
            'video_url' => $video_url
        ]);
    }

    // Cette méthode permet d'afficher toutes les œuvres, avec pagination
    public function liste(): void
    {
        // Je détermine la page actuelle (au minimum 1)
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        // Je fixe le nombre d'œuvres par page
        $parPage = 6;
        $offset = ($page - 1) * $parPage;

        $modele = new Oeuvre();
        $oeuvres = $modele->getPaginated($parPage, $offset);
        $total = $modele->countAll();
        $totalPages = ceil($total / $parPage);

        // Je sépare les œuvres en deux catégories : films et BD, pour les onglets
        $films = array_filter($oeuvres, fn($o) => strtolower($o['nom']) === 'film');
        $bds   = array_filter($oeuvres, fn($o) => strtolower($o['nom']) === 'bd');

        // Et j'affiche la vue avec toutes les données nécessaires
        View::render('oeuvres/listeOeuvresView', [
            'films' => $films,
            'bds' => $bds,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    // Cette méthode est l'entrée principale du contrôleur : elle redirige vers la liste
    public function index(): void
    {
        // Pratique pour garder une route propre type /oeuvre
        $this->liste();
    }
}
