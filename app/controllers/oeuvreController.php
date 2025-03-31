<?php
// app/controllers/oeuvreController.php

// J'inclus les helpers liés aux erreurs (notamment la fonction render404)
require_once ROOT . '/app/helpers/errorHelper.php';

// -----------------------------------------------------------
// CONTRÔLEUR ŒUVRE – GÈRE LES ACTIONS LIÉES AUX ŒUVRES
// -----------------------------------------------------------
//
// Ce fichier appartient à la couche "Contrôleur" du MVC.
// Il sert d'intermédiaire entre :
//  → les modèles (ici : Oeuvre.php qui dialogue avec la BDD)
//  → les vues (ici : listeOeuvresView.php pour afficher les œuvres)
//
// Objectif : afficher la liste des œuvres enregistrées dans la base
// -----------------------------------------------------------

class OeuvreController
{
    // Méthode appelée quand l’URL est /oeuvre/liste
    public function liste()
    {
        // 1. J'inclus le modèle associé aux œuvres
        // Il contient les méthodes pour interagir avec la BDD (récupérer, ajouter…)
        require_once ROOT . '/app/models/oeuvre.php';

        // 2. Je crée une instance du modèle Oeuvre
        // Je lui passe la connexion à la BDD (déjà stockée dans $GLOBALS['conn'])
        $oeuvre = new Oeuvre($GLOBALS['conn']);

        // 3. J'appelle la méthode getAll() pour récupérer toutes les œuvres
        // Cette méthode me renvoie un tableau associatif contenant les œuvres
        $oeuvres = $oeuvre->getAll();

        // 4. Pour chaque œuvre, je vais chercher les genres associés
        // Je complète chaque œuvre avec un tableau "genres" grâce à la méthode getGenresByOeuvre()
        foreach ($oeuvres as &$o) {
            $o['genres'] = $oeuvre->getGenresByOeuvre($o['id_oeuvre']);
        }
        
        // 5. J'affiche la vue associée à cette action
        // → Le tableau $oeuvres sera accessible dans la vue pour affichage
        require_once ROOT . '/app/views/oeuvres/listeOeuvresView.php';
    }

    public function fiche($id)
    {
        // 1. J'inclus le modèle Oeuvre pour accéder aux méthodes liées à la BDD
        require_once ROOT . '/app/models/oeuvre.php';

        // 2. Je crée une instance du modèle
        $oeuvreModel = new Oeuvre($GLOBALS['conn']);

        // 3. Je récupère les infos de l’œuvre grâce à son ID
        $oeuvre = $oeuvreModel->getById($id);

        // 4. Si l’œuvre n’existe pas, redirection vers la page 404 en utilisant la methode render404
        if (!$oeuvre) {
            render404("Œuvre introuvable");
            return;
        }

        // 5. Je récupère la liste des genres associés à cette œuvre (SF, Western…)
        $genres = $oeuvreModel->getGenresByOeuvre($id);

        // 6. J'appelle la vue pour afficher la fiche détaillée
        require_once ROOT . '/app/views/oeuvres/ficheOeuvreView.php';
    }
}
