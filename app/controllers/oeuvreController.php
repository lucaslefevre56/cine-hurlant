<?php

// app/Controllers/OeuvreController.php
namespace App\Controllers;

use App\Models\Oeuvre;
use App\Core\View;
use App\Core\ErrorHandler;

class OeuvreController
{
    public function fiche(int $id): void
    {
        $oeuvreModel = new Oeuvre();
        $oeuvre = $oeuvreModel->getById($id);

        if (!$oeuvre) {
            ErrorHandler::render404("Œuvre introuvable");
        }

        $genres = $oeuvreModel->getGenresByOeuvre($id);
        $video_url = $oeuvre['video_url'] ?? null;

        View::render('oeuvres/ficheOeuvreView', [
            'oeuvre' => $oeuvre,
            'genres' => $genres,
            'video_url' => $video_url
        ]);
    }

    public function liste(): void
    {
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $parPage = 6;
        $offset = ($page - 1) * $parPage;

        $modele = new Oeuvre();
        $oeuvres = $modele->getPaginated($parPage, $offset);
        $total = $modele->countAll();
        $totalPages = ceil($total / $parPage);

        View::render('oeuvres/listeOeuvresView', [
            'oeuvres' => $oeuvres,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function index(): void
    {
        // Redirection logique interne vers liste()
        $this->liste();
    }
}
