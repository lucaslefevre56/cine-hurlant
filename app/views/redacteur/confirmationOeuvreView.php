<!-- app/views/redacteur/confirmationOeuvreView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<h2>Ajout réussi</h2>

<?php if (!empty($message)): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<p><a href="/cine-hurlant/public/">Retour à l’accueil</a></p>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
