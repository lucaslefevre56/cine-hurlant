<?php
// app/views/templates/header.php

use App\Helpers\AuthHelper;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ciné-Hurlant</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">

    <!-- Typographies -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
</head>
<body>

<header class="site-header">

    <!-- Haut de page : Logo à gauche, infos utilisateur à droite -->
    <div class="header-top">
        <div class="logo">
            <a href="<?= BASE_URL ?>/">
                <img src="<?= BASE_URL ?>/public/images/logo-footer.jpg" alt="Logo Ciné-Hurlant" class="logo-img">
            </a>
        </div>

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

    <!-- Navigation principale + barre de recherche -->
<div class="nav-recherche">
    <nav class="menu-principal">
        <a href="<?= BASE_URL ?>/">Accueil</a>
        <a href="<?= BASE_URL ?>/oeuvre/liste">Oeuvres</a>
        <a href="<?= BASE_URL ?>/article/liste">Articles</a>
    </nav>

    <!-- Formulaire + résultats dynamiques -->
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

        <!-- Résultats affichés dynamiquement -->
        <div id="resultats-recherche" class="resultats-recherche" style="display: none;"></div>
    </div>
</div>


    <!-- Liens admin/rédacteur -->
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
