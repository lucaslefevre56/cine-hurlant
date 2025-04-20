<!-- app/views/admin/adminPanelView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<!-- Conteneur global stylisé pour la page d’administration -->
<div class="page-admin-panel">

    <!-- Titre principal de la page -->
    <h2>Panneau d'administration</h2>

    <!-- Barre de navigation des onglets (utilisateurs, articles, etc.) -->
    <div class="admin-nav">
        <button class="tab-btn active" data-tab="utilisateurs">Utilisateurs</button>
        <button class="tab-btn" data-tab="articles">Articles</button>
        <button class="tab-btn" data-tab="oeuvres">Œuvres</button>
        <button class="tab-btn" data-tab="commentaires">Commentaires</button>
    </div>

    <!-- Contenu dynamique chargé en fonction de l’onglet sélectionné -->
    <div id="admin-content">
        <?php
        // Si aucune vue spécifique n’a été chargée, on affiche la vue d’accueil admin par défaut
        $contenu ??= 'admin/adminAccueilView';
        require_once ROOT . '/app/views/' . $contenu . '.php';
        ?>
    </div>

    <!-- Lien de retour vers la page d’accueil -->
    <p><a href="<?= BASE_URL ?>/">← Revenir à l’accueil</a></p>

</div>

<!-- Inclusion des scripts JS pour les actions d’onglets et suppression -->
<script src="<?= BASE_URL ?>/public/js/admin.js"></script>
<script src="<?= BASE_URL ?>/public/js/supprimer.js" defer></script>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>