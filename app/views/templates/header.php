<?php
// app/views/templates/header.php

use App\Helpers\AuthHelper;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciné-Hurlant - Analyses croisées SF : cinéma & BD</title>
    <meta name="description" content="Ciné-Hurlant explore les liens entre science-fiction au 
    cinéma et en bande dessinée. Analyses, articles et suggestions d'œuvres pour passionnés curieux.">

    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">

</head>

<body>

    <header class="site-header">

        <!-- Bouton menu burger (mobile uniquement) -->
        <button class="burger-menu" aria-label="Menu mobile">☰</button>

        <div class="top-header">
            <!-- Logo -->
            <div class="logo">
                <a href="<?= BASE_URL ?>/">
                    <img src="<?= BASE_URL ?>/public/images/logo-footer.jpg" alt="Logo Ciné-Hurlant" class="logo-img">
                </a>
            </div>

            <!-- Bloc utilisateur (affiché sur desktop) -->
            <div class="utilisateur">
                <?php if (AuthHelper::isLoggedIn()) : ?>
                    <p>
                        Bienvenue <strong><?= htmlspecialchars($_SESSION['user']['nom']) ?></strong> |
                        <a href="<?= BASE_URL ?>/utilisateur/profil">Mon profil</a>
                        <span class="connecte">✔</span> |
                        <a href="<?= BASE_URL ?>/auth/logout" class="btn-deconnexion">Déconnexion</a>
                    </p>
                <?php else : ?>
                    <a href="<?= BASE_URL ?>/auth/login" class="btn-login">Connexion</a>
                    <a href="<?= BASE_URL ?>/auth/register" class="btn-inscription">Inscription</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Navigation principale desktop -->
        <div class="nav-recherche">
            <nav class="menu-principal">
                <a href="<?= BASE_URL ?>/">Accueil</a>
                <a href="<?= BASE_URL ?>/oeuvre/liste">Oeuvres</a>
                <a href="<?= BASE_URL ?>/article/liste">Articles</a>
            </nav>

            <!-- Barre de recherche -->
            <div class="bloc-recherche">
                <form id="form-recherche" method="GET" action="<?= BASE_URL ?>/recherche" class="form-recherche">
                    <input type="text" id="recherche" name="q" placeholder="Recherche film ou BD..." required>
                    <select name="type" id="type-recherche">
                        <option value="">Tout</option>
                        <option value="oeuvre">Œuvres</option>
                        <option value="article">Articles</option>
                    </select>
                    <button type="submit">🔍</button>
                </form>
                <div id="resultats-recherche" class="resultats-recherche" style="display: none;"></div>
            </div>
        </div>

        <!-- Menu mobile déroulant -->
        <nav class="menu-mobile">
            <a href="<?= BASE_URL ?>/">Accueil</a>
            <a href="<?= BASE_URL ?>/oeuvre/liste">Oeuvres</a>
            <a href="<?= BASE_URL ?>/article/liste">Articles</a>

            <?php if (AuthHelper::isLoggedIn()) : ?>
                <a href="<?= BASE_URL ?>/utilisateur/profil">Mon profil</a>
                <a href="<?= BASE_URL ?>/auth/logout" class="btn-deconnexion">Déconnexion</a>
            <?php else : ?>
                <a href="<?= BASE_URL ?>/auth/login">Connexion</a>
                <a href="<?= BASE_URL ?>/auth/register">Inscription</a>
            <?php endif; ?>

            <?php if (AuthHelper::hasAnyRole(['admin', 'redacteur'])) : ?>
                <?php if (AuthHelper::isUserAdmin()) : ?>
                    <a href="<?= BASE_URL ?>/admin">Admin Panel</a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/redacteur">Rédacteur Panel</a>
                <a href="<?= BASE_URL ?>/redacteur/ajouterOeuvre">➕ Ajouter une œuvre</a>
                <a href="<?= BASE_URL ?>/redacteur/ajouterArticle">➕ Ajouter un article</a>
            <?php endif; ?>
        </nav>

        <!-- Menu rédacteur (version desktop) -->
        <?php if (AuthHelper::hasAnyRole(['admin', 'redacteur'])) : ?>
            <nav class="menu-redacteur">
                <?php if (AuthHelper::isUserAdmin()) : ?>
                    <a href="<?= BASE_URL ?>/admin">Admin Panel</a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/redacteur">Rédacteur Panel</a>
                <a href="<?= BASE_URL ?>/redacteur/ajouterOeuvre">➕ Ajouter une œuvre</a>
                <a href="<?= BASE_URL ?>/redacteur/ajouterArticle">➕ Ajouter un article</a>
            </nav>
        <?php endif; ?>

        <script>
            const BASE_URL = "<?= BASE_URL ?>";
        </script>

    </header>