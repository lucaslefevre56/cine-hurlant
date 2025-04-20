<!-- app/views/authentification/registerView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<!-- Conteneur principal centré pour l’authentification -->
<div class="page-register">

    <!-- Titre principal -->
    <h2>Inscription</h2>

    <!-- Affichage des éventuelles erreurs (ex : email déjà utilisé) -->
    <?php if (!empty($erreur)) : ?>
        <p class="message-erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <!-- Formulaire d’inscription envoyé en POST -->
    <form method="POST" action="<?= BASE_URL ?>/auth/register">

        <!-- Champ pour le nom de l’utilisateur -->
        <label for="nom">Nom</label>
        <input
            type="text"
            name="nom"
            id="nom"
            required
            autocomplete="name"
            value="<?= isset($nom) ? htmlspecialchars($nom) : '' ?>">

        <!-- Champ pour l’adresse email -->
        <label for="email">Email</label>
        <input
            type="email"
            name="email"
            id="email"
            required
            autocomplete="email"
            value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">

        <!-- Champ pour créer le mot de passe -->
        <label for="password">Mot de passe</label>
        <input
            type="password"
            name="password"
            id="password"
            required
            autocomplete="new-password">

        <!-- Aide visuelle : règles de complexité du mot de passe -->
        <ul class="regles-mdp">
            <li>Au moins 8 caractères</li>
            <li>Une lettre majuscule</li>
            <li>Une lettre minuscule</li>
            <li>Un chiffre</li>
            <li>Un caractère spécial (ex: !, @, #, $...)</li>
        </ul>

        <!-- Confirmation du mot de passe -->
        <label for="confirm">Confirmer le mot de passe</label>
        <input
            type="password"
            name="confirm"
            id="confirm"
            required
            autocomplete="new-password">

        <!-- Bouton d’envoi du formulaire -->
        <button type="submit">S'inscrire</button>
    </form>

    <!-- Lien vers la page de connexion -->
    <p class="retour-connexion">
        <a href="<?= BASE_URL ?>/auth/login">⬅️ Déjà inscrit ? Se connecter</a>
    </p>


</div>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>