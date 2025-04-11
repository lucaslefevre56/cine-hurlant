<?php
// app/views/templates/header.php

// Je charge la classe AuthHelper (isLoggedIn, isUserRedacteur, etc.)
use App\Helpers\AuthHelper;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ciné-Hurlant</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">

    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>


</head>

<body>

    <header style="background: #222; color: white; padding: 1rem;">
        <h1>
            <a href="<?= BASE_URL ?>/" style="color: white; text-decoration: none;">
                Ciné-Hurlant
            </a>
        </h1>

        <nav style="margin-top: 0.5rem;">
            <a href="<?= BASE_URL ?>/oeuvre/liste" style="color: white; margin-right: 1rem;">Les œuvres</a>
            <a href="<?= BASE_URL ?>/article/liste" style="color: white; margin-right: 1rem;">Les articles</a>

            <?php if (AuthHelper::isLoggedIn()) : ?>
                <span style="margin-right: 1rem;">|</span>
                <a href="<?= BASE_URL ?>/auth/logout" style="color: #faa;">Se déconnecter</a>
            <?php else : ?>
                <a href="<?= BASE_URL ?>/auth/login" style="color: #0af;">Se connecter</a> |
                <a href="<?= BASE_URL ?>/auth/register" style="color: #0af;">S'inscrire</a>
            <?php endif; ?>
        </nav>

        <?php if (AuthHelper::isLoggedIn()) : ?>
            <p style="margin-top: 0.5rem;">
                Bienvenue <strong><?= htmlspecialchars($_SESSION['user']['nom']) ?></strong> !

                <a href="<?= BASE_URL ?>/utilisateur/profil" style="color: #0af; margin-left: 1rem;">
                    Mon profil
                </a>

                <span style="color: lime; margin-left: 1rem;">Connecté ✔</span>
            </p>

            <nav style="margin-top: 0.5rem;">
                <?php if (AuthHelper::isUserAdmin()) : ?>
                    <a href="<?= BASE_URL ?>/admin" style="color: orange; margin-right: 1rem;">
                        Admin Panel
                    </a>
                <?php endif; ?>

                <?php if (AuthHelper::hasAnyRole(['admin', 'redacteur'])) : ?>
                    <a href="<?= BASE_URL ?>/redacteur" style="color: lightgreen; margin-right: 1rem;">
                        Rédacteur Panel
                    </a>
                    <a href="<?= BASE_URL ?>/redacteur/ajouterOeuvre" style="color: lightgreen; margin-right: 1rem;">
                        Ajouter une œuvre
                    </a>
                    <a href="<?= BASE_URL ?>/redacteur/ajouterArticle" style="color: lightgreen;">
                        Ajouter un article
                    </a>
                <?php endif; ?>

            </nav>
        <?php endif; ?>
    </header>