<!-- app/views/authentification/loginView.php -->
 
<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<?php if (!empty($erreur)) : ?>
    <p style="color: red;"><?= $erreur ?></p>
<?php endif; ?>

<h2>Connexion</h2>

<form method="POST" action="">
    
    <!-- Champ email -->
    <label for="email">Email</label>
    <input type="email" name="email" id="email" autocomplete="email" required>

    <!-- Champ mot de passe -->
    <label for="password">Mot de passe</label>
    <input type="password" name="password" id="password" autocomplete="current-password" required>

    <!-- Bouton de soumission -->
    <button type="submit">Se connecter</button>

</form>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>