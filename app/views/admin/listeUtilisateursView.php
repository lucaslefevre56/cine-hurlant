<!-- app/views/admin/listeUtilisateursView.php -->

<h2>Gestion des utilisateurs</h2>

<?php if (!empty($message)) : ?>
    <div class="message-success"><?= htmlspecialchars($message) ?></div>
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
            <th>Restaurer</th> <!-- Ajout d'une colonne pour restaurer -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($utilisateurs as $u): ?>
            <tr>
                <td><?= $u['id_utilisateur'] ?></td>
                <td><?= htmlspecialchars($u['nom']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['role']) ?></td>
                <td>
                    <form action="<?= BASE_URL ?>/admin/changerRole" method="post">
                        <input type="hidden" name="id_utilisateur" value="<?= $u['id_utilisateur'] ?>">
                        <select name="role">
                            <option value="utilisateur" <?= $u['role'] === 'utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
                            <option value="redacteur" <?= $u['role'] === 'redacteur' ? 'selected' : '' ?>>Rédacteur</option>
                            <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <button type="submit">Modifier</button>
                    </form>
                </td>
                <td>
                    <form action="<?= BASE_URL ?>/admin/supprimerUtilisateur/<?= $u['id_utilisateur'] ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </td>
                <td>
                    <?php if ($u['actif'] == 0): ?>
                        <form action="<?= BASE_URL ?>/admin/restaurerUtilisateur/<?= $u['id_utilisateur'] ?>" method="POST">
                            <button type="submit" class="btn btn-success">Restaurer</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
