<?php
// app/core/Router.php

// J'inclus les helpers liés aux erreurs (notamment la fonction render404)
require_once ROOT . '/app/helpers/errorHelper.php';


// Je crée une classe Router qui va gérer toutes les URL du site
// Elle sera appelée dans index.php pour savoir quel contrôleur utiliser
class Router
{
    // Méthode principale appelée depuis index.php
    public function handleRequest()
    {
        // Je récupère l’URL tapée dans le navigateur
        // Exemple : /cine-hurlant/public/redacteur/ajouterOeuvre
        $uri = $_SERVER['REQUEST_URI'];

        // Je nettoie l’URL pour enlever la partie fixe "/cine-hurlant/public/"
        // Résultat après nettoyage : "redacteur/ajouterOeuvre"
        $uri = str_replace('/cine-hurlant/public/', '', $uri);

        // Je découpe le reste de l’URL en morceaux grâce au slash
        // Exemple : "redacteur/ajouterOeuvre" → ['redacteur', 'ajouterOeuvre']
        $segments = explode('/', trim($uri, '/'));

        // Si aucun segment n’est présent (genre juste "/"), je redirige vers la page d’accueil
        if (empty($segments[0])) {
            $controllerName = 'AccueilController'; // Contrôleur par défaut
            $method = 'index'; // Méthode par défaut
            $params = []; // Aucun paramètre
        } else {
            // Je transforme le 1er segment en nom de contrôleur
            // 'redacteur' → 'RedacteurController'
            $controllerName = ucfirst($segments[0]) . 'Controller';

            // Je regarde si le 2e segment existe pour savoir quelle méthode appeler
            // Si rien, je mets "index" par défaut
            $method = isset($segments[1]) ? $segments[1] : 'index';

            // Le reste (3e, 4e, etc.) ce sont les éventuels paramètres
            $params = array_slice($segments, 2);
        }

        // Je construis le chemin vers le fichier PHP du contrôleur
        $controllerFile = ROOT . "/app/controllers/$controllerName.php";

        // Je vérifie que le fichier existe bien
        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            // Je vérifie que la classe existe dans le fichier
            if (class_exists($controllerName)) {
                // Je crée une instance de cette classe
                $controller = new $controllerName();

                // Je vérifie que la méthode demandée existe dans la classe
                if (method_exists($controller, $method)) {
                    // J'appelle cette méthode avec les éventuels paramètres (ex : show(4))
                    call_user_func_array([$controller, $method], $params);
                } else {
                    // Si la méthode n’existe pas → erreur 404
                    render404("Méthode '$method' introuvable dans $controllerName");
                }
            } else {
                // Si la classe n’existe pas → erreur
                render404("Classe '$controllerName' inexistante");
            }
        } else {
            // Si le fichier du contrôleur n’existe pas → erreur
            render404("Fichier du contrôleur '$controllerName.php' introuvable");
        }
    }
   
}
