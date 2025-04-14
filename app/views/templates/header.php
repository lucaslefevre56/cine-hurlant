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

        <!-- Formulaire de recherche principal -->

        <!-- Ce formulaire permet à l’utilisateur de rechercher une œuvre ou un article -->
        <form id="form-recherche" method="GET" action="<?= BASE_URL ?>/recherche" style="margin-top: 1rem;">

            <!-- Zone de saisie libre : titre ou auteur 
             name = q, c'est une convention standard sur le web pour querry (on le voit
         notamment dans l'url en faisant une recherche google par exemple)-->
            <label for="recherche" style="display: none;">Recherche :</label>
            <input type="text" id="recherche" name="q" placeholder="Rechercher un titre ou un auteur..." required>

            <!-- Menu déroulant pour filtrer le type (œuvre, article ou tout) -->
            <select name="type" id="type-recherche">
                <option value="">Tout</option>
                <option value="oeuvre">Œuvres</option>
                <option value="article">Articles</option>
            </select>

            <!-- Bouton de validation classique (au cas où JS est désactivé) -->
            <button type="submit">🔍</button>

            <!-- Zone où s’afficheront les résultats dynamiques en AJAX -->
            <div id="resultats-recherche" class="resultats-recherche" style="display: none;">
                <!-- Résultats injectés ici dynamiquement par JS -->
            </div>
        </form>


    </header>