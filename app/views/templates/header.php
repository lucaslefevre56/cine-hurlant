<?php
// app/views/templates/header.php

// Je charge mes fonctions d’authentification (isLoggedIn, isUserRedacteur, etc.)
require_once ROOT . '/app/helpers/authHelpers.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ciné-Hurlant</title>
</head>

<body>

    <!-- En-tête du site avec fond sombre -->
    <header style="background: #222; color: white; padding: 1rem;">

        <h1>
            <a href="/cine-hurlant/public/" style="color: white; text-decoration: none;">Ciné-Hurlant</a>
        </h1>

        <!-- Lien vers la liste des œuvres -->
        <nav>
            <a href="/cine-hurlant/public/oeuvre/liste" style="color: white; margin-left: 1rem;">Les œuvres</a>
        </nav>


        <?php if (isset($_SESSION['user'])) : ?>
            <!-- Si un utilisateur est connecté, j’affiche son nom -->
            <?php if (!empty($_SESSION['user']['nom'])) : ?>
                <p>Bienvenue <?= htmlspecialchars($_SESSION['user']['nom']) ?> !</p>
            <?php endif; ?>

            <!-- Lien de déconnexion -->
            <a href="/cine-hurlant/public/auth/logout">Se déconnecter</a>

            <!-- Message de confirmation de connexion -->
            <?php if (isLoggedIn()) : ?>
                <p style="color: lime;">Connecté ✔</p>
            <?php endif; ?>

            <!-- Si l’utilisateur est admin, j’affiche un lien vers le panel admin -->
            <?php if (isUserAdmin()) : ?>
                <a href="/cine-hurlant/public/admin">Admin Panel</a>
            <?php endif; ?>

            <!-- Si l’utilisateur est rédacteur, il peut ajouter une œuvre ou un article -->
            <?php if (isUserRedacteur()) : ?>
                <a href="/cine-hurlant/public/redacteur/ajouterOeuvre">Ajouter une œuvre</a>
                <a href="/cine-hurlant/public/redacteur">Ajouter un article</a>
            <?php endif; ?>

        <?php else : ?>
            <!-- Si personne n’est connecté → je propose de se connecter ou s’inscrire -->
            <a href="/cine-hurlant/public/auth/login">Se connecter</a> |
            <a href="/cine-hurlant/public/auth/register">S'inscrire</a>
        <?php endif; ?>
    </header>