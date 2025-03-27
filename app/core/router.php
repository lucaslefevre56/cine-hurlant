<?php
// app/core/Router.php

// Je crée une classe appelée Router
// En PHP, une classe sert à regrouper du code sous forme de blocs logiques 
// (ici, tout ce qui concerne le routing du site)
class Router
{
    // Je crée une fonction dans cette classe qu’on appelle une "méthode"
    // Elle s’appelle handleRequest() = "gérer la requête"
    // C’est cette méthode que je vais appeler depuis index.php pour démarrer le routage
    public function handleRequest()
    {
        // Je récupère l'URL complète demandée par l'utilisateur
        $uri = $_SERVER['REQUEST_URI'];

        // Je nettoie l'URL pour enlever tout ce qui précède "vraiment" ma route
        // Exemple : /cine-hurlant/public/article/show/4 → je vire /cine-hurlant/public/
        $uri = str_replace('/cine-hurlant/public/', '', $uri);

        // Je découpe le reste de l'URL en segments
        // Exemple : article/show/4 → ['article', 'show', '4']
        $segments = explode('/', trim($uri, '/'));

        // Si l'utilisateur n’a rien tapé dans l’URL (genre juste "/"), je le redirige vers 
        // la page d’accueil
        if (empty($segments[0])) {
            $controllerName = 'AccueilController';
            $method = 'index';
            $params = [];
        } else {
            // Je transforme le premier segment en nom de contrôleur
            // ex : 'article' → 'ArticleController'
            $controllerName = ucfirst($segments[0]) . 'Controller';

            // Je regarde si une méthode est précisée (2e segment), sinon je mets 'index' par défaut
            $method = isset($segments[1]) ? $segments[1] : 'index';

            // Tout ce qui vient après (3e segment et suivants), ce sont les paramètres
            $params = array_slice($segments, 2);
        }

        // Je construis le chemin vers le fichier du contrôleur
        // $controllerName contient déjà le nom exact de la classe (ex : ArticleController)
        // Je le transforme en chemin de fichier .php pour l’inclure
        // Et avec ROOT, je suis à la racine du projet, donc je peux pointer où je veux
        // sans dépendre de mon dossier actuel.
        $controllerFile = ROOT . "/app/controllers/$controllerName.php";

        // Je vérifie si le fichier du contrôleur existe
        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            // Je vérifie si la classe existe bien
            if (class_exists($controllerName)) {
                $controller = new $controllerName();

                // Je vérifie si la méthode demandée existe dans cette classe
                if (method_exists($controller, $method)) {
                    // J'appelle dynamiquement la méthode avec les paramètres (même s’il y en a plusieurs)
                    call_user_func_array([$controller, $method], $params);
                } else {
                    // Méthode inconnue → erreur 404
                    echo "Erreur 404 : méthode '$method' introuvable dans le contrôleur $controllerName.";
                }
            } else {
                // Classe inconnue → erreur
                echo "Erreur : la classe '$controllerName' n’existe pas.";
            }
        } else {
            // Fichier de contrôleur inexistant → erreur
            echo "Erreur : le fichier du contrôleur '$controllerName.php' est introuvable.";
        }
    }
}


// Ici je définis une classe nommée Router,
// et une méthode publique handleRequest() que je pourrai appeler depuis index.php.
// Cette méthode contiendra toute ma logique de routing : lire l’URL, découper, 
// appeler le bon contrôleur.