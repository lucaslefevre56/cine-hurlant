<!-- app/views/authentification/registerView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<h2>Inscription</h2>

<?php if (!empty($erreur)) : ?>
    <div class="message-error">
        <?= htmlspecialchars($erreur) ?>
    </div>
<?php endif; ?>

<!-- Formulaire d'inscription -->
<form method="POST" action="<?= BASE_URL ?>/auth/register">

    <label for="nom">Nom</label>
    <input
        type="text"
        name="nom"
        id="nom"
        required
        autocomplete="name"
        value="<?= isset($nom) ? htmlspecialchars($nom) : '' ?>">

    <label for="email">Email</label>
    <input
        type="email"
        name="email"
        id="email"
        required
        autocomplete="email"
        value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">

    <label for="password">Mot de passe</label>
    <input
        type="password"
        name="password"
        id="password"
        required
        autocomplete="new-password">

    <!-- Règles du mot de passe -->
    <ul style="margin-top: 5px; font-size: 0.9em; color: #555;">
        <li>Au moins 8 caractères</li>
        <li>Une lettre majuscule</li>
        <li>Une lettre minuscule</li>
        <li>Un chiffre</li>
        <li>Un caractère spécial (ex: !, @, #, $...)</li>
    </ul>

    <label for="confirm">Confirmer le mot de passe</label>
    <input
        type="password"
        name="confirm"
        id="confirm"
        required
        autocomplete="new-password">

    <button type="submit">S'inscrire</button>
</form>

<p><a href="<?= BASE_URL ?>/auth/login">⬅️ Déjà inscrit ? Se connecter</a></p>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
