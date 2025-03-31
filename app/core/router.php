<?php
// app/core/Router.php
namespace App\Core;

// J'inclus les helpers liés aux erreurs (la fonction render404)
use function App\Helpers\render404;

// Je crée une classe Router qui va gérer toutes les URL du site
class Router
{
    // Méthode principale appelée depuis index.php
    public function handleRequest()
    {
        // Je récupère l’URL tapée dans le navigateur
        $uri = $_SERVER['REQUEST_URI'];

        // Je nettoie l’URL pour enlever la partie fixe "/cine-hurlant/public/"
        $uri = str_replace('/cine-hurlant/public/', '', $uri);

        // Je découpe l’URL : "redacteur/ajouterOeuvre" → ['redacteur', 'ajouterOeuvre']
        $segments = explode('/', trim($uri, '/'));

        // Valeurs par défaut
        $controllerName = 'AccueilController';
        $method = 'index';
        $params = [];

        // Si des segments sont présents, je les utilise
        if (!empty($segments[0])) {
            $controllerName = ucfirst($segments[0]) . 'Controller';
            $method = isset($segments[1]) ? $segments[1] : 'index';
            $params = array_slice($segments, 2);
        }

        // Je construis dynamiquement le nom complet de la classe avec namespace
        $controllerClass = 'App\\Controllers\\' . $controllerName;

        // Je vérifie que la classe existe (chargée automatiquement par Composer)
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();

            if (method_exists($controller, $method)) {
                call_user_func_array([$controller, $method], $params);
            } else {
                render404("Méthode '$method' introuvable dans $controllerClass");
            }
        } else {
            render404("Classe '$controllerClass' inexistante");
        }
    }
}
