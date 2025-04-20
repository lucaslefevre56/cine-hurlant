<!-- app/views/authentification/loginView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<!-- Conteneur principal centré pour la mise en forme -->
<div class="page-auth">

    <!-- Si une erreur existe (ex : mauvais identifiants), je l’affiche ici -->
    <?php if (!empty($erreur)) : ?>
        <p class="message-erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <!-- Titre principal de la page -->
    <h2>Connexion</h2>

    <!-- Formulaire de connexion envoyé en POST -->
    <form method="POST" action="">

        <!-- Champ pour l’email avec saisie assistée -->
        <label for="email">Email</label>
        <input type="email" name="email" id="email" autocomplete="email" required>

        <!-- Champ pour le mot de passe (masqué + saisie assistée) -->
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" autocomplete="current-password" required>

        <!-- Bouton de soumission du formulaire -->
        <button type="submit">Se connecter</button>

    </form>

</div>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>