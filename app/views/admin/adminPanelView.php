<!-- app/views/admin/adminPanelView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<h2>Panneau d'administration</h2>

<div class="admin-nav">
    <button class="tab-btn active" data-tab="utilisateurs">Utilisateurs</button>
    <button class="tab-btn" data-tab="articles">Articles</button>
    <button class="tab-btn" data-tab="oeuvres">Œuvres</button>
    <button class="tab-btn" data-tab="commentaires">Commentaires</button>
</div>

<div id="admin-content">
    <?php
    $contenu ??= 'admin/adminAccueilView';
    require_once ROOT . '/app/views/' . $contenu . '.php';
    ?>
</div>

<script src="<?= BASE_URL ?>/public/js/admin.js"></script>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>