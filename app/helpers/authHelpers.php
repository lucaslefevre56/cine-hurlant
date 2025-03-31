<?php
// app/helpers/authHelpers.php

// Je rends disponible le modèle Utilisateur ici pour pouvoir l’utiliser dans les fonctions
require_once ROOT . '/app/models/utilisateur.php';

function isLoggedIn()
{
    // Je vérifie si la session 'user' est définie → donc si quelqu’un est connecté
    return isset($_SESSION['user']);
}

function isUserAdmin()
{
    // Si personne n’est connecté → on renvoie false direct
    if (!isLoggedIn()) {
        return false;
    }

    // Je récupère l'ID de l'utilisateur depuis la session
    $id = $_SESSION['user']['id'];

    // Je crée un objet Utilisateur pour accéder à la méthode isAdmin()
    $utilisateur = new Utilisateur($GLOBALS['conn']);

    // Je renvoie le résultat de isAdmin()
    return $utilisateur->isAdmin($id);
}

function isUserRedacteur()
{
    // Si personne n’est connecté → false direct
    if (!isLoggedIn()) {
        return false;
    }

    // Je récupère l'ID du user en session
    $id = $_SESSION['user']['id'];

    // Je crée une instance du modèle
    $utilisateur = new Utilisateur($GLOBALS['conn']);

    // J’utilise la méthode isRedacteur() du modèle
    return $utilisateur->isRedacteur($id);
}

?>