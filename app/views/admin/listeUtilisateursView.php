<div class="admin-utilisateurs">

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
                <th>Désactiver</th>
                <th>Restaurer</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $u): ?>
                <tr>
                    <td data-label="ID"><?= $u['id_utilisateur'] ?></td>
                    <td data-label="Nom"><?= htmlspecialchars($u['nom']) ?></td>
                    <td data-label="Email"><?= htmlspecialchars($u['email']) ?></td>
                    <td data-label="Rôle actuel"><?= htmlspecialchars($u['role']) ?></td>
                    <td data-label="Changer de rôle">
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
                    <td data-label="Désactiver">
                        <form action="<?= BASE_URL ?>/admin/supprimerUtilisateur/<?= $u['id_utilisateur'] ?>" method="POST">
                            <button type="submit" class="btn btn-danger btn-desactiver">Désactiver</button>
                        </form>
                    </td>
                    <td data-label="Restaurer">
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

</div>
