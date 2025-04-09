<!-- app/views/admin/listeUtilisateursView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

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
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
