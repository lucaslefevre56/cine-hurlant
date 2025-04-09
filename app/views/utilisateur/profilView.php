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
<form action="<?= BASE_URL ?>/utilisateur/supprimer" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
    <button type="submit" class="btn btn-danger">🗑️ Supprimer mon compte</button>
</form>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
