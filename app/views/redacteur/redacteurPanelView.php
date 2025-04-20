<!-- app/views/redacteur/redacteurPanelView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<div class="page-redacteur-panel">

    <h2>Panneau du rédacteur</h2>

    <div class="redacteur-nav">
        <button class="tab-btn active" data-tab="mesArticles">Mes articles</button>
        <button class="tab-btn" data-tab="mesOeuvres">Mes œuvres</button>
    </div>

    <div id="redacteur-content">
        <?php
        $contenu ??= 'redacteur/redacteurAccueilView';
        require_once ROOT . '/app/views/' . $contenu . '.php';
        ?>
    </div>

    <p><a href="<?= BASE_URL ?>/">← Revenir à l’accueil</a></p>

</div>

<script src="<?= BASE_URL ?>/public/js/redacteur.js"></script>
<script src="<?= BASE_URL ?>/public/js/supprimer.js" defer></script>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
