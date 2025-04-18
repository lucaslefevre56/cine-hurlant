<?php

// Je commence par charger Composer, sinon mes classes ne seront pas reconnues (obligatoire)
require_once __DIR__ . '/vendor/autoload.php';

// Maintenant je peux utiliser mes classes sans les require à la main (merci l'autoload)
use App\Core\Config;
use App\Core\Router;

// Je définis ROOT et BASE_URL pour pouvoir les utiliser partout dans le projet
define('ROOT', Config::getRoot());
define('BASE_URL', Config::getBaseUrl());

// Démarrage de la session utilisateur (obligatoire pour tout ce qui est connexion, messages flash, etc.)
session_start();

// Je déclenche mon routeur perso : c’est lui qui va décider quel contrôleur appeler en fonction de l’URL
$router = new Router();
$router->handleRequest();
