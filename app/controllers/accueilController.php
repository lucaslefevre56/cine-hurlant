<?php
// app/controllers/AccueilController.php

// Je crée ma classe AccueilController
class AccueilController
{
    // Méthode appelée quand on accède à /accueil ou à l’URL racine du site
    public function index()
    {
        // Je charge la vue d’accueil
        require_once ROOT . '/app/views/accueil/indexView.php';
    }
}
