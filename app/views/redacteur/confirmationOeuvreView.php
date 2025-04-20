<!-- app/views/redacteur/confirmationOeuvreView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<div class="page-confirmation-oeuvre">

    <h2>Ajout réussi</h2>

    <?php if (!empty($message)) : ?>
        <div class="message-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <p><a href="<?= BASE_URL ?>/">Retour à l’accueil</a></p>
    <p><a href="<?= BASE_URL ?>/redacteur/ajouterOeuvre">Ajouter une autre œuvre</a></p>

</div>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>