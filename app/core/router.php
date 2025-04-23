<?php
// app/Core/Router.php

namespace App\Core;

class Router
{
    public function handleRequest(): void
    {
        // Je récupère l’URL complète saisie par l'utilisateur (ex : /cine-hurlant/oeuvre/fiche/2?page=1)
        // Cette URL contient à la fois le chemin et d'éventuels paramètres GET après le "?"
        $uri = $_SERVER['REQUEST_URI'];

        // Je nettoie l'URL en supprimant tout ce qui suit le "?" 
        // Cela me permet de me concentrer uniquement sur le chemin qui m'intéresse pour le routage
        $uri = strtok($uri, '?');

        // Je détecte le chemin de base où se trouve mon projet (utile si mon site est dans un sous-dossier)
        // Exemple : si mon projet est dans /cine-hurlant, je récupère ce morceau pour l'ignorer ensuite
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

        // Je retire ce chemin de base de l'URL réelle pour isoler uniquement la partie logique de mon MVC
        // Je m'assure aussi qu'il n'y ait pas de "/" parasite au début
        $url = '/' . ltrim(str_replace($basePath, '', $uri), '/');

        // Je découpe cette URL propre en segments grâce au séparateur "/"
        // Exemple : "/oeuvre/fiche/2" devient ['oeuvre', 'fiche', '2']
        $segments = explode('/', trim($url, '/'));

        // Je sécurise le cas où quelqu’un taperait directement /index.php dans l’URL
        // Si c’est le cas, je le supprime des segments pour ne pas perturber le routage
        if (!empty($segments[0]) && $segments[0] === 'index.php') {
            array_shift($segments);  // Je retire "index.php" du tableau
        }

        // Je déduis automatiquement le contrôleur à appeler :
        // - Si un segment existe, je le transforme en nom de contrôleur (majuscule + "Controller")
        // - Sinon, je redirige vers "AccueilController" par défaut
        $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'AccueilController';

        // Même principe pour la méthode :
        // - Si une méthode est précisée dans l’URL, je la prends
        // - Sinon, j’appelle la méthode "index" par défaut
        $method = !empty($segments[1]) ? $segments[1] : 'index';

        // Je récupère tous les paramètres supplémentaires passés dans l’URL (ex : l'ID d'une œuvre)
        $params = array_slice($segments, 2);

        // Je construis le nom complet du contrôleur avec son namespace
        // Exemple : "OeuvreController" devient "App\Controllers\OeuvreController"
        $controllerClass = 'App\\Controllers\\' . $controllerName;

        // Je vérifie que ce contrôleur existe bien dans mon projet
        if (class_exists($controllerClass)) {
            // Si oui, je l’instancie dynamiquement
            $controller = new $controllerClass();

            // Ensuite, je vérifie que la méthode demandée existe dans ce contrôleur
            if (method_exists($controller, $method)) {
                // Si tout est bon, j'appelle la méthode avec les paramètres récupérés
                call_user_func_array([$controller, $method], $params);
            } else {
                // Si la méthode n'existe pas, j’affiche une erreur 404 personnalisée
                ErrorHandler::render404("Méthode '$method' introuvable dans $controllerClass");
            }
        } else {
            // Si le contrôleur n'existe pas, je renvoie également une 404 personnalisée
            ErrorHandler::render404("Contrôleur '$controllerClass' inexistant");
        }
    }
}


// Mon Router traduit chaque URL en action concrète. Il va chercher le contrôleur et la méthode 
// demandée sans que j’aie besoin de déclarer mes routes à la main 
// comme on le ferait dans un framework. Si l’URL est valide, il appelle la bonne méthode, 
// sinon il envoie une erreur 404 personnalisée. 