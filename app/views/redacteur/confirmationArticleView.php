<!-- app/views/redacteur/confirmationArticleView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<!-- Titre de confirmation -->
<h2>Ajout réussi</h2>

<!-- Message de succès si présent -->
<?php if (!empty($message)): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- Liens de navigation -->
<p><a href="<?= BASE_URL ?>/">Retour à l’accueil</a></p>
<p><a href="<?= BASE_URL ?>/redacteur/ajouterArticle">Ajouter un autre article</a></p>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
