<?php

// Inclure la configuration
require_once __DIR__ . '/app/Core/Config.php';

// Définir ROOT via la méthode de la classe Config
define('ROOT', \App\Core\Config::getRoot());

// Définir BASE_URL via la méthode de la classe Config
define('BASE_URL', \App\Core\Config::getBaseUrl()); // Définir BASE_URL globalement

// Continuer avec le reste de la logique...
require_once __DIR__ . '/vendor/autoload.php';
use App\Core\Router;

session_start();
$router = new Router();
$router->handleRequest();
?>
