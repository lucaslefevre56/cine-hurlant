<!-- views/utilisateur/profilView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<h2>Mon profil</h2>

<?php if (!empty($erreur)) : ?>
    <p style="color:red"><?= htmlspecialchars($erreur) ?></p>
<?php endif; ?>

<p><strong>Pseudo :</strong> <?= htmlspecialchars($utilisateur['nom']) ?></p>
<p><strong>Email :</strong> <?= htmlspecialchars($utilisateur['email']) ?></p>
<p><strong>Rôle :</strong> <?= htmlspecialchars($utilisateur['role']) ?></p>
<p><strong>Inscrit depuis :</strong> <?= date('d/m/Y H:i', strtotime($utilisateur['date_enregistrement'])) ?></p>

<!-- Lien optionnel pour modifier le mot de passe -->
<p><a href="<?= BASE_URL ?>/utilisateur/modifierMotDePasse">🔑 Modifier mon mot de passe</a></p>

<!-- Bouton de suppression -->
<form action="<?= BASE_URL ?>/utilisateur/supprimer" method="post">
    <button type="submit" class="btn btn-danger btn-desactiver">Désactiver mon compte</button>
</form>

<p>INFO : Un compte désactivé peut-être restauré sur demande à l'administrateur</p>

<p><a href="<?= BASE_URL ?>/">← Revenir à l’accueil</a></p>

<script src="<?= BASE_URL ?>/public/js/supprimer.js" defer></script>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
