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

    // Cette méthode permet d'afficher toutes les œuvres avec double pagination
    public function liste(): void
    {
        // Je récupère les numéros de page des deux onglets (films et BD)
        $pageFilms = isset($_GET['pageFilms']) ? max(1, (int) $_GET['pageFilms']) : 1;
        $pageBD    = isset($_GET['pageBD'])    ? max(1, (int) $_GET['pageBD'])    : 1;

        $parPage = 6;

        $offsetFilms = ($pageFilms - 1) * $parPage;
        $offsetBD    = ($pageBD - 1) * $parPage;

        $modele = new Oeuvre();

        // Je récupère les œuvres de chaque type avec pagination dédiée
        $films = $modele->getPaginatedByType('film', $parPage, $offsetFilms);
        $bds   = $modele->getPaginatedByType('bd', $parPage, $offsetBD);

        // J’ajoute les genres à chaque œuvre
        foreach ($films as &$oeuvre) {
            $oeuvre['genres'] = $modele->getGenresByOeuvre($oeuvre['id_oeuvre']);
        }
        foreach ($bds as &$oeuvre) {
            $oeuvre['genres'] = $modele->getGenresByOeuvre($oeuvre['id_oeuvre']);
        }
        unset($oeuvre);

        // Je calcule le nombre total de pages pour chaque catégorie
        $totalPagesFilms = ceil($modele->countByType('film') / $parPage);
        $totalPagesBD    = ceil($modele->countByType('bd') / $parPage);

        // Et je rends la vue avec toutes les données nécessaires
        View::render('oeuvres/listeOeuvresView', [
            'films' => $films,
            'bds' => $bds,
            'pageFilms' => $pageFilms,
            'pageBD' => $pageBD,
            'totalPagesFilms' => $totalPagesFilms,
            'totalPagesBD' => $totalPagesBD
        ]);
    }

    // Cette méthode est l'entrée principale du contrôleur : elle redirige vers la liste
    public function index(): void
    {
        $this->liste();
    }
}
