<!-- app/views/utilisateur/modifierMotDePasseView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<h2>Modifier mon mot de passe</h2>

<?php if (!empty($erreur)) : ?>
    <div style="color: red;">
        <?= $erreur ?>
    </div>
<?php endif; ?>


<?php if (!empty($confirmation)) : ?>
    <p style="color: green;"><?= htmlspecialchars($confirmation) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="ancien_password">Mot de passe actuel</label>
    <input type="password" name="ancien_password" id="ancien_password" required>

    <label for="nouveau_password">Nouveau mot de passe</label>
    <input type="password" name="nouveau_password" id="nouveau_password" required>

    <ul style="margin-top: 5px; font-size: 0.9em; color: #555;">
        <li>Au moins 8 caractères</li>
        <li>Une lettre majuscule</li>
        <li>Une lettre minuscule</li>
        <li>Un chiffre</li>
        <li>Un caractère spécial (ex: !, @, #, $...)</li>
    </ul>

    <label for="confirmation_password">Confirmation du nouveau mot de passe</label>
    <input type="password" name="confirmation_password" id="confirmation_password" required>

    <button type="submit">Mettre à jour</button>
</form>

<p><a href="<?= BASE_URL ?>/utilisateur/profil">⬅️ Retour au profil</a></p>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>