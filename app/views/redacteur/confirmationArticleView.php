<!-- app/views/redacteur/confirmationArticleView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<div class="page-confirmation-article">

<!-- Titre de confirmation -->
<h2>Ajout réussi</h2>

<!-- Message de succès si présent -->
<?php if (!empty($message)) : ?>
    <div class="message-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- Liens de navigation -->
<p><a href="<?= BASE_URL ?>/">Retour à l’accueil</a></p>
<p><a href="<?= BASE_URL ?>/redacteur/ajouterArticle">Ajouter un autre article</a></p>

</div>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
