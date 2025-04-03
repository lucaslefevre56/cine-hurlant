<?php
// app/Controllers/OeuvreController.php

namespace App\Controllers;

// Chargement automatique du modèle Oeuvre via Composer (PSR-4)
use App\Models\Oeuvre;

// J'inclus les helpers liés aux erreurs (notamment la fonction render404)
use function App\Helpers\render404;

// -----------------------------------------------------------
// CONTRÔLEUR ŒUVRE – GÈRE LES ACTIONS LIÉES AUX ŒUVRES
// -----------------------------------------------------------
// Il sert d'intermédiaire entre :
//  → le modèle Oeuvre (accès à la base de données)
//  → les vues (liste et fiche)
// Son objectif : afficher toutes les œuvres ou une fiche précise
// -----------------------------------------------------------

class OeuvreController
{
    // Méthode appelée quand l’URL est /oeuvre/liste
    public function liste()
    {
        // 1. Je crée une instance du modèle Oeuvre (plus besoin de connexion à passer)
        $oeuvre = new Oeuvre();

        // 2. Je récupère toutes les œuvres enregistrées dans la base
        $oeuvres = $oeuvre->getAll();

        // 3. Pour chaque œuvre, je vais chercher les genres associés
        foreach ($oeuvres as &$o) {
            $o['genres'] = $oeuvre->getGenresByOeuvre($o['id_oeuvre']);
        }

        // 4. J'affiche la vue associée avec le tableau $oeuvres
        require_once ROOT . '/app/views/oeuvres/listeOeuvresView.php';
    }

    // Méthode appelée quand l’URL est /oeuvre/fiche/:id
    public function fiche($id)
    {
        // 1. Je crée une instance du modèle Oeuvre
        $oeuvreModel = new Oeuvre();

        // 2. Je récupère les infos de l’œuvre demandée
        $oeuvre = $oeuvreModel->getById($id);

        // 3. Si elle n’existe pas, je déclenche une erreur 404
        if (!$oeuvre) {
            render404("Œuvre introuvable");
            return;
        }

        // 4. Je récupère les genres liés à cette œuvre
        $genres = $oeuvreModel->getGenresByOeuvre($id);

        // Si l'œuvre a un lien vidéo, je récupère cette information également
        $video_url = $oeuvre['video_url'] ?? null;

        // 5. J'affiche la vue de fiche détaillée
        require_once ROOT . '/app/views/oeuvres/ficheOeuvreView.php';
    }

    public function index()
    {
        // Redirection vers la liste des œuvres
        header('Location: /cine-hurlant/public/oeuvre/liste');
        exit;
    }
}
