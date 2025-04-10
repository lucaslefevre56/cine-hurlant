<!-- app/views/admin/listeUtilisateursView.php -->

<?php
// Tableau pour traduire les rôles en libellé humain (aligné avec la BDD)
$labels = [
    'utilisateur' => 'Utilisateur',
    'redacteur' => 'Rédacteur',
    'admin' => 'Administrateur'
];
?>

<h2>Gestion des utilisateurs</h2>

<?php if (!empty($_SESSION['message'])): ?>
    <p style="color: green;"><?= htmlspecialchars($_SESSION['message']) ?></p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle actuel</th>
            <th>Changer de rôle</th>
            <th>Supprimer</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($utilisateurs as $u): ?>
            <tr>
                <td><?= $u['id_utilisateur'] ?></td>
                <td><?= htmlspecialchars($u['nom']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>

                <!-- Affiche le rôle actuel de manière lisible et robuste -->
                <?php $cleRole = strtolower(trim($u['role'])); ?>
                <td><?= $labels[$cleRole] ?? 'Rôle non défini' ?></td>

                <td>
                    <?php if ($u['id_utilisateur'] !== $_SESSION['user']['id']) : ?>
                        <form action="<?= BASE_URL ?>/admin/changerRole" method="post">
                            <input type="hidden" name="id_utilisateur" value="<?= $u['id_utilisateur'] ?>">
                            <select name="role">
                                <option value="utilisateur" <?= $cleRole === 'utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
                                <option value="redacteur" <?= $cleRole === 'redacteur' ? 'selected' : '' ?>>Rédacteur</option>
                                <option value="admin" <?= $cleRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <button type="submit">Modifier</button>
                        </form>
                    <?php else: ?>
                        Impossible de changer votre propre rôle...C'est comme ça...
                    <?php endif; ?>
                </td>

                <!-- Bouton de suppression -->
                <td>
                    <?php if ($u['id_utilisateur'] !== $_SESSION['user']['id']) : ?>
                        <form action="<?= BASE_URL ?>/admin/supprimerUtilisateur/<?= $u['id_utilisateur'] ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    <?php else: ?>
                        Impossible de supprimer votre propre compte.
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
