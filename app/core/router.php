<?php
// app/Core/Router.php

namespace App\Core;

class Router
{
    public function handleRequest(): void
    {
        // Je choppe l’URL complète tapée par l’utilisateur (ex: /cine-hurlant/oeuvre/fiche/2)
        $uri = $_SERVER['REQUEST_URI'];

        // Je vire tout ce qui est après le "?" (ex: ?page=2), on garde juste le chemin
        $uri = strtok($uri, '?');

        // Je récupère le chemin de base de mon projet (ça marche même s’il est dans un sous-dossier)
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

        // Je retire ce chemin de base de l'URL, pour ne garder que la partie utile (contrôleur/méthode)
        $url = '/' . ltrim(str_replace($basePath, '', $uri), '/');

        // Je découpe ce qu’il reste de l’URL en segments (ex: ['oeuvre', 'fiche', '2'])
        $segments = explode('/', trim($url, '/'));

        // Je récupère le nom du contrôleur (par défaut : AccueilController si rien dans l’URL)
        $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'AccueilController';

        // Je récupère la méthode à appeler dans le contrôleur (par défaut : index)
        $method = !empty($segments[1]) ? $segments[1] : 'index';

        // Je récupère les éventuels paramètres supplémentaires (ex: id de l'œuvre)
        $params = array_slice($segments, 2);

        // Je construis le nom complet de la classe contrôleur avec son namespace
        $controllerClass = 'App\\Controllers\\' . $controllerName;

        // Si la classe existe, je l’instancie
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();

            // Si la méthode demandée existe dans cette classe, je l'appelle avec les bons paramètres
            if (method_exists($controller, $method)) {
                call_user_func_array([$controller, $method], $params);
            } else {
                // Sinon je balance une 404 personnalisée (méthode introuvable)
                ErrorHandler::render404("Méthode '$method' introuvable dans $controllerClass");
            }
        } else {
            // Si le contrôleur n'existe pas, pareil : 404
            ErrorHandler::render404("Contrôleur '$controllerClass' inexistant");
        }
    }
}
