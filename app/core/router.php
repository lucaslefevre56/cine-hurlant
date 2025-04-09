<?php
// app/Core/Router.php

namespace App\Core;

class Router
{
    public function handleRequest(): void
    {
        //  1. Récupère l'URL demandée (ex: /cine-hurlant/oeuvre/fiche/2)
        $uri = $_SERVER['REQUEST_URI'];

        //  2. Enlève les éventuels paramètres GET
        $uri = strtok($uri, '?');

        //  3. Récupère le dossier de base dynamiquement (ex: /cine-hurlant)
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

        //  4. Retire le chemin de base de l'URI (ex: /oeuvre/fiche/2)
        $url = '/' . ltrim(str_replace($basePath, '', $uri), '/');

        //  5. Découpe l’URL en segments
        $segments = explode('/', trim($url, '/'));

        //  6. Contrôleur, méthode, paramètres
        $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'AccueilController';

        $method = !empty($segments[1]) ? $segments[1] : 'index';

        $params = array_slice($segments, 2);

        //  7. Construction du nom complet de classe
        $controllerClass = 'App\\Controllers\\' . $controllerName;

        //  8. Exécution
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();

            if (method_exists($controller, $method)) {
                call_user_func_array([$controller, $method], $params);
            } else {
                ErrorHandler::render404("Méthode '$method' introuvable dans $controllerClass");
            }
        } else {
            ErrorHandler::render404("Contrôleur '$controllerClass' inexistant");
        }
    }
}
