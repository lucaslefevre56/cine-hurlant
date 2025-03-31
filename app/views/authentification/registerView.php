<!-- app/views/authentification/registerView.php -->
 
<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<?php if (!empty($erreur)) : ?>
    <p style="color: red;"><?= $erreur ?></p>
<?php endif; ?>

<h2>Inscription</h2>

<!-- Formulaire d'inscription -->
<form method="POST" action="/cine-hurlant/public/auth/register">
    
    <!-- Champ nom -->
    <label for="nom">Nom</label>
    <input 
        type="text" 
        name="nom" 
        id="nom" 
        required 
        autocomplete="name"
    >

    <!-- Champ email -->
    <label for="email">Email</label>
    <input 
        type="email" 
        name="email" 
        id="email" 
        required 
        autocomplete="email"
    >

    <!-- Champ mot de passe -->
    <label for="password">Mot de passe</label>
    <input 
        type="password" 
        name="password" 
        id="password" 
        required 
        autocomplete="new-password"
    >

    <!-- Champ confirmation du mot de passe -->
    <label for="confirm">Confirmer le mot de passe</label>
    <input 
        type="password" 
        name="confirm" 
        id="confirm" 
        required 
        autocomplete="new-password"
    >

    <!-- Bouton d'envoi -->
    <button type="submit">S'inscrire</button>

</form>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>