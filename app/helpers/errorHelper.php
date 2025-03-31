<?php
// app/helpers/errorHelper.php

// Cette fonction me permet d’afficher une vraie page 404 personnalisée
// Elle peut être appelée depuis n’importe quel contrôleur ou depuis le routeur
function render404($message = '')
{
    // J’envoie un code HTTP 404 au navigateur
    http_response_code(404);

    // Je prépare un message d’erreur personnalisé (optionnel)
    // Cette variable sera récupérée dans la vue 404.php
    $erreur = $message;
    
    // J’affiche la page d’erreur depuis le dossier /views/erreur/
    require_once ROOT . '/app/views/erreur/404.php';

    // Je stoppe l’exécution du script (obligatoire pour ne pas continuer plus loin)
    exit;
}
